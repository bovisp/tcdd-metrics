<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    public function badges() {
        return $this->belongsToMany(Badge::class);
        //define relationship to different table?
    }
}
