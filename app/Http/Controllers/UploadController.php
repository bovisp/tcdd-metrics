<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rules\UniqueCometAccess;
use App\Imports\CometUploadImport;
use Illuminate\Support\Facades\DB;
use App\Rules\UniqueCometCompletion;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class UploadController extends Controller
{
    
    /**
    * Stores COMET access data.
    *
    * @param array request must contain an array of sheets each being an array of rows
    *
    * @return array response includes message and status code
    *
    * @api
    */
    public function storeAccesses() {
        $data = request()->all();
        foreach($data as $sheet) {
            foreach($sheet as $row) {
                $validator = Validator::make($row, [
                    'email' => [new UniqueCometAccess($row['Module'], $row['date'])]
                ]);

                if (!$validator->fails()) {
                    DB::connection('mysql')->table('comet_access')->insert([
                        'email' => $row['email'],
                        'last' => $row['last'],
                        'first' => $row['first'],
                        'module' => $row['Module'],
                        'language' => $row['language'],
                        'sessions' => $row['sessions'],
                        'elapsed_time' => $row['elapsed_time'],
                        'session_pages' => $row['session_pages'],
                        'date' => $row['date']
                    ]);
                } else {
                    continue;
                }
            }
        }
        return response('Successfully uploaded COMET data.', 200);
    }

    /**
    * Stores COMET completion data.
    *
    * @param array request must contain an array of sheets each being an array of rows
    *
    * @return array response includes message and status code
    *
    * @api
    */
    public function storeCompletions() {
        $data = request()->all();
        foreach($data as $sheet) {
            foreach($sheet as $row) {
                $validator = Validator::make($row, [
                    'email' => [new UniqueCometCompletion($row['Module'], $row['date_completed'])]
                ]);

                if(!$validator->fails()) {
                    DB::connection('mysql')->table('comet_completion')->insert([
                        'email' => $row['email'],
                        'last' => $row['Last_name'],
                        'first' => $row['First_name'],
                        'module' => $row['Module'],
                        'language' => $row['Language'],
                        'score' => $row['score'],
                        'date_completed' => $row['date_completed']
                    ]);
                } else {
                    continue;
                }
            }
        }
        return response('Successfully uploaded COMET data.', 200);
    }

    /**
    * Converts uploaded spreadsheet of COMET data to collection.
    *
    * @param array request includes a spreadsheet file
    *
    * @return array response includes a collection with the data from the spreadsheet
    *
    * @api
    */
    public function upload(Request $request) {
        $data = Excel::toCollection(new CometUploadImport, $request->file('file'), null, \Maatwebsite\Excel\Excel::XML);
        return $data;
    }

    /**
    * Gets correct French comet module titles from database.
    *
    * @return array response includes array of correct French comet module titles
    *
    * @api
    */
    public function getCorrectTitles() {
        return DB::connection('mysql')->select("SELECT c.id, c.title
        FROM `comet_modules` c
        WHERE c.language_id = 2
        ");
    }
}
