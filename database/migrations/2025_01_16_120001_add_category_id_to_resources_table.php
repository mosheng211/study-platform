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
        Schema::table('resources', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->after('id')->comment('分类ID');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            
            // 保留原来的category字段，以便数据迁移
            // $table->dropColumn('category'); // 暂时不删除，等数据迁移完成后再删除
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
}; 