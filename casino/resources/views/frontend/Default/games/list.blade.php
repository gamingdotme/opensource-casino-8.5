@extends('frontend.Default.layouts.app')

@section('page-title', $title)
@section('body', $body)
@section('keywords', $keywords)
@section('description', $description)

@section('content')
<style type="text/css">
        @charset "UTF-8";
        [ng\:cloak],
        [ng-cloak],
        [data-ng-cloak],
        [x-ng-cloak],
        .ng-cloak,
        .x-ng-cloak,
        .ng-hide:not(.ng-hide-animate) {
                display: none !important;
        }

        ng\:form {
                display: block;
        }

        .ng-animate-shim {
                visibility: hidden;
        }

        .ng-anchor {
                position: absolute;
        }
</style>
	@php
        if(Auth::check() && auth()->user()->email == 'demo01@gmail.com'){
            \Auth::logout();
            echo "<script>location.reload()</script>";
        }
        if(Auth::check()){
            $currency = auth()->user()->present()->shop ? auth()->user()->present()->shop->currency : '';
        } else{
            $currency = '';
        }
	@endphp
    <!-- BLOCK WITH GAMES -->
    <main class="carcass__body">
        <div class="main-content">
            <div class="contain">

                <div class="ng-scope">
                    <div class="ng-scope">
                        <div class="mobile-slider ng-scope ng-isolate-scope" template="mobile-slider" category="mobile-slider">
                            <div class="carousel-fade carousel ng-scope ng-isolate-scope" >
                                <ol class="carousel-indicators">
                                    <!-- ngRepeat: slide in slides track by $index -->
                                    <li class="ng-scope active"></li>
                                    <!-- end ngRepeat: slide in slides track by $index -->
                                    <li class="ng-scope"></li>
                                    <!-- end ngRepeat: slide in slides track by $index -->
                                    <li class="ng-scope"></li>
                                    <!-- end ngRepeat: slide in slides track by $index -->
                                    <li class="ng-scope"></li>
                                    <!-- end ngRepeat: slide in slides track by $index -->
                                    <li class="ng-scope"></li>
                                    <!-- end ngRepeat: slide in slides track by $index -->
                                </ol>
                                <div class="carousel-inner">
                                    <!-- ngRepeat: slide in slides -->
                                    <div class="item text-center ng-scope ng-isolate-scope active">
                                        <div class="mobile-slider__img ng-scope" style="background-image: url(/woocasino/images/bg/472/original/WooCasino-mob-slider-WolfGold-ENG-768x640-min.jpg)"></div>
                                    </div>
                                    <!-- end ngRepeat: slide in slides -->
                                    <div class="item text-center ng-scope ng-isolate-scope">
                                        <div class="mobile-slider__img ng-scope" style="background-image: url(/woocasino/images/bg/499/original/4---Playamo-768%d1%85640-vip-program(ENG)(1).jpg)"></div>
                                    </div>
                                    <!-- end ngRepeat: slide in slides -->
                                    <div class="item text-center ng-scope ng-isolate-scope">
                                        <div class="mobile-slider__img ng-scope" style="background-image: url(/woocasino/images/bg/435/original/2-Live768-640eng.jpg)"></div>
                                    </div>
                                    <!-- end ngRepeat: slide in slides -->
                                    <div class="item text-center ng-scope ng-isolate-scope">
                                        <div class="mobile-slider__img ng-scope" style="background-image: url(/woocasino/images/bg/436/original/3-Mission768-640.jpg)"></div>
                                    </div>
                                    <!-- end ngRepeat: slide in slides -->
                                    <div class="item text-center ng-scope ng-isolate-scope">
                                        <div class="mobile-slider__img ng-scope" style="background-image: url(/woocasino/images/bg/541/original/woo_drops_770x640-min.jpg)"></div>
                                    </div>
                                    <!-- end ngRepeat: slide in slides -->
                                </div>
                                <a class="left carousel-control">
                                    <span class="glyphicon glyphicon-chevron-left"></span>
                                </a>
                                <a class="right carousel-control">
                                    <span class="glyphicon glyphicon-chevron-right"></span>
                                </a>
                            </div>
                            <!-- end ngIf: slides.length -->
                            <div class="mobile-slider__content">
                                <div class="ng-binding ng-isolate-scope ng-scope">
                                    <div>
                                                                        @if( !isset(auth()->user()->username) )
                                        <div class="ng-isolate-scope">
                                            <!-- ngIf: !$root.data.user.email -->
                                             <button class="modal-btn button button-primary header-auth__reg-btn ng-scope" data-name="modal-register" ng-click="openModal($event, '#registration-confirm')">@lang('app.register')</button>
                                                                                        @else
                                                                                        <div><button class="statuses-panel_btn button button-primary ng-scope" ng-click="openModal($event, '#my-account')">@lang('app.depositb')</button></div>
                                                                                @endif
                                            <!-- end ngIf: !$root.data.user.email -->
                                            <!-- ngIf: $root.data.user.email -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end ngIf: ['home'].includes(state.current.page_name) -->
                        <div class="main-content__first-screen">
                            <div class="main-content__slider main-slider ng-isolate-scope" category="main-slider">
                                <!-- ngIf: slides.length -->
                                <div class="carousel-fade carousel ng-scope ng-isolate-scope">
                                    <ol class="carousel-indicators">
                                        <!-- ngRepeat: slide in slides track by $index -->
                                        <li class="ng-scope active"></li>
                                        <!-- end ngRepeat: slide in slides track by $index -->
                                        <li class="ng-scope"></li>
                                        <!-- end ngRepeat: slide in slides track by $index -->
                                        <li class="ng-scope"></li>
                                        <!-- end ngRepeat: slide in slides track by $index -->
                                        <li class="ng-scope"></li>
                                        <!-- end ngRepeat: slide in slides track by $index -->
                                        <li class="ng-scope"></li>
                                        <!-- end ngRepeat: slide in slides track by $index -->
                                    </ol>
                                    <div class="carousel-inner">
                                        <!-- ngRepeat: slide in slides -->
                                        <div class="item text-center ng-scope ng-isolate-scope active">
                                            <div class="main-slider__img ng-scope" style="background-image: url(/woocasino/images/bg/486/original/WooCasino-slider-WolfGold-1310x380-min.jpg)"></div>
                                        </div>
                                        <!-- end ngRepeat: slide in slides -->
                                        <div class="item text-center ng-scope ng-isolate-scope">
                                            <div class="main-slider__img ng-scope" style="background-image: url(/woocasino/images/bg/350/original/vip1310.jpg)"></div>
                                        </div>
                                        <!-- end ngRepeat: slide in slides -->
                                        <div class="item text-center ng-scope ng-isolate-scope">
                                            <div class="main-slider__img ng-scope" style="background-image: url(/woocasino/images/bg/352/original/livedeal1310.jpg)"></div>
                                        </div>
                                        <!-- end ngRepeat: slide in slides -->
                                        <div class="item text-center ng-scope ng-isolate-scope">
                                            <div class="main-slider__img ng-scope" style="background-image: url(/woocasino/images/bg/351/original/mission1310.jpg)"></div>
                                        </div>
                                        <!-- end ngRepeat: slide in slides -->
                                        <div class="item text-center ng-scope ng-isolate-scope">
                                            <div class="main-slider__img ng-scope" style="background-image: url(/woocasino/images/bg/540/original/1310x380-min.jpg)"></div>
                                        </div>
                                        <!-- end ngRepeat: slide in slides -->
                                        <div type="main-slider" class="ng-binding ng-scope ng-isolate-scope">
                                            <div class="main-slider__promo active">
                                                <a class="main-slider__link" href=""></a>
                                                <div class="main-slider__promo-wrp">
                                                    <p class="main-slider__promo-txt">@lang('app.welcome_package')
                                                        <br> <span class="main-slider__promo-separator">
                                                            $/€ <span class="text-color-yellow"> 200&nbsp;</span> </span>
                                                        <br> <span class="main-slider__promo-bg"><span class="text-color-blue">+ 200</span> @lang('app.free_spins')</span>
                                                    </p>
                                                    <div class="main-slider__btn-wrp ng-isolate-scope">
                                                        <!-- ngIf: !$root.data.user.email -->
														
                                                        <button class="button button-secondary"> @lang('app.play_now') </button>
                                                        <!-- end ngIf: !$root.data.user.email -->
                                                        <!-- ngIf: $root.data.user.email -->
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="main-slider__promo">
                                                <a class="main-slider__link" href=""></a>
                                                <div class="main-slider__promo-wrp">
                                                    <p class="main-slider__promo-txt">@lang('app.daily') @lang('app.slot_race')
                                                        <br> <span class="main-slider__promo-separator">
                                                            €/$ &nbsp; <span class="text-color-yellow"> 800 &nbsp;</span></span>
                                                        <br> <span class="main-slider__promo-bg">
                                                            <span class="text-color-blue">+ 800 </span> @lang('app.free_spin_every_day')! </span>
                                                    </p>
                                                    <div class="main-slider__btn-wrp ng-isolate-scope">
                                                        <!-- ngIf: !$root.data.user.email -->
														
                                                        <button class="button button-secondary"> @lang('app.play_now') </button>
                                                        <!-- end ngIf: !$root.data.user.email -->
                                                        <!-- ngIf: $root.data.user.email -->
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="main-slider__promo" href=""></a>
                                                <div class="main-slider__promo-wrp">
                                                    <p class="main-slider__promo-txt">@lang('app.live_casinos')
                                                        <br> <span class="main-slider__promo-separator">
                                                            <span class="text-color-yellow">@lang('app.at_your_fingertips')</span> </span>
                                                    </p>
                                                    <div class="main-slider__btn-wrp ng-isolate-scope">
                                                        <!-- ngIf: !$root.data.user.email -->
														
                                                        <button class="button button-secondary"> @lang('app.play_now') </button>
                                                        <!-- end ngIf: !$root.data.user.email -->
                                                        <!-- ngIf: $root.data.user.email -->
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="main-slider__promo">
                                                <a class="main-slider__link" href=""></a>
                                                <div class="main-slider__promo-wrp">
                                                    <p class="main-slider__promo-txt">@lang('app.complete_all_missions')
                                                        <br> <span class="main-slider__promo-separator"><span class="text-color-yellow">@lang('app.and') @lang('app.get_more_than') <br>$/€150,000 in @lang('app.rewards')</span> </span>
                                                    </p>
                                                    <div class="main-slider__btn-wrp ng-isolate-scope">
                                                        <!-- ngIf: !$root.data.user.email -->
														
                                                        <button class="button button-secondary"> @lang('app.play_now') </button>
                                                        <!-- end ngIf: !$root.data.user.email -->
                                                        <!-- ngIf: $root.data.user.email -->
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="main-slider__promo">
                                                <a class="main-slider__link" href=""></a>
                                            </div>
                                            <div class="main-slider__promo">
                                                <a class="main-slider__link" href=""></a>
                                            </div>
                                        </div>
                                    </div>
                                    <a class="left carousel-control">
                                        <span class="glyphicon glyphicon-chevron-left"></span>
                                    </a>
                                    <a class="right carousel-control">
                                        <span class="glyphicon glyphicon-chevron-right"></span>
                                    </a>
                                </div>
                                <!-- end ngIf: slides.length -->
                            </div>
                            <div class="main-content__latest-winners last-winners ng-isolate-scope">
                                <h3 class="last-winners__title ng-binding">@lang('app.last_winner')</h3>
                                <ul class="last-winners__list ng-scope">
                                    @if(count($games) > 0)
                                        @for ($i = 0;$i < 5;$i++)
                                        @php
                                        $g = $games[(int)(rand(0, count($games)-1))];
                                        $p = ['Samuel J','Robert R ','Macy L','Jimmy TM','The Ace','Lenny L','Killer Player','Margerie M','Stiff T','Pinnacle Gamer','Jeanette P','Gordon R', 'Mary E', 'Daniel R', 'Sumit A'];
                                        @endphp
                                        <li class="last-winners__item ng-scope">
                                            <button class="last-winners__img-block">
                                                <img class="last-winners__img" src="/frontend/Default/ico/{{ $g->name }}.jpg">
                                               
                                            </button>
                                            <div class="last-winners__info">
                                                <div class="last-winners__info-wrp">
                                                    <p class="last-winners__name  ng-binding">{{$p[rand(0, 14)]}}  @lang('app.just_won')</p>
                                                    <p class="last-winners__game-name"> <span class="last-winners__game-in ng-binding">in</span>
                                                        <button class="last-winners__game-link">{{ $g->title }}</button>
                                                    </p>
                                                </div>
                                                <p class="last-winners__sum ng-binding">€ {{number_format(rand(5, 3000)/rand(1,10), 2)}}</p>
                                            </div>
                                        </li>
                                        @endfor
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="category-panel">
                        <nav class="category-panel__menu games-menu ng-isolate-scope" name="games_menu">
                            <ul class="games-menu__list">
                                <li class="games-menu__item games-menu__item--bitcoin">
                                    <a class="games-menu__link games-menu__link--bitcoin" href="{{ route('frontend.game.list.category', 'all') }}"> <i class="games-menu-icon games-menu-icon--bitcoin"></i> <span class="games-menu__title ng-scope">@lang('app.all')</span> </a>
                                </li>

                                <li class="games-menu__item games-menu__item--woo_choice">
                                    <a class="games-menu__link games-menu__link--woo_choice" href="{{ route('frontend.game.list.category', 'hot') }}"> <i class="games-menu-icon games-menu-icon--woo_choice"></i> <span class="games-menu__title ng-scope" >@lang('app.hot_game')</span> </a>
                                </li>
                                <!-- end ngRepeat: filter_collection in gamesData.data.collections | limitTo: 9 -->
                                <li class="games-menu__item games-menu__item--new-games">
                                    <a class="games-menu__link games-menu__link--new-games" href="{{ route('frontend.game.list.category', 'new') }}"> <i class="games-menu-icon games-menu-icon--new-games"></i> <span class="games-menu__title ng-scope" >@lang('app.new')</span> </a>
                                </li>
                                <!-- end ngRepeat: filter_collection in gamesData.data.collections | limitTo: 9 -->
                                <li class="games-menu__item games-menu__item--slots">
                                    <a class="games-menu__link games-menu__link--slots" href="{{ route('frontend.game.list.category', 'slots') }}"> <i class="games-menu-icon games-menu-icon--slots"></i> <span class="games-menu__title ng-scope" >@lang('app.slots')</span> </a>
                                </li>
                                <!-- end ngRepeat: filter_collection in gamesData.data.collections | limitTo: 9 -->
                                <li class="games-menu__item games-menu__item--bonus_buy_slots">
                                    <a class="games-menu__link games-menu__link--bonus_buy_slots" href="{{ route('frontend.game.list.category', 'jackpot') }}"> <i class="games-menu-icon games-menu-icon--bonus_buy_slots"></i> <span class="games-menu__title ng-scope" >Jackpot</span> </a>
                                </li>
                                <li class="games-menu__item games-menu__item--roulette-games">
                                    <a class="games-menu__link games-menu__link--roulette-games" href="{{ route('frontend.game.list.category', 'roulette') }}"> <i class="games-menu-icon games-menu-icon--roulette-games"></i> <span class="games-menu__title ng-scope" >Roulette</span> </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                    <div class="providers ng-isolate-scope">
                        <div class="providers__panel">
                            <a class="providers__btn-all ng-scope" href="{{ route('frontend.game.list.category', 'all') }}">@lang('app.all')</a>
                            <ul class="providers__panel-list">

                            @php
                            $top_categories = ['netent', 'playtech', 'pragmatic', 'wazdan', 'igtech', 'evolution', 'amatic', 'isoftbet'];
                            @endphp
                            @foreach ($top_categories as $k=>$v)
                                <li class="providers__item ng-scope">
                                    <a class="providers__link" scroll-up="" href="{{ route('frontend.game.list.category', $v) }}">
                                        <span class="providers__icon">
                                            <img class="providers__icon-img providers__icon-img--{{$v}}" alt="{{$v}}" src="/frontend/Default/svg/{{$v}}.svg">
                                        </span>
                                        <span class="providers__name ng-scope">{{ lcfirst($v) }}</span> </a>
                                </li>
                            @endforeach
                            </ul>
                            <button class="providers__toggler">
                                <span class="providers__toggler-text ng-scope">@lang('app.all_providers')</span>
                                <span class="ng-scope ng-hide">@lang('app.close')</span>
                            </button>
                        </div>
                        <ul class="providers__dropdown ng-hide">
                            @if ($categories && count($categories))
                                @foreach($categories AS $index=>$category)
                                @if (!in_array($category->href, $top_categories))
                                <li class="providers__dropdown-item ng-scope">
                                    <a class="providers__link" href="{{ route('frontend.game.list.category', $category->href) }}">
                                        <span class="providers__icon">
                                            <img class="providers__icon-img providers__icon-img--1x2gaming" alt="" src="/frontend/Default/svg/{{$category->href}}.svg">
                                        </span>
                                        <span class="providers__name ng-scope">{{ $category->title }}</span> </a>
                                </li>
                                @endif
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- uiView: -->
        <div class="ng-scope">
            <!-- uiView: -->
            <div class="contain ng-scope">
                <section class="games-list ng-isolate-scope">
                    <div class="games-list__title-wrp">
                        <h1 class="games-list__title ng-scope"></h1>
                    </div>
                    <div class="games-list__wrap ng-scope">
                        @if ($games && count($games))
                            @foreach ($games as $key=>$game)
                            <div class="game-item ng-scope">
                                <div class="game-item game-item--overflow ng-scope">
                                    <div class="game-item__block">
                                        <img class="game-item__img" src="{{ $game->name ? '/frontend/Default/ico/' . $game->name . '.jpg' : '' }}" casino-lazy-src="{{ $game->name ? '/frontend/Default/ico/' . $game->name . '.jpg' : '' }}" alt="{{ $game->title }}" loading="true" style="opacity: 1;"> 
                                       
                                        <!-- ngIf: game | gameJackpotByCurrency : $root.data.user.currency : 'EUR' -->
                                    </div>
                                    <div class="game-item__labels">
                                        @if($game->label)
                                        <div class="game-item__label game-item__label--hot ng-binding ng-scope">{{ mb_strtoupper($game->label) }}</div>
                                        @endif
                                        <div class="game-item__label game-item__label--bitcoin ng-scope"></div>
                                    </div>
                                    <div class="game-item__label-live ng-scope"> <span class="game-item__label-live-txt">Active</span> </div>
                                    <div class="game-item__overlay ng-scope">
                                        <div class="game-item__actions">
                                            @if( isset(auth()->user()->username) )
                                                <a href="{{ route('frontend.game.go', $game->name) }}?api_exit=/" class="button button-primary ng-scope ng-binding">@lang('app.play_now')</a>
                                            @else
                                                <a href="{{ route('frontend.game.go', $game->name) }}/prego?api_exit=/" class="button button-primary ng-scope ng-binding">Demo</a>
											<br>
                                                <a href="javascript:;" class="button button-primary ng-scope ng-binding" ng-click="openModal($event, '#login-modal')">@lang('app.login')</a>
                                            @endif
                                            <!-- <button class="button button-primary ng-scope ng-binding">@lang('play_now')</button> -->
                                        </div>
                                    </div>
                                    <div class="game-item__panel">
                                        <p class="game-item__panel-provider ng-binding">{{ isset($cat1->title) ? $cat1->title : lcfirst($category1) }}</p>
                                        <p class="game-item__panel-title ng-binding">{{ $game->title }}</p>
                                        <!-- ngIf: $root.data.user.email && $root.data.device === 'mobile' -->
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @endif
                    </div>
                </section>
                <div type="advantages" class="ng-binding ng-isolate-scope ng-scope">
                    <div class="advantages">
                        <div class="advantages__list">
                            <div class="advantages__item"> <i class="advantages__icon icon-woo-money"></i>
                                <p class="advantages__title">@lang('app.adtitle_item1') </p>
                                <p class="advantages__descr">@lang('app.adbody_item1')</p>
                            </div>
                            <div class="advantages__item"> <i class="advantages__icon icon-woo-transaction"></i>
                                <p class="advantages__title">@lang('app.adtitle_item2')</p>
                                <p class="advantages__descr">@lang('app.adbody_item2') </p>
                            </div>
                            <div class="advantages__item"> <i class="advantages__icon icon-woo-lightning"></i>
                                <p class="advantages__title">@lang('app.adtitle_item3') </p>
                                <p class="advantages__descr">@lang('app.adbody_item3') </p>
                            </div>
                            <div class="advantages__item"> <i class="advantages__icon icon-woo-security"></i>
                                <p class="advantages__title">@lang('app.adtitle_item4') </p>
                                <p class="advantages__descr">@lang('app.adbody_item4')  </p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ngIf: $root.data.current_ip.country_code !== 'SE' && $root.data.user.country !== 'SE' -->
                <div type="promo-block" class="ng-binding ng-scope ng-isolate-scope">
                    <div class="promo-block">
                        <h2 class="promo-block__ttl">@lang('app.best_casino_title')</h2>
                        <div class="promo-block__video-block">
                            <div class="promo-block__video-descr">
                                <p>@lang('app.best_casino1') </p>
                                <p> @lang('app.best_casino2') </p>
                                <p>@lang('app.best_casino3') </p>
                            </div>
                        </div>
                        <h2 class="promo-block__ttl">@lang('app.mobile_online_title')</h2>
                        <div>
                           <p>@lang('app.mobile_online1')</P>
						   <P>@lang('app.mobile_online2')</p>
						   <P>@lang('app.mobile_online3')</p>
                        </div>
                        <div class="toggle-block">
                            <div class="toggle-block__item">
                                <h3 class="toggle-block_header collapsed" data-toggle="collapse" data-target="#toggle-block-1">
                                    @lang('app.whatis_best_title')
                                </h3>
                                <div id="toggle-block-1" class="toggle-block__body collapse in">
                                    <div class="toggle-block__body-wrp">
									<p>@lang('app.whatis_best1')</p>
									<p>@lang('app.whatis_best2')</p>
									<p>@lang('app.whatis_best3')</p>
                                        </div>
                                </div>
                            </div>
                            <div class="toggle-block__item">
                                <h3 class="toggle-block_header collapsed" data-toggle="collapse" data-target="#toggle-block-2">
                                    @lang('app.howto_play_casino_title')
                                </h3>
                                <div id="toggle-block-2" class="toggle-block__body collapse in">
                                    <div class="toggle-block__body-wrp">
                                        <p>@lang('app.howto_play_casino1') </p>
										<p>@lang('app.howto_play_casino2') </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script>
    var timerHdle = null;
    function call_timer() {
        return setInterval(() => {
            $('.carousel-inner').each(function(){
                if($(this).find('.item.active').index() < 4) {
                    $(this).find('.item.active').removeClass('active').next().addClass('active');
                } else {
                    $(this).find('.item.active').removeClass('active');
                    $(this).find('.item').eq(0).addClass('active');
                }
                if($(this).find('.main-slider__promo.active').index() < 4) {
                    $(this).find('.main-slider__promo.active').removeClass('active').next().addClass('active');
                } else {
                    $(this).find('.main-slider__promo.active').removeClass('active');
                    $(this).find('.main-slider__promo').eq(0).addClass('active');
                }
            })
            $('.carousel-indicators').each(function(){
                if($(this).find('li.active').index() < 4) {
                    $(this).find('li.active').removeClass('active').next().addClass('active');
                } else {
                    $(this).find('li.active').removeClass('active');
                    $(this).find('li').eq(0).addClass('active');
                }
            })
        }, 5000);
    }
    timerHdle = call_timer()
    $('.carousel-indicators').find('li').click(function(){
        clearInterval(timerHdle)
        var index = $(this).index()
        $(this).parent().find('.active').removeClass('active')
        $(this).parent().find('li').eq(index).addClass('active');

        $(this).parent().parent().find('.carousel-inner .item.active').removeClass('active')
        $(this).parent().parent().find('.carousel-inner .item').eq(index).addClass('active')

        $(this).parent().parent().find('.carousel-inner .main-slider__promo.active').removeClass('active')
        $(this).parent().parent().find('.carousel-inner .main-slider__promo').eq(index).addClass('active')
        timerHdle = call_timer()
    })
    $('.providers__toggler').click(function(){
        $('.providers__dropdown').toggleClass('ng-hide');
    })
    </script>
@stop

@section('scripts')
@stop

