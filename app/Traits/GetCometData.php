<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

trait GetCometData {

    /**
    * Formats training portal course name into English and French columns.
    *
    * @param array $collection Collection of training portal courses
    *
    * @param string $englishColumn Name of column with English course information
    *
    * @param string $frenchColumn Name of column with French course information
    *
    * @return array $formattedCollection Collection after formatting English and French columns
    */
    private function getCometAccesses($startTimestamp, $endTimestamp)
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
            WHERE UNIX_TIMESTAMP(cma.date) BETWEEN {$startTimestamp} AND {$endTimestamp}
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

        return $cometAccessesByModule;
    }

    /**
    * Formats training portal course name into single column with both English and French names
    *
    * @param array $collection Collection of training portal courses
    *
    * @param string $column Name of column with both English and French course information
    *
    * @return array $formattedCollection Collection after formatting column with both English and French information
    */
    private function getCometCompletions($startTimestamp, $endTimestamp)
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
            WHERE UNIX_TIMESTAMP(cmc.date_completed) BETWEEN {$startTimestamp} AND {$endTimestamp}
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
        return $cometCompletionsByModule;
    }
}
