<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class CourseViewsByCourse implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $startTimestamp;
    protected $endTimestamp;

    public function __construct($startDateTime, $endDateTime)
    {
        $this->startTimestamp = $startDateTime->timestamp;
        $this->endTimestamp = $endDateTime->timestamp;
    }

    public function collection()
    {
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
        AND l.timecreated BETWEEN {$this->startTimestamp} AND {$this->endTimestamp}
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
