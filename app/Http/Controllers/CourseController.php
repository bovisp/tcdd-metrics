<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    public function index() {
        //only courses not already in course-languages
        $assignedCourseIds = DB::connection('mysql')->table('course_language')->select('course_id')->get()->map(function ($assignedCourseId) {
            return $assignedCourseId->course_id;
        })->toArray();

        // need regex to clean up course fullname
        return DB::connection('mysql2')->table('mdl_course')->whereNotIn('id', $assignedCourseIds)->orderBy('fullname', 'asc')->get();
    }
}
