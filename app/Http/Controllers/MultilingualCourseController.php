<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MultilingualCourseController extends Controller
{
    public function store() {
        request()->validate([
            'multilingual_course_group_id' => 'exists:multilingual_course_group,id',
            'course_id' => 'exists:mysql2.mdl_course,id'
        ]);

        DB::connection('mysql')->table('multilingual_course')->insert([
            'course_id' => request('course_id'),
            'multilingual_course_group_id' => request('multilingual_course_group_id')
        ]);

        return 'Course successfully assigned a multlingual course group.';
    }

    public function index() {
        $collection = DB::connection('mysql')->table('multilingual_course')
            ->join('moodledb.mdl_course', 'multilingual_course.course_id', '=', 'moodledb.mdl_course.id')
            ->join('multilingual_course_group', 'multilingual_course.multilingual_course_group_id', '=', 'multilingual_course_group.id')
            ->select('multilingual_course.id', 'multilingual_course.course_id as course_id', 'moodledb.mdl_course.fullname as fullname', 'multilingual_course.multilingual_course_group_id as multilingual_course_group_id', 'multilingual_course_group.name as course_group_name')
            ->orderBy('multilingual_course.id', 'asc')
            ->get();
        return $this->formatCollection($collection);
    }

    public function destroy($multilingualCourseId) {
        DB::connection('mysql')->table('multilingual_course')
            ->delete([
                'id' => $multilingualCourseId
            ]);
        return response("Successfully deleted this multilingual course.", 200);
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
