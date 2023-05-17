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
        Schema::table('auto_push', function (Blueprint $table) {
            //

            $table->integer('article_status')->default(1)->unsigned()->comment('文章状态，1正常，2下架');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('auto_push', function (Blueprint $table) {
            //
            $table->dropColumn('article_status');
        });
    }
};
