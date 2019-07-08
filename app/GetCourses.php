<?php

namespace App;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GetCourses {
    /**
    * Returns training portal course information from database.
    *
    * Query returns courses that issue a completion badge, are visible, and are not archived
    *
    * @return array response includes array of training portal courses
    *
    * @api
    */
    public function getMoodleCourses($lang) {
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
    public function getCometCourses($lang) {
        $cometCoursesOther = new Collection;

        if($lang === "fr") {
            $cometCoursesMscFunded = (object)[
                "id" => 1,
                "name" => "Modules COMET Financés par le MSC",
                "courses" => collect(DB::connection('mysql')->select("SELECT ct.id, ct.title as 'longTitle', ct.title as 'shortTitle', ct.publish_date as 'publishDate', ct. last_updated as 'lastUpdated', ct.completion_time as 'completionTime', ct.description as 'description', ct.topics, ct.url as 'URL'
                            FROM `comet_modules` ct
                            WHERE ct.include_in_catalog = TRUE AND ct.msc_funded = TRUE
                            AND ct.language_id = 2
                            ORDER BY ct.title"))
            ];

            $cometCoursesOther = (object)[
                "id" => 2,
                "name" => "Autres Modules d'intérêt de COMET",
                "courses" => collect(DB::connection('mysql')->select("SELECT ct.id, ct.title as 'longTitle', ct.title as 'shortTitle', ct.publish_date as 'publishDate', ct. last_updated as 'lastUpdated', ct.completion_time as 'completionTime', ct.description as 'description', ct.topics, ct.url as 'URL'
                            FROM `comet_modules` ct
                            WHERE ct.include_in_catalog = TRUE AND ct.msc_funded = FALSE
                            AND ct.language_id = 2
                            ORDER BY ct.title"))
            ];

        } else if ($lang === "en") {
            $cometCoursesMscFunded = (object)[
                "id" => 1,
                "name" => "MSC-funded COMET Modules",
                "courses" => collect(DB::connection('mysql')->select("SELECT ct.id, ct.title as 'longTitle', ct.title as 'shortTitle', ct.publish_date as 'publishDate', ct. last_updated as 'lastUpdated', ct.completion_time as 'completionTime', ct.description as 'description', ct.topics, ct.url as 'URL'
                            FROM `comet_modules` ct
                            WHERE ct.include_in_catalog = TRUE AND ct.msc_funded = TRUE
                            AND ct.language_id = 1
                            ORDER BY ct.title"))
            ];

            $cometCoursesOther = (object)[
                "id" => 2,
                "name" => "Other COMET Modules of Interest",
                "courses" => collect(DB::connection('mysql')->select("SELECT ct.id, ct.title as 'longTitle', ct.title as 'shortTitle', ct.publish_date as 'publishDate', ct. last_updated as 'lastUpdated', ct.completion_time as 'completionTime', ct.description as 'description', ct.topics, ct.url as 'URL'
                            FROM `comet_modules` ct
                            WHERE ct.include_in_catalog = TRUE AND ct.msc_funded = FALSE
                            AND ct.language_id = 1
                            ORDER BY ct.title"))
            ];
        }

        $cometCourses = [$cometCoursesMscFunded, $cometCoursesOther];

        return $cometCourses;
    }
}
