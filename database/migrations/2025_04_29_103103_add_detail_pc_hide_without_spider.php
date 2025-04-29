<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('special', function (Blueprint $table) {
            //
            $table->integer('detail_pc_hide_without_spider')->unsigned()->default(2)->comment("js隐藏pc详情页，不包含搜索引擎爬虫和搜索引擎的链接点过来的,1是2否");
            $table->integer('detail_mobile_hide_without_spider')->unsigned()->default(2)->comment("js隐藏mobile详情页，不包含搜索引擎爬虫和搜索引擎的链接点过来的,1是2否");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('special', function (Blueprint $table) {
            //
            $table->dropColumn("detail_pc_hide_without_spider");
            $table->dropColumn("detail_mobile_hide_without_spider");
        });
    }
};
