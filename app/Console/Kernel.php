<?php

namespace App\Console;

use Carbon\Carbon;
use App\Jobs\GenerateReportJob;
use App\Jobs\GenerateCatalogJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // cron expressions:
        //      minute(0 - 59) | hour(0 - 23) | day(1 - 31) | month(1 - 12) | weekday(0 - 6) | year(optional)
        //      e.g. cron expression for quarterly (Jan, Apr, Jul, Oct): '0 0 1 1,4,7,10 *'

        // quarterly
        $subQuarterDateTime = Carbon::now()->subQuarter();
        $schedule->job(new GenerateReportJob($subQuarterDateTime))->cron('1 6 1 1,4,7,10 *');
        
        // annually
        $subYearDateTime = Carbon::now()->subYear();
        $schedule->job(new GenerateReportJob($subYearDateTime))->cron('0 6 1 4 *');

        // test
        // $schedule->job(new GenerateReportJob($subYearDateTime))->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}