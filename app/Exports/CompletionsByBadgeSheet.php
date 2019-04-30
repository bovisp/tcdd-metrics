<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Traits\FormatCollection;

class CompletionsByBadgeSheet implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize
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
        $query = "SELECT c.id as 'Course Id', c.fullname as 'english_course_name', c.fullname as 'french_course_name', b.id, b.name, l.name as 'Language', count(bi.badgeid) as 'Badges Issued'
        FROM `moodledb`.`mdl_badge_issued` bi
        INNER JOIN `moodledb`.`mdl_badge` b ON bi.badgeid = b.id
        INNER JOIN `moodledb`.`mdl_course` c ON b.courseid = c.id
        LEFT OUTER JOIN `tcdd-metrics`.`badge_language` bl ON bi.badgeid = bl.badge_id
        LEFT OUTER JOIN `tcdd-metrics`.`languages` l ON bl.language_id = l.id
        WHERE bi.badgeid IN (44,45,8,22,11,12,27,28,34,31,43,42)
        AND bi.dateissued BETWEEN {$this->startTimestamp} AND {$this->endTimestamp}
        GROUP BY bi.badgeid
        ORDER BY c.id";

        $collection = collect(DB::connection('mysql2')->select($query));

        return $this->formatTwoColumns($collection, 'english_course_name', 'french_course_name');
    }

    public function headings(): array
    {
        return [
            'Course Id',
            'English Course Name',
            'French Course Name',
            'Badge Id',
            'Badge Name',
            'Badge Language',
            'Completions'
        ];
    }

    public function title(): string
    {
        return 'Completions By Badge';
    }
}
