<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GenerateReportController extends Controller
{
    public function store() {
        request()->validate([
            'reportIds.*' => 'exists:report_types,id'
        ]);

        $reportIds = request()->input('reportIds');
        $startDateTime = request()->input('startDateTime');
        $endDateTime = request()->input('endDateTime');
        // dispatch job for each report type but only send one email

        foreach($reportIds as $reportId) {
            $reportName = DB::connection('mysql')->table('report_types')
                ->select('name')
                ->where('id', '=', $reportId)->get()[0]->name;

            $formattedReportName = str_replace(' ', '', $reportName);
            $job = "App\Jobs\\Generate" . $formattedReportName;
            $job::dispatch($startDateTime, $endDateTime);
        };
    }
}
