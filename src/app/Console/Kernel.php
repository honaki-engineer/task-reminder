<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();

        $debug = config('schedule.debug_minute');

        $delete = $schedule->command('tasks:delete-old-completed')->withoutOverlapping();
        $remind = $schedule->command('send:daily-slack')->withoutOverlapping();
            
        if($debug) {
            // 毎分テスト用
            $delete->everyMinute();
            $remind->everyMinute();
        } else {
            $delete->dailyAt('03:00'); // 毎日夜中の3時に実行
            $remind->dailyAt('04:00'); // 毎日夜中の4時に実行
        }
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
