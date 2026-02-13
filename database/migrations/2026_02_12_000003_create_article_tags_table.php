<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticleTagsTable extends Migration
{
    public function up()
    {
        Schema::create('article_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->comment('标签名称');
            $table->string('slug', 50)->unique()->comment('URL 别名');
            $table->string('color', 20)->comment('标签颜色');
            $table->unsignedInteger('article_count')->default(0)->comment('文章数量');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('article_tags');
    }
}