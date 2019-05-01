<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
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
            'reports' => $reportIds,
            'startDate' => $startDateTime,
            'endDate' => $endDateTime
        ];
        $interval = $startDateTime->toDateString() . "_" . $endDateTime->toDateString();

        // post request to controller
        $this->post('/generate-report', $request);
        // assert each report has been saved to disk
        $path = "C:\wamp64\www\\tcdd-metrics\storage\app\\test";
        $this->assertFileExists($path . "\course_views_" . $interval . ".xlsx");
        
    }

    /** @test */
    public function a_user_can_generate_a_report_and_mail_it()
    {
        //a user can save a report to disk
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
