<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Traits\FormatCollection;

class CourseViewsByParentCategorySheet implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize
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
        $query = "SELECT IFNULL(cc.parent, 'null') as 'Id', IFNULL((select name from `mdl_course_categories` where id = cc.parent), '(No parent category)') as 'english_category_name', IFNULL((select name from `mdl_course_categories` where id = cc.parent), '(No parent category)') as 'french_category_name', count(*) as 'views'
            FROM `mdl_logstore_standard_log` l
            LEFT OUTER JOIN `mdl_role_assignments` a
                ON l.contextid = a.contextid
                AND l.userid = a.userid
            INNER JOIN `mdl_course` c ON l.courseid = c.id
            INNER JOIN `mdl_course_categories` cc on c.category = cc.id
            WHERE l.target = 'course'
            AND l.action = 'viewed'
            AND l.courseid > 1
            AND (a.roleid IN (5, 6, 7) OR l.userid = 1)
            AND l.timecreated BETWEEN {$this->startTimestamp} AND {$this->endTimestamp}
            AND c.category != 29
            AND c.visible != 0
            GROUP BY cc.parent
            ORDER BY views desc";

        $collection = collect(DB::connection('mysql2')->select($query));
        
        return $this->formatTwoColumns($collection, 'english_category_name', 'french_category_name');
    }

    public function headings(): array
    {
        return [
            'Category Id',
            'English Parent Category Name',
            'French Parent Category Name',
            'Views'
        ];
    }

    public function title(): string
    {
        return 'Views By Parent Category';
    }
}
