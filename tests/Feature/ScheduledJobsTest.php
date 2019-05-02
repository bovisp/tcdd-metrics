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
        $startDateTime = Carbon::now()->subYear();
        $endDateTime = Carbon::now();
        GenerateReportJob::dispatch($startDateTime, $endDateTime, null);

        //assert that email has been sent
        Mail::assertSent(TrainingMetricsReports::class);
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
        $startDateTime = Carbon::now()->subYear();
        $endDateTime = Carbon::now();
        $interval = $startDateTime->toDateString() . "_" . $endDateTime->toDateString();
        $reportNames = ['Course Completions', 'Course Views'];

        GenerateReportJob::dispatch($startDateTime, $endDateTime, null);

        //assert that spreadsheets have been saved to disk
        foreach($reportNames as $reportName) {
            $fileName = str_replace(" ", "_", $reportName);
            $this->assertFileExists("C:\wamp64\www\\tcdd-metrics\storage\app\\test\\" . $fileName . "_" . $interval . ".xlsx");
        }
    }
}
