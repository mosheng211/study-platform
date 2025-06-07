<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ResourceController extends Controller
{
    /**
     * 获取资源列表
     */
    public function index(Request $request)
    {
        $query = Resource::with('creator:id,username,real_name')
            ->active();

        // 搜索
        if ($request->has('search') && !empty($request->get('search'))) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // 分类筛选
        if ($request->has('category') && !empty($request->get('category'))) {
            $query->byCategory($request->get('category'));
        }

        // 类型筛选
        if ($request->has('type') && !empty($request->get('type'))) {
            $query->byType($request->get('type'));
        }

        // 难度筛选
        if ($request->has('difficulty') && !empty($request->get('difficulty'))) {
            $query->where('difficulty', $request->get('difficulty'));
        }

        // 推荐资源
        if ($request->get('featured') === 'true') {
            $query->featured();
        }

        // 排序
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (in_array($sortBy, ['created_at', 'rating', 'view_count', 'download_count'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // 分页
        $perPage = min($request->get('per_page', 15), 50);
        $resources = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $resources
        ]);
    }

    /**
     * 创建新资源
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:video,document,link,book,tool',
            'category' => 'required|string|max:255',
            'url' => 'nullable|url',
            'file_path' => 'nullable|string',
            'thumbnail' => 'nullable|string',
            'duration' => 'nullable|integer|min:0',
            'difficulty' => 'required|in:beginner,intermediate,advanced',
            'tags' => 'nullable|array',
            'is_featured' => 'boolean',
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
            'type' => $request->type,
            'category' => $request->category,
            'url' => $request->url,
            'file_path' => $request->file_path,
            'thumbnail' => $request->thumbnail,
            'duration' => $request->duration,
            'difficulty' => $request->difficulty,
            'tags' => $request->tags,
            'creator_id' => $request->user()->id,
            'is_featured' => $request->get('is_featured', false),
        ]);

        $resource->load('creator:id,username,real_name');

        return response()->json([
            'success' => true,
            'message' => '资源创建成功',
            'data' => ['resource' => $resource]
        ], 201);
    }

    /**
     * 获取单个资源详情
     */
    public function show($id)
    {
        $resource = Resource::with('creator:id,username,real_name')
            ->active()
            ->find($id);

        if (!$resource) {
            return response()->json([
                'success' => false,
                'message' => '资源不存在'
            ], 404);
        }

        // 增加浏览次数
        $resource->incrementViewCount();

        return response()->json([
            'success' => true,
            'data' => ['resource' => $resource]
        ]);
    }

    /**
     * 更新资源
     */
    public function update(Request $request, $id)
    {
        $resource = Resource::find($id);

        if (!$resource) {
            return response()->json([
                'success' => false,
                'message' => '资源不存在'
            ], 404);
        }

        $user = $request->user();

        // 检查权限：只有创建者或管理员可以编辑
        if ($resource->creator_id !== $user->id && !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => '没有权限编辑此资源'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'type' => 'sometimes|in:video,document,link,book,tool',
            'category' => 'sometimes|string|max:255',
            'url' => 'nullable|url',
            'file_path' => 'nullable|string',
            'thumbnail' => 'nullable|string',
            'duration' => 'nullable|integer|min:0',
            'difficulty' => 'sometimes|in:beginner,intermediate,advanced',
            'tags' => 'nullable|array',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = $request->only([
            'title', 'description', 'type', 'category', 'url', 'file_path',
            'thumbnail', 'duration', 'difficulty', 'tags'
        ]);

        // 只有管理员可以设置推荐和启用状态
        if ($user->isAdmin()) {
            $updateData = array_merge($updateData, $request->only(['is_featured', 'is_active']));
        }

        $resource->update($updateData);
        $resource->load('creator:id,username,real_name');

        return response()->json([
            'success' => true,
            'message' => '资源更新成功',
            'data' => ['resource' => $resource]
        ]);
    }

    /**
     * 删除资源
     */
    public function destroy(Request $request, $id)
    {
        $resource = Resource::find($id);

        if (!$resource) {
            return response()->json([
                'success' => false,
                'message' => '资源不存在'
            ], 404);
        }

        $user = $request->user();

        // 检查权限：只有创建者或管理员可以删除
        if ($resource->creator_id !== $user->id && !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => '没有权限删除此资源'
            ], 403);
        }

        $resource->delete();

        return response()->json([
            'success' => true,
            'message' => '资源删除成功'
        ]);
    }

    /**
     * 获取资源分类列表
     */
    public function categories()
    {
        $categories = Category::active()
            ->ordered()
            ->select('id', 'name', 'slug', 'description', 'color', 'icon')
            ->withCount('resources')
            ->get();

        return response()->json([
            'success' => true,
            'data' => ['categories' => $categories]
        ]);
    }

    /**
     * 下载资源
     */
    public function download($id)
    {
        $resource = Resource::active()->find($id);

        if (!$resource) {
            return response()->json([
                'success' => false,
                'message' => '资源不存在'
            ], 404);
        }

        if (!$resource->file_path && !$resource->url) {
            return response()->json([
                'success' => false,
                'message' => '该资源不支持下载'
            ], 400);
        }

        // 增加下载次数
        $resource->incrementDownloadCount();

        return response()->json([
            'success' => true,
            'data' => [
                'download_url' => $resource->url ?: $resource->file_path,
                'filename' => $resource->title
            ]
        ]);
    }

    /**
     * 获取推荐资源
     */
    public function featured()
    {
        $resources = Resource::with('creator:id,username,real_name')
            ->active()
            ->featured()
            ->orderBy('rating', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => ['resources' => $resources]
        ]);
    }
}
