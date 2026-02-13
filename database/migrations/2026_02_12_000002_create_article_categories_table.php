<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticleCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('article_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('分类名称');
            $table->string('slug', 100)->unique()->comment('URL 别名');
            $table->string('description', 500)->comment('分类描述');
            $table->foreignId('parent_id')->default(0)->comment('父分类 ID');
            $table->integer('sort_order')->default(0)->comment('排序');
            $table->string('icon', 100)->comment('分类图标');
            $table->tinyInteger('status')->default(1)->comment('状态：0-禁用，1-启用');
            $table->unsignedInteger('article_count')->default(0)->comment('文章数量');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('parent_id');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('article_categories');
    }
}