<div class="menu-mobile menu-activated-on-click color-scheme-dark">
    <div class="mm-logo-buttons-w"><a class="mm-logo" href="{{ route('netpos') }}"
            data-original-title="" title="">
            <img src="https://netpos.gapi.lol/img/cashier-white/img/logo.png"></a>
        <div class="mm-buttons">
            <div class="content-panel-open2">
                <a href="{{ route('netpos') }}" data-original-title="" title="">
                    <div class="os-icon os-icon-email-2-at2"></div>
                </a>
            </div>
            <div class="content-panel-open">
                <div class="os-icon os-icon-grid-circles"></div>
            </div>
            <div class="mobile-menu-trigger">
                <div class="os-icon os-icon-hamburger-menu-1"></div>
            </div>
        </div>
    </div>
    <div class="menu-and-user">
        <div class="logged-user-w">
            <div class="logged-user-info-w">
                <div class="logged-user-name"></div>
                <div class="logged-user-role">Cashier</div>
            </div>
        </div>
        <ul class="main-menu">
            <li class="">
                <a href="{{ route('netpos') }}" data-original-title="" title="">
                    <div class="icon-w">
                        <i class="os-icon os-icon-hamburger-menu-1"></i>
                    </div>
                    <span class="text-uppercase">Home</span>
                </a>
            </li>
            <li class="has-sub-menu">
                <a href="{{ route('netpos') }}" data-original-title="" title="">
                    <div class="icon-w">
                        <div class="os-icon os-icon-window-content"></div>
                    </div>
                    <span class="text-uppercase">Dashboard</span>
                </a>
                <ul class="sub-menu">
                    <li><a href="{{ route('netpos.transactions') }}" class="text-uppercase"
                            data-original-title="" title="">Transactions</a></li>                   
                    <li>
                        <a href="{{ route('netpos.cashier.profile') }}" class="text-uppercase"
                            data-original-title="" title="">Cashiers profile</a>
                    </li>
                    <li>
                        <a href="{{ route('netpos.jackpot') }}" class="text-uppercase"
                            data-original-title="" title="">Jackpot</a>
                    </li>
                </ul>
            </li>
            <li class="">
                <a href="{{ route('netpos.user.new') }}" data-original-title="" title="">
                    <div class="icon-w">
                        <i class="os-icon os-icon-user-male-circle"></i>
                    </div>
                    <span class="text-uppercase">New user</span>
                </a>
            </li>
            <li class="">
                <a href="{{ route('netpos') }}" data-original-title="" title="">
                    <div class="icon-w">
                        <i class="os-icon os-icon os-icon-edit-1"></i>
                    </div>
                    <span class="text-uppercase">Profile</span>
                </a>
            </li>
            <li class="has-sub-menu"><a href="#" data-original-title="" title="">
                    <div class="icon-w">
                        <div class="os-icon os-icon-window-content"></div>
                    </div>
                    <span>Languages</span>
                </a>
                <ul class="sub-menu">
                    <li><a href="" data-original-title=""
                            title="">English</a></li>
                    <li><a href="" data-original-title=""
                            title="">German</a></li>
                    <li><a href="" data-original-title=""
                            title="">Greek</a></li>
                    <li><a href="" data-original-title=""
                            title="">Italy</a></li>
                </ul>
            </li>
            <li class="">
                <a href="{{ route('backend.auth.netpos.logout') }}" data-original-title="" title="">
                    <div class="icon-w">
                        <div class="os-icon os-icon-signs-11"></div>
                    </div>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="desktop-menu menu-side-w menu-activated-on-click">
    <div class="logo-w"><a class="logo" href="{{ route('netpos') }}" data-original-title="" title="">
            <img style="width: 80px;" src="https://netpos.gapi.lol/img/cashier-white/img/logo.png"></a>
    </div>
    <div class="menu-and-user">
        <div class="logged-user-w">
            <div class="logged-user-i">
                <div class="logged-user-info-w">
                    <div class="logged-user-name">				@if(Auth::user()->shop) {{ Auth::user()->shop->name }} @else @lang('app.no_shop') @endif
                    </div>
                    <div class="logged-user-role">Cashier</div>
                </div>
            </div>
        </div>
        <ul class="main-menu" style="padding: 0px;">
            <li class="">
                <a href="{{ route('netpos') }}" data-original-title="" title="">
                    <div class="icon-w">
                        <i class="os-icon os-icon-hamburger-menu-1"></i>
                    </div>
                    <span class="text-uppercase">Home</span>
                </a>
            </li>
            <li class="has-sub-menu ">
                <a href="{{ route('netpos') }}" data-original-title="" title="">
                    <div class="icon-w">
                        <div class="os-icon os-icon-window-content"></div>
                    </div>
                    <span class="text-uppercase">Dashboard</span>
                </a>
                <ul class="sub-menu">
            
                    <li><a href="{{ route('netpos.transactions') }}" class="text-uppercase"
                            data-original-title="" title="">Transactions</a></li>
             
                    <li>
                        <a href="{{ route('netpos.cashier.profile') }}" class="text-uppercase"
                            data-original-title="" title="">Cashiers profile</a>
                    </li>
                    <li>
                        <a href="{{ route('netpos.jackpot') }}" class="text-uppercase"
                            data-original-title="" title="">Jackpot</a>
                    </li>
                </ul>
            </li>
            <li class="">
                <a href="{{ route('netpos.user.new') }}" data-original-title="" title="">
                    <div class="icon-w">
                        <i class="os-icon os-icon-user-male-circle"></i>
                    </div>
                    <span class="text-uppercase">New user</span>
                </a>
            </li>
            <li class="">
                <a href="{{ route('netpos') }}" data-original-title="" title="">
                    <div class="icon-w">
                        <i class="os-icon os-icon os-icon-edit-1"></i>
                    </div>
                    <span class="text-uppercase">Profile</span>
                </a>
            </li>
            <li class="">
                <a href="{{route('backend.auth.netpos.logout')}}" data-original-title="" title="">
                    <div class="icon-w">
                        <div class="os-icon os-icon-signs-11"></div>
                    </div>
                    <span class="text-uppercase">Logout</span>
                </a>
            </li>
        </ul>
        <div class="side-menu-magic" style="padding: 5px 5px 1px 5px; padding-top: 10px;">
            <p>© 2001 - {{ now()->year }} Copyright</p>
        </div>
    </div>
    <div class="text-center" style="padding-bottom: 16px;     border-top: 1px solid rgba(0, 0, 0, 0.05);">
        <form class="form-inline justify-content-sm-end center-block lang_selector" id="changeland">
            <input type="hidden" name="lang" id="lang" value="en">
            <label for="languegesDrop"></label>
            <select class="form-control form-control-sm rounded bright" data-width="fit" id="languegesDrop"
                data-live-search="true">
                <option selected="" value="en">English</option>
                <option value="de">German</option>
                <option value="gr">Greek</option>
                <option value="it">Italy</option>
            </select>
        </form>
    </div>
</div>