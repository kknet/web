<?php

namespace App\Console;

use Carbon\Carbon;
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
        \App\Console\Commands\ExportTranslationsToCsv::class,
        \App\Console\Commands\ImportTranslationsFromCsv::class,
        \App\Console\Commands\UpdateIcoProgress::class,
        \App\Console\Commands\UpdateIcoTxns::class,
        \App\Console\Commands\ExportContributorsForCountry::class,
        \App\Console\Commands\ExportUsersWithoutKYC::class,
        \App\Console\Commands\GenerateRefundEntries::class,
        \App\Console\Commands\RetrieveTokenAndContributionAmounts::class,
        \App\Console\Commands\SetCountryIps::class,
        \App\Console\Commands\CheckAffiliateCommissions::class,
        \App\Console\Commands\EuropeanUnionContributionReport::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
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
