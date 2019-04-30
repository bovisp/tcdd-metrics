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

class ExportCompletionsByBadge implements WithMultipleSheets
{
    use Exportable;

    protected $startTimestamp;
    protected $endTimestamp;

    public function __construct($startTimestamp, $endTimestamp)
    {
        $this->startTimestamp = $startTimestamp;
        $this->endTimestamp = $endTimestamp;
    }

    public function sheets(): array
    {
        $sheets = [];
        $sheets = [new CompletionsByBadgeSheet($this->startTimestamp, $this->endTimestamp),
            new CompletionsByCourseSheet($this->startTimestamp, $this->endTimestamp)];

        return $sheets;
    }
}
