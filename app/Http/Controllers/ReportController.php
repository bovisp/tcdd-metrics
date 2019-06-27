<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Charts\Frappe;
use App\Charts\testChart;
use Illuminate\Http\Request;
use App\Jobs\GenerateReportJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Khill\Lavacharts\Laravel\LavachartsFacade\Lava;

class ReportController extends Controller
{
    // public function lavaTest() {
    //     $stocksTable = \Lava::DataTable();

    //     $stocksTable->addStringColumn('Course')
    //     ->addNumberColumn('English')
    //     ->addNumberColumn('French');

    //     $stocksTable->addRow([
    //         'One', rand(800,1000), rand(800,1000)
    //     ]);
    //     $stocksTable->addRow([
    //         'Two', rand(800,1000), rand(800,1000)
    //     ]);
    //     $stocksTable->addRow([
    //         'Three', rand(800,1000), rand(800,1000)
    //     ]);
    //     $stocksTable->addRow([
    //         'Four', rand(800,1000), rand(800,1000)
    //     ]);
    //     $stocksTable->addRow([
    //         'Five', rand(800,1000), rand(800,1000)
    //     ]);

    //     $lineChart = \Lava::LineChart('MyStocks', $stocksTable);

    //     $columnChart = \Lava::ColumnChart('MyStocks', $stocksTable, [
    //         'isStacked' => true
    //     ]);

    //     $pdf = \PDF::loadView('lavaCharts');
    //     return $pdf->download('test.pdf');

    //     return view('lavaCharts');
    // }

    // public function laravelChartsTest() {
    //     $chart = new testChart;
    //     $chart->labels(['One', 'Two', 'Three', 'Four', 'Five']);
    //     $chart->dataset('My dataset 1', 'bar', [rand(800,1000), rand(800,1000), rand(800,1000), rand(800,1000), rand(800,1000)]);
    //     $chart->dataset('My dataset 2', 'bar', [rand(800,1000), rand(800,1000), rand(800,1000), rand(800,1000), rand(800,1000)]);

    //     $pdf = \PDF::loadView('laravelCharts', compact('chart'));
    //     return $pdf->download('test.pdf');

    //     return view('laravelCharts', compact('chart'));

    //     // $chart = new Frappe;
    //     // $chart->labels(['One', 'Two', 'Three', 'Four', 'Five']);
    //     // $chart->dataset('My dataset 1', 'bar', [rand(800,1000), rand(800,1000), rand(800,1000), rand(800,1000), rand(800,1000)]);
    //     // $chart->dataset('My dataset 2', 'bar', [rand(800,1000), rand(800,1000), rand(800,1000), rand(800,1000), rand(800,1000)]);

    //     // return view('laravelCharts', compact('chart'));
    // }

    /**
    * Dispatches GenerateReportJob (generates and emails reports).
    *
    * @param array request contains array of reportIds, start and end dates
    *
    * @return array response includes message and status code
    *
    * @api
    */
    public function emailReports() {
        if(sizeOf(request()->input('reportIds')) < 1) {
            return response("Report Ids are required.", 422);
        }
        request()->validate([
            'reportIds.*' => 'exists:report_types,id',
            'startDate' => 'required',
            'endDate' => 'required'
        ]);
        $reportIds = request()->input('reportIds');
        $startDateTime = Carbon::parse(request()->input('startDate'));
        $endDateTime = Carbon::parse(request()->input('endDate'));

        GenerateReportJob::dispatch($startDateTime, $endDateTime, $reportIds);

        return response("Successfully generated reports.", 200);
    }

    /**
    * Returns list of report types from database.
    *
    * @return array response includes array of report types
    *
    * @api
    */
    public function index() {
        return DB::connection('mysql')->table('report_types')->orderBy('name', 'asc')->get();
    }

    /**
    * Returns minimum date with data for course completions and course views report types.
    *
    * @return array response includes array of timestamps
    *
    * @api
    */
    public function minDateTimestamp() {
        $minDateCourseCompletions = Cache::rememberForever('minDateCourseCompletions', function () {
            $queryMinDateCourseCompletions = "SELECT min(bi.dateissued) as '1'
                            FROM `moodledb`.`mdl_badge_issued` bi
                            INNER JOIN `moodledb`.`mdl_badge` b ON bi.badgeid = b.id
                            INNER JOIN `moodledb`.`mdl_course` c ON b.courseid = c.id
                            LEFT OUTER JOIN `tcdd-metrics`.`badge_language` bl ON bi.badgeid = bl.badge_id
                            LEFT OUTER JOIN `tcdd-metrics`.`languages` l ON bl.language_id = l.id
                            WHERE bi.badgeid IN (44,45,8,22,11,12,27,28,34,31,43,42)";

            return collect(DB::connection('mysql2')->select($queryMinDateCourseCompletions))[0];
        });

        $minDateCourseViews = Cache::rememberForever('minDateCourseViews', function () {
            $queryMinDateCourseViews = "SELECT min(l.timecreated) as '2'
                            FROM `mdl_logstore_standard_log` l
                            LEFT OUTER JOIN `mdl_role_assignments` a
                                ON l.contextid = a.contextid
                                AND l.userid = a.userid
                            WHERE l.target = 'course'
                            AND l.action = 'viewed'
                            AND l.courseid > 1
                            AND (a.roleid IN (5, 6, 7) OR l.userid = 1)";

            return collect(DB::connection('mysql2')->select($queryMinDateCourseViews))[0];
        });

        $minDateCOMETCompletions = Cache::rememberForever('minDateCOMETCompletions', function () {
            $queryMinDateCOMETCompletions = "SELECT UNIX_TIMESTAMP(min(date_completed)) as '3'
                            FROM `comet_completion` c";

            return collect(DB::connection('mysql')->select($queryMinDateCOMETCompletions))[0];
        });

        $minDateCOMETAccesses = Cache::rememberForever('minDateCOMETAccesses', function () {
            $queryminDateCOMETAccesses = "SELECT UNIX_TIMESTAMP(min(date)) as '4'
                            FROM `comet_access` c";

            return collect(DB::connection('mysql')->select($queryminDateCOMETAccesses))[0];
        });

        return [$minDateCourseCompletions, $minDateCourseViews, $minDateCOMETCompletions, $minDateCOMETAccesses];
    }
}
