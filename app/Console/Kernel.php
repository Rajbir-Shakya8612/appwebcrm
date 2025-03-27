<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * These cron jobs are run in the background by a process server.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        // Check for meeting reminders every minute
        $schedule->command('meetings:check-reminders')->everyMinute();
        
        // Create monthly plans on the first day of each month at 00:01
        $schedule->command('plans:create-monthly')->monthlyOn(1, '00:01');
        
        // Update plan achievements daily at midnight
        $schedule->command('plans:update-achievements')->dailyAt('00:00');
        
        // Check plan deadlines daily
        $schedule->command('plans:check-deadlines')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');

        $this->commands([
            \App\Console\Commands\CheckMeetingReminders::class,
            \App\Console\Commands\CreateMonthlyPlans::class,
            \App\Console\Commands\UpdatePlanAchievements::class,
            \App\Console\Commands\CheckPlanDeadlines::class,
        ]);
    }

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\CheckMeetingReminders::class,
        Commands\CreateMonthlyPlans::class,
        Commands\UpdatePlanAchievements::class,
        Commands\CheckPlanDeadlines::class,
    ];
} 