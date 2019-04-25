<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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
        $collection = DB::connection('mysql')->table('course_language')
            ->join('moodledb.mdl_course', 'course_language.course_id', '=', 'moodledb.mdl_course.id')
            ->join('languages', 'course_language.language_id', '=', 'languages.id')
            ->select('course_language.id', 'course_language.course_id as course_id', 'moodledb.mdl_course.fullname as fullname', 'course_language.language_id as language_id', 'languages.name as language_name')
            ->orderBy('course_language.id', 'asc')
            ->get();
        return $this->formatCollection($collection);
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
