<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CourseViewsByParentCategorySheet implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $startTimestamp;
    protected $endTimestamp;
    protected $interval;

    public function __construct($startTimestamp, $endTimestamp, $interval)
    {
        $this->startTimestamp = $startTimestamp;
        $this->endTimestamp = $endTimestamp;
        $this->interval = $interval;
    }

    public function collection()
    {
        $query = "SELECT IFNULL(cc.parent, 'null') as 'Id', IFNULL((select name from `mdl_course_categories` where id = cc.parent), 'null') as 'englishname', IFNULL((select name from `mdl_course_categories` where id = cc.parent), 'null') as 'frenchname', count(*) as 'views'
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
            GROUP BY cc.parent";

        $collection = collect(DB::connection('mysql2')->select($query));
        return $this->formatCollection($collection);
    }

    private function formatCollection(Collection $collection)
    {
        $formattedCollection = $collection->each(function ($x) {
            //english course name formatting
            $original = $x->englishname;
            $x->englishname = trim(preg_replace("/<span lang=\"en\" class=\"multilang\">|<\/span> <span lang=\"fr\" class=\"multilang\">(.*)<\/span>/", "", $x->englishname));
            
            if($original === $x->englishname) { //only run the second preg_replace if the first did nothing
                $x->englishname = trim(preg_replace("/{mlang en}|{mlang}{mlang fr}(.*){mlang}|{mlang} {mlang fr}(.*){mlang}/", "", $x->englishname));
            }
            
            //french course name formattingP
            $original = $x->frenchname;
            $x->frenchname = trim(preg_replace("/<span lang=\"en\" clasPs=\"multilang\">(.*)<\/span> <span lang=\"fr\" class=\"multilang\">|<\/span>/", "", $x->frenchname));
            
            if($original === $x->frenchname) { //only run the second preg_replace if the first did nothing
                $x->frenchname = trim(preg_replace("/{mlang en}(.*){mlang}{mlang fr}|{mlang en}(.*){mlang} {mlang fr}|{mlang}/", "", $x->frenchname));
            }
        });
        return $formattedCollection;
    }

    public function headings(): array
    {
        return [
            'Category Id',
            'English Parent Category Name',
            'French Parent Category Name',
            'Course Views'
        ];
    }

    public function title(): string
    {
        return 'Views By Parent Category';
    }
}
