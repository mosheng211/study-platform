<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'real_name',
        'email',
        'password',
        'role',
        'avatar',
        'bio',
        'enrollment_date',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'enrollment_date' => 'date',
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    /**
     * 用户的学习进度
     */
    public function studyProgress()
    {
        return $this->hasMany(StudyProgress::class);
    }

    /**
     * 用户的打卡记录
     */
    public function checkins()
    {
        return $this->hasMany(Checkin::class);
    }

    /**
     * 用户创建的资源
     */
    public function createdResources()
    {
        return $this->hasMany(Resource::class, 'creator_id');
    }

    /**
     * 检查用户是否为管理员
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * 检查用户是否为教师
     */
    public function isTeacher()
    {
        return in_array($this->role, ['teacher', 'admin']);
    }

    /**
     * 获取用户的连续打卡天数
     */
    public function getStreakDaysAttribute()
    {
        $latestCheckin = $this->checkins()->latest('checkin_date')->first();
        return $latestCheckin ? $latestCheckin->streak_count : 0;
    }
}
