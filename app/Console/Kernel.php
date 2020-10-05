<?php

namespace App\Console;

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
        // $schedule->command('inspire')->hourly();
         $schedule->command('tse:update-stocks-at-morning')->dailyAt('08:10')->timezone('Asia/Tehran');
         $schedule->command('tse:update-stocks-at-noon')->dailyAt('14:15')->timezone('Asia/Tehran');
         $schedule->command('tse:update-stocks-at-night')->dailyAt('02:00')->timezone('Asia/Tehran');
         $schedule->command('tse:update-stocks-every-minutes')->everyMinute();

         $schedule->command('test:cron')->everyMinute();
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
