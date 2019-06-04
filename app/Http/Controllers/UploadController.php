<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\CometUploadImport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class UploadController extends Controller
{
    public function storeAccesses() {
        $data = request()->all();
        foreach($data as $sheet) {
            foreach($sheet as $row) {
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
            }
        }
        return response('Successfully uploaded COMET data.', 200);
    }

    public function storeCompletions() {
        $data = request()->all();
        foreach($data as $sheet) {
            foreach($sheet as $row) {
                DB::connection('mysql')->table('comet_completion')->insert([
                    'email' => $row['email'],
                    'last' => $row['Last_name'],
                    'first' => $row['First_name'],
                    'module' => $row['Module'],
                    'language' => $row['Language'],
                    'score' => $row['score'],
                    'date_completed' => $row['date_completed']
                ]);
            }
        }
        return response('Successfully uploaded COMET data.', 200);
    }

    public function upload(Request $request) {
        //$path = $request->file('file')->store('/');
        $data = Excel::toCollection(new CometUploadImport, $request->file('file'), null, \Maatwebsite\Excel\Excel::XML);
        return $data;
    }
}
