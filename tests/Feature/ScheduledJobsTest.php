<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\Jobs\GenerateCourseViews;
use App\Jobs\GenerateCourseCompletions;
use App\Mail\CourseCompletions;
use App\Mail\CourseViews;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ScheduledJobsTest extends TestCase
{
    /** @test */
    public function generate_course_views_sends_email() {
        $this->withExceptionHandling();
        Mail::fake();
        Mail::assertNothingSent();

        //dispatch GenerateReport
        $startDateTime = Carbon::now()->subYear();
        $endDateTime = Carbon::now();
        $interval = $startDateTime->toDateString() . "_" . $endDateTime->toDateString();
        GenerateCourseViews::dispatch($startDateTime, $endDateTime);

        //assert that email has been sent
        Mail::assertSent(CourseViews::class);

        //delete spreadsheet
        @unlink("C:\wamp64\www\\tcdd-metrics\storage\app\\test\course_views_" . $interval . ".xlsx");
    }

    /** @test */
    public function generate_course_views_saves_a_file() {
        Mail::fake();
        Mail::assertNothingSent();

        //dispatch GenerateReport
        $startDateTime = Carbon::now()->subYear();
        $endDateTime = Carbon::now();
        $interval = $startDateTime->toDateString() . "_" . $endDateTime->toDateString();
        GenerateCourseViews::dispatch($startDateTime, $endDateTime);

        //assert that spreadsheet has been saved to disk
        $this->assertFileExists("C:\wamp64\www\\tcdd-metrics\storage\app\\test\course_views_" . $interval . ".xlsx");

        //delete spreadsheet
        @unlink("C:\wamp64\www\\tcdd-metrics\storage\app\\test\course_views_" . $interval . ".xlsx");
    }

    /** @test */
    public function generate_course_completions_sends_email() {
        $this->withExceptionHandling();
        Mail::fake();
        Mail::assertNothingSent();

        //dispatch GenerateReport
        $startDateTime = Carbon::now()->subYear();
        $endDateTime = Carbon::now();
        $interval = $startDateTime->toDateString() . "_" . $endDateTime->toDateString();
        GenerateCourseCompletions::dispatch($startDateTime, $endDateTime);

        //assert that email has been sent
        Mail::assertSent(CourseCompletions::class);

        //delete spreadsheet
        @unlink("C:\wamp64\www\\tcdd-metrics\storage\app\\test\course_completions_" . $interval . ".xlsx");
    }

    /** @test */
    public function generate_course_completions_saves_a_file() {
        Mail::fake();
        Mail::assertNothingSent();

        //dispatch GenerateReport
        $startDateTime = Carbon::now()->subYear();
        $endDateTime = Carbon::now();
        $interval = $startDateTime->toDateString() . "_" . $endDateTime->toDateString();
        GenerateCourseCompletions::dispatch($startDateTime, $endDateTime);

        //assert that spreadsheets have been saved to disk
        $this->assertFileExists("C:\wamp64\www\\tcdd-metrics\storage\app\\test\course_completions_" . $interval . ".xlsx");

        //delete spreadsheet
        @unlink("C:\wamp64\www\\tcdd-metrics\storage\app\\test\course_completions_" . $interval . ".xlsx");
    }
}
