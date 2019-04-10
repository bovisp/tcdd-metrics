<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
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

        $sheets[] = new CompletionsByBadgeSheet($this->startTimestamp, $this->endTimestamp);

        return $sheets;
    }
}
