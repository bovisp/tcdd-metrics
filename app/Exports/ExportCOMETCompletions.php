<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ExportCOMETCompletions implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize
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
        $cometCompletionsByModule = collect(DB::connection('mysql')->select("
            SELECT ANY_VALUE(cmc.language) as 'language', cmc.module as 'englishTitle', 
                   ANY_VALUE(cm2.title) as 'frenchTitle', 
                   count(cmc.module) as 'englishCompletions', 
                   count(cmc.module) as 'frenchCompletions',
                   count(cmc.module) as 'totalCompletions'
            FROM comet_completion cmc
            LEFT OUTER JOIN `curltest`.`comet_modules` cm ON cmc.module = cm.title
            LEFT OUTER JOIN `curltest`.`comet_modules` cm2 on cm.id = cm2.english_version_id
            WHERE UNIX_TIMESTAMP(cmc.date_completed) BETWEEN {$this->startTimestamp} AND {$this->endTimestamp}
            GROUP BY cmc.module
            ORDER BY count(cmc.module) DESC
        "));

        foreach($cometCompletionsByModule as $x) {
            if($x->frenchTitle) {
                $frenchRow = $cometCompletionsByModule->where('englishTitle', '=', $x->frenchTitle)->first();
                if($frenchRow) {
                    $frenchRowKey = $cometCompletionsByModule->search($frenchRow);
                    $x->frenchCompletions = $frenchRow->frenchCompletions;
                    unset($cometCompletionsByModule[$frenchRowKey]);
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
            
        return $cometCompletionsByModule->sortByDesc('totalCompletions');
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
