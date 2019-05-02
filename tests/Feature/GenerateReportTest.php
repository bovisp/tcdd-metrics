<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use App\Mail\TrainingMetricsReports;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class GenerateReportTest extends TestCase
{
    use DatabaseMigrations;
    /** @test */
    public function a_user_can_generate_a_report_and_save_it_to_disk()
    {
        $this->withoutExceptionHandling();
        Mail::fake();
        // insert report types
        DB::connection('mysql')->table('report_types')->insert([
            'id' => 1,
            'name' => 'Course Completions'
        ]);
        DB::connection('mysql')->table('report_types')->insert([
            'id' => 2,
            'name' => 'Course Views'
        ]);

        // create request
        $reportIds = [1,2];
        $startDateTime = Carbon::now()->subCentury();
        $endDateTime = Carbon::now();
        $request = [
            'reportIds' => $reportIds,
            'startDateTime' => $startDateTime,
            'endDateTime' => $endDateTime
        ];
        $interval = $startDateTime->toDateString() . "_" . $endDateTime->toDateString();

        // post request to controller
        $this->post('/reports', $request);

        // assert each report has been saved to disk
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

    /** @test */
    public function a_user_can_generate_a_report_and_mail_it()
    {
        $this->withoutExceptionHandling();
        Mail::fake();
        // insert report types
        DB::connection('mysql')->table('report_types')->insert([
            'id' => 1,
            'name' => 'Course Completions'
        ]);
        DB::connection('mysql')->table('report_types')->insert([
            'id' => 2,
            'name' => 'Course Views'
        ]);

        // create request
        $reportIds = [1,2];
        $startDateTime = Carbon::now()->subCentury();
        $endDateTime = Carbon::now();
        $request = [
            'reportIds' => $reportIds,
            'startDateTime' => $startDateTime,
            'endDateTime' => $endDateTime
        ];

        // post request to controller
        $this->post('/reports', $request);

        //assert that email has been sent
        Mail::assertSent(TrainingMetricsReports::class);
    }
}
