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
        Schema::create('access_key', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('app_id')->unique()->comment('app_id');
            $table->string('app_secret')->comment('app_secret');
            $table->integer('status')->default(1)->comment('状态，1是正常，2是禁用');
            $table->timestamp('last_use')->nullable()->comment('上一次使用时间');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('access_key');
    }
};
