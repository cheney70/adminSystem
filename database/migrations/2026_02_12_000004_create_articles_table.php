<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    public function up()
    {
        // 检查articles表是否存在
        if (!Schema::hasTable('articles')) {
            Schema::create('articles', function (Blueprint $table) {
                $table->id();
                $table->string('title', 255)->comment('文章标题');
                $table->string('slug', 255)->unique()->comment('URL 别名');
                $table->text('summary')->comment('文章摘要');
                $table->longText('content')->comment('文章内容');
                $table->string('cover_image', 500)->comment('封面图片');
                $table->unsignedBigInteger('category_id')->nullable()->comment('分类 ID');
                $table->unsignedBigInteger('author_id')->comment('作者 ID');
                $table->tinyInteger('status')->default(0)->comment('状态：0-草稿，1-已发布，2-已下架');
                $table->tinyInteger('is_top')->default(0)->comment('是否置顶：0-否，1-是');
                $table->tinyInteger('is_hot')->default(0)->comment('是否热门：0-否，1-是');
                $table->tinyInteger('is_recommend')->default(0)->comment('是否推荐：0-否，1-是');
                $table->unsignedInteger('view_count')->default(0)->comment('阅读量');
                $table->unsignedInteger('like_count')->default(0)->comment('点赞数');
                $table->unsignedInteger('comment_count')->default(0)->comment('评论数');
                $table->timestamp('published_at')->nullable()->comment('发布时间');
                $table->timestamps();
                $table->softDeletes();
                
                $table->index('category_id');
                $table->index('author_id');
                $table->index('status');
                $table->index('published_at');
            });

            // 添加外键约束
            Schema::table('articles', function (Blueprint $table) {
                $table->foreign('category_id')->references('id')->on('article_categories')->onDelete('set null');
                $table->foreign('author_id')->references('id')->on('admins')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('articles');
    }
}