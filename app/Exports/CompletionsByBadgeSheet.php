<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Traits\GetTrainingPortalData;

class CompletionsByBadgeSheet implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize
{
    use GetTrainingPortalData;
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
        return $this->getTrainingPortalCompletions($this->startTimestamp, $this->endTimestamp);
    }

    public function headings(): array
    {
        return [
            'Course Id',
            'English Category Name',
            'French Category Name',
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
