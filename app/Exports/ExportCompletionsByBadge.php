<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ExportCompletionsByBadge implements FromCollection, WithHeadings, ShouldAutoSize
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
            'Id',
            'Badge Name',
            'Badges Issued'
        ];
    }
}
