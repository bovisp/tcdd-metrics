<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Jobs\GenerateReportJob;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function store() {
        request()->validate([
            'reportIds.*' => 'exists:report_types,id'
        ]);
        $reportIds = request()->input('reportIds');
        $startDateTime = Carbon::parse(request()->input('startDate'));
        $endDateTime = Carbon::parse(request()->input('endDate'));

        GenerateReportJob::dispatch($startDateTime, $endDateTime, $reportIds);

        return response("Successfully generated reports.", 200);
    }

    public function index() {
        return DB::connection('mysql')->table('report_types')->orderBy('name', 'asc')->get();
    }
}
