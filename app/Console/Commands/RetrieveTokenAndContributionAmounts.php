<?php

namespace App\Console\Commands;

use App\Refund;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RetrieveTokenAndContributionAmounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ico:retrieve-amounts {amount}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $balanceAbi = '0x70a08231000000000000000000000000';
        Refund::onlyUnprocessed()->limit($this->argument('amount'))->get()->each(function (Refund $refund) use ($balanceAbi) {
            $this->line('Processing ' . $refund->wallet);
            $response = static::callEth(env('ICO_ADDRESS'), $balanceAbi . substr($refund->wallet, 2));
            $weiSent = preciseHexDec($response->result);

            if (bccomp($weiSent, 0) === 1) {
                $response = static::callEth(env('TOKEN_ADDRESS'), $balanceAbi . substr($refund->wallet, 2));
                $tokensReceived = preciseHexDec($response->result);

                $refund->tokens = bcdiv($tokensReceived, bcpow(10, 18), 18);
                $refund->ether = bcdiv($weiSent, bcpow(10, 18), 18);
                $refund->amounts_updated_at = Carbon::now();

                $this->line('Updated: ' . $refund->tokens . ' BDG : ' . $refund->ether . ' ETH');

                $refund->save();
            } else {
                $this->line('Rejecting refund #' . $refund->id . ' because contribution is 0.');
                $refund->amounts_updated_at = Carbon::now();
                $refund->save();
            }
        });
    }

/**
 * Makes ETH call from etherscan API
 *
 * @param $address
 * @param $data
 * @param string $tag
 * @return mixed
 * @throws \Exception
 */
public
static function callEth($address, $data, $tag = 'latest')
{
    $client = new Client();

    $response = $client->get('https://api.etherscan.io/api', [
        'query' => [
            'module' => 'proxy',
            'action' => 'eth_call',
            'to' => $address,
            'data' => $data,
            'tag' => $tag,
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
