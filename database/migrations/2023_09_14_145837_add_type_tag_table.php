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
        Schema::table('article_tag', function (Blueprint $table) {
            //
            $table->enum('type', ['title', 'content'])->default('title')->comment('标签类型，title是根据文章标题查找，content是根据文章内容查找');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('article_tag', function (Blueprint $table) {
            //
            $table->dropColumn('type');
        });
    }
};
