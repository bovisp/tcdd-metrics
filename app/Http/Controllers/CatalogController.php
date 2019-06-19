<?php

namespace App\Http\Controllers;

use App\CourseFormatter;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class CatalogController extends Controller
{
    /**
    * Generates and downloads pdf file of training portal and COMET courses.
    *
    * @return array response initiates download of pdf file
    *
    * @api
    */
    public function downloadPDF() {
        // get language from request

        request()->validate([
            'language_id' => 'required|exists:languages,id'
        ]);

        $lang = '';
        if(request('language_id') === 1) {
            $lang = 'en';
        } else if (request('language_id') === 2) {
            $lang = "fr";
        }

        $courseFormatter = new CourseFormatter();

        $moodleCourses = $this->getMoodleCourses($lang);
        $formattedMoodleCourses = $courseFormatter->formatMoodleCourses($lang, $moodleCourses);
        $cometCourses = $this->getCometCourses($lang);
        $formattedCometCourses = $courseFormatter->formatCometCourses($lang, $cometCourses);

        $data = ['lang' => $lang,
            'moodleCourses' => $formattedMoodleCourses,
            'cometCourses' => $formattedCometCourses];

        if($lang === 'fr') {
            $pdf = \PDF::loadView('frenchCoursesByCategoryPDF', $data);
        } else if($lang === 'en') {
            $pdf = \PDF::loadView('englishCoursesByCategoryPDF', $data);
        }
  
        return $pdf->download('test.pdf');
    }

    /**
    * Returns training portal course information from database.
    *
    * Query returns courses that issue a completion badge, are visible, and are not archived
    *
    * @return array response includes array of training portal courses
    *
    * @api
    */
    private function getMoodleCourses($lang) {
        $moodleCourseCategories = collect(DB::connection('mysql2')
            ->select("SELECT c.id, c.name
            FROM `mdl_course_categories` c
            WHERE c.id NOT IN (29)
            ORDER BY c.name"));

        if($lang === "fr") {
            $moodleCourseCategories->push((object)[
                "id" => 0,
                "name" => "Autres ressources"
            ]);
        } else if($lang === "en") {
            $moodleCourseCategories->push((object)[
                "id" => 0,
                "name" => "Other Resources"
            ]);
        }

        $moodleCoursesByCategory = $moodleCourseCategories->map(function ($category) {
            // if($category->id === 0) {
            //     $category = (array)$category;
            //     $category['courses'] = collect(DB::connection('mysql2')
            //         ->select("SELECT c.id, c.fullname as 'longTitle', c.fullname as 'shortTitle', c.summary as 'keywords', c.summary as 'estimatedtime', c.timecreated as 'timecreated', c.timecreated as 'lastmodified', c.summary as 'description', c.summary as 'objectives'
            //         FROM mdl_course c
            //         WHERE c.category != 29
            //         AND c.id != 1
            //         AND c.id NOT IN (
            //             SELECT c.id
            //             FROM `mdl_course` c
            //             INNER JOIN `mdl_badge` b on b.courseid = c.id
            //             AND c.visible != 0
            //             AND b.id IN (44,45,8,22,11,12,27,28,34,31,43,42)
            //             GROUP BY c.id
            //         )"));
            //     $category = (object)$category;

            //     return $category;
            // }

            $categoryId = $category->id;
            $category = (array)$category;
            $category['courses'] = collect(DB::connection('mysql2')
                ->select("SELECT c.id, ca.id as 'category', c.fullname as 'longTitle', c.fullname as 'shortTitle', c.summary as 'keywords', c.summary as 'estimatedtime', c.timecreated as 'timecreated', max(cm.added) as 'lastmodified', c.summary as 'description', c.summary as 'objectives'
                FROM `mdl_course` c
                INNER JOIN `mdl_course_modules` cm on c.id = cm.course
                INNER JOIN `mdl_course_categories` ca on c.category = ca.id
                INNER JOIN `mdl_badge` b on b.courseid = c.id
                WHERE c.category = {$categoryId}
                AND c.visible != 0
                AND b.id IN (44,45,8,22,11,12,27,28,34,31,43,42)
                GROUP BY c.id"));
            $category = (object)$category;

            return $category;
        });

        return $moodleCoursesByCategory;
    }

    /**
    * Returns COMET course information from database.
    *
    * Query returns courses that are funded by MSC
    *
    * @param string $lang is the language of the courses (English or French)
    *
    * @return array response includes array of MSC-funded COMET courses
    *
    * @api
    */
    private function getCometCourses($lang) {
        $cometCoursesOther = new Collection;

        if($lang === "fr") {
            $cometCoursesMscFunded = (object)[
                "id" => 1,
                "name" => "Modules COMET financés par le MSC",
                "courses" => collect(DB::connection('mysql')->select("SELECT ct.id, ct.title as 'longTitle', ct.title as 'shortTitle', ct.publish_date as 'publishDate', ct. last_updated as 'lastUpdated', ct.completion_time as 'completionTime', ct.description as 'description', ct.topics, ct.url as 'URL'
                            FROM `curltest`.`comet_modules` ct
                            WHERE ct.include_in_catalog = TRUE AND ct.msc_funded = TRUE
                            AND ct.language = 'french'
                            ORDER BY ct.title"))
            ];

            $cometCoursesOther = (object)[
                "id" => 2,
                "name" => "Autres modules d'intérêt de COMET",
                "courses" => collect(DB::connection('mysql')->select("SELECT ct.id, ct.title as 'longTitle', ct.title as 'shortTitle', ct.publish_date as 'publishDate', ct. last_updated as 'lastUpdated', ct.completion_time as 'completionTime', ct.description as 'description', ct.topics, ct.url as 'URL'
                            FROM `curltest`.`comet_modules` ct
                            WHERE ct.include_in_catalog = TRUE AND ct.msc_funded = FALSE
                            AND ct.language = 'french'
                            ORDER BY ct.title"))
            ];

        } else if ($lang === "en") {
            $cometCoursesMscFunded = (object)[
                "id" => 1,
                "name" => "MSC-funded COMET Modules",
                "courses" => collect(DB::connection('mysql')->select("SELECT ct.id, ct.title as 'longTitle', ct.title as 'shortTitle', ct.publish_date as 'publishDate', ct. last_updated as 'lastUpdated', ct.completion_time as 'completionTime', ct.description as 'description', ct.topics, ct.url as 'URL'
                            FROM `curltest`.`comet_modules` ct
                            WHERE ct.include_in_catalog = TRUE AND ct.msc_funded = TRUE
                            AND ct.language = 'english'
                            ORDER BY ct.title"))
            ];

            $cometCoursesOther = (object)[
                "id" => 2,
                "name" => "Other COMET Modules of Interest",
                "courses" => collect(DB::connection('mysql')->select("SELECT ct.id, ct.title as 'longTitle', ct.title as 'shortTitle', ct.publish_date as 'publishDate', ct. last_updated as 'lastUpdated', ct.completion_time as 'completionTime', ct.description as 'description', ct.topics, ct.url as 'URL'
                            FROM `curltest`.`comet_modules` ct
                            WHERE ct.include_in_catalog = TRUE AND ct.msc_funded = FALSE
                            AND ct.language = 'english'
                            ORDER BY ct.title"))
            ];
        }

        $cometCourses = [$cometCoursesMscFunded, $cometCoursesOther];

        return $cometCourses;
    }
}
