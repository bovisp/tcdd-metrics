<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\Jobs\GenerateReportJob;
use Illuminate\Support\Facades\DB;
use App\Mail\TrainingMetricsReports;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ScheduledJobsTest extends TestCase
{
    use DatabaseMigrations;
    /** @test */
    public function generate_report_sends_email() {
        $this->withExceptionHandling();
        Mail::fake();
        Mail::assertNothingSent();
        // insert report types
        DB::connection('mysql')->table('report_types')->insert([
            'id' => 1,
            'name' => 'Course Completions'
        ]);
        DB::connection('mysql')->table('report_types')->insert([
            'id' => 2,
            'name' => 'Course Views'
        ]);

        //dispatch GenerateReport
        $reportIds = [1,2];
        $startDate = Carbon::now()->subYear();
        $endDate = Carbon::now();
        $interval = $startDate->toDateString() . "_" . $endDate->toDateString();
        GenerateReportJob::dispatch($startDate, $endDate, $reportIds);

        //assert that email has been sent
        Mail::assertSent(TrainingMetricsReports::class);

        // delete spreadsheets
        $path = "C:\wamp64\www\\tcdd-metrics\storage\app\\test";
        foreach($reportIds as $reportId) {
            $reportName = DB::connection('mysql')->table('report_types')
                ->select('name')
                ->where('id', '=', $reportId)->get()[0]->name;

            $formattedReportName = str_replace(' ', '_', $reportName);
            @unlink($path . "\\" . $formattedReportName . "_" . $interval . ".xlsx");
        };
    }

    /** @test */
    public function generate_report_saves_a_file() {
        $this->withExceptionHandling();
        Mail::fake();
        Mail::assertNothingSent();

        // insert report types
        DB::connection('mysql')->table('report_types')->insert([
            'id' => 1,
            'name' => 'Course Completions'
        ]);
        DB::connection('mysql')->table('report_types')->insert([
            'id' => 2,
            'name' => 'Course Views'
        ]);

        //dispatch GenerateReport
        $reportIds = [1,2];
        $startDate = Carbon::now()->subYear();
        $endDate = Carbon::now();
        $interval = $startDate->toDateString() . "_" . $endDate->toDateString();
        GenerateReportJob::dispatch($startDate, $endDate, $reportIds);

        //assert that spreadsheets have been saved to disk
        $path = "C:\wamp64\www\\tcdd-metrics\storage\app\\test";
        foreach($reportIds as $reportId) {
            $reportName = DB::connection('mysql')->table('report_types')
                ->select('name')
                ->where('id', '=', $reportId)->get()[0]->name;

            $formattedReportName = str_replace(' ', '_', $reportName);
            $this->assertFileExists($path . "\\" . $formattedReportName . "_" . $interval . ".xlsx");
            @unlink($path . "\\" . $formattedReportName . "_" . $interval . ".xlsx");
        };
    }
}
