<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Exports\CompletionsByBadgeSheet;
use App\Exports\CompletionsByCourseSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExportCompletionsByBadge implements WithMultipleSheets
{
    use Exportable;

    protected $startTimestamp;
    protected $endTimestamp;
    protected $interval;

    public function __construct($startTimestamp, $endTimestamp, $interval)
    {
        $this->startTimestamp = $startTimestamp;
        $this->endTimestamp = $endTimestamp;
        $this->interval = $interval;
    }

    public function sheets(): array
    {
        $sheets = [];
        $sheets = [new CompletionsByBadgeSheet($this->startTimestamp, $this->endTimestamp, $this->interval),
            new CompletionsByCourseSheet($this->startTimestamp, $this->endTimestamp, $this->interval)];

        return $sheets;
    }
}
