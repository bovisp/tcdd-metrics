<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\Jobs\GenerateReport;
use App\Mail\CourseCompletions;
use App\Mail\CourseViews;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ScheduledJobsTest extends TestCase
{
    /** @test */
    public function generate_report_sends_email() {
        $this->withExceptionHandling();
        Mail::fake();
        Mail::assertNothingSent();

        //dispatch GenerateReport
        $startDateTime = Carbon::now()->subYear();
        $endDateTime = Carbon::now();
        $interval = $startDateTime->toDateString() . "_" . $endDateTime->toDateString();
        GenerateReport::dispatch($startDateTime, $endDateTime);

        //assert that email has been sent
        Mail::assertSent(CourseCompletions::class);
        Mail::assertSent(CourseViews::class);

        //delete spreadsheets
        $path = "C:\wamp64\www\\tcdd-metrics\storage\app\\test";
        @unlink($path . "\course_views_" . $interval . ".xlsx");
        @unlink($path . "\course_completions_" . $interval . ".xlsx");
    }

    /** @test */
    public function generate_report_saves_a_file() {
        Mail::fake();
        Mail::assertNothingSent();

        //dispatch GenerateReport
        $startDateTime = Carbon::now()->subYear();
        $endDateTime = Carbon::now();
        $interval = $startDateTime->toDateString() . "_" . $endDateTime->toDateString();
        GenerateReport::dispatch($startDateTime, $endDateTime);

        //assert that spreadsheets have been saved to disk
        $path = "C:\wamp64\www\\tcdd-metrics\storage\app\\test";
        $this->assertFileExists($path . "\course_views_" . $interval . ".xlsx");
        $this->assertFileExists($path . "\course_completions_" . $interval . ".xlsx");

        //delete spreadsheets
        @unlink($path . "\course_views_" . $interval . ".xlsx");
        @unlink($path . "\course_completions_" . $interval . ".xlsx");
    }
}
