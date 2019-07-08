<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    protected $connection = 'mysql2'; //connects to moodle DB
    protected $table = 'mdl_badge';

    // public function languages() {
    //     return $this->belongsToMany(Language::class);
    // }
}
