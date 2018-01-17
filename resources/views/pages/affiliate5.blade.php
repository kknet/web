@extends('layouts.dashboard', ['navBarOnly' => true, 'bodyClass' => 'login-page get-tokens logged-in', 'hideFooter' => true])

@section('content')

    <div class="container-fluid">
        @include('partials.referral-menu')
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 pt-3 ">

            <div class="main container-main">
                <div class="container">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="referral-description">
                                <h3>Referral program - 200 000 BDG token pool!</h3>
                                <p>The referral program ended along with the crowdsale, on December 28th or when the hard cap was reached. <strong>Each person who signed-up with your unique referral link by that time  earned you one stake.</strong></p>
                                <p>In order to receive your tokens, please go to <a href="{{ route('wallet') }}">Change My Wallet</a> section in the <strong>Referral Program</strong> tab and add the wallet address to which you wish to receive your BDG tokens. Please do that by <strong>February 1st</strong>, otherwise we won’t be able to transfer you your tokens and they will return to BitDegree scholarship pool which means you will <strong>no longer be eligible to claim them</strong>.</p>
                                <p><strong>What is a stake and how the rewards will be calculated?</strong></p>
                                <p>The pool for the referral program is 200 000 tokens. Each person who signs up on the BitDegree page with your unique referral code gives you one stake.  Let’s say that when the referral program ended, you’ve referred 10 unique visitors. And other participants referred 990 more. So in total we have 1 000 referrals, so 1 000 stakes.</p>
                                <p>As the pool is of 480 000 tokens, 1 stake = 480 000 / 1 000 = 480 tokens.</p>
                                <p>You referred 10 people, so you would receive 480x10=4800 tokens in total.</p>
                                <p><strong>Tokens will be distributed by February 15th.</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
