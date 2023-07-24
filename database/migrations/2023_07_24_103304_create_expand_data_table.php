<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expand_data', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('article_id')->unsigned()->comment('文章id');
            $table->integer('article_expand_detail_id')->unsigned()->comment('article_expand_detail表id');
            $table->integer('article_expand_id')->unsigned()->comment('article_expand表id');
            $table->string('name')->comment('字段名称');
            $table->string('desc')->comment('字段描述');
            $table->integer('type')->unsigned()->comment('字段类型');
            $table->string('select_list')->default('')->comment('下拉框选项');
            $table->string('model_name')->default('')->comment('模型名称');
            $table->string('label')->default('')->comment('显示字段');
            $table->string('condition')->default('')->comment('查询条件');
            $table->string('default_condition')->default('')->comment('默认查询条件');
            $table->string('show_field')->default('')->comment('选中字段');
            $table->text('value')->nullable()->comment('内容');

            $table->unique(['article_id', 'article_expand_detail_id']);


        });
        DB::statement("ALTER TABLE `expand_data` COMMENT='拓展数据表'");//表注释
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expand_data');
    }
};
