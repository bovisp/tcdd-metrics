<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\FormatCollection;
use Illuminate\Support\Facades\DB;

class WebinarAttendanceController extends Controller
{
    use FormatCollection;

    /**
    * Stores new webinar attendance in database.
    *
    * @param array request includes course Id, language Id and number of attendees
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

        DB::connection('mysql')->table('webinar_attendance')->insert([
            'course_id' => request('course_id'),
            'language_id' => request('language_id'),
            'attendees' => request('attendees')
        ]);

        return 'Webinar successfully assigned number of attendees.';
    }

    /**
    * Returns existing webinar attendance
    *
    * @return array response includes array of webinar attendance
    *
    * @api
    */
    public function index() {
        $collection = DB::connection('mysql')->table('webinar_attendance')
        ->join('moodledb.mdl_course', 'webinar_attendance.course_id', '=', 'moodledb.mdl_course.id')
        ->join('languages', 'webinar_attendance.language_id', '=', 'languages.id')
        ->select(
            'webinar_attendance.id',
            'webinar_attendance.course_id as course_id', 
            'moodledb.mdl_course.fullname as english_course_name',
            'moodledb.mdl_course.fullname as french_course_name',
            'webinar_attendance.language_id as language_id', 
            'languages.name as language_name',
            'webinar_attendance.attendees as attendees'
        )
        ->get();

        return $this->formatTwoColumns($collection, "english_course_name", "french_course_name");
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
