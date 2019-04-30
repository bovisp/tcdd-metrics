<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Traits\FormatCollection;

class MultilingualCourseController extends Controller
{
    use FormatCollection;

    public function store() {
        request()->validate([
            'course_id' => 'exists:mysql2.mdl_course,id',
        ]);

        //create multilingual course group if request does not include it
        if(request('multilingual_course_group_id')) {
            $mlangcoursegroupid = request('multilingual_course_group_id');
        } else {
            $mlangcoursegroupid = DB::connection('mysql')->table('multilingual_course_group')->insertGetId(['name' => '']);
        }

        DB::connection('mysql')->table('multilingual_course')->insert([
            'course_id' => request('course_id'),
            'multilingual_course_group_id' => $mlangcoursegroupid
        ]);

        return ['multilingual_course_group_id' => $mlangcoursegroupid,
        'message' => 'Successfully assigned a course to a multilingual course group.'];
    }

    public function index() {
        $collection = DB::connection('mysql')->table('multilingual_course')
            ->join('moodledb.mdl_course', 'multilingual_course.course_id', '=', 'moodledb.mdl_course.id')
            ->join('multilingual_course_group', 'multilingual_course.multilingual_course_group_id', '=', 'multilingual_course_group.id')
            ->select('multilingual_course.id', 'multilingual_course.course_id as course_id', 'moodledb.mdl_course.fullname as fullname', 'multilingual_course.multilingual_course_group_id as multilingual_course_group_id', 'multilingual_course_group.name as course_group_name')
            ->orderBy('multilingual_course.multilingual_course_group_id', 'asc')
            ->get();

        return $this->formatOneColumn($collection, "fullname");
    }

    public function destroy($multilingualCourseId) {
        
        // also remove course group if it no longer has any associated courses
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
        return response("Successfully deleted this multilingual course and its course group.", 200);
    }

    public function update($multilingualCourseId) {
        DB::connection('mysql')->table('multilingual_course')
        ->where(['id' => $multilingualCourseId])
        ->update([
            'course_id' => request('course_id'),
            'multilingual_course_group_id' => request('multilingual_course_group_id')
        ]);

        return response("Successfully updated this multilingual course's group.", 200);
    }
}