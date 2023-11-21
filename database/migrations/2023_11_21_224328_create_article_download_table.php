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
        Schema::create('article_download', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('article_id')->unique()->unsigned()->comment('文章id');
            $table->integer('library_id')->unsigned()->comment('文章库文章id');
            $table->integer('apk_id')->unsigned()->comment('文章库软件id');
            $table->integer('download_site_id')->unsigned()->comment('下载服务器id');
            $table->string('file_path', 1000)->comment('文件下载路径，需要拼接下载服务器');
            $table->integer('save_type')->unsigned()->comment('储存类型，1是常规储存，2是网盘储存');
            $table->string('pan_password')->default('')->comment('网盘提取码');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('article_download');
    }
};
