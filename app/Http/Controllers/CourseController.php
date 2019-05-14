<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Traits\FormatCollection;

class CourseController extends Controller
{
    use FormatCollection;

    public function index() {
        $assignedCourseIds = [];
        if(request()->query('filter') === 'notinclang') {
            $assignedCourseIds = DB::connection('mysql')->table('course_language')->select('course_id')->get()->map(function ($assignedCourseId) {
                return $assignedCourseId->course_id;
            })->toArray();
        } elseif(request()->query('filter') === 'notinmlang') {
            $assignedCourseIds = DB::connection('mysql')->table('multilingual_course')->select('course_id')->get()->map(function ($assignedCourseId) {
                return $assignedCourseId->course_id;
            })->toArray();
        }
        $collection = collect(DB::connection('mysql2')->table('mdl_course')->whereNotIn('id', $assignedCourseIds)->orderBy('fullname', 'asc')->get());
        
        return $this->formatOneColumn($collection, "fullname");
    }
}
