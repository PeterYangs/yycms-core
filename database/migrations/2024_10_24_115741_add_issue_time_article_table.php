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
        Schema::table('article', function (Blueprint $table) {
            //
            $table->timestamp('issue_time')->nullable()->comment('文章更新时间');
        });
        DB::statement("UPDATE article SET `issue_time` = `updated_at`");//数据复制
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('article', function (Blueprint $table) {
            //
            $table->dropColumn('issue_time');
        });
    }
};
