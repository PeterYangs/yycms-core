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
        Schema::create('download_site', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('rule')->comment('下载服务器拼接规则，需要使用占位符{path},如http://www.baidu.com/{path}');
            $table->string('note')->default('')->comment('备注');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('download_site');
    }
};
