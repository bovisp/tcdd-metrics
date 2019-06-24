<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ExportCOMETAccesses implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize
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
        $cometAccessesByModule = collect(DB::connection('mysql')->select("
            SELECT ANY_VALUE(cma.language) as 'language', cma.module as 'englishTitle', 
                   ANY_VALUE(cm2.title) as 'frenchTitle', 
                   count(cma.module) as 'englishCompletions', 
                   count(cma.module) as 'frenchCompletions',
                   count(cma.module) as 'totalCompletions'
            FROM comet_access cma
            LEFT OUTER JOIN `curltest`.`comet_modules` cm ON cma.module = cm.title
            LEFT OUTER JOIN `curltest`.`comet_modules` cm2 on cm.id = cm2.english_version_id
            WHERE UNIX_TIMESTAMP(cma.date) BETWEEN {$this->startTimestamp} AND {$this->endTimestamp}
            GROUP BY cma.module
            ORDER BY count(cma.module) DESC
        "));

        foreach($cometAccessesByModule as $x) {
            if($x->frenchTitle) {
                $frenchRow = $cometAccessesByModule->where('englishTitle', '=', $x->frenchTitle)->first();
                if($frenchRow) {
                    $frenchRowKey = $cometAccessesByModule->search($frenchRow);
                    $x->frenchCompletions = $frenchRow->frenchCompletions;
                    unset($cometAccessesByModule[$frenchRowKey]);
                } else {
                    $x->frenchCompletions = 0;
                }
            } else {
                $x->frenchTitle = $x->englishTitle;
                if(strtolower($x->language) === "french") {
                    $x->englishCompletions = 0;
                } else {
                    $x->frenchCompletions = 0;
                }
            }
            $x->totalCompletions = $x->englishCompletions + $x->frenchCompletions;
            unset($x->language);
        };
            
        return $cometAccessesByModule->sortByDesc('totalCompletions');
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
