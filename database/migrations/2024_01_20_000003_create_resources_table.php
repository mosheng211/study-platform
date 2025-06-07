<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['video', 'document', 'link', 'book', 'tool']);
            $table->string('category');
            $table->enum('difficulty', ['beginner', 'intermediate', 'advanced']);
            $table->string('url');
            $table->integer('duration')->nullable(); // 视频时长（分钟）
            $table->string('thumbnail')->nullable(); // 缩略图
            $table->boolean('is_featured')->default(false); // 是否推荐
            $table->integer('view_count')->default(0); // 查看次数
            $table->integer('download_count')->default(0); // 下载次数
            $table->decimal('rating', 2, 1)->nullable(); // 评分
            $table->unsignedBigInteger('creator_id'); // 创建者ID
            $table->string('creator_name'); // 创建者名称
            $table->timestamps();

            $table->foreign('creator_id')->references('id')->on('users');
            $table->index(['type', 'category', 'difficulty']);
            $table->index(['is_featured', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('resources');
    }
}; 