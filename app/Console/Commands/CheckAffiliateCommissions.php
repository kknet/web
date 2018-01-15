<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckAffiliateCommissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'affiliate:check-commissions {mode}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks affiliate commissions';

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
        if($this->argument('mode') === 'update') {
            DB::table('users')
                ->join('balances', 'users.wallet', '=', 'balances.wallet')
                ->whereNotNull('users.affiliate_id')
                ->whereNotNull('users.wallet')
                ->get()
                ->each(function ($user) {
                    $this->line('Processing ' . $user->wallet);

                    try {
                        cache()->rememberForever('user-txns-' . $user->id, function () use ($user) {
                            $firstTx = null;
                            $txns = collect(static::retrieveTransactions($user->wallet)->result)->filter(function ($tx) {
                                return strtolower($tx->to) === strtolower(env('ICO_ADDRESS'));
                            })->each(function ($tx) use (&$firstTx) {
                                $ts = (int)$tx->timeStamp;

                                if ($firstTx === null) {
                                    $firstTx = $ts;
                                    return;
                                }

                                if ($firstTx > $ts) {
                                    $firstTx = $ts;
                                }
                            });

                            $this->line('Transactions updated!');

                            return [
                                'firstTx' => $firstTx,
                                'txns' => $txns,
                            ];
                        });
                    } catch (\Exception $e) {
                        $this->error($e->getMessage());
                    }
                });
        }

        if($this->argument('mode') === 'list') {
            $usedWallets = collect([]);

            $sum = 0;

            DB::table('users')
                ->join('balances', 'users.wallet', '=', 'balances.wallet')
                ->whereNotNull('users.affiliate_id')
                ->whereNotNull('users.wallet')
                ->orderBy('wallet_updated_at', 'asc')
                ->get()
                ->each(function ($user) use (&$usedWallets, &$sum) {
                    $wallet = strtolower($user->wallet);
                    $walletUpdated = strtotime($user->wallet_updated_at);
                    $approvedCommission = collect([]);

                    if(!$usedWallets->contains($wallet)) {
                        $key = 'user-txns-' . $user->id;
                        if(cache()->has($key)) {
                            if($walletUpdated < cache()->get($key)['firstTx']) {
                                $commission = bcmul($user->ico_balance, 0.05, 18);
                                $approvedCommission->put($wallet, $commission);
                                $sum = bcadd($sum, $commission, 18);
                                $this->line(implode("\t", [$user->wallet, $user->ico_balance,  $commission]));
                            }
                        } else {
                            $this->error('Missing transaction data for user ' . $user->id);
                        }
                    }
                });

            $this->line('Total: '.$sum);
        }
    }

    /**
     * Retrieves list of transactions for a given address
     *
     * @param $address
     * @return mixed
     * @throws \Exception
     */
    public static function retrieveTransactions($address) {
        $client = new Client();

        $response = $client->get('https://api.etherscan.io/api', [
            'query' => [
                'module' => 'account',
                'action' => 'txlist',
                'address' => $address,
                'startblock' => 4656247,
                'endblock' => 4814052,
                'apikey' => env('ETHERSCAN_API_KEY'),
            ],
            'timeout' => 30,
            'http_errors' => false,
        ]);

        $content = $response->getBody()->getContents();

        if ($response->getStatusCode() != 200) {
            Log::error('ETH call failed', [
                'status' => $response->getStatusCode(),
                'body' => $content,
            ]);
            throw new \Exception('ETH call failed');
        }

        return json_decode($content);
    }
}
