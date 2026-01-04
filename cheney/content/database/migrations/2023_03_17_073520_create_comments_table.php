<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger("object_id")->default(0)->comment("评论对象id");
            $table->string("object_name")->default(0)->comment("评论对象名称");
            $table->bigInteger("user_id")->default(0)->comment("用户id");
            $table->smallInteger("star")->default(5)->comment("星级");
            $table->text("images")->comment("评论图片");
            $table->string("content")->default('')->comment("评论内容");
            $table->char("status",20)->default('DISABLE')->comment("状态：ENABLE:启用,DISABLE:禁用");
            $table->timestamps();
            $table->softDeletes();
            $table->index("object_id","comments_table_object_id_index");
            $table->index("user_id","comments_table_user_id_index");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
}
