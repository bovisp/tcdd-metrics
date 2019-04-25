<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MultilingualCourseController extends Controller
{
    public function store() {
        request()->validate([
            'course_id' => 'exists:mysql2.mdl_course,id',
            'multilingual_course_group_id' => 'exists:multilingual_course_group,id'
        ]);

        //create multilingual course group if request does not contain it
        if(request('multilingual_course_group_id')) {
            $mlangcoursegroupid = request('multilingual_course_group_id');
        } else {
            $mlangcoursegroupid = DB::connection('mysql')->table('multilingual_course_group')->insertGetId([]);
        }

        DB::connection('mysql')->table('multilingual_course')->insert([
            'course_id' => request('course_id'),
            'multilingual_course_group_id' => $mlangcoursegroupid
        ]);

        return 'Successfully assigned a course to a multilingual course group.';
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
        $mlangCourseGroupId = DB::connection('mysql')->table('multilingual_course')
            ->where('id', '=', $multilingualCourseId)
            ->select('multilingual_course_group_id')
            ->get()
            ->map(function ($multilingualCourseGroupId) {
                return $multilingualCourseGroupId->multilingual_course_group_id;
            })[0];

        DB::connection('mysql')->table('multilingual_course')
            ->delete([
                'id' => $multilingualCourseId
            ]);
        // also remove course group if no longer has any associated courses
        $doNotDeleteCourseGroup = DB::connection('mysql')->table('multilingual_course')
                ->where('multilingual_course_group_id', '=', $mlangCourseGroupId)
                ->select()->get();
        
        if(count($doNotDeleteCourseGroup) > 0) {
            return response("Successfully deleted this multilingual course.", 200);
        }
        DB::connection('mysql')->table('multilingual_course_group')
            ->delete([
                'id' => $mlangCourseGroupId
            ]);
        return response("Successfully deleted this multilingual course and its multilingual course group.", 200);
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
