<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticleTagRelationsTable extends Migration
{
    public function up()
    {
        // 检查article_tag_relations表是否存在
        if (!Schema::hasTable('article_tag_relations')) {
            Schema::create('article_tag_relations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('article_id')->comment('文章 ID');
                $table->unsignedBigInteger('tag_id')->comment('标签 ID');
                $table->timestamps();
                
                $table->unique(['article_id', 'tag_id']);
                $table->index('article_id');
                $table->index('tag_id');
            });

            // 添加外键约束
            Schema::table('article_tag_relations', function (Blueprint $table) {
                $table->foreign('article_id')->references('id')->on('articles')->onDelete('cascade');
                $table->foreign('tag_id')->references('id')->on('article_tags')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('article_tag_relations');
    }
}