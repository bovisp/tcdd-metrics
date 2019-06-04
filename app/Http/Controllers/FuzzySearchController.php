<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\OriginalTitlesImport;
use Illuminate\Support\Facades\Config;

class FuzzySearchController extends Controller
{
    public function getOriginalTitles() {
        $array = Excel::toArray(new OriginalTitlesImport, 'C:\wamp64\www\pdf-test\storage\app\public\MSC_funded_modules.csv');

        $um = [];
        for ($i = 0; $i < count($array[0]); $i++) {
            array_push($um, $array[0][$i][0]);
        }
        return $um;
    }

    public function getCorrectTitles() {
        return DB::connection('mysql')->select("SELECT c.id, c.titleFr
        FROM `curltest`.`comet_french` c");
    }

    public function storeCorrectTitles() {;
        foreach(request()->all() as $title) {
            DB::connection('mysql')->insert("insert into `curltest`.`msc_comet_test` (titleFr) values (\"{$title}\")");
        }

        return response('Saved corrected titles.', 200);
    }
}
