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
        Schema::create('study_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('day_number'); // 第几天 (1-54)
            $table->string('phase'); // 学习阶段 (web-basics, csharp-basics, vue-framework, automation)
            $table->string('title'); // 当天学习标题
            $table->text('content'); // 学习内容描述
            $table->integer('study_hours')->default(0); // 学习时长(分钟)
            $table->enum('status', ['not_started', 'in_progress', 'completed'])->default('not_started');
            $table->text('homework_description')->nullable(); // 作业描述
            $table->text('homework_submission')->nullable(); // 作业提交内容
            $table->enum('homework_status', ['not_submitted', 'submitted', 'reviewed'])->default('not_submitted');
            $table->integer('score')->nullable(); // 评分 (0-100)
            $table->text('notes')->nullable(); // 学习笔记
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'day_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_progress');
    }
};
