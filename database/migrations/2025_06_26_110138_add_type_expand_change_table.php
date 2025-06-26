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
        Schema::table('expand_change', function (Blueprint $table) {
            //
            $table->integer('type')->default(1)->unsigned()->comment('类型，1是拓展属性，2是下载链接');
            $table->string('download_url', 1000)->default("")->comment('下载链接');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('expand_change', function (Blueprint $table) {
            //
            $table->dropColumn('type');
            $table->dropColumn('download_url');
        });
    }
};
