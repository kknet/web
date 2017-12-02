@extends('layouts.dashboard', ['navBarOnly' => true, 'bodyClass' => 'login-page get-tokens logged-in', 'hideFooter' => true])

@section('content')

    <div class="container-fluid">
        <div class="col-sm-3 col-md-2 hidden-xs-down sidebar">
            <h4>@lang('ico.whats-next')</h4>
            <ul class="sidebar-nav">
                <li class="step"><a href="">@lang('ico.step', ['number' => 1])</a></li>
                <li class="step-other"><a href="#"><span>@lang('ico.step', ['number' => 2])</span></a></li>
                <li class="step-other"><a href="#"><span>@lang('ico.other')</span></a></li>
            </ul>
        </div>

        <div class="col-sm-12  col-md-10 col-md-offset-2 pt-3 ">

            <div class="main container-main">
                <div class="container">
                    <div class="row">
                        <h1 class="text-center">@lang('user.eth-wallet')</h1>
                    </div>

                    <form action="{{ route('wallet') }}" method="post">
                        <div class="row">
                            <div class="col-xs-12 col-md-10 col-md-offset-1 personal-details">
                                <div class="row">
                                    <div class="col-md-12">
                                        @include('partials.status')
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        Please provide your personal ETH-address from an wallet that supports ERC20-Tokens (For example MyEtherWallet). This is the address you will be sending ETH from. If you have no ETH-address, check <a href="{{ route('participate') }}">this tutorial</a> or <a href="https://t.me/bitdegree" target="_blank">contact our Support</a>.
                                        <div class="form-group">
                                            <label for="input-wallet">@lang('user.eth-wallet')</label>
                                            <input tabindex="5" type="text" class="form-control" value="{{ old('wallet') }}" placeholder="0x" name="wallet" id="input-wallet">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-md-10 col-md-offset-1 buttons">
                                <div class="content dashboard-buttons">
                                    <div class="right text-right">
                                        <button tabindex="8" type="submit" class="btn btn-primary button-save">@lang('user.save')</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {!! csrf_field() !!}
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
