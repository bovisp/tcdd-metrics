<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\GenerateReportJob;
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
        
        GenerateReportJob::dispatch($reportIds, $startDateTime, $endDateTime);
    }
}
