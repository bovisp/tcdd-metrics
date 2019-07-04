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
            
        //check to see if request course and lang id are already in db, if so, return 422
        $id = DB::connection('mysql')->table('webinar_attendance')
              ->where([
                  ['language_id', '=', request('language_id')],
                  ['course_id', '=', request('course_id')]
              ])
              ->select('id')
              ->get();
        if(count($id) > 0) {
            return response('Record already exists for that webinar and language.', 422);
        };

        DB::connection('mysql')->table('webinar_attendance')->insert([
            'course_id' => request('course_id'),
            'language_id' => request('language_id'),
            'attendees' => request('attendees')
        ]);

        return response('Webinar successfully assigned number of attendees.', 200);
    }

    /**
    * Returns existing webinar attendance.
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
    * Deletes english and french attendance records for a webinar.
    *
    * @param array request includes course id
    *
    * @return array response includes message and status code
    *
    * @api
    */
    public function destroy($courseId) {
        DB::connection('mysql')->table('webinar_attendance')
        ->where(
            'course_id', '=', $courseId
        )
        ->delete();

        return response("Successfully deleted this webinar's attendance records.", 200);
    }

    /**
    * Updates english and french attendance records for a webinar.
    *
    * @param array request includes english attendees and french attendees
    *
    * @return array response includes message and status code
    *
    * @api
    */
    public function update($courseId) {
        DB::connection('mysql')->table('webinar_attendance')
        ->updateOrInsert(
            ['course_id' => $courseId, 'language_id' => 1],
            ['attendees' => request('english_attendees')]
        );

        DB::connection('mysql')->table('webinar_attendance')
        ->updateOrInsert(
            ['course_id' => $courseId, 'language_id' => 2],
            ['attendees' => request('french_attendees')]
        );

        return response("Successfully updated this webinar's attendance.", 200);
    }
}
