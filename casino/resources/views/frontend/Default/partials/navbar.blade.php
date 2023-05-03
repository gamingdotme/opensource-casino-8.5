<?php if(isset($_GET['merchant_id'])):?>

<div class="modal" id="restore-password-success" style="display: block;">
    <header class="modal__header">
        <div class="span modal__title"> @lang('app.pay_ok_title')</div>
        <span ng-click="closeModal($event)" class="modal__icon icon icon_cancel js-close-popup"></span>
    </header>
    <div class="modal__content">
        <div id="restore-password-success-text" class="modal-text">@lang('app.pay_ok_desk')</div>
    </div>
    <div class="popup__footer">
        <input type="submit" ng-click="openModal($event, '#my-account')" value="OK <?php echo $_GET['merchant_id']; ?>"
            class="popup__button button" />
    </div>
</div>

<script>
    history.pushState('', '', '/');
</script>
<?php endif;?>

<!-- MENU MOBILE -->
@include('frontend.partials.messages')
<!-- Games -->
 <style>
        /* Marquee  */
.marquee {
    height: 48px;
    overflow: hidden;
    position: relative;
    background: #333;
    color: #333;
    border: 1px solid #4a4a4a;


}
.image {
    /* height: 50px; */
    width: overflow: hidden;
    position: absolute;
    background: #;
    background: -moz-linear-gradient(97deg, #e6c85d 0%, #c39232 100%);
    background: -webkit-gradient(linear, 97deg, color-stop(0%, #e6c85d), color-stop(100%, #c39232));
    background: -webkit-linear-gradient(
97deg
 , #e6c85d 0%, #c39232 100%);
    background: -o-linear-gradient(97deg, #e6c85d 0%, #c39232 100%);
    background: -ms-linear-gradient(97deg, #e6c85d 0%, #c39232%);
    background: linear-gradient(
97deg
 , #e6c85d 0%, #c39232 100%);
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#001c10', endColorstr='#00673c',GradientType=0 );
    z-index: 9;
    font-size: 26px;
    padding: 7px;
    text-transform: uppercase;
}
.marquee p:nth-child {
    color:red;
    transform: translateX(-50%);
    position: absolute;
    width: 100%;
    height: 100%;
    margin: 0;
    line-height: 50px;
    text-align: center;

    animation: scroll-left 25s linear infinite;
}

.marquee p {
    position: absolute;
    width: 100%;
    height: 100%;
    margin: 0;
    line-height: 50px;
    text-align: center;color:#999999; font-family:helvetica;

    animation: scroll-left 20s linear infinite;
}

@keyframes scroll-left {
    0% {
        transform: translateX(55%);
    }
    100% {
        transform: translateX(-55%);
    }
}
    </style>
<section  id="woocasino" class="carcass">
    @if (Auth::check())
        @include('frontend.Default.partials.header_logged')
    @else
        @include('frontend.Default.partials.header_not_logged')
    @endif
    <div class="top-bar">
        <!-- jackpot -->

        <div style="display: flex;width: calc(100% - 350px);">
            <marquee style="">
                <div style="flex-wrap: nowrap;display: flex;">
                @if(isset($jpgs))
					
                    @foreach($jpgs AS $index=>$jpg)
                        @if($jpg->view)
							
                        @else
                        <div class="grid-item grid-item--width3 grid-item--height2">
                            <div class="grid__content" style="text-align: center;
                                vertical-align: middle;
                                padding: 5px;
                                margin-left: 10px;
                                border-radius: 10px;
                                border: 2px solid orange;">
                                <div class="jackpot jackpot--value s">JACKPOT € {{ number_format($jpg->balance, 2,".","") }}</div>
                            </div>
                        </div>
                        @endif
                    @endforeach
				@else
					<div>No jackpots</div>
                @endif
                </div>
            </marquee>
        </div>
        <!-- jackpot -->
        <div class="top-bar__content">
            @if( !isset(auth()->user()->username) )
            <div class="top-bar__anon ng-scope" ng-if="!$root.data.user.email">
                <button class="top-bar__sign-up button button-primary button-small ng-scope" ng-click="openModal($event, '#registration-confirm')">@lang('app.register')</button>
                <button class="top-bar__sign-in button button-secondary button-small ng-scope" ng-click="openModal($event, '#login-modal')">@lang('app.log_in')</button>
            </div>
            @else
            <link rel="stylesheet" href="/woocasino/css/payment.css">
            <div class="header-auth__anon-btn-wrp">
                <a class="modal-btn button button-primary ng-scope" ng-click="openModal($event, '#my-account')">{{trans('app.my_profile')}}</a>
                <a href="{{ route('frontend.auth.logout') }}" class="modal-btn button button-secondary ng-scope">@lang('app.logout')</a>
            </div>
            @endif
            @php
            $lang = [
                'en' => 'English',
                'de' => 'Deutsch',
            ];
            if (isset($_COOKIE['language'])) {
                $laut = htmlspecialchars($_COOKIE['language']);
            } else {
                $laut = 'de';
            }
            @endphp
            <div class="language-select top-bar__locale locale-selector--small locale-selector ng-isolate-scope" dropdown="">
                <button class="locale-selector__selector">
                    <img class="locale-selector__img" alt="{{$lang[$laut]}}" src="https://cdn2.softswiss.net/flags/square/{{$laut}}.svg">
                    <span class="locale-selector__name ng-binding">{{ strtoupper(substr($lang[$laut], 0, 3)) }}</span>
                </button>
                <ul role="menu" class="locale-selector__dropdown">
                    @foreach ($lang as $k=>$v)
                    <li class="locale-selector__item" style="margin: 4px 6px;">
                        <i data-lang-code="{{$k}}" role="button">
                            <img class="locale-selector__img" alt="{{$v}}" src="https://cdn2.softswiss.net/flags/square/{{$k}}.svg"> <span class="locale-selector__dropdown-name ng-binding">{{ strtoupper(substr($v, 0, 3)) }}</span>
                        </i>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    <!-- ngIf: ['home'].includes(state.current.page_name) -->
    <div class="sticky-games-menu-mob ng-scope" scroll-position="">
        <div class="games-menu-mob ng-isolate-scope" name="games_menu_mob">
            <div class="providers-mob ng-isolate-scope" name="games_providers_mob">
                <button class="providers-mob__btn" type="button">
                    <i class="icon-woo-filters"></i>
                    <span class="providers-mob__btn-text ng-binding">Filter</span>
                </button>
                <div class="providers-mob__dropdown" role="menu">
                    <ul class="providers-mob__list providers-mob__list--exclusive">
                        @if ($categories && count($categories))
                            @foreach($categories AS $index=>$category)
                                <li class="providers-mob__item ng-scope">
                                    <a class="providers-mob__link providers-mob__link--exclusive" scroll-up="" href="{{ route('frontend.game.list.category', $category->href) }}">
                                        <span class="providers-mob__icon-wrp">
                                            <span class="providers-mob__icon">
                                                <img class="providers-mob__icon-img providers-mob__icon-img--pragmaticplay" src="/frontend/Default/svg/{{$category->href}}.svg">
                                            </span>
                                            <span class="providers-mob__name ng-scope">{{ $category->title }}</span>
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
            <nav class="games-menu-mob__menu">
                <ul class="games-menu-mob__list">
                    @if ($categories && count($categories))
                        @foreach($categories AS $index=>$category)
                            <li class="games-menu-mob__item games-menu-mob__item--woo_choice">
                                <a class="games-menu-mob__link games-menu-mob__link--woo_choice" scroll-up="" href="{{ route('frontend.game.list.category', $category->href) }}"> <i class="games-menu-mob__icon icon-woo-menu-default icon-woo-woo_choice"></i> <span class="games-menu-mob__title ng-scope" >{{ $category->title }}</span> </a>
                            </li>
                        @endforeach
                    @endif
                </ul>
            </nav>
        </div>
    </div>

    <div class="mobile-menu ng-scope">
        <div class="mobile-menu__wrap ng-scope">
            <div class="header-auth ng-isolate-scope">
                <div class="header-auth__anon ng-scope">
                    <div class="header-auth__anon-status">
                        <img class="header-auth__anon-img" src="/woocasino/resources/images/status/anon.svg" alt="">
                    </div>
                    @if (!Auth::check())
                    <div class="header-auth__anon-btn-wrp">
                        <button class="button button-primary header-auth__reg-btn ng-scope" ng-click="openModal($event, '#registration-confirm')">@lang('app.register')</button>
                        <button class="button button-secondary header-auth__login-btn ng-scope" ng-click="openModal($event, '#login-modal')">@lang('app.log_in')</button>
                    </div>
                    @else
                    <div class="statuses-panel">
                        <div class="statuses-panel__wrp">
                            <a class="statuses-panel__wrp-img ng-scope" >
                                <img  class="statuses-panel__img ng-scope" alt="statuses" src="/woocasino/resources/images/status/w1.svg">
                            </a>
                            <div class="balance-info ng-isolate-scope" type="balance-selector">

                                <p class="balance-info__elem ng-scope">
                                 <div > <span style=" font-size:26px;color:#ffbb39;" class="info-value balanceValue">{{ number_format(auth()->user()->balance, 2, '.', '') }}
                                {{ isset($currency) ? $currency : 'EUR' }}</span></div>
                                </p>
                            </div>
                        </div>
                        <div class="statuses-panel__wrp-status">
                            <button class="statuses-panel__btn button button-primary button-pay" ng-click="openModal($event, '#my-account')">@lang('app.depositb')</button>

                            <div class="ng-hide">
                                <p class="statuses-panel__name-status ng-binding">W1</p>
                                <div class="statuses-panel__points ng-scope" >
                                    <div class="status-line">
                                        <div class="status-line__progress">
                                            <div class="status-line__progress-full"style="width: 0%;"></div>
                                        </div>
                                        <p class="status-line__text ng-binding">0/25 (0%)</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <div class="mobile-menu__nav">
                <nav class="mobile-menu__nav-menu header-menu ng-scope ng-isolate-scope">
                    <ul class="header-menu__list">
                        <li class="header-menu__item">
                            <!-- ngIf: $root.data.user.email -->
                        </li>
                        <li class="header-menu__item ng-scope">
                            <a class="header-menu__link header-menu__link--games"  href="{{ route('frontend.game.list.category', 'all') }}">
                                <i class="header-menu__icon icon-woo-menu-default icon-woo-all"></i>
                                <span class="header-menu__text ng-binding">@lang('app.all')</span>
                            </a>
                        </li>
                        <li class="header-menu__item ng-scope">
                            <a class="header-menu__link header-menu__link--games" href="{{ route('frontend.game.list.category', 'hot') }}">
                                 <i class="header-menu__icon icon-woo-menu-default icon-woo-poker"></i>
                                 <span class="header-menu__text ng-binding" >@lang('app.hot_game')</span>
                            </a>
                        </li>
                        <li class="header-menu__item ng-scope">
                            <a class="header-menu__link header-menu__link--games" href="{{ route('frontend.game.list.category', 'new') }}">
                                 <i class="header-menu__icon icon-woo-menu-default icon-woo-new-games"></i>
                                 <span class="header-menu__text ng-binding" >@lang('app.new')</span>
                            </a>
                        </li>
                        <li class="header-menu__item ng-scope">
                            <a class="header-menu__link header-menu__link--games" href="{{ route('frontend.game.list.category', 'slots') }}"> <i class="header-menu__icon icon-woo-menu-default icon-woo-video-slots"></i> <span class="header-menu__text ng-binding" >@lang('app.slots')</span> </a>
                        </li>
                        <li class="header-menu__item ng-scope">
                            <a class="header-menu__link header-menu__link--games" href="{{ route('frontend.game.list.category', 'jackpot') }}"> <i class="header-menu__icon icon-woo-menu-default  icon-woo-blackjack"></i> <span class="header-menu__text ng-binding" >Jackpot</span> </a>
                        </li>
                        <li class="header-menu__item ng-scope">
                            <a class="header-menu__link header-menu__link--games" href="{{ route('frontend.game.list.category', 'roulette') }}"> <i class="header-menu__icon icon-woo-menu-default icon-woo-roulette"></i> <span class="header-menu__text ng-binding" >Roulette</span> </a>
                        </li>
                    </ul>
                </nav>
                @php
                $lang = [
                    'en' => 'ENG',
                    'de' => 'DEU',
                ];
                if (isset($_COOKIE['language'])) {
                    $laut = htmlspecialchars($_COOKIE['language']);
                } else {
                    $laut = 'de';
                }
                @endphp
                <div class="mobile-menu__nav-btn">
                    <div class="language-select mobile-menu__locale locale-selector ng-isolate-scope" dropdown="" >
                        <button class="locale-selector__selector" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown">
                            <img class="locale-selector__img" src="https://cdn.softswiss.net//flags/square/{{$laut}}.svg">
                            <span class="locale-selector__name ng-binding">{{ $lang[$laut] }}</span>
                        </button>
                        <ul role="menu" class="locale-selector__dropdown">
                            @foreach ($lang as $k=>$v)
                                 <li class="locale-selector__item ng-scope">
                                    <i data-lang-code="{{$k}}" role="button">
                                        <img class="locale-selector__img" alt="{{$v}}" src="https://cdn.softswiss.net//flags/square/{{$k}}.svg">
                                        <span class="locale-selector__dropdown-name ng-binding">{{$v}}</span>
                                    </i>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>
    $('.locale-selector__selector').click(function(){
        $(this).parent().toggleClass('open');
    })
    $('.providers-mob__btn').click(function(){
        $(this).parent().toggleClass('open')
    })
</script>

