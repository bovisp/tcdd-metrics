<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use App\Exports\CourseViewsByCourse;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Mail\EmailCourseViewsByCourse;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GenerateReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $dir;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->dir = env('APP_ENV') === 'testing' ? 'test' : '';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Excel::store(new CourseViewsByCourse, $this->dir ? $this->dir . '/' . 'course_views_by_course.xlsx' : 'course_views_by_course.xlsx');

        Mail::to('me@me.com')->send(new EmailCourseViewsByCourse);
    }
}
