<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCometAccess extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comet_access', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->nullable();
            $table->string('last')->nullable();
            $table->string('first')->nullable();
            $table->string('module');
            $table->string('language');
            $table->unsignedInteger('sessions')->nullable();
            $table->float('elapsed_time')->nullable();
            $table->unsignedInteger('session_pages')->nullable();
            $table->date('date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comet_access');
    }
}
