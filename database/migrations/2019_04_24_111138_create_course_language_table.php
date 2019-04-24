<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourseLanguageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_language', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->unsignedInteger('course_id')->unique();
            $table->unsignedInteger('language_id');
            $table->unsignedInteger('multilingual_course_id')->nullable();

            $table->foreign('language_id')->references('id')->on('languages');
            $table->foreign('multilingual_course_id')->references('id')->on('multilingual_course');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_language');
    }
}
