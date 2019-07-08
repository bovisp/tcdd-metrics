<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TrainingMetricsReports extends Mailable
{
    use Queueable, SerializesModels;

    protected $interval;
    protected $reportNames;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($interval, $reportNames)
    {
        $this->interval = $interval;
        $this->reportNames = $reportNames;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = $this->view('emails.trainingmetricsreports');
        foreach($this->reportNames as $reportName) {
            $fileName = str_replace(' ', '_', $reportName);
            $email->attachFromStorage(env("APP_ENV") === "testing" ? "test\\" . $fileName . "_" . $this->interval . '.xlsx' : $fileName . "_" . $this->interval . ".xlsx");
        }
        return $email;
    }
}
