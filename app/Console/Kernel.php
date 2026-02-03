<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        
        // Run nearby hotels calculation monthly on the 1st day at 2:00 AM
        // DISABLED: Uncomment below to re-enable
        // $schedule->command('hotels:calculate-nearby --limit=0')
        //          ->monthly()
        //          ->at('02:00')
        //          ->withoutOverlapping()
        //          ->runInBackground();
        
        // Fetch hotel images from HotelLook API
        // 3 parallel cronjobs running simultaneously on server
        // Local server: Processing first 10,000 locations (batch 1)
        // Server: Processing remaining 70,000 locations (3 cronjobs × ~23,333 each)
        
        // Cronjob 1: Locations 10,000-30,000 (runs at 1:00 AM)
        $schedule->command('hotels:fetch-images --offset=10000 --limit=20000')
                 ->dailyAt('01:00')
                 ->withoutOverlapping()
                 ->runInBackground();
        
        // Cronjob 2: Locations 30,000-50,000 (runs at 1:30 AM - staggered start)
        $schedule->command('hotels:fetch-images --offset=30000 --limit=20000')
                 ->dailyAt('01:30')
                 ->withoutOverlapping()
                 ->runInBackground();
        
        // Cronjob 3: Locations 50,000-70,000 (runs at 2:00 AM - staggered start)
        $schedule->command('hotels:fetch-images --offset=50000 --limit=20000')
                 ->dailyAt('02:00')
                 ->withoutOverlapping()
                 ->runInBackground();
        
        // Note: First 10,000 locations handled by local server (batch 1)
        // Already processed locations will be automatically skipped
        // Each cronjob runs daily and resumes from where it stopped if interrupted
        
        // Email notification system - Generate notifications based on user search history
        $schedule->command('email:generate-notifications')
                 ->dailyAt('09:00')
                 ->withoutOverlapping();
        
        // Send scheduled email notifications (runs every 2 hours)
        $schedule->command('email:send-scheduled --limit=100')
                 ->everyTwoHours()
                 ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
