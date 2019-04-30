<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CompletionsByCourseSheet implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize
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
        $query = "(SELECT c.id as 'Course Id', c.fullname as 'englishname', c.fullname as 'frenchname', count(c.id) as 'completions'
        FROM `mdl_badge_issued` bi
        INNER JOIN `mdl_badge` b ON bi.badgeid = b.id
        INNER JOIN `mdl_course` c ON b.courseid = c.id
        WHERE bi.badgeid IN (44,45,8,22,11,12,27,28,34,31,43,42)
        AND c.id NOT IN (SELECT ml.course_id FROM `tcdd-metrics`.`multilingual_course` ml)
        AND bi.dateissued BETWEEN {$this->startTimestamp} AND {$this->endTimestamp}
        GROUP BY c.id)
        UNION ALL
        (SELECT mlg.id as 'course_group_id', mlg.name as 'course_group_name', mlg.name as 'course_group_name', count(mlg.id) as 'completions'
        FROM `mdl_badge_issued` bi
        INNER JOIN `mdl_badge` b ON bi.badgeid = b.id
        INNER JOIN `mdl_course` c ON b.courseid = c.id
        INNER JOIN `tcdd-metrics`.`multilingual_course` ml ON c.id = ml.course_id
        INNER JOIN `tcdd-metrics`.`multilingual_course_group` mlg ON ml.multilingual_course_group_id = mlg.id
        WHERE bi.badgeid IN (44,45,8,22,11,12,27,28,34,31,43,42)
        AND bi.dateissued BETWEEN {$this->startTimestamp} AND {$this->endTimestamp}
        GROUP BY mlg.id)";

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
            
            //french course name formatting
            $original = $x->frenchname;
            $x->frenchname = trim(preg_replace("/<span lang=\"en\" class=\"multilang\">(.*)<\/span> <span lang=\"fr\" class=\"multilang\">|<\/span>/", "", $x->frenchname));
            
            if($original === $x->frenchname) { //only run the second preg_replace if the first did nothing
                $x->frenchname = trim(preg_replace("/{mlang en}(.*){mlang}{mlang fr}|{mlang en}(.*){mlang} {mlang fr}|{mlang}/", "", $x->frenchname));
            }
        });
        return $formattedCollection;
    }

    public function headings(): array
    {
        return [
            'Course/Course Group Id',
            'English Course Name',
            'French Course Name',
            'Completions'
        ];
    }

    public function title(): string
    {
        return 'Completions By Course';
    }
}
