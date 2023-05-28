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
        Schema::table('auto_push', function (Blueprint $table) {
            //
            $table->integer('is_gpt')->default(0)->unsigned()->comment('是否用gpt替换内容');
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
            $table->dropColumn('is_gpt');
        });
    }
};
