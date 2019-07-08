<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Traits\FormatCollection;

class CourseLanguageController extends Controller
{
    use FormatCollection;

    /**
    * Stores new course language in database.
    *
    * @param array request includes course Id and language Id
    *
    * @return array response includes message and status code
    *
    * @api
    */
    public function store() {
        request()->validate([
            'language_id' => 'required|exists:languages,id',
            'course_id' => 'required|exists:mysql2.mdl_course,id'
        ]);

        DB::connection('mysql')->table('course_language')->insert([
            'course_id' => request('course_id'),
            'language_id' => request('language_id')
        ]);

        return 'Course(s) successfully assigned a language.';
    }

    /**
    * Returns existing course languages
    *
    * @return array response includes array of course languages
    *
    * @api
    */
    public function index() {
        $collection = DB::connection('mysql')->table('course_language')
            ->join('moodledb.mdl_course', 'course_language.course_id', '=', 'moodledb.mdl_course.id')
            ->join('languages', 'course_language.language_id', '=', 'languages.id')
            ->select('course_language.id', 'course_language.course_id as course_id', 
                'moodledb.mdl_course.fullname as fullname', 'course_language.language_id as language_id', 
                'languages.name as language_name')
            ->orderBy('fullname', 'asc')
            ->get();

        return $this->formatOneColumn($collection, "fullname");
    }

    /**
    * Deletes course language from database.
    *
    * @param array request includes course language Id
    *
    * @return array response includes message and status code
    *
    * @api
    */
    public function destroy($courseLanguageId) {
        DB::connection('mysql')->table('course_language')
            ->delete([
                'id' => $courseLanguageId
            ]);

        return response("Successfully deleted this course's language.", 200);
    }

    /**
    * Updates name of existing course group.
    *
    * @param array request includes course language Id
    *
    * @return array response includes message and status code
    *
    * @api
    */
    public function update($courseLanguageId) {
        request()->validate([
            'language_id' => 'exists:languages,id'
        ]);

        DB::connection('mysql')->table('course_language')
            ->where(['id' => $courseLanguageId])
            ->update([
                'course_id' => request('course_id'),
                'language_id' => request('language_id')
            ]);

        return response("Successfully updated this course's language.", 200);
    }
}
