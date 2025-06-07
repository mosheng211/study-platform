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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('分类名称');
            $table->string('slug')->unique()->comment('分类标识符');
            $table->text('description')->nullable()->comment('分类描述');
            $table->string('color', 7)->default('#1890ff')->comment('分类颜色');
            $table->string('icon')->nullable()->comment('分类图标');
            $table->integer('sort_order')->default(0)->comment('排序顺序');
            $table->boolean('is_active')->default(true)->comment('是否激活');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
}; 