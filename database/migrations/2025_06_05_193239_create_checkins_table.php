<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('checkins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('checkin_date'); // 打卡日期
            $table->integer('study_minutes')->default(0); // 学习时长(分钟)
            $table->text('study_content')->nullable(); // 学习内容
            $table->text('notes')->nullable(); // 学习心得
            $table->enum('mood', ['excellent', 'good', 'normal', 'tired', 'difficult'])->default('normal'); // 学习心情
            $table->json('completed_tasks')->nullable(); // 完成的任务列表
            $table->integer('streak_count')->default(1); // 连续打卡天数
            $table->timestamps();
            
            $table->unique(['user_id', 'checkin_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkins');
    }
};
