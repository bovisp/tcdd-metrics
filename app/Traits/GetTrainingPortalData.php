<?php

namespace App\Traits;

use App\Traits\FormatCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

trait GetTrainingPortalData {
    use FormatCollection;

    /**
    * Formats training portal course name into English and French columns.
    *
    * @param array $collection Collection of training portal courses
    *
    * @param string $englishColumn Name of column with English course information
    *
    * @param string $frenchColumn Name of column with French course information
    *
    * @return array $formattedCollection Collection after formatting English and French columns
    */
    private function getTrainingPortalViews($startTimestamp, $endTimestamp)
    {
        $query = "SELECT l.courseid, cc.name 'english_category_name', cc.name 'french_category_name', c.fullname 'english_course_name', c.fullname 'french_course_name', lg.name as 'Language', count(*) as 'views'
        FROM mdl_logstore_standard_log l
        LEFT OUTER JOIN mdl_role_assignments a
            ON l.contextid = a.contextid
            AND l.userid = a.userid
        INNER JOIN mdl_course c ON l.courseid = c.id
        INNER JOIN `mdl_course_categories` cc ON c.category = cc.id
        LEFT OUTER JOIN `tcdd-metrics`.`course_language` cl ON l.courseid = cl.course_id
        LEFT OUTER JOIN `tcdd-metrics`.`languages` lg ON cl.language_id = lg.id
        WHERE l.target = 'course'
        AND l.action = 'viewed'
        AND l.courseid > 1
        AND (a.roleid IN (5, 6, 7) OR l.userid = 1)
        AND l.timecreated BETWEEN {$startTimestamp} AND {$endTimestamp}
        AND c.category != 29
        AND c.visible != 0
        GROUP BY l.courseid";

        $collection = collect(DB::connection('mysql2')->select($query));
        $formattedCollection = $this->formatTwoColumns($collection, 'english_course_name', 'french_course_name');
        
        return $this->formatTwoColumns($formattedCollection, 'english_category_name', 'french_category_name');
    }

    /**
    * Formats training portal course name into single column with both English and French names
    *
    * @param array $collection Collection of training portal courses
    *
    * @param string $column Name of column with both English and French course information
    *
    * @return array $formattedCollection Collection after formatting column with both English and French information
    */
    private function getTrainingPortalCompletions($startTimestamp, $endTimestamp)
    {
        $query = "SELECT c.id as 'Course Id', cc.name 'english_category_name', cc.name 'french_category_name', c.fullname as 'english_course_name', c.fullname as 'french_course_name', b.id, b.name, l.name as 'Language', count(bi.badgeid) as 'completions'
        FROM `moodledb`.`mdl_badge_issued` bi
        INNER JOIN `moodledb`.`mdl_badge` b ON bi.badgeid = b.id
        INNER JOIN `moodledb`.`mdl_course` c ON b.courseid = c.id
        INNER JOIN `moodledb`.`mdl_course_categories` cc ON c.category = cc.id
        LEFT OUTER JOIN `tcdd-metrics`.`badge_language` bl ON bi.badgeid = bl.badge_id
        LEFT OUTER JOIN `tcdd-metrics`.`languages` l ON bl.language_id = l.id
        WHERE bi.badgeid IN (44,45,8,22,11,12,27,28,34,31,43,42)
        AND bi.dateissued BETWEEN {$startTimestamp} AND {$endTimestamp}
        AND c.category != 29
        AND c.visible != 0
        GROUP BY bi.badgeid
        ORDER BY c.id";

        $collection = collect(DB::connection('mysql2')->select($query));
        $formattedCollection = $this->formatTwoColumns($collection, 'english_course_name', 'french_course_name');

        return $this->formatTwoColumns($formattedCollection, 'english_category_name', 'french_category_name');
    }
}
