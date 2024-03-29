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
        Schema::table('website_push', function (Blueprint $table) {
            //
            $table->string('msg',500)->default('')->comment('接口返回结果');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('website_push', function (Blueprint $table) {
            //
            $table->dropColumn('msg');
        });
    }
};
