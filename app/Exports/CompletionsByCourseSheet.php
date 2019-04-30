<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Traits\FormatCollection;

class CompletionsByCourseSheet implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize
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
        $query = "(SELECT c.id as 'Course Id', c.fullname as 'english_course_name', c.fullname as 'french_course_name', count(c.id) as 'completions'
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

        return $this->formatTwoColumns($collection, 'english_course_name', 'french_course_name');
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
