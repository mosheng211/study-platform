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
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['video', 'document', 'link', 'book', 'tool'])->default('document');
            $table->string('category'); // HTML/CSS, JavaScript, C#, Vue.js, 自动化等
            $table->string('url')->nullable(); // 资源链接
            $table->string('file_path')->nullable(); // 本地文件路径
            $table->string('thumbnail')->nullable(); // 缩略图
            $table->integer('duration')->nullable(); // 视频时长(分钟)
            $table->enum('difficulty', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->decimal('rating', 3, 2)->default(0); // 评分 0-5.00
            $table->integer('view_count')->default(0); // 浏览次数
            $table->integer('download_count')->default(0); // 下载次数
            $table->foreignId('creator_id')->constrained('users')->onDelete('cascade'); // 创建者
            $table->boolean('is_featured')->default(false); // 是否推荐
            $table->boolean('is_active')->default(true); // 是否启用
            $table->json('tags')->nullable(); // 标签
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
