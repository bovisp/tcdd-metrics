<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use App\Mail\TrainingMetricsReports;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GenerateReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $dir;
    protected $startDateTime;
    protected $endDateTime;
    protected $interval;
    protected $reportIds;
    protected $reportNames;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($startDateTime, $endDateTime = null, $reportIds = null)
    {
        $this->startDateTime = $startDateTime;
        $this->endDateTime = $endDateTime === null ? Carbon::now() : $endDateTime;
        $this->dir = env('APP_ENV') === 'testing' ? 'test' : '';
        $this->interval = $this->startDateTime->toDateString() . "_" . $this->endDateTime->toDateString();
        $this->reportIds = $reportIds === null ?  DB::connection('mysql')->table('report_types')
            ->select('id')->get()
            ->map(function ($reportId){
                return $reportId->id;
            })->toArray()
            : $reportIds;

        $this->reportNames = [];

        foreach($this->reportIds as $reportId) {
            $reportName = DB::connection('mysql')->table('report_types')
                ->select('name')
                ->where('id', '=', $reportId)
                ->get()->first();
            array_push($this->reportNames, $reportName->name);
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //generate spreadsheets and save to disk
        foreach($this->reportNames as $reportName) {
            $export = "App\Exports\Export" . str_replace(' ', '', $reportName);
            $fileName = str_replace(' ', '_', $reportName);
            Excel::store(new $export($this->startDateTime->timestamp, $this->endDateTime->timestamp), 
                $this->dir ? $this->dir . "\\" . $fileName . "_" . $this->interval . ".xlsx" : $fileName . "_" . $this->interval . ".xlsx"
            );
        }

        //email spreadsheets
        Mail::to('paul.bovis@canada.ca')->send(new TrainingMetricsReports($this->interval, $this->reportNames));

        //delete spreadsheets from disk
        foreach($this->reportNames as $reportName) {
            $fileName = str_replace(' ', '_', $reportName);
            @unlink("C:\wamp64\www\\tcdd-metrics\storage\app\\" . $fileName . "_" . $this->interval . ".xlsx");
        }
    }
}
