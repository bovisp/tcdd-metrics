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
use App\Traits\GetCometData;
use App\Traits\GetTrainingPortalData;

class ReportController extends Controller
{
    use GetCometData;
    use GetTrainingPortalData;

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

    public function cometAccesses() {
        return $this->getCometAccesses(0, 4102444800)->sortByDesc('totalCompletions');
    }

    public function cometCompletions() {
        return $this->getCometCompletions(0, 4102444800)->sortByDesc('totalCompletions');
    }

    public function trainingPortalViews() {
        return $this->getTrainingPortalViews(0, 4102444800)->sortByDesc('views');
    }

    public function trainingPortalCompletions() {
        return $this->getTrainingPortalCompletions(0, 4102444800)->sortByDesc('completions');
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
