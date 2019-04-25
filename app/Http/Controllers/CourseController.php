<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class CourseController extends Controller
{
    public function index() {
        //only courses not already in course-languages
        $assignedCourseIds = DB::connection('mysql')->table('course_language')->select('course_id')->get()->map(function ($assignedCourseId) {
            return $assignedCourseId->course_id;
        })->toArray();

        // need regex to clean up course fullname
        $collection = collect(DB::connection('mysql2')->table('mdl_course')->whereNotIn('id', $assignedCourseIds)->orderBy('fullname', 'asc')->get());
        return $this->formatCollection($collection);
    }

    private function formatCollection(Collection $collection)
    {
        $formattedCollection = $collection->each(function ($x) {
            $original = $x->fullname;
            //english course name formatting
            $englishname = trim(preg_replace("/<span lang=\"en\" class=\"multilang\">|<\/span> <span lang=\"fr\" class=\"multilang\">(.*)<\/span>/", "", $x->fullname));
            
            if($original === $englishname) { //only run the second preg_replace if the first did nothing
                $englishname = trim(preg_replace("/{mlang en}|{mlang}{mlang fr}(.*){mlang}|{mlang} {mlang fr}(.*){mlang}/", "", $x->fullname));
            }
            
            //french course name formatting
            $frenchname = trim(preg_replace("/<span lang=\"en\" class=\"multilang\">(.*)<\/span> <span lang=\"fr\" class=\"multilang\">|<\/span>/", "", $x->fullname));
            
            if($original === $frenchname) { //only run the second preg_replace if the first did nothing
                $frenchname = trim(preg_replace("/{mlang en}(.*){mlang}{mlang fr}|{mlang en}(.*){mlang} {mlang fr}|{mlang}/", "", $x->fullname));
            }

            $x->fullname = $englishname . " / " . $frenchname;
        });
        return $formattedCollection;
    }
}
