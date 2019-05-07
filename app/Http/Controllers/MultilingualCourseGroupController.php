<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MultilingualCourseGroupController extends Controller
{
    public function index() {
        return DB::connection('mysql')->table('multilingual_course_group')->orderBy('name', 'asc')->get();
    }
}
