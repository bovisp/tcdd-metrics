<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CourseViewsByCourseSheet implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize
{
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
        $query = "select l.courseid, c.fullname 'english_course_name', c.fullname 'french_course_name', count(*) as 'course_views', cc.name 'english_category_name', cc.name 'french_category_name'
        FROM mdl_logstore_standard_log l
        LEFT OUTER JOIN mdl_role_assignments a
            ON l.contextid = a.contextid
            AND l.userid = a.userid
        INNER JOIN mdl_course c ON l.courseid = c.id
        INNER JOIN `mdl_course_categories` cc on c.category = cc.id
        WHERE l.target = 'course'
        AND l.action = 'viewed'
        AND l.courseid > 1
        AND (a.roleid IN (5, 6, 7) OR l.userid = 1)
        AND l.timecreated BETWEEN {$this->startTimestamp} AND {$this->endTimestamp}
        GROUP BY l.courseid";

        $collection = collect(DB::connection('mysql2')->select($query));
        return $this->formatCollection($collection);
    }

    private function formatCollection(Collection $collection)
    {
        $formattedCollection = $collection->each(function ($x) {
            //english course name formatting
            $original = $x->english_course_name;
            $x->english_course_name = trim(preg_replace("/<span lang=\"en\" class=\"multilang\">|<\/span> <span lang=\"fr\" class=\"multilang\">(.*)<\/span>/", "", $x->english_course_name));
            
            if($original === $x->english_course_name) { //only run the second preg_replace if the first did nothing
                $x->english_course_name = trim(preg_replace("/{mlang en}|{mlang}{mlang fr}(.*){mlang}|{mlang} {mlang fr}(.*){mlang}/", "", $x->english_course_name));
            }
            
            //french course name formatting
            $original = $x->french_course_name;
            $x->french_course_name = trim(preg_replace("/<span lang=\"en\" class=\"multilang\">(.*)<\/span> <span lang=\"fr\" class=\"multilang\">|<\/span>/", "", $x->french_course_name));
            
            if($original === $x->french_course_name) { //only run the second preg_replace if the first did nothing
                $x->french_course_name = trim(preg_replace("/{mlang en}(.*){mlang}{mlang fr}|{mlang en}(.*){mlang} {mlang fr}|{mlang}/", "", $x->french_course_name));
            }

            //english category name formatting
            $original = $x->english_category_name;
            $x->english_category_name = trim(preg_replace("/<span lang=\"en\" class=\"multilang\">|<\/span> <span lang=\"fr\" class=\"multilang\">(.*)<\/span>/", "", $x->english_category_name));
            
            if($original === $x->english_category_name) { //only run the second preg_replace if the first did nothing
                $x->english_category_name = trim(preg_replace("/{mlang en}|{mlang}{mlang fr}(.*){mlang}|{mlang} {mlang fr}(.*){mlang}/", "", $x->english_category_name));
            }
            
            //french category name formatting
            $original = $x->french_category_name;
            $x->french_category_name = trim(preg_replace("/<span lang=\"en\" class=\"multilang\">(.*)<\/span> <span lang=\"fr\" class=\"multilang\">|<\/span>/", "", $x->french_category_name));
            
            if($original === $x->french_category_name) { //only run the second preg_replace if the first did nothing
                $x->french_category_name = trim(preg_replace("/{mlang en}(.*){mlang}{mlang fr}|{mlang en}(.*){mlang} {mlang fr}|{mlang}/", "", $x->french_category_name));
            }
        });
        return $formattedCollection;
    }

    public function headings(): array
    {
        return [
            'Course Id',
            'English Course Name',
            'French Course Name',
            'Views',
            'English Category Name',
            'French Category Name'
        ];
    }

    public function title(): string
    {
        return 'Views By Course';
    }
}
