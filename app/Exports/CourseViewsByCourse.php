<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CourseViewsByCourse implements FromCollection, WithHeadings, ShouldAutoSize
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
        $query = "select l.courseid, c.fullname 'englishname', c.fullname 'frenchname', count(*) as 'Course Views'
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
        GROUP BY l.courseid";

        $collection = collect(DB::connection('mysql2')->select($query));
        return $this->formatCollection($collection);
    }

    private function formatCollection(Collection $collection)
    {
        $formattedCollection = $collection->each(function ($x) {
            //english course name formatting
            $original = $x->englishname;
            $x->englishname = trim(preg_replace("/<span lang=\"en\" class=\"multilang\">|<\/span> <span lang=\"fr\" class=\"multilang\">(.*)<\/span>/", "", $x->englishname));
            
            if($original === $x->englishname) { //only run the second preg_replace if the first did nothing
                $x->englishname = trim(preg_replace("/{mlang en}|{mlang} {mlang fr}(.*){mlang}/", "", $x->englishname));
            }

            //french course name formatting
            $original = $x->frenchname;
            $x->frenchname = trim(preg_replace("/<span lang=\"en\" class=\"multilang\">(.*)<\/span> <span lang=\"fr\" class=\"multilang\">|<\/span>/", "", $x->frenchname));
            
            if($original === $x->frenchname) { //only run the second preg_replace if the first did nothing
                $x->frenchname = trim(preg_replace("/{mlang en}(.*){mlang} {mlang fr}|{mlang}/", "", $x->frenchname));
            }
        });
        return $formattedCollection;
    }

    public function headings(): array
    {
        return [
            'Id',
            'English Course Name',
            'French Course Name',
            'Course Views'
        ];
    }
}
