<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CompletionsCourseSheet implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize
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
        //need to group by course id and give course name
        $query = "SELECT bi.badgeid as 'Id', b.name as 'Badge Name', count(*) as 'Badges Issued'
        FROM `mdl_badge_issued` bi
        INNER JOIN `mdl_badge` b ON bi.badgeid = b.id
        WHERE bi.badgeid IN (44,45,8,22,11,12,27,28,34,31,43,42)
        AND bi.dateissued BETWEEN {$this->startTimestamp} AND {$this->endTimestamp}
        GROUP BY bi.badgeid";

        $collection = collect(DB::connection('mysql2')->select($query));
        return $collection;
    }

    private function formatCollection(Collection $collection)
    {

    }

    public function headings(): array
    {
        return [
            'Id', //need id?
            'Course Name',
            'Completions'
        ];
    }

    public function title(): string
    {
        return 'Completions By Badge';
    }
}
