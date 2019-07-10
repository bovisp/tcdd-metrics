<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCometCompletion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comet_completion', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->nullable();
            $table->string('last')->nullable();
            $table->string('first')->nullable();
            $table->string('module');
            $table->string('language');
            $table->unsignedInteger('score')->nullable();
            $table->date('date_completed');

            // $table->unique(['email', 'module', 'date_completed']); Syntax error or access violation: 1071 Specified key was too long; max key length is 1000 bytes???
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comet_completion');
    }
}
