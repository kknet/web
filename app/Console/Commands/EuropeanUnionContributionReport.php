<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EuropeanUnionContributionReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ico:eu-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily contribution report from the EU';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private $euCountries = [
        'AT',
        'IT',
        'BE',
        'LV',
        'BG',
        'LT',
        'HR',
        'LU',
        'CY',
        'MT',
        'CZ',
        'NL',
        'DK',
        'PL',
        'EE',
        'PT',
        'FI',
        'RO',
        'FR',
        'SK',
        'DE',
        'SI',
        'GR',
        'ES',
        'HU',
        'SE',
        'IE',
        'GB',
    ];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        sort($this->euCountries);

        $countries = DB::table('users')
            ->select(DB::raw('users.wallet, ifnull(country, country_ip) as country'))
            ->whereNotNull('users.wallet')
            ->groupBy('users.wallet')
            ->get()
            ->map(function ($user) {
                $user->wallet = strtolower($user->wallet);
                return $user;
            })
            ->pluck('country', 'wallet')
            ->filter(function ($country) {
                return in_array($country, $this->euCountries);
            });

        $txns = cache()->rememberForever('ico-txns-report', function () {
            return $this->getTransactions();
        })->map(function ($txns, $day) use ($countries) {
            $result = [];
            foreach($this->euCountries as $country) {
                $result[$country] = 0;
            }

            foreach($txns as $tx) {
               if($countries->has($tx->from)) {
                   $result[$countries->get($tx->from)] = bcadd($result[$countries->get($tx->from)], $tx->value);
               }
            }

            return $result;
        });

        foreach($txns as $day => $countries) {
            $this->line($day);

            foreach($countries as $country => $value) {
                $eth = bcdiv($value, bcpow(10, 18), 18);
                $this->line($country." => ".$eth);
            }

            echo PHP_EOL;
        }
    }

    public function getTransactions(){
        $this->line('Retrieving transactions...');
        $transactions = collect([]);
        $lastBlock = 0;

        while(true) {
            $client = new Client();

            $response = $client->get('https://api.etherscan.io/api', [
                'query' => [
                    'module' => 'account',
                    'action' => 'txlist',
                    'address' => env('ICO_ADDRESS'),
                    'startblock' => $lastBlock,
                    'endblock' => '999999999999990',
                    'apikey' => env('ETHERSCAN_API_KEY'),
                    'sort' => 'asc',
                ],
                'timeout' => 60,
                'http_errors' => false,
            ]);

            $content = $response->getBody()->getContents();

            if ($response->getStatusCode() != 200) {
                $this->line('Could not retrieve the transactions: ' . $content);
                Log::error('Could not retrieve the transactions', [
                    'status' => $response->getStatusCode(),
                    'body' => $content,
                ]);
                return;
            }

            $txns = collect(json_decode($content)->result)->keyBy('hash');

            if($txns->count() > 0) {
                $lastBlock = $txns->last()->blockNumber - 1;
                $countBefore = $transactions->count();
                $transactions = $transactions->merge($txns);

                if($countBefore == $transactions->count()) {
                    break;
                }

                $this->line('Retrieved ' . $txns->count(). ' transactions');
            }
        }

        return $transactions->filter(function ($tx) {
            return $tx->isError == '0' && bccomp($tx->value, 0) == 1;
        })->map(function($tx) {
            $tx->from = strtolower($tx->from);
            return $tx;
        })->groupBy(function ($tx) {
            return date('Y-m-d', $tx->timeStamp);
        });
    }
}
