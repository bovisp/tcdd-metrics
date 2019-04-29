<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CompletionsByCourseGroupSheet implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize
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
        $query = "SELECT mlg.id as 'course_group_id', mlg.name as 'course_group_name', count(mlg.id) as 'completions'
        FROM `mdl_badge_issued` bi
        INNER JOIN `mdl_badge` b ON bi.badgeid = b.id
        INNER JOIN `mdl_course` c ON b.courseid = c.id
        INNER JOIN `tcdd-metrics`.`multilingual_course` ml ON c.id = ml.course_id
        INNER JOIN `tcdd-metrics`.`multilingual_course_group` mlg ON ml.multilingual_course_group_id = mlg.id
        WHERE bi.badgeid IN (44,45,8,22,11,12,27,28,34,31,43,42)
        AND bi.dateissued BETWEEN {$this->startTimestamp} AND {$this->endTimestamp}
        GROUP BY mlg.id";

        return collect(DB::connection('mysql2')->select($query));
    }

    public function headings(): array
    {
        return [
            'Course Group Id',
            'Course Group Name',
            'Completions'
        ];
    }

    public function title(): string
    {
        return 'Completions By Course Group';
    }
}
