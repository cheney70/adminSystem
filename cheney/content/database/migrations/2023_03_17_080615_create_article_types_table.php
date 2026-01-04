<?php
//classify
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticleTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("name")->default('')->comment("分类名称");
            $table->bigInteger("parent_id")->default('')->comment("父id");
            $table->string("sign")->default('')->comment("类型标识");
            $table->string("icon")->default('')->comment("分类图标");
            $table->string("remarks")->default('')->comment("分类描述");
            $table->char("status",20)->default('ENABLE')->comment("状态:ENABLE:启用,DISABLE:禁用");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('types');
    }
}
