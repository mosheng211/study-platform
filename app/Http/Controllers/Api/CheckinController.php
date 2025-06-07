<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Checkin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class CheckinController extends Controller
{
    /**
     * 获取用户打卡记录
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $year = $request->get('year', Carbon::now()->year);
        $month = $request->get('month', Carbon::now()->month);

        $checkins = $user->checkins()
            ->whereYear('checkin_date', $year)
            ->whereMonth('checkin_date', $month)
            ->orderBy('checkin_date', 'desc')
            ->get();

        // 计算本月统计
        $stats = [
            'total_checkins' => $checkins->count(),
            'total_study_minutes' => $checkins->sum('study_minutes'),
            'average_study_minutes' => $checkins->avg('study_minutes'),
            'current_streak' => $this->getCurrentStreak($user->id),
            'longest_streak' => $this->getLongestStreak($user->id),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'checkins' => $checkins,
                'stats' => $stats,
            ]
        ]);
    }

    /**
     * 今日打卡
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today();

        // 检查今天是否已经打卡
        $existingCheckin = $user->checkins()
            ->where('checkin_date', $today)
            ->first();

        if ($existingCheckin) {
            return response()->json([
                'success' => false,
                'message' => '今天已经打卡过了'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'studyTime' => 'required|numeric|min:0.5|max:24', // 支持小数，最多24小时
            'content' => 'required|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'quality' => 'required|integer|min:1|max:5', // 1-5分评分
            'tags' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        // 计算连续打卡天数
        $streakCount = $this->calculateNewStreak($user->id);

        // 将小时转换为分钟
        $studyMinutes = intval($request->studyTime * 60);

        $checkin = Checkin::create([
            'user_id' => $user->id,
            'checkin_date' => $today,
            'study_minutes' => $studyMinutes,
            'study_content' => $request->content,
            'notes' => $request->notes,
            'mood' => $this->convertQualityToMood($request->quality),
            'completed_tasks' => $request->tags, // 使用tags作为完成任务
            'streak_count' => $streakCount,
        ]);

        return response()->json([
            'success' => true,
            'message' => '打卡成功！',
            'data' => ['checkin' => $checkin]
        ], 201);
    }

    /**
     * 获取今日打卡状态
     */
    public function todayStatus(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today();

        $todayCheckin = $user->checkins()
            ->where('checkin_date', $today)
            ->first();

        $hasCheckedIn = !is_null($todayCheckin);

        return response()->json([
            'success' => true,
            'data' => [
                'has_checked_in' => $hasCheckedIn,
                'checkin' => $todayCheckin,
                'current_streak' => $this->getCurrentStreak($user->id),
            ]
        ]);
    }

    /**
     * 获取打卡历史
     */
    public function history(Request $request)
    {
        $user = $request->user();
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = $user->checkins();

        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        } else {
            // 默认获取最近30天
            $query->dateRange(Carbon::now()->subDays(30), Carbon::now());
        }

        $checkins = $query->orderBy('checkin_date', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => ['checkins' => $checkins]
        ]);
    }

    /**
     * 获取打卡排行榜
     */
    public function leaderboard(Request $request)
    {
        $type = $request->get('type', 'monthly'); // monthly, weekly, all_time
        $limit = min($request->get('limit', 20), 100);

        $query = Checkin::with('user:id,username,real_name,avatar');

        switch ($type) {
            case 'weekly':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
            case 'monthly':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'all_time':
            default:
                $startDate = null;
                $endDate = null;
                break;
        }

        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }

        // 按用户分组，计算总学习时间和打卡天数
        $leaderboard = $query->selectRaw('
                user_id,
                COUNT(*) as checkin_days,
                SUM(study_minutes) as total_study_minutes,
                MAX(streak_count) as max_streak
            ')
            ->groupBy('user_id')
            ->orderByDesc('total_study_minutes')
            ->limit($limit)
            ->get();

        // 加载用户信息
        $leaderboard->load('user:id,username,real_name,avatar');

        return response()->json([
            'success' => true,
            'data' => [
                'leaderboard' => $leaderboard,
                'type' => $type,
            ]
        ]);
    }

    /**
     * 获取打卡统计数据
     */
    public function stats(Request $request)
    {
        $user = $request->user();

        $allCheckins = $user->checkins;
        $thisWeekCheckins = $user->checkins()
            ->whereBetween('checkin_date', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])
            ->get();

        // 计算本周打卡率（本周已打卡天数 / 7天）
        $weeklyRate = round(($thisWeekCheckins->count() / 7) * 100, 1);

        // 计算平均学习时长（转换为小时，保留1位小数）
        $avgStudyMinutes = $allCheckins->avg('study_minutes') ?: 0;
        $avgStudyTime = round($avgStudyMinutes / 60, 1);

        $stats = [
            'totalDays' => $allCheckins->count(),
            'consecutiveDays' => $this->getCurrentStreak($user->id),
            'avgStudyTime' => $avgStudyTime,
            'weeklyRate' => $weeklyRate,
            
            // 保持原有数据结构，便于其他功能使用
            'all_time' => [
                'total_checkins' => $allCheckins->count(),
                'total_study_minutes' => $allCheckins->sum('study_minutes'),
                'average_study_minutes' => $avgStudyMinutes,
                'longest_streak' => $this->getLongestStreak($user->id),
            ],
            'this_week' => [
                'total_checkins' => $thisWeekCheckins->count(),
                'total_study_minutes' => $thisWeekCheckins->sum('study_minutes'),
                'average_study_minutes' => $thisWeekCheckins->avg('study_minutes'),
            ],
            'current_streak' => $this->getCurrentStreak($user->id),
        ];

        return response()->json([
            'success' => true,
            'data' => ['stats' => $stats]
        ]);
    }

    /**
     * 获取打卡日期列表
     */
    public function dates(Request $request)
    {
        $user = $request->user();
        $year = $request->get('year', Carbon::now()->year);
        $month = $request->get('month', Carbon::now()->month);

        // 获取指定年月的所有打卡日期
        $checkinDates = $user->checkins()
            ->whereYear('checkin_date', $year)
            ->whereMonth('checkin_date', $month)
            ->pluck('checkin_date')
            ->map(function ($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })
            ->toArray();

        return response()->json([
            'success' => true,
            'data' => [
                'dates' => $checkinDates,
                'year' => $year,
                'month' => $month,
                'total' => count($checkinDates)
            ]
        ]);
    }

    /**
     * 更新打卡记录
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $checkin = Checkin::where('user_id', $user->id)->find($id);

        if (!$checkin) {
            return response()->json([
                'success' => false,
                'message' => '打卡记录不存在'
            ], 404);
        }

        // 只能修改今天的打卡记录
        if (!$checkin->checkin_date->isToday()) {
            return response()->json([
                'success' => false,
                'message' => '只能修改今天的打卡记录'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'study_minutes' => 'sometimes|integer|min:1|max:1440',
            'study_content' => 'sometimes|string|max:1000',
            'notes' => 'sometimes|string|max:1000',
            'mood' => 'sometimes|in:excellent,good,normal,tired,difficult',
            'completed_tasks' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        $checkin->update($request->only([
            'study_minutes', 'study_content', 'notes', 'mood', 'completed_tasks'
        ]));

        return response()->json([
            'success' => true,
            'message' => '打卡记录更新成功',
            'data' => ['checkin' => $checkin]
        ]);
    }

    /**
     * 计算当前连续打卡天数
     */
    private function getCurrentStreak($userId)
    {
        return Checkin::calculateStreakForUser($userId);
    }

    /**
     * 计算历史最长连续打卡天数
     */
    private function getLongestStreak($userId)
    {
        $checkins = Checkin::where('user_id', $userId)
            ->orderBy('checkin_date')
            ->pluck('checkin_date');

        if ($checkins->isEmpty()) {
            return 0;
        }

        $maxStreak = 1;
        $currentStreak = 1;

        for ($i = 1; $i < $checkins->count(); $i++) {
            $currentDate = Carbon::parse($checkins[$i]);
            $previousDate = Carbon::parse($checkins[$i - 1]);

            if ($currentDate->diffInDays($previousDate) === 1) {
                $currentStreak++;
                $maxStreak = max($maxStreak, $currentStreak);
            } else {
                $currentStreak = 1;
            }
        }

        return $maxStreak;
    }

    /**
     * 计算新的连续打卡天数
     */
    private function calculateNewStreak($userId)
    {
        $yesterday = Carbon::yesterday();
        $yesterdayCheckin = Checkin::where('user_id', $userId)
            ->where('checkin_date', $yesterday)
            ->first();

        if ($yesterdayCheckin) {
            return $yesterdayCheckin->streak_count + 1;
        }

        return 1;
    }

    /**
     * 获取最近成就
     */
    public function achievements(Request $request)
    {
        $limit = min($request->get('limit', 10), 50);
        
        // 获取最近的优秀打卡记录（质量评分>=4）
        $recentAchievements = Checkin::with('user:id,username,real_name,avatar')
            ->where('mood', 'excellent')
            ->orWhere('mood', 'good')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($checkin) {
                return [
                    'id' => $checkin->id,
                    'user' => [
                        'id' => $checkin->user->id,
                        'username' => $checkin->user->username,
                        'real_name' => $checkin->user->real_name ?? $checkin->user->username,
                        'avatar' => $checkin->user->avatar,
                    ],
                    'achievement_type' => $checkin->mood === 'excellent' ? 'excellent_study' : 'good_study',
                    'achievement_text' => $checkin->mood === 'excellent' ? '优秀学习' : '良好学习',
                    'study_content' => $checkin->study_content,
                    'study_time' => $checkin->formatted_study_time,
                    'created_at' => $checkin->created_at->format('Y-m-d H:i:s'),
                    'checkin_date' => $checkin->checkin_date->format('Y-m-d'),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'achievements' => $recentAchievements,
                'total' => $recentAchievements->count(),
            ]
        ]);
    }

    /**
     * 获取个人排名
     */
    public function personalRank(Request $request)
    {
        $user = $request->user();
        $type = $request->get('type', 'monthly');

        // 获取个人统计数据
        $personalStats = $this->getUserStats($user->id, $type);
        
        // 获取个人在排行榜中的位置
        $rank = $this->getUserRankPosition($user->id, $type);
        
        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'real_name' => $user->real_name ?? $user->username,
                    'avatar' => $user->avatar,
                ],
                'rank' => $rank,
                'stats' => $personalStats,
                'type' => $type,
            ]
        ]);
    }

    /**
     * 获取用户统计数据
     */
    private function getUserStats($userId, $type)
    {
        $query = Checkin::where('user_id', $userId);

        switch ($type) {
            case 'weekly':
                $query->dateRange(Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek());
                break;
            case 'monthly':
                $query->dateRange(Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth());
                break;
        }

        $checkins = $query->get();

        return [
            'checkin_days' => $checkins->count(),
            'total_study_minutes' => $checkins->sum('study_minutes'),
            'total_study_hours' => round($checkins->sum('study_minutes') / 60, 1),
            'average_study_minutes' => $checkins->avg('study_minutes'),
            'current_streak' => $this->getCurrentStreak($userId),
        ];
    }

    /**
     * 获取用户排名位置
     */
    private function getUserRankPosition($userId, $type)
    {
        $query = Checkin::selectRaw('
                user_id,
                SUM(study_minutes) as total_study_minutes
            ');

        switch ($type) {
            case 'weekly':
                $query->dateRange(Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek());
                break;
            case 'monthly':
                $query->dateRange(Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth());
                break;
        }

        $rankings = $query->groupBy('user_id')
            ->orderByDesc('total_study_minutes')
            ->pluck('total_study_minutes', 'user_id');

        $position = 1;
        foreach ($rankings as $uid => $minutes) {
            if ($uid == $userId) {
                return $position;
            }
            $position++;
        }

        return null; // 用户未在排行榜中
    }

    /**
     * 将质量评分转换为心情
     */
    private function convertQualityToMood($quality)
    {
        switch ($quality) {
            case 5:
                return 'excellent';
            case 4:
                return 'good';
            case 3:
                return 'normal';
            case 2:
                return 'tired';
            case 1:
            default:
                return 'difficult';
        }
    }
}
