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
        Schema::create('article_obj', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('main')->unsigned()->comment('主id');
            $table->integer('slave')->unsigned()->comment('从id');
            $table->unique(['main', 'slave']);

        });

        DB::statement("ALTER TABLE `article_obj` COMMENT='文章一对一关联设置'");//表注释
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('article_obj');
    }
};
