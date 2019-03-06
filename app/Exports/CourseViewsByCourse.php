<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;

class CourseViewsByCourse implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct()
    {
        $end_timestamp = Carbon::now()->timestamp;
        $start_timestamp = Carbon::now()->subMonths(3)->timestamp;
    }

    public function collection()
    {
        $end_timestamp = Carbon::now()->timestamp;
        $start_timestamp = Carbon::now()->subMonths(3)->timestamp;
        //sql query for course views by course
        //inject start and end dates here in where clause for particular interval
        //end date = now, start date = now - interval time span
        $collection = collect(DB::connection('mysql2')->select("select l.courseid, c.fullname, count(*) as 'Course Views'
        FROM mdl_logstore_standard_log l
        LEFT OUTER JOIN mdl_role_assignments a
            ON l.contextid = a.contextid
            AND l.userid = a.userid
        INNER JOIN mdl_course c ON l.courseid = c.id
        WHERE l.target = 'course'
        AND l.action = 'viewed'
        AND l.courseid > 1
        AND (a.roleid IN (5, 6, 7) OR l.userid = 1)
        AND {$start_timestamp} AND {$end_timestamp}
        GROUP BY l.courseid
        "));

        return $this->formatCollection($collection);
    }

    private function formatCollection(Collection $collection)
    {
        $formattedCollection = $collection->each(function ($x) {
            $original = $x->fullname;
            $x->fullname = preg_replace("/<span lang=\"en\" class=\"multilang\">|<\/span> <span lang=\"fr\" class=\"multilang\">(.*)<\/span>/", "", $x->fullname);
            
            if($original === $x->fullname) { //only run the second preg_replace if the first did nothing
                $x->fullname = preg_replace("/{mlang en}|{mlang} {mlang fr}(.*){mlang}/", "", $x->fullname);
            }
        });

        return $formattedCollection;
    }
}
