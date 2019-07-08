<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use App\Exports\ExportCompletionsByBadge;
use App\Exports\ExportCourseViewsByCourse;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Mail\CourseCompletions;
use App\Mail\CourseViews;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GenerateCourseCompletions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $dir;
    protected $startDateTime;
    protected $endDateTime;
    protected $interval;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($startDateTime, $endDateTime = null)
    {
        $this->startDateTime = $startDateTime;
        $this->endDateTime = $endDateTime === null ? Carbon::now() : $endDateTime;
        $this->dir = env('APP_ENV') === 'testing' ? 'test' : '';
        $this->interval = $this->startDateTime->toDateString() . "_" . $this->endDateTime->toDateString();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //generate spreadsheet and save to disk
        Excel::store(new ExportCompletionsByBadge($this->startDateTime->timestamp, $this->endDateTime->timestamp), $this->dir ? $this->dir . "/" . "course_completions_" . $this->interval . ".xlsx" : "course_completions_" . $this->interval . ".xlsx");

        //email spreadsheet
        Mail::to('me@me.com')->send(new CourseCompletions($this->interval));

        //delete spreadsheet from disk
        @unlink("C:\wamp64\www\\tcdd-metrics\storage\app\course_completions_" . $this->interval . ".xlsx");
    }
}
