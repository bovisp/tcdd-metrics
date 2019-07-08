<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Traits\GetCometData;

class ExportCOMETAccesses implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize
{
    use GetCometData;
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
        return $this->getCometAccesses($this->startTimestamp, $this->endTimestamp)->sortByDesc('totalCompletions');
    }

    public function headings(): array
    {
        return [
            'English Title',
            'French Title',
            'English Accesses',
            'French Accesses',
            'Total Accesses'
        ];
    }

    public function title(): string
    {
        return 'COMET Accesses';
    }
}
