<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'day_number',
        'phase',
        'title',
        'content',
        'study_hours',
        'status',
        'homework_description',
        'homework_submission',
        'homework_status',
        'score',
        'notes',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * 关联用户
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 获取学习阶段的中文名称
     */
    public function getPhaseNameAttribute()
    {
        $phases = [
            'web-basics' => 'Web技术基础',
            'csharp-basics' => 'C#编程基础',
            'vue-framework' => 'Vue.js框架',
            'automation' => '浏览器自动化',
        ];

        return $phases[$this->phase] ?? $this->phase;
    }

    /**
     * 获取状态的中文名称
     */
    public function getStatusNameAttribute()
    {
        $statuses = [
            'not_started' => '未开始',
            'in_progress' => '进行中',
            'completed' => '已完成',
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * 检查是否已完成
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * 标记为完成
     */
    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }
}
