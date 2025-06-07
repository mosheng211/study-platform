<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Checkin extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'checkin_date',
        'study_minutes',
        'study_content',
        'notes',
        'mood',
        'completed_tasks',
        'streak_count',
    ];

    protected $casts = [
        'checkin_date' => 'date',
        'completed_tasks' => 'array',
    ];

    protected $appends = [
        'study_time',
        'content',
        'quality',
        'tags'
    ];

    /**
     * 关联用户
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 获取心情的中文名称
     */
    public function getMoodNameAttribute()
    {
        $moods = [
            'excellent' => '非常好',
            'good' => '良好',
            'normal' => '一般',
            'tired' => '疲惫',
            'difficult' => '困难',
        ];

        return $moods[$this->mood] ?? $this->mood;
    }

    /**
     * 获取学习时长的格式化文本
     */
    public function getFormattedStudyTimeAttribute()
    {
        $hours = floor($this->study_minutes / 60);
        $minutes = $this->study_minutes % 60;

        if ($hours > 0) {
            return $hours . '小时' . ($minutes > 0 ? $minutes . '分钟' : '');
        }

        return $minutes . '分钟';
    }

    /**
     * 获取以小时为单位的学习时长（前端兼容）
     */
    public function getStudyTimeAttribute()
    {
        return round($this->study_minutes / 60, 1);
    }

    /**
     * 获取学习内容（前端兼容）
     */
    public function getContentAttribute()
    {
        return $this->study_content;
    }

    /**
     * 获取质量评分（前端兼容）
     */
    public function getQualityAttribute()
    {
        // 根据mood转换为1-5的评分
        $moodToQuality = [
            'excellent' => 5,
            'good' => 4,
            'normal' => 3,
            'tired' => 2,
            'difficult' => 1,
        ];

        return $moodToQuality[$this->mood] ?? 3;
    }

    /**
     * 获取标签（前端兼容）
     */
    public function getTagsAttribute()
    {
        return $this->completed_tasks ?? [];
    }

    /**
     * 检查是否为今日打卡
     */
    public function isTodayCheckin()
    {
        return $this->checkin_date->isToday();
    }

    /**
     * 作用域：获取指定用户的打卡记录
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * 作用域：获取指定日期范围的打卡记录
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('checkin_date', [$startDate, $endDate]);
    }

    /**
     * 作用域：获取本月的打卡记录
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('checkin_date', Carbon::now()->month)
                    ->whereYear('checkin_date', Carbon::now()->year);
    }

    /**
     * 计算连续打卡天数
     */
    public static function calculateStreakForUser($userId)
    {
        $checkins = self::where('user_id', $userId)
            ->orderBy('checkin_date', 'desc')
            ->get();

        if ($checkins->isEmpty()) {
            return 0;
        }

        $streak = 1;
        $currentDate = $checkins->first()->checkin_date;

        foreach ($checkins->skip(1) as $checkin) {
            $expectedDate = $currentDate->copy()->subDay();
            
            if ($checkin->checkin_date->equalTo($expectedDate)) {
                $streak++;
                $currentDate = $checkin->checkin_date;
            } else {
                break;
            }
        }

        return $streak;
    }
}
