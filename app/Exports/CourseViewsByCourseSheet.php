<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Traits\FormatCollection;

class CourseViewsByCourseSheet implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize
{
    use FormatCollection;
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $startTimestamp;
    protected $endTimestamp;

    public function __construct($startTimestamp, $endTimestamp)
    {
        $this->startTimestamp = $startTimestamp;
        $this->endTimestamp = $endTimestamp;
    }

    public function collection()
    {
        $query = "SELECT l.courseid, cc.name 'english_category_name', cc.name 'french_category_name', c.fullname 'english_course_name', c.fullname 'french_course_name', lg.name as 'Language', count(*) as 'course_views'
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
        AND l.timecreated BETWEEN {$this->startTimestamp} AND {$this->endTimestamp}
        AND c.category != 29
        AND c.visible != 0
        GROUP BY l.courseid";

        $collection = collect(DB::connection('mysql2')->select($query));
        $formattedCollection = $this->formatTwoColumns($collection, 'english_course_name', 'french_course_name');
        
        return $this->formatTwoColumns($formattedCollection, 'english_category_name', 'french_category_name');
    }

    public function headings(): array
    {
        return [
            'Course Id',
            'English Category Name',
            'French Category Name',
            'English Course Name',
            'French Course Name',
            'Course Language',
            'Views'
        ];
    }

    public function title(): string
    {
        return 'Views By Course';
    }
}
