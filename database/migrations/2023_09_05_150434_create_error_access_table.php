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
        Schema::create('error_access', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('ip')->default('')->comment('ip');

            $table->mediumText('url')->comment('访问完整链接');

            $table->text('referer')->nullable()->comment('跳转来源');

            $table->text('query')->nullable()->comment('query数据');

            $table->text('agent')->nullable()->comment('设备');

        });

        DB::statement("ALTER TABLE `error_access` COMMENT='错误记录日志表'");//表注释
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('error_access');
    }
};
