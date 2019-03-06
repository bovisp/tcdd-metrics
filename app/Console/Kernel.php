<?php

namespace App\Console;

use Carbon\Carbon;
use App\Jobs\SendEmail;
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
        $end_timestamp = Carbon::now()->timestamp;
        $start_timestamp = Carbon::now()->subMonths(3)->timestamp;
        //dd(Carbon::now()->subMonths(3)->timestamp);

        // $schedule->command('inspire')
        //          ->hourly();

        //call job
        // minute(0 - 59) hour(0 - 23) day(1 - 31) month(1 - 12) weekday(0 - 6) year(optional)
        // cron expression for Jan, Apr, Jul, Oct - 0 0 1 1,4,7,10 *
        //need to pass timespan to job
        //$schedule->job(new SendEmail)->cron('* 14 4 3 *');
        $schedule->job(new SendEmail)->everyMinute();
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
