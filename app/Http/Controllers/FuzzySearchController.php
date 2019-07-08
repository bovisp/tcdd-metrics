<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\OriginalTitlesImport;
use Illuminate\Support\Facades\Config;

class FuzzySearchController extends Controller
{
    /**
    * Converts uploaded spreadsheet of MSC-funded COMET modules to collection.
    *
    * @param array request includes a spreadsheet file
    *
    * @return array response includes a collection with the data from the spreadsheet
    *
    * @api
    */
    public function getOriginalTitles(Request $request) {
        $data = Excel::toCollection(new OriginalTitlesImport, $request->file('file'), null, \Maatwebsite\Excel\Excel::CSV);
        return $data;
    }

    /**
    * Stores correct titles of French MSC-funded COMET modules.
    *
    * @param array request includes array of correct titles
    *
    * @return array response includes message and status code
    *
    * @api
    */
    public function storeCorrectTitles() {;
        foreach(request()->all() as $title) {
            DB::connection('mysql')->insert("insert into `curltest`.`msc_comet_test` (titleFr) values (\"{$title}\")");
        }

        return response('Saved corrected titles.', 200);
    }
}
