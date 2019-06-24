<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CometListController extends Controller
{
    function index () {
        $collection = collect(DB::connection('mysql')
            ->select("SELECT *
            FROM `curltest`.`comet_modules`
            WHERE msc_funded = FALSE
            ORDER BY title"));

        return $collection->each(function ($row) {
            $row->description = $this->truncate($row->description);
        });
    }

    function update () {
        // store new blacklist
        $data = request()->all();
        foreach($data as $row) {
            DB::connection('mysql')->table('curltest.comet_modules')
            ->where('id', $row['id'])
            ->update([
                'include_in_catalog' => $row['include_in_catalog'],
            ]);
        }
        return response('Successfully updated COMET module blacklist.', 200);
    }

    private function truncate($string, $length=250, $append="...") {
        $string = trim($string);
        $string = preg_replace("~\n~", " ", $string);
      
        if(strlen($string) > $length) {
          $string = wordwrap($string, $length);
          $string = explode("\n", $string, 2);
          $string = rtrim($string[0], " ,") . $append;
        }
        return $string;
    }
}
