<?php

namespace App\Exports;

use App\Traits\FormatCollection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ExportStayConnectedWebinars implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize
{
    use FormatCollection;
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
        // query below has to combine webinar views and webinar live attendance using a join across databases
        //      to access a table in a different database, specify the database name before the table using dot syntax, e.g. `tcdd-metrics`.`webinar_attendance`
        //      for views, use a similar query to the one in CourseViewsByCourseSheet, where category = 28 for Stay Connected
        //      for attendance, query the webinar_attendance table
        //      run query results through formatTwoColumns for correct english and french course names
        // then the query results must be processed to combine the french views/attendance with the english views/attendance in one row for each course 
        //      below is code doing something similar for COMET completions
        // the headings array below shows the columns needed for the final collection
        //      for views, use only one of the values from the rows for a course (either french or english)
        // afterwards, uncomment the code in report types seeder and seed the db.
        //      check if UI for generate report includes the new report type and sends a spreadsheet for it


        $webinarStats = collection(DB::connection('mysql2')->select("
            SELECT wa.course_id, c.fullname 'english_course_name', c.fullname 'french_course_name', wa.language_id, wa.attendees as 'en_attendees', wa.attendees as 'fr_attendees', wa.attendees as 'total_attendees', count(l.courseid) as 'views'
            FROM `tcdd-metrics`.`webinar_attendance` wa
            INNER JOIN mdl_course c ON wa.course_id = c.id
            LEFT OUTER JOIN mdl_logstore_standard_log l ON l.courseid = wa.course_id
            LEFT OUTER JOIN mdl_role_assignments a
                ON l.contextid = a.contextid
                AND l.userid = a.userid
            WHERE l.target = 'course'
            AND l.action = 'viewed'
            AND l.courseid > 1
            AND (a.roleid IN (5, 6, 7) OR l.userid = 1)
            AND l.timecreated BETWEEN {$this->startTimestamp} AND {$this->endTimestamp}
            AND c.category = 28
            AND c.visible != 0
            GROUP BY wa.course_id, wa.language_id
        "));
        $formattedWebinarStats = $this->formatTwoColumns($webinarStats, 'english_course_name', 'french_course_name');

        foreach($formattedWebinarStats as $x) {
            if($x->language_id === 1) {
                $frenchRow = $formattedWebinarStats->where('course_id', '=', $x->course_id)->where('language_id', '=', '2')->first();
                if($frenchRow) {
                    $frenchRowKey = $formattedWebinarStats->search($frenchRow);
                    $x->fr_attendees = $frenchRow->fr_attendees;
                    unset($formattedWebinarStats[$frenchRowKey]);
                } else {
                    $x->fr_attendees = 0;
                }
            } elseif($x->language_id === 2) {
                $englishRow = $formattedWebinarStats->where('course_id', '=', $x->course_id)->where('language_id', '=', '1')->first();
                if($englishRow) {
                    $englishRowKey = $formattedWebinarStats->search($englishRow);
                    $x->en_attendees = $englishRow->er_attendees;
                    unset($formattedWebinarStats[$englishRowKey]);
                } else {
                    $x->en_attendees = 0;
                }
            }
            $x->total_attendees = $x->en_attendees + $x->fr_attendees;
            unset($x->language_id);
            unset($x->course_id);
        };
        
        return $formattedWebinarStats;
    }

    public function headings(): array
    {
        return [
            'English webinar name',
            'French webinar name',
            'English live attendance',
            'French live attendance',
            'Sum of English and French live attendance',
            'Portal views'
        ];
    }

    public function title(): string
    {
        return 'Stay Connected Webinars';
    }
}
