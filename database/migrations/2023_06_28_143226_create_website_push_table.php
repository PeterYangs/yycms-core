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
        Schema::create('website_push', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->engine = 'InnoDB';
            $table->integer('article_id')->unsigned()->default(0)->comment('文章id');

            $table->string('link')->default("")->comment('推送链接');

            $table->string('spider')->comment('站长类型');

            $table->enum('platform', ['pc', 'mobile'])->comment('设备类型');

        });
        DB::statement("ALTER TABLE `website_push` COMMENT='网站推送记录表'");//表注释
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('website_push');
    }
};
