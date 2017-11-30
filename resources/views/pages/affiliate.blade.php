@extends('layouts.dashboard', ['navBarOnly' => true, 'bodyClass' => 'login-page get-tokens logged-in', 'hideFooter' => true])

@section('content')

    <div class="col-sm-12 col-md-12 pt-3 ">

        <div class="main container-main">
            <div class="container">

                <div class="row">
                    <div class="col-xs-12 col-md-12 text-center">
                        <h1>@lang('affiliate.headline')</h1>
                        <h3 class="subtitle">@lang('affiliate.subtitle')</h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <div class="row">
                            <div class="col-md-6 text-center">
                                <div class="well well-important">
                                    https://stude.co/{{ auth()->user()->id }}
                                </div>
                            </div>
                            <div class="col-md-6 text-center">
                                <div class="well well-important">
                                    @if($referralCount == 0)
                                        @lang('affiliate.1-referral')
                                    @else
                                        @lang('affiliate.n-referrals', ['number' => $referralCount + 1])
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="referral-description">
                            <h3>@lang('affiliate.how')</h3>
                            <ul>
                                <li>@lang('affiliate.how-descr-1')</li>
                                <li>@lang('affiliate.how-descr-2')</li>
                                <li>@lang('affiliate.how-descr-3')</li>
                                <li>@lang('affiliate.how-descr-4')</li>
                                <li>@lang('affiliate.how-descr-5')</li>
                            </ul>
                            <h3>@lang('affiliate.restrictions')</h3>
                            <ul>
                                <li>@lang('affiliate.restriction-1')</li>
                                <li>@lang('affiliate.restriction-2')</li>
                            </ul>
                            <p>@lang('affiliate.breaking')</p>
                        </div>
                    </div>
                 </div>

                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <h1 class="text-center">@lang('affiliate.banners-to-use')</h1>
                        <p class="text-center subtitle">@lang('affiliate.banners-to-use-subtitle')</p>

                        @foreach($banners as $banner)
                            <div class="col-xs-12 col-md-6">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h4>@lang('affiliate.banner') <span class="label label-default pull-left">{{ $banner['width'].'x'.$banner['height'] }}</span></h4>
                                </div>
                                <div class="panel-body">
                                    <div class="banner-image text-center">
                                        <img src="{{ $banner['url'] }}" alt="@lang('affiliate.banner-alt')">
                                    </div>
                                    <textarea readonly onclick="this.setSelectionRange(0, this.value.length)" class="form-control select-all">{{ '<a href="'.route('affiliate-cookie', auth()->id()).'" target="_blank"><img src="'.$banner['url'].'" width="'.$banner['width'].'" height="'.$banner['height'].'" alt="'.__('affiliate.banner-alt').'"></a>'}}</textarea>
                                </div>
                            </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                    <div class="col-md-12 share text-center">
                        <h3>@lang('user.share')</h3>
                        <div class="share-arrow"></div>
                        <div class="share-buttons large">
                            <a class="telegram-share" href="javascript:window.open('https://t.me/share/url?url=https://stude.co/{{ auth()->user()->id }}', '_blank')">



                                <i></i>
                                <span>Telegram</span>
                            </a>
                            <a class="twitter-share" href="http://twitter.com/intent/tweet?url=https://stude.co/{{ auth()->user()->id }}&text=World's%20first%20blockchain-powered%20education%20platform%20with%20scholarships&via=bitdegree_org&hashtags=education%2Cblockchain%2CICO" target="_blank" class="share-btn twitter">
                                <i class="fa fa-twitter"></i> Tweet
                            </a>

                        </div>
                        <!-- AddToAny BEGIN -->
                        <div class="a2a_kit a2a_kit_size_32 a2a_default_style">
                            <a class="a2a_dd" href="https://www.addtoany.com/share"></a>
                            <a class="a2a_button_facebook"></a>
                            <a class="a2a_button_google_plus"></a>
                            <a class="a2a_button_linkedin"></a>
                            <a class="a2a_button_reddit"></a>
                            <a class="a2a_button_tumblr"></a>
                        </div>
                        <script>
                            var a2a_config = a2a_config || {};
                            a2a_config.linkname = "BitDegree";
                            a2a_config.linkurl = "https://stude.co/{{ auth()->user()->id }}";
                            a2a_config.color_main = "D7E5ED";
                            a2a_config.color_border = "AECADB";
                            a2a_config.color_link_text = "333333";
                            a2a_config.color_link_text_hover = "333333";
                        </script>
                        <script async src="https://static.addtoany.com/menu/page.js"></script>
                        <!-- AddToAny END -->
                    </div>
                </div>

                </div>
            </div>
        </div>
    </div>

@endsection
