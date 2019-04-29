<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CourseViewsByCourse extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $interval;

    public function __construct($interval)
    {
        $this->interval = $interval;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.courseviewsbycourse')
            ->attachFromStorage(env('APP_ENV') === 'testing' ? 'test\course_views_' . $this->interval . '.xlsx' : 'course_views_' . $this->interval . '.xlsx');
    }
}
