<?php

namespace App\Console;


use Illuminate\Console\Scheduling\Schedule;
use App\Http\Controllers\SparepartController;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Sparepart; // Gunakan model tunggal Sparepart

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call([new SparepartController, 'checkStock'])->daily();
    }
    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
