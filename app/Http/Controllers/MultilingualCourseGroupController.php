<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MultilingualCourseGroupController extends Controller
{
    /**
    * Returns existing course groups.
    *
    * @return array response includes array of course groups
    *
    * @api
    */
    public function index() {
        return DB::connection('mysql')->table('multilingual_course_group')->orderBy('name', 'asc')->get();
    }

    /**
    * Stores new course group in database.
    *
    * @param array request includes course group name
    *
    * @return array response includes message and status code
    *
    * @api
    */
    public function store() {
        request()->validate([
            'course_group_name' => 'required|unique:multilingual_course_group,name'
        ]);

        DB::connection('mysql')->table('multilingual_course_group')
            ->insert(['name' => request('course_group_name')]);

        return response('Successfully created a course group.', 200);
    }

    /**
    * Deletes course group from database.
    *
    * @param array request includes course group Id
    *
    * @return array response includes message and status code
    *
    * @api
    */
    public function destroy($multilingualCourseGroupId) {        
        DB::connection('mysql')->table('multilingual_course_group')
            ->delete([
                'id' => $multilingualCourseGroupId
            ]);

        return response("Successfully deleted this course group.", 200);
    }

    /**
    * Updates name of existing course group.
    *
    * @param array request includes course group Id
    *
    * @return array response includes message and status code
    *
    * @api
    */
    public function update($multilingualCourseGroupId) {
        DB::connection('mysql')->table('multilingual_course_group')
            ->where(['id' => $multilingualCourseGroupId])
            ->update([
                'name' => request('course_group_name')
            ]);

        return response("Successfully updated this course group.", 200);
    }
}
