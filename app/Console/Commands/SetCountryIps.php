<?php

namespace App\Console\Commands;

use App\User;
use GeoIp2\Exception\AddressNotFoundException;
use Illuminate\Console\Command;

class SetCountryIps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:country-ips';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates country IPs';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $users = User::whereNull('country_ip')->get();

        $this->line('Checking ' . $users->count() . ' users');

        $progressBar = $this->output->createProgressBar($users->count());

        $users->each(function (User $u) use ($progressBar) {
            try {
                $result = app()->make('geoip')->country($u->ip);

                if ($result instanceof \GeoIp2\Model\Country) {
                    $u->country_ip = $result->country->isoCode;
                    $u->save();
                }

                $progressBar->advance();
            } catch (AddressNotFoundException $e) {
                // Address was not found
            }
        });

        $progressBar->finish();

        $this->line('Done!');
    }
}
