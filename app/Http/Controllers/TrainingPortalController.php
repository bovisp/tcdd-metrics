<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\FormatCollection;
use Illuminate\Support\Facades\DB;

class TrainingPortalController extends Controller
{
    use FormatCollection;
    
    function index () {
        $collection = collect(DB::connection('mysql2')
        ->select("SELECT c.id as course_id, c.fullname as fullname, cc.id, cc.id as include_in_catalog
            FROM mdl_course c
            LEFT OUTER JOIN `tcdd-metrics`.`course_catalog` cc ON c.id = cc.course_id
            WHERE c.category != 29
            AND c.id != 1
            AND c.visible != 0
            AND c.id NOT IN (
                SELECT c.id
                FROM `mdl_course` c
                INNER JOIN `mdl_badge` b on b.courseid = c.id
                AND c.visible != 0
                AND b.id IN (44,45,8,22,11,12,27,28,34,31,43,42)
                GROUP BY c.id
            )"
        ));

        $collection = $collection->map(function ($row) {
            if($row->include_in_catalog) {
                $row->include_in_catalog = true;
            } else {
                $row->include_in_catalog = false;
            }
            return $row;
        });

        return $this->formatOneColumn($collection, "fullname");
    }

    function update () {
        // store new blacklist
        $data = request()->all();
        // foreach($data as $row) {
        //     DB::connection('mysql')->table('curltest.comet_modules')
        //     ->where('id', $row['id'])
        //     ->update([
        //         'include_in_catalog' => $row['include_in_catalog'],
        //     ]);
        // }

        foreach($data as $row) {
            if($row['id'] && !$row['include_in_catalog']) {
                DB::connection('mysql')->select("
                    DELETE FROM course_catalog
                    WHERE id = {$row['id']}
                ");
            } else if (!$row['id'] && $row['include_in_catalog']) {
                DB::connection('mysql')->select("
                    INSERT INTO course_catalog (course_id)
                    VALUES ({$row['course_id']})
                ");
            }
        }

        return response('Successfully updated Training Portal course blacklist.', 200);
    }
}
