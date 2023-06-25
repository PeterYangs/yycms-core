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
            $table->integer('js_pc_hide')->default(2)->unsigned()->comment('js pc端详情页隐藏（排除搜索引擎）,1是 2否')->change();
            $table->integer('js_mobile_hide')->default(2)->unsigned()->comment('js mobile端详情页隐藏（排除搜索引擎）,1是 2否')->change();
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
        });
    }
};
