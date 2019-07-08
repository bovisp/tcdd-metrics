<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\FormatCollection;
use Illuminate\Support\Facades\DB;

class WebinarController extends Controller
{
    use FormatCollection;

    /**
    * Returns array of training portal webinars not already in webinar_attendance.
    *
    * @return array response includes array of training portal webinars
    *
    * @api
    */
    public function index() {
        $assignedCourseIds = DB::connection('mysql')->table('webinar_attendance')->select('course_id')->get()->map(function ($assignedCourseId) {
            return $assignedCourseId->course_id;
        })->toArray();

        $collection = collect(DB::connection('mysql2')->table('mdl_course')
            ->select(['id', 'fullname as english_course_name', 'fullname as french_course_name'])
            ->where('category', '=', 28)
            // ->whereNotIn('id', $assignedCourseIds)
            ->get());
        
        return $this->formatTwoColumns($collection, "english_course_name", "french_course_name");
    }
}
