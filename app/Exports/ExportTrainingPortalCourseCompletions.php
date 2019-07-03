<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Exports\CompletionsByBadgeSheet;
use App\Exports\CompletionsByCourseSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Exports\CompletionsByCourseGroupSheet;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Traits\GetTrainingPortalData;

class ExportTrainingPortalCourseCompletions implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize
{
    use GetTrainingPortalData;

    protected $startTimestamp;
    protected $endTimestamp;

    public function __construct($startTimestamp, $endTimestamp)
    {
        $this->startTimestamp = $startTimestamp;
        $this->endTimestamp = $endTimestamp;
    }

    public function collection()
    {
        return $this->getTrainingPortalCompletions($this->startTimestamp, $this->endTimestamp)->sortByDesc('totalCompletions');
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
        return 'Training Portal Completions';
    }
}
