<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Traits\GetCometData;

class ExportCOMETCompletions implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize
{
    use GetCometData;

    protected $startTimestamp;
    protected $endTimestamp;
    
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($startTimestamp, $endTimestamp)
    {
        $this->startTimestamp = $startTimestamp;
        $this->endTimestamp = $endTimestamp;
    }

    public function collection()
    {
        return $this->getCometCompletions($this->startTimestamp, $this->endTimestamp)->sortByDesc('totalCompletions');
    }

    public function headings(): array
    {
        return [
            'English Title',
            'French Title',
            'English Completions',
            'French Completions',
            'Total Completions'
        ];
    }

    public function title(): string
    {
        return 'COMET Completions';
    }
}
