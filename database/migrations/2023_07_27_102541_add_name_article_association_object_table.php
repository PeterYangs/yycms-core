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
        Schema::table('article_association_object', function (Blueprint $table) {
            //

            $table->string('name')->default('association_object')->comment('字段');

            $table->dropIndex('article_association_object_main_slave_unique');

            $table->unique(['main', 'slave', 'name']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('article_association_object', function (Blueprint $table) {
            //
            $table->dropColumn('name');
        });
    }
};
