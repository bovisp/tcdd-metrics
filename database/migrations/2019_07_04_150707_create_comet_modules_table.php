<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCometModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comet_modules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->date('publish_date');
            $table->date('last_updated')->nullable();
            $table->string('completion_time');
            $table->string('image_src');
            $table->mediumText('description');
            $table->string('topics');
            $table->string('url');
            $table->boolean('include_in_catalog')->default(true);
            $table->boolean('msc_funded')->default(false);
            $table->unsignedInteger('language_id');
            $table->unsignedInteger('english_version_id')->nullable();
            $table->timestamps();

            $table->foreign('language_id')->references('id')->on('languages');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comet_modules');
    }
}
