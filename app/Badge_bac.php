<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    protected $connection = 'mysql2'; //connects to moodle DB

    public function languages() {
        return $this->belongsToMany(Language::class);
    }

    // Schema::connection('mysql')->create('badge_language', function (Blueprint $table) {
    //     $table->bigIncrements('id');
    //     $table->unsignedInteger('language_id');
    //     $table->unsignedInteger('badge_id')->unique();

    //     $table->foreign('language_id')->references('id')->on('language');
    // });
}
