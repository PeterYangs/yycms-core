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
        Schema::table('category', function (Blueprint $table) {
            //

            $table->integer('tag_relate_rule')->default(0)->unsigned()->comment('标签关联规则，0不设置标签,1是根据标题关联，2是根据内容关联，3是根据标题和内容关联，当前分类未设置会根据父级设置');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('category', function (Blueprint $table) {
            //
            $table->dropColumn('tag_relate_rule');
        });
    }
};
