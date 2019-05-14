<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MultilingualCourseGroupController extends Controller
{
    public function index() {
        return DB::connection('mysql')->table('multilingual_course_group')->orderBy('name', 'asc')->get();
    }

    public function store() {
        request()->validate([
            'course_group_name' => 'required|unique:multilingual_course_group,name'
        ]);

        DB::connection('mysql')->table('multilingual_course_group')
            ->insert(['name' => request('course_group_name')]);

        return response('Successfully created a course group.', 200);
    }

    public function destroy($multilingualCourseGroupId) {        
        DB::connection('mysql')->table('multilingual_course_group')
            ->delete([
                'id' => $multilingualCourseGroupId
            ]);

        return response("Successfully deleted this course group.", 200);
    }

    public function update($multilingualCourseGroupId) {
        DB::connection('mysql')->table('multilingual_course_group')
            ->where(['id' => $multilingualCourseGroupId])
            ->update([
                'name' => request('course_group_name')
            ]);

        return response("Successfully updated this course group.", 200);
    }
}
