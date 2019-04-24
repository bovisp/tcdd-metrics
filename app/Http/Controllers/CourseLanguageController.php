<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseLanguageController extends Controller
{
    public function store() {
        request()->validate([
            'language_id' => 'exists:languages,id',
            'course_id' => 'exists:mysql2.mdl_course,id',
            'multilingual_course_id' => 'exists:multilingual_course,id'
        ]);

        DB::connection('mysql')->table('course_language')->insert([
            'course_id' => request('course_id'),
            'language_id' => request('language_id'),
            'multilingual_course_id' => request('multilingual_course_id')
        ]);

        return 'Course successfully assigned a language.';
    }

    public function index() {
        return DB::connection('mysql')->table('course_language')
            ->join('moodledb.mdl_course', 'course_language.course_id', '=', 'moodledb.mdl_course.id')
            ->join('languages', 'course_language.language_id', '=', 'languages.id')
            ->select('course_language.id', 'course_language.course_id as course_id', 'moodledb.mdl_course.fullname as course_name', 'course_language.language_id as language_id', 'languages.name as language_name')
            ->orderBy('course_language.id', 'asc')
            ->get();
    }

    public function destroy($courseLanguageId) {
        DB::connection('mysql')->table('course_language')
            ->delete([
                'id' => $courseLanguageId
            ]);
        return response("Successfully deleted this course's language.", 200);
    }

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
