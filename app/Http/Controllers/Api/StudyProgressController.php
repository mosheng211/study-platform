<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StudyProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudyProgressController extends Controller
{
    /**
     * 获取用户的学习进度列表
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $phase = $request->get('phase');
        $status = $request->get('status');

        $query = $user->studyProgress();

        if ($phase) {
            $query->where('phase', $phase);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $progress = $query->orderBy('day_number')->get();

        // 计算统计数据
        $stats = [
            'total_days' => $progress->count(),
            'completed_days' => $progress->where('status', 'completed')->count(),
            'in_progress_days' => $progress->where('status', 'in_progress')->count(),
            'not_started_days' => $progress->where('status', 'not_started')->count(),
            'total_study_hours' => $progress->sum('study_hours'),
            'average_score' => $progress->where('score', '>', 0)->avg('score'),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'progress' => $progress,
                'stats' => $stats,
            ]
        ]);
    }

    /**
     * 获取特定天数的学习进度
     */
    public function show(Request $request, $dayNumber)
    {
        $user = $request->user();
        
        $progress = $user->studyProgress()
            ->where('day_number', $dayNumber)
            ->first();

        if (!$progress) {
            return response()->json([
                'success' => false,
                'message' => '未找到该天的学习进度'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => ['progress' => $progress]
        ]);
    }

    /**
     * 更新学习进度
     */
    public function update(Request $request, $dayNumber)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|in:not_started,in_progress,completed',
            'study_hours' => 'sometimes|integer|min:0',
            'homework_submission' => 'sometimes|string',
            'notes' => 'sometimes|string',
            'score' => 'sometimes|integer|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        $progress = $user->studyProgress()
            ->where('day_number', $dayNumber)
            ->first();

        if (!$progress) {
            return response()->json([
                'success' => false,
                'message' => '未找到该天的学习进度'
            ], 404);
        }

        $updateData = $request->only([
            'status', 'study_hours', 'homework_submission', 'notes', 'score'
        ]);

        // 如果状态变为进行中，记录开始时间
        if ($request->status === 'in_progress' && $progress->status === 'not_started') {
            $updateData['started_at'] = now();
        }

        // 如果状态变为已完成，记录完成时间
        if ($request->status === 'completed' && $progress->status !== 'completed') {
            $updateData['completed_at'] = now();
            if ($request->homework_submission) {
                $updateData['homework_status'] = 'submitted';
            }
        }

        $progress->update($updateData);

        return response()->json([
            'success' => true,
            'message' => '学习进度更新成功',
            'data' => ['progress' => $progress]
        ]);
    }

    /**
     * 初始化用户的学习进度
     */
    public function initialize(Request $request)
    {
        $user = $request->user();

        // 检查是否已经初始化过
        if ($user->studyProgress()->exists()) {
            return response()->json([
                'success' => false,
                'message' => '学习进度已经初始化过了'
            ], 400);
        }

        // 54天学习计划数据
        $studyPlan = $this->getStudyPlanData();

        $progressData = [];
        foreach ($studyPlan as $day => $data) {
            $progressData[] = [
                'user_id' => $user->id,
                'day_number' => $day,
                'phase' => $data['phase'],
                'title' => $data['title'],
                'content' => $data['content'],
                'homework_description' => $data['homework'] ?? null,
                'status' => 'not_started',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        StudyProgress::insert($progressData);

        return response()->json([
            'success' => true,
            'message' => '学习进度初始化成功',
            'data' => ['total_days' => count($progressData)]
        ]);
    }

    /**
     * 获取学习统计数据
     */
    public function stats(Request $request)
    {
        $user = $request->user();
        $progress = $user->studyProgress;

        $phaseStats = [];
        $phases = ['web-basics', 'csharp-basics', 'vue-framework', 'automation'];

        foreach ($phases as $phase) {
            $phaseProgress = $progress->where('phase', $phase);
            $phaseStats[$phase] = [
                'total' => $phaseProgress->count(),
                'completed' => $phaseProgress->where('status', 'completed')->count(),
                'in_progress' => $phaseProgress->where('status', 'in_progress')->count(),
                'not_started' => $phaseProgress->where('status', 'not_started')->count(),
                'study_hours' => $phaseProgress->sum('study_hours'),
                'average_score' => $phaseProgress->where('score', '>', 0)->avg('score'),
            ];
        }

        $overallStats = [
            'total_days' => $progress->count(),
            'completed_days' => $progress->where('status', 'completed')->count(),
            'completion_rate' => $progress->count() > 0 ? 
                round($progress->where('status', 'completed')->count() / $progress->count() * 100, 2) : 0,
            'total_study_hours' => $progress->sum('study_hours'),
            'average_score' => $progress->where('score', '>', 0)->avg('score'),
            'current_phase' => $this->getCurrentPhase($progress),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'overall' => $overallStats,
                'phases' => $phaseStats,
            ]
        ]);
    }

    /**
     * 获取当前学习阶段
     */
    private function getCurrentPhase($progress)
    {
        $inProgress = $progress->where('status', 'in_progress')->first();
        if ($inProgress) {
            return $inProgress->phase;
        }

        $lastCompleted = $progress->where('status', 'completed')
            ->sortByDesc('day_number')
            ->first();

        if (!$lastCompleted) {
            return 'web-basics';
        }

        $nextDay = $progress->where('day_number', '>', $lastCompleted->day_number)
            ->sortBy('day_number')
            ->first();

        return $nextDay ? $nextDay->phase : 'completed';
    }

    /**
     * 获取54天学习计划数据
     */
    private function getStudyPlanData()
    {
        return [
            1 => ['phase' => 'web-basics', 'title' => 'HTML 入门：标签、结构、常用元素', 'content' => '学习HTML基础标签和页面结构', 'homework' => '创建一个简单的个人介绍页面'],
            2 => ['phase' => 'web-basics', 'title' => 'HTML 表单与输入控件', 'content' => '学习表单元素和用户输入', 'homework' => '制作一个注册表单'],
            3 => ['phase' => 'web-basics', 'title' => 'CSS 基础：选择器、颜色、字体、背景', 'content' => '学习CSS基础样式设置', 'homework' => '给之前的页面添加样式'],
            4 => ['phase' => 'web-basics', 'title' => 'CSS 盒模型、布局方式（flex/grid）', 'content' => '学习CSS布局技术', 'homework' => '使用 flex 布局制作响应式导航栏'],
            5 => ['phase' => 'web-basics', 'title' => 'JavaScript 基础语法：变量、函数、条件语句', 'content' => '学习JavaScript基础语法', 'homework' => '写一个计算BMI的脚本'],
            // ... 继续添加其他49天的数据
            // 为了简化，这里只展示前5天，实际应用中需要完整的54天数据
        ];
    }
}
