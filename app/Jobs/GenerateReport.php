<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use App\Exports\ExportCompletionsByBadge;
use App\Exports\ExportCourseViewsByCourse;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Mail\CompletionsByBadge;
use App\Mail\CourseViewsByCourse;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GenerateReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $dir;
    protected $startTimestamp;
    protected $endTimestamp;
    protected $interval;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($startTimestamp, $endTimestamp = null, $interval)
    {
        $this->startTimestamp = $startTimestamp;
        $this->endTimestamp = $endTimestamp === null ? Carbon::now()->timestamp : $endTimestamp;
        $this->interval = $interval;
        $this->dir = env('APP_ENV') === 'testing' ? 'test' : '';
        
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Excel::store(new ExportCourseViewsByCourse($this->startTimestamp, $this->endTimestamp, $this->interval), $this->dir ? $this->dir . '/' . 'course_views_by_course_' . $this->interval . '.xlsx' : 'course_views_by_course_' . $this->interval . '.xlsx');
        Mail::to('me@me.com')->send(new CourseViewsByCourse($this->interval));
        Excel::store(new ExportCompletionsByBadge($this->startTimestamp, $this->endTimestamp, $this->interval), $this->dir ? $this->dir . '/' . 'completions_by_badge_' . $this->interval . '.xlsx' : 'completions_by_badge_' . $this->interval . '.xlsx');
        Mail::to('me@me.com')->send(new CompletionsByBadge($this->interval));
    }
}
