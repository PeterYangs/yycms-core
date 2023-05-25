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
        Schema::table('category_route', function (Blueprint $table) {
            //
            $table->integer('is_main')->default(0)->unsigned()->comment('是否是主路由');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('category_route', function (Blueprint $table) {
            //
            $table->dropColumn('is_main');
        });
    }
};
