<?php

namespace App\Console;

use Carbon\Carbon;
use App\Jobs\GenerateCourseViews;
use App\Jobs\GenerateCourseCompletions;
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
        $schedule->job(new GenerateCourseViews($subQuarterDateTime))->cron('2 6 1 1,4,7,10 *');
        $schedule->job(new GenerateCourseCompletions($subQuarterDateTime))->cron('3 6 1 1,4,7,10 *');
        
        // annually
        $subYearDateTime = Carbon::now()->subYear();
        $schedule->job(new GenerateCourseViews($subYearDateTime))->cron('0 6 1 1 *');
        $schedule->job(new GenerateCourseCompletions($subYearDateTime))->cron('1 6 1 1 *');

        // test
        $schedule->job(new GenerateCourseViews(Carbon::now()->subCentury()))->everyMinute();
        $schedule->job(new GenerateCourseCompletions(Carbon::now()->subCentury()))->everyMinute();
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
