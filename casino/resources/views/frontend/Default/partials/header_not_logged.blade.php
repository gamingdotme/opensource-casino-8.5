<link rel="stylesheet" href="/woocasino/css/appef20.css">

<header class="header">
    <div class="header__mob-container">
        <div class="header__logo">
            <a class="header__logo-link" scroll-up="" href="#"> <img class="header__logo-img" src="/woocasino/resources/images/logo.png" alt="WooCasino"> </a>
        </div>
        <div class="header__mob-wrp">
            <button class="header__mob-btn button button-secondary button-small ng-scope" ng-click="openModal($event, '#login-modal')">@lang('app.log_in')</button>
            <a class="header__mobile-menu"> <span class="header__mobile-menu-icon"></span> <span class="header__mobile-menu-icon"></span> <span class="header__mobile-menu-icon"></span> </a>
        </div>
    </div>
    <div class="header__container">
        <div class="header__logo">
            <a class="header__logo-link" scroll-up="" href="#"> <img class="header__logo-img" src="/woocasino/resources/images/logo.png" alt="WooCasino"> </a>
        </div>
        <div class="header__container-bg">
            <div class="header-auth ng-isolate-scope">
                <div class="header-auth__anon ng-scope">
                    <div class="header-auth__anon-status"> <img class="header-auth__anon-img" src="/woocasino/resources/images/status/anon.svg" alt=""> </div>
                    @if( !isset(auth()->user()->username) )
                    <div class="header-auth__anon-btn-wrp">
                        <button class="modal-btn button button-primary header-auth__reg-btn ng-scope" data-name="modal-register" ng-click="openModal($event, '#registration-confirm')">@lang('app.register')</button>
                        <button class="modal-btn button button-secondary header-auth__login-btn ng-scope" ng-click="openModal($event, '#login-modal')" >@lang('app.log_in')</button>
                    </div>
                    @endif
                </div>
            </div>
            <nav class="header-menu ng-scope ng-isolate-scope" type="main-menu">
                <div class="header-menu__live">
                    <a class="header-menu__live-link" scroll-up="" href="{{route('frontend.game.list.category', 'slots')}}"> 
                        <span class="header-menu__live-icon icon-woo-menu-default icon-woo-blackjack"></span> <span class="header-menu__live-text ng-scope">@lang('app.slots')</span> 
                    </a>
                    <a class="header-menu__live-link" scroll-up="" href="{{route('frontend.game.list.category', 'hot')}}">
                        <span class="header-menu__live-icon icon-woo-menu-default icon-woo-roulette"></span> <span class="header-menu__live-text ng-scope">@lang('app.hot_game')</span>
                    </a>
                </div>
                @if(isset($categories))
                <ul class="header-menu__list">
                    @if( settings('use_all_categories') || true)
                        <li class="header-menu__item ng-scope">
                            <a class="header-menu__link header-menu__link--games @if($currentSliderNum != -1 && $currentSliderNum == 'all') header-menu__link--current @endif" scroll-up="" href="{{ route('frontend.game.list.category', 'all') }}"> <i class="header-menu__icon icon-woo-menu-default icon-woo-bgaming-slot-battle"></i> <span class="header-menu__text ng-binding">@lang('app.all')</span> </a>
                        </li>
                    @endif
                    @if( settings('use_new_categories') || true)
                        <li class="header-menu__item ng-scope">
                            <a class="header-menu__link header-menu__link--games @if($currentSliderNum != -1 && $currentSliderNum == 'new') header-menu__link--current @endif" scroll-up="" href="{{ route('frontend.game.list.category', 'new') }}"> <i class="header-menu__icon icon-woo-menu-default icon-woo-bgaming-slot-battle"></i> <span class="header-menu__text ng-binding">@lang('app.new')</span> </a>
                        </li>
                    @endif
                    @if ($categories && count($categories))
                        @foreach($categories AS $index=>$category)
                            <li class="header-menu__item ng-scope">
                                <a class="header-menu__link header-menu__link--games @if($currentSliderNum != -1 && $currentSliderNum == $category->href) header-menu__link--current @endif" scroll-up="" href="{{ route('frontend.game.list.category', $category->href) }}"> <i class="header-menu__icon icon-woo-menu-default icon-woo-bgaming-slot-battle"></i> <span class="header-menu__text ng-binding">{{ $category->title }}</span> </a>
                            </li>
                        @endforeach
                    @endif
                </ul>
                @endif
            </nav>
        </div>
    </div>
</header>
