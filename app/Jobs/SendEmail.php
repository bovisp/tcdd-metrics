<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Exports\CourseViewsByCourse;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Mail\EmailCourseViewsByCourse;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendEmail implements ShouldQueue
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
        
        //create exports class
        Excel::store(new CourseViewsByCourse, $this->dir ? $this->dir . '/' . 'courseviewsbycourse.xlsx' : 'courseviewsbycourse.xlsx');

        Mail::to('me@me.com')->send(new EmailCourseViewsByCourse);
    }
}
