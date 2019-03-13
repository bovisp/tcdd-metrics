<?php

namespace App\Console;

use Carbon\Carbon;
use App\Jobs\GenerateReport;
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
        // $schedule->command('inspire')
        //          ->hourly();

        // cron expressions:
        //      minute(0 - 59) | hour(0 - 23) | day(1 - 31) | month(1 - 12) | weekday(0 - 6) | year(optional)
        //      e.g. cron expression for quarterly (Jan, Apr, Jul, Oct): '0 0 1 1,4,7,10 *'

        //quarterly
        //$schedule->job(new GenerateReport)->cron('0 0 1 1,4,7,10 *');
        $endDateTime = Carbon::now();
        $startDateTime = Carbon::now()->subQuarter();
        $schedule->job(new GenerateReport($startDateTime, $endDateTime))->everyMinute();

        //$startDateTime = $endDateTime->subYear();
        //annually
        $schedule->job(new GenerateReport($startDateTime, $endDateTime))->cron('0 0 1 1 *');
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
