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
            $table->text("pc_js")->comment("脚本，如统计代码之类的");
            $table->text("mobile_js")->comment("脚本，如统计代码之类的");
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
            $table->dropColumn('pc_js');
            $table->dropColumn('mobile_js');

        });
    }
};
