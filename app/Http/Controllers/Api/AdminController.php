<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Checkin;
use App\Models\StudyProgress;
use App\Models\Resource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * 获取系统统计数据
     */
    public function getSystemStats(Request $request)
    {
        // 基础统计
        $totalUsers = User::count();
        $totalCheckins = Checkin::count();
        $totalResources = Resource::count();
        $activeUsers = User::whereHas('checkins', function($query) {
            $query->where('checkin_date', '>=', Carbon::now()->subDays(7));
        })->count();

        // 今日统计
        $todayCheckins = Checkin::whereDate('checkin_date', Carbon::today())->count();
        $todayRegistrations = User::whereDate('created_at', Carbon::today())->count();

        // 本月统计
        $monthlyCheckins = Checkin::whereMonth('checkin_date', Carbon::now()->month)
            ->whereYear('checkin_date', Carbon::now()->year)
            ->count();
        
        $monthlyRegistrations = User::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // 学习时长统计
        $totalStudyMinutes = Checkin::sum('study_minutes');
        $avgStudyMinutes = Checkin::avg('study_minutes');

        // 最近7天的打卡统计
        $recentCheckins = Checkin::selectRaw('DATE(checkin_date) as date, COUNT(*) as count')
            ->where('checkin_date', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'overview' => [
                    'total_users' => $totalUsers,
                    'total_checkins' => $totalCheckins,
                    'total_resources' => $totalResources,
                    'active_users' => $activeUsers,
                ],
                'today' => [
                    'checkins' => $todayCheckins,
                    'registrations' => $todayRegistrations,
                ],
                'monthly' => [
                    'checkins' => $monthlyCheckins,
                    'registrations' => $monthlyRegistrations,
                ],
                'study_stats' => [
                    'total_minutes' => $totalStudyMinutes,
                    'total_hours' => round($totalStudyMinutes / 60, 1),
                    'average_minutes' => round($avgStudyMinutes, 1),
                ],
                'recent_checkins' => $recentCheckins,
            ]
        ]);
    }

    /**
     * 获取用户列表
     */
    public function getUsers(Request $request)
    {
        $query = User::query();
        
        // 搜索过滤
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('real_name', 'like', "%{$search}%");
            });
        }

        // 角色过滤
        if ($request->has('role')) {
            $query->where('role', $request->get('role'));
        }

        // 状态过滤
        if ($request->has('status')) {
            if ($request->get('status') === 'active') {
                $query->whereNull('deleted_at');
            }
        }

        $users = $query->withCount(['checkins', 'studyProgress'])
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * 获取用户详情
     */
    public function getUserDetail(Request $request, $id)
    {
        $user = User::with(['checkins' => function($query) {
            $query->orderBy('checkin_date', 'desc')->limit(10);
        }, 'studyProgress'])->find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => '用户不存在'
            ], 404);
        }

        // 统计数据
        $stats = [
            'total_checkins' => $user->checkins()->count(),
            'total_study_minutes' => $user->checkins()->sum('study_minutes'),
            'current_streak' => Checkin::calculateStreakForUser($user->id),
            'avg_quality' => $user->checkins()->avg('mood'),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'stats' => $stats,
            ]
        ]);
    }

    /**
     * 创建用户
     */
    public function createUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'real_name' => 'nullable|string|max:255',
            'role' => 'required|in:admin,teacher,student',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'real_name' => $request->real_name,
            'role' => $request->role,
        ]);

        return response()->json([
            'success' => true,
            'message' => '用户创建成功',
            'data' => ['user' => $user]
        ], 201);
    }

    /**
     * 更新用户
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => '用户不存在'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'username' => 'sometimes|string|max:255|unique:users,username,' . $id,
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
            'real_name' => 'sometimes|string|max:255',
            'role' => 'sometimes|in:admin,teacher,student',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update($request->only(['username', 'email', 'real_name', 'role']));

        return response()->json([
            'success' => true,
            'message' => '用户更新成功',
            'data' => ['user' => $user]
        ]);
    }

    /**
     * 更新用户状态
     */
    public function updateUserStatus(Request $request, $id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => '用户不存在'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        // 这里可以根据需要实现用户状态逻辑
        // 例如设置 is_active 字段或者软删除

        return response()->json([
            'success' => true,
            'message' => '用户状态更新成功'
        ]);
    }

    /**
     * 删除用户
     */
    public function deleteUser(Request $request, $id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => '用户不存在'
            ], 404);
        }

        // 防止删除管理员账户
        if ($user->role === 'admin') {
            return response()->json([
                'success' => false,
                'message' => '不能删除管理员账户'
            ], 403);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => '用户删除成功'
        ]);
    }

    /**
     * 获取用户进度
     */
    public function getUserProgress(Request $request, $userId)
    {
        $user = User::find($userId);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => '用户不存在'
            ], 404);
        }

        $progress = $user->studyProgress()->orderBy('day_number')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'progress' => $progress,
            ]
        ]);
    }

    /**
     * 获取用户打卡记录
     */
    public function getUserCheckins(Request $request, $userId)
    {
        $user = User::find($userId);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => '用户不存在'
            ], 404);
        }

        $checkins = $user->checkins()
            ->orderBy('checkin_date', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $checkins
        ]);
    }

    /**
     * 获取学习数据统计
     */
    public function getStudyData(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subDays(30));
        $endDate = $request->get('end_date', Carbon::now());

        // 按日期统计打卡数据
        $dailyStats = Checkin::selectRaw('
                DATE(checkin_date) as date,
                COUNT(*) as checkin_count,
                SUM(study_minutes) as total_minutes,
                AVG(study_minutes) as avg_minutes
            ')
            ->dateRange($startDate, $endDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // 按用户统计
        $userStats = Checkin::selectRaw('
                user_id,
                COUNT(*) as checkin_count,
                SUM(study_minutes) as total_minutes
            ')
            ->with('user:id,username,real_name')
            ->dateRange($startDate, $endDate)
            ->groupBy('user_id')
            ->orderByDesc('total_minutes')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'daily_stats' => $dailyStats,
                'user_stats' => $userStats,
                'date_range' => [
                    'start' => $startDate,
                    'end' => $endDate,
                ]
            ]
        ]);
    }

    /**
     * 获取资源管理列表
     */
    public function getResources(Request $request)
    {
        $query = Resource::query();
        
        if ($request->has('search') && !empty($request->get('search'))) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('type') && !empty($request->get('type'))) {
            $query->where('type', $request->get('type'));
        }

        if ($request->has('category_id') && !empty($request->get('category_id'))) {
            $query->where('category_id', $request->get('category_id'));
        }

        $resources = $query->with(['creator:id,username,real_name', 'category:id,name,color'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $resources
        ]);
    }

    /**
     * 获取资源详情
     */
    public function getResourceDetail(Request $request, $id)
    {
        $resource = Resource::with('creator:id,username,real_name', 'category')->find($id);
        
        if (!$resource) {
            return response()->json([
                'success' => false,
                'message' => '资源不存在'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => ['resource' => $resource]
        ]);
    }

    /**
     * 创建资源
     */
    public function createResource(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'content' => 'nullable|string',
            'type' => 'required|in:article,video,audio,document,link',
            'category_id' => 'required|exists:categories,id',
            'difficulty' => 'nullable|in:beginner,intermediate,advanced',
            'url' => 'nullable|url',
            'file_path' => 'nullable|string|max:500',
            'duration' => 'nullable|integer|min:0',
            'tags' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        $resource = Resource::create([
            'title' => $request->title,
            'description' => $request->description,
            'content' => $request->content,
            'type' => $request->type,
            'category_id' => $request->category_id,
            'difficulty' => $request->difficulty ?? 'beginner',
            'url' => $request->url,
            'file_path' => $request->file_path,
            'duration' => $request->duration,
            'tags' => $request->tags,
            'sort_order' => $request->sort_order ?? 1,
            'is_featured' => $request->is_featured ?? false,
            'is_published' => $request->is_published ?? true,
            'creator_id' => auth()->id(), // 设置创建者
            'published_at' => $request->is_published ? now() : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => '资源创建成功',
            'data' => ['resource' => $resource->load('creator:id,username,real_name', 'category')]
        ], 201);
    }

    /**
     * 更新资源
     */
    public function updateResource(Request $request, $id)
    {
        $resource = Resource::find($id);
        
        if (!$resource) {
            return response()->json([
                'success' => false,
                'message' => '资源不存在'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:1000',
            'content' => 'sometimes|nullable|string',
            'type' => 'sometimes|in:article,video,audio,document,link',
            'category_id' => 'sometimes|exists:categories,id',
            'difficulty' => 'sometimes|nullable|in:beginner,intermediate,advanced',
            'url' => 'sometimes|nullable|url',
            'file_path' => 'sometimes|nullable|string|max:500',
            'duration' => 'sometimes|nullable|integer|min:0',
            'tags' => 'sometimes|nullable|string|max:500',
            'sort_order' => 'sometimes|integer|min:0',
            'is_featured' => 'sometimes|boolean',
            'is_published' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        // 如果发布状态改变，更新发布时间
        if ($request->has('is_published')) {
            if ($request->is_published && !$resource->is_published) {
                $request->merge(['published_at' => now()]);
            } elseif (!$request->is_published) {
                $request->merge(['published_at' => null]);
            }
        }

        $resource->update($request->only([
            'title', 'description', 'content', 'type', 'category_id', 
            'difficulty', 'url', 'file_path', 'duration', 'tags', 
            'sort_order', 'is_featured', 'is_published', 'published_at'
        ]));

        return response()->json([
            'success' => true,
            'message' => '资源更新成功',
            'data' => ['resource' => $resource->load('creator:id,username,real_name', 'category')]
        ]);
    }

    /**
     * 删除资源
     */
    public function deleteResource(Request $request, $id)
    {
        $resource = Resource::find($id);
        
        if (!$resource) {
            return response()->json([
                'success' => false,
                'message' => '资源不存在'
            ], 404);
        }

        $resource->delete();

        return response()->json([
            'success' => true,
            'message' => '资源删除成功'
        ]);
    }

    /**
     * 获取系统设置
     */
    public function getSystemSettings(Request $request)
    {
        // 模拟系统设置数据
        $settings = [
            'site_name' => '编程学习计划管理平台',
            'registration_enabled' => true,
            'max_upload_size' => 10, // MB
            'allowed_file_types' => ['pdf', 'doc', 'docx', 'ppt', 'pptx'],
            'study_plan_duration' => 54, // 天数
        ];

        return response()->json([
            'success' => true,
            'data' => ['settings' => $settings]
        ]);
    }

    /**
     * 更新系统设置
     */
    public function updateSystemSettings(Request $request)
    {
        // 这里应该实现系统设置的更新逻辑
        return response()->json([
            'success' => true,
            'message' => '系统设置更新成功'
        ]);
    }

    /**
     * 获取分类列表
     */
    public function getCategories(Request $request)
    {
        $query = Category::query();
        
        // 搜索条件
        if ($request->has('search') && !empty($request->get('search'))) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // 状态筛选（只有当is_active有明确值时才添加条件）
        if ($request->has('is_active') && $request->get('is_active') !== '' && $request->get('is_active') !== null) {
            $query->where('is_active', $request->get('is_active'));
        }

        $categories = $query->withCount('resources')
            ->ordered()
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * 获取分类详情
     */
    public function getCategoryDetail(Request $request, $id)
    {
        $category = Category::withCount('resources')->find($id);
        
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => '分类不存在'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => ['category' => $category]
        ]);
    }

    /**
     * 创建分类
     */
    public function createCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories',
            'slug' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string|max:1000',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        $category = Category::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'description' => $request->description,
            'color' => $request->color ?? '#1890ff',
            'icon' => $request->icon,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->is_active ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => '分类创建成功',
            'data' => ['category' => $category]
        ], 201);
    }

    /**
     * 更新分类
     */
    public function updateCategory(Request $request, $id)
    {
        $category = Category::find($id);
        
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => '分类不存在'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255|unique:categories,name,' . $id,
            'slug' => 'sometimes|string|max:255|unique:categories,slug,' . $id,
            'description' => 'sometimes|nullable|string|max:1000',
            'color' => 'sometimes|nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon' => 'sometimes|nullable|string|max:255',
            'sort_order' => 'sometimes|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        $category->update($request->only([
            'name', 'slug', 'description', 'color', 'icon', 'sort_order', 'is_active'
        ]));

        return response()->json([
            'success' => true,
            'message' => '分类更新成功',
            'data' => ['category' => $category]
        ]);
    }

    /**
     * 删除分类
     */
    public function deleteCategory(Request $request, $id)
    {
        $category = Category::find($id);
        
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => '分类不存在'
            ], 404);
        }

        // 检查是否有资源使用此分类
        $resourceCount = $category->resources()->count();
        if ($resourceCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "该分类下还有 {$resourceCount} 个资源，无法删除"
            ], 400);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => '分类删除成功'
        ]);
    }

    /**
     * 获取所有激活的分类（用于下拉选择）
     */
    public function getActiveCategories(Request $request)
    {
        $categories = Category::active()
            ->ordered()
            ->select('id', 'name', 'slug', 'color', 'icon')
            ->get();

        return response()->json([
            'success' => true,
            'data' => ['categories' => $categories]
        ]);
    }

    /**
     * 数据导出
     */
    public function exportData(Request $request)
    {
        $type = $request->get('type', 'users');
        
        switch ($type) {
            case 'users':
                $data = User::with(['checkins', 'studyProgress'])->get();
                break;
            case 'checkins':
                $data = Checkin::with('user')->get();
                break;
            case 'progress':
                $data = StudyProgress::with('user')->get();
                break;
            case 'categories':
                $data = Category::withCount('resources')->get();
                break;
            default:
                return response()->json([
                    'success' => false,
                    'message' => '不支持的导出类型'
                ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $data,
            'type' => $type,
            'exported_at' => now(),
        ]);
    }
} 