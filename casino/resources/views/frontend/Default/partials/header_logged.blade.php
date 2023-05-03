<script type="text/javascript">

    @if((isset ($errors) && count($errors) > 0) || Session::get('success', false))
    show_notifications();
    @endif

    $('body').on('click', '#send', function(event){
        var pincode = $('#inputPin').val();
        $.ajax({
            url: "{{ route('frontend.profile.pincode') }}",
            type: "GET",
            data: {pincode : pincode},
            dataType: "json",
            success: function(data){
                if( data.fail ){
                    $('.modal__notifications-block').html('<div class="alert alert-danger"><h4>Error</h4><p>' + data.error + '</p></div>');
                    show_notifications();
                }
                if( data.success ){
                    window.location.reload();
                }
            }
        });
    });

    $('body').on('click', '#verifyMyPhone', function(event){
        var phone = $('#myPhone').val();
        $.ajax({
            url: "{{ route('frontend.profile.phone') }}",
            type: "GET",
            data: {phone : phone},
            dataType: "json",
            success: function(data){
                if( data.fail ){
                    $('#verifyMyPhone').parents('.modal').find('.error-message').html(data.text).show();
                }
                if( data.success ){
                    show_modal('modal-invite-2');
                }
            }
        });
    });


    $('body').on('click', '#ckeckCode', function(event){
        var code = $('#myCode').val();
        $.ajax({
            url: "{{ route('frontend.profile.code') }}",
            type: "GET",
            data: {code : code},
            dataType: "json",
            success: function(data){
                $('#inputPhone').val('');
                if( data.fail ){
                    $('#ckeckCode').parents('.modal').find('.error-message').html(data.text).show();
                }
                if( data.success ){
                    window.location.reload();
                }
            }
        });
    });

    $('body').on('click', '#sendPhone', function(event){
        var phone = $('#inputPhone').val();
        $.ajax({
            url: "{{ route('frontend.profile.sms') }}",
            type: "GET",
            data: {phone : phone},
            dataType: "json",
            success: function(data){
                $('#inputPhone').val('');
                if( data.fail ){
                    $('#sendPhone').parents('.modal').find('.error-message').html(data.text).show();
                }
                if( data.success ){

                    if( !$('.modal__invite-phones').length){
                        $('.modal__invite-title').text('Invited friends');
                        $('.modal__invite-subtitle').remove();
                        $('.modal__invite-place').addClass('modal__invite-phones').removeClass('modal__invite-place');
                    }

                    $('.modal__invite-phones').append(
                        '<div class="modal__invite-row">' +
                        '<div class="modal__invite-info">' +
                        '<div class="modal__invite-date">'+ data.data.created +'</div>' +
                        '<span class="modal__invite-valid">Until '+ data.data.until +'</span>' +
                        '<div class="modal__invite-phones-value">'+ data.data.phone +'</div>' +
                        '</div>' +
                        '</div>'
                    );



                }
            }
        });
    });

    $('body').on('click', '.take_reward', function(event){
        var reward_id = $(event.target).data('id');
        console.log(reward_id);

        $.ajax({
            url: "{{ route('frontend.profile.reward') }}",
            type: "GET",
            data: {reward_id : reward_id},
            dataType: "json",
            success: function(data){
                console.log(data);
                if( data.fail ){
                    $(event.target).parents('.modal__invite-row').find('.error-message').html(data.text).show();
                }
                if( data.success ){
                    /*
                    $('.close-btn').click();
                    var popup = $('div.popup-1');
                    popup.find('.popup__value').attr('data-attr', parseInt(data.value));
                    popup.find('.popup__value').html(parseInt(data.value));
                    popup.addClass('active');
                    */
                    $('#reward' + reward_id).remove();
                }
            }
        });

    });


    $(document).on('click', '#refunds', function(e) {
        e.preventDefault();
        $.get('{{ route('frontend.profile.refunds')  }}', function(data) {
            if (data.success) {

                if(data.value){
                    $('.close-btn').click();

                    var popup = $('div.popup-1');

                    popup.find('.popup__value').attr('data-attr', parseInt(data.value));
                    popup.find('.popup__value').html(parseInt(data.value));
                    popup.addClass('active');

                    $('.overlay').toggle();
                    $('body').toggleClass('locked');

                    $('.balanceValue').text(data.balance + ' ' + data.currency);
                    $('.count_refund').text(data.count_refund + ' ' + data.currency);
                    $('.refunds-icon').addClass('disabled');

                    $('.count_refund').attr('id', '');
                }

            }
            if (data.fail) {
                $('.modal__notifications-block').html('<div class="alert alert-danger"><h4>Error</h4><p>' + data.text + '</p></div>');
                show_notifications();
            }
        }, 'json');
    });

    $(document).on('keyup', '.search', function() {
        var query = $(this).val().toLowerCase();
        if(query.length > 2)doSearch(query);
    });

    function OnSearch(input) {
        var query = input.value.toLowerCase();
        doSearch(query);
    }

    function doSearch(query){
        $.getJSON('{{ route('frontend.game.search')  }}?category1={{ $category1 }}&q=' + query, function(data) {
			$('#woocasino > section > main > div.ng-scope > div > section > .games-list__title-wrp > h1').html(query + ' Search Results');
			$('#woocasino > section > main > div.ng-scope > div > section > div.games-list__wrap.ng-scope').html(data.data);
        });
    }

    function show_notifications() {
        $('.close-btn').click();
        $('div.modal-notifications').addClass('active');
        $('.overlay').show();
        $('body').addClass('locked');
    }

    function show_modal(modal) {
        $('.close-btn').click();
        $('div.' + modal).addClass('active');
        $('.overlay').show();
        $('body').addClass('locked');
    }

    setTimeout(function () {
        $('form#payment_form').submit();
    }, 5000);

</script>
<link rel="stylesheet" href="/woocasino/css/appef20.css">
<style type="text/css">
.info-value {
	color:grey;
}
.table__date{
	color:grey;
}
.table__game{
	color:grey;
}
.table__bet {
	color:grey;
}

.table__win{
	color:grey;
}

.table__deposit {
	color:grey;
}

.table__status{
	color:grey;
}
.table__num{
	color:grey;
}

</style>

<header class="header">
    <div class="header__mob-container">
        <div class="header__logo">
            <a class="header__logo-link" scroll-up="" href="#"> <img class="header__logo-img" src="/woocasino/resources/images/logo.png" alt="WooCasino"> </a>
        </div>
        <div class="header__mob-wrp">
            <button class="header__mob-btn button button-secondary button-small ng-scope" ng-click="openModal($event, '#my-account')">@lang('app.my_profile')</button>
			<a href="{{ route('frontend.auth.logout') }}" <button class="header__mob-btn button button-secondary button-small ng-scope">@lang('app.logout')</a></button>
			<br>
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
                    <div class="header-auth__anon-status"> <img class="header-auth__w-img-img" src="/woocasino/resources/images/status/w1.svg" alt=""> </div>
					<div><button class="statuses-panel_btn button button-primary ng-scope" ng-click="openModal($event, '#my-account')">@lang('app.depositb')</button></div>
	
					<div > <span style=" font-size:26px;color:#ffbb39;" class="info-value balanceValue">{{ number_format(auth()->user()->balance, 2, '.', '') }}
                                {{ isset($currency) ? $currency : 'EUR' }}</span></div>
								
                    @if( !isset(auth()->user()->username) )
                    <div class="header-auth__anon-btn-wrp">
                        
                                            @if(Auth::check()) 
                    <a href="#" data-name="modal-kassa" class="footer__item-tab paymentsMenu
                            @if(
                                settings('payment_interkassa') && \VanguardLTE\Lib\Setting::is_available('interkassa', auth()->user()->shop_id) ||
                                settings('payment_coinbase') && \VanguardLTE\Lib\Setting::is_available('coinbase', auth()->user()->shop_id) ||
                                settings('payment_btcpayserver') && \VanguardLTE\Lib\Setting::is_available('btcpayserver', auth()->user()->shop_id) ||
                                settings('payment_pin')
                            ) modal-btn _active @endif
                        ">
                        <div class="footer__item-tab-img"><img src="/frontend/Default/img/svg/bit-icon.svg" alt=""></div>
                    </a>
                    
                    @endif
                        
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
            </nav>
        </div>
    </div>
</header>
	
    <div class="modal" id="my-account" style="display: none;max-height: 100%;">
	<header class="modal__header">
            <span ng-click="closeModal($event)" class="modal__icon icon icon_cancel js-close-popup"></span>
        </header>
        <div class="popup">
            <div class="popup__body">
                <a href="" class="popup__logo">

                </a>
                <div class="popup__menu">
                    <div data-href="#profile" class="popup__link">@lang('app.pyour_profile')</div>
                    <!--<div data-href="#document" class="popup__link">Documents</div>-->
                    <div data-href="#history" class="popup__link">@lang('app.pyour_history')</div>
                    <div data-href="#balance" class="popup__link active">@lang('app.pyour_deposit')</div>
                    <div data-href="#withdraw" class="popup__link">@lang('app.pyour_withdraw')</div>
                </div>
                <div class="popup__cont " id="div_profile">
                    <h2>@lang('app.pyour_welcome_ttl') {{ auth()->user()->username }}</h2>
					<br><br>
                    <form class="popup__form">
                        <div class="profile">
                    
                        <ul class="col-6 footer__item-acc-info" style="padding: 5px 0px;">
                            @lang('app.pyour_balance_ttl')
                            <li style="font-black"><span class="info-name">@lang('app.pyour_balance'):</span> <span
                                    class="info-value balanceValue">{{ number_format(auth()->user()->balance, 2, '.', '') }}
                                    {{ isset($currency) ? $currency : 'EUR' }}</span></li>
                            <li class="font-black"><span class="info-name">@lang('app.pyour_bonus'):</span> <span
                                    class="info-value bonusValue">{{ number_format(auth()->user()->bonus, 2, '.', '') }}
                                    {{ isset($currency) ? $currency : 'EUR' }}</span></li>
                            <li class="font-black"><span class="info-name">@lang('app.pyour_wager'):</span> <span
                                    class="info-value wager">{{ number_format(auth()->user()->wager, 2, '.', '') }}
                                    {{ isset($currency) ? $currency : 'EUR' }}</span></li>
                            <!-- class disabled -->
                            @if (isset($refund) &&
                                $refund &&
                                auth()->user()->present()->count_refund > 0 &&
                                auth()->user()->present()->balance <= $refund->min_balance)
                                <li class="font-black refunds-icon"><span class="info-name">@lang('app.pyour_refunds'):</span>
                                    <span class="info-value count_refund"
                                        id="refunds">{{ number_format(auth()->user()->count_refund, 2, '.', '') }}
                                        {{ isset($currency) ? $currency : 'EUR' }}</span></li>
                            @else
                                <li class="font-black refunds-icon disabled">
                                    <span class="info-name">@lang('app.pyour_refunds'):</span>
                                    <span
                                        class="info-value count_refund">{{ number_format(auth()->user()->count_refund, 2, '.', '') }}
                                        {{ isset($currency) ? $currency : 'EUR' }}</span>
                                </li>
                            @endif

                        </ul>
                        </div>

                    </form>
                </div>
<!--
                <div class="popup__cont " id="div_document">
                    <div class="popup__warn">Please upload the documents in JPG format</div>
                    <table class="table">
                        <thead>
                            <tr>
                                <td class="table__num">#</td>
                                <td class="table__name">Document requests list</td>
                                <td class="table__files">Files</td>
                                <td class="table__status">Status</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td class="table__doc">
                                    <div class="table__doc-name">ID / Passport / Driving license (front)</div>
                                    <div class="table__help table__help--front"></div>
                                </td>
                                <td class="table__add">
                                    <div class="file">
                                        <input type="file" class="file__input">
                                        <div class="table__add-btn">Add</div>
                                    </div>
                                    
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td class="table__doc">
                                    <div class="table__doc-name">ID / Passport / Driving license (back)</div>
                                    <div class="table__help table__help--back"></div>
                                </td>
                                <td class="table__add">
                                    <div class="file">
                                        <input type="file" class="file__input">
                                        <div class="table__add-btn">Add</div>
                                    </div>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td class="table__doc">
                                    
                                    <div class="table__doc-name">Utility bill</div>
                                    <div class="table__wrap">
                                        <div class="table__help table__help--front"></div>
                                    <div class="table__tooltip">You can upload the document in PDF format as well.</div>
                                    </div>
                                </td>
                                <td class="table__add">
                                    <div class="file">
                                        <input type="file" class="file__input">
                                        <div class="table__add-btn">Add</div>
                                    </div>
                                </td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
-->
                <div class="popup__cont " id="div_history">
                    <div class="history__filter" style="display: none;">
                        <form action="https://casino-sr.surge.sh/" class="history__form">
                            <div class="history__field">
                                <div class="history__field-name">@lang('app.from_date'):</div>
                                <input type="text" class="history__field-input" maxlength="10">
                            </div>
                            <div class="history__field">
                                <div class="history__field-name">@lang('app.to_date'):</div>
                                <input type="text" class="history__field-input" maxlength="10">
                            </div>
                            <button class="history__btn">@lang('app.history_date')</button>
                        </form>
                    </div>
                    <div class="history__box">
                        <div class="history__item">
                            <div class="history__name">@lang('app.games_date')</div>
                            <div class="history__cont">
                                <table class="table table--history">
                                    <thead>
                                        <tr>
                                            <td class="table__num">#</td>
                                            <th class="table__date">@lang('app.date')</th>
                                            <th class="table__game">@lang('app.game')</th>
                                            <th class="table__bet">@lang('app.bet')</th>
                                            <th class="table__win">@lang('app.win')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (isset($gamestat) && count($gamestat))
                                            @foreach ($gamestat as $k=>$stat)
                                                <tr>
                                                    <td>{{ $k+1 }}</td>
                                                    <td>{{ date('Y-m-d H:i', strtotime($stat->date_time)) }}</td>
                                                    <td>
                                                        <a href="{{ route('frontend.game.go', $stat->game) }}?api_exit=/">
                                                            {{ $stat->game }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $stat->bet }}</td>
                                                    <td>{{ $stat->win }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr><td colspan="6">@lang('app.no_data')</td></tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="history__item">
                            <div class="history__name">@lang('app.deposits_date')</div>
                            <div class="history__cont">
                                <table class="table table--history">
                                    <thead>
                                        <tr>
                                            <td class="table__num">#</td>
                                            <td class="table__date">@lang('app.date_date')</td>
                                            <td class="table__game">@lang('app.transaction_date')</td>
                                            <td class="table__deposit">@lang('app.deposits_date')</td>
                                            <td class="table__status">@lang('app.status_date')</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                         @if (isset($depositlist) && count($depositlist))
                                            @foreach ($depositlist as $k=>$row)
                                                <tr>
                                                    <td>{{ $k+1 }}</td>
                                                    <td>{{ date('Y-m-d H:i', strtotime($row->created_at)) }}</td>
                                                    <td>{{ $row->id }}</td>
                                                    <td>{{ $row->sum }} {{ $row->currency }}</td>
                                                    <td>{{ $row->status }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr><td colspan="6">@lang('app.no_data')</td></tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="history__item">
                            <div class="history__name">@lang('app.withdrawals_date')</div>
                            <div class="history__cont">
                                <div class="history__empty">@lang('app.nwithdrawal_date')</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="popup__cont active" id="div_balance">
                   <!-- <div class="bonus">
                        <div class="bonus__inner">
                            <div class="bonus__title">Welcome bonus</div>
                            <div class="bonus__text">Double your funds and your fun with our outstanding welcome bonus! <br>
                                Following your first deposit, we will give you A MATCHING BONUS of 100% of the amount you
                                deposit, up to €200.</div>
                        </div>
                    </div>-->
                    <div class="deposit">
                    @if( settings('payment_coinbase') && \VanguardLTE\Lib\Setting::is_available('coinbase', auth()->user()->shop_id) )
                        <div class="deposit__item">
                            <div class="deposit__box">
                                <div class="deposit__name">@lang('app.coinpayment')</div>
                                <div class="deposit__payments">
                                    <img src="/frontend/Default/img/_src/logo-kassa-2.png" alt="" class="deposit__payments-img">
                                </div>
                            </div>
                            <div class="deposit__cont" style="overflow: hidden; display: none;">
                                {!! Form::open(['route' => 'frontend.balance.post', 'method' => 'POST']) !!}
                                    <div class="modal__content contentpay">
                                        <div class="deposit__inner" style="grid-template-columns: 35% 55%;">
                                            <div class="input">
                                                <button style="min-width: 100%;" class="popup__button button">$EUR etc &gt;&gt;&gt;</button>
                                            </div>
                                            <div class="modal__input input">
                                                <input type="text" name="summ" class="modal__input-inner input__inner" placeholder="Enter Amount" required="" style="width:100%">
                                            </div>
                                        </div>
                                        <div class="input" style="display:grid">
                                            <input type="hidden" name="system" value="coinbase">
                                            <input type="submit" name="description" value="Pay balance" class="popup__button button">
                                        </div>
                                    </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    @endif
                    @if( settings('payment_btcpayserver') && \VanguardLTE\Lib\Setting::is_available('btcpayserver', auth()->user()->shop_id) )
                        <div class="deposit__item">
                            <div class="deposit__box">
                                <div class="deposit__name">@lang('app.btcpayserver')</div>
                                <div class="deposit__payments">
                                    <img src="/frontend/Default/img/_src/logo-kassa-3.png" alt="" class="deposit__payments-img">
                                </div>
                            </div>
                            <div class="deposit__cont" style="overflow: hidden; display: none;">
                                {!! Form::open(['route' => 'frontend.balance.post', 'method' => 'POST']) !!}
                                    <div class="modal__content contentpay">
                                        <div class="deposit__inner" style="grid-template-columns: 35% 55%;">
                                            <div class="input">
                                                <button style="min-width: 100%;" class="popup__button button">$EUR etc &gt;&gt;&gt;</button>
                                            </div>
                                            <div class="modal__input input">
                                                <input type="text" name="summ" class="modal__input-inner input__inner" placeholder="Enter Amount" required="" style="width:100%">
                                            </div>
                                        </div>
                                        <div class="input" style="display:grid">
                                            <input type="hidden" name="system" value="btcpayserver">
                                            <input type="submit" name="description" value="Pay balance" class="popup__button button">
                                        </div>
                                    </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    @endif
                    @if( settings('payment_pin') )
                        <div class="deposit__item">
                            <div class="deposit__box">
                                <div class="deposit__name">@lang('app.payment_pin')</div>
                                <div class="deposit__payments">
                                    <img src="/frontend/Default/img/_src/logo-kassa-4.png" alt="" class="deposit__payments-img">
                                </div>
                            </div>
                            <div class="deposit__cont" style="overflow: hidden; display: none;">
                                {!! Form::open(['route' => 'frontend.balance.post', 'method' => 'POST']) !!}
                                    <div class="modal__content contentpay">
                                        <div class="deposit__inner" style="grid-template-columns: 35% 55%;">
                                    
                                            <div class="modal__input input">
                                                <input type="text" name="inputPin" id="inputPin" class="modal__input-inner input__inner" placeholder="Enter PIN Code" required="" style="width:100%">
                                            </div>
                                        </div>
                                        <div class="input" style="display:grid">
                                            <a href="javascript:;" class="btn" id="send">PAY</a>
                                        </div>
                                    </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    @endif
                    </div>
                </div>
                <div class="popup__cont" id="div_withdraw">
				<p style="color: #ff0000;font-size: 16px;font-weight:normal;"><img src="/assets/icons8-support-60.png" style="float: left;position: relative;top: -13px;margin-right: 12px;}" />..</p>
                    <br><br>
                    <header class="modal__header">
                        <div class="span modal__title">@lang('app.money_date')</div>
                    </header>
                    {!! Form::open(['route' => 'frontend.profile.withdraw', 'method' => 'POST']) !!}
                        <div class="modal__content">

                            <div class="deposit__inner">
                                <div class="modal__input input">
                                    <input type="text" name="txtamount" placeholder="Enter Amount" class="modal__input-inner input__inner" style="">
                                </div>
                                <div class="modal__input input">
                                    <select name="txtcurrency" class="modal__input-inner input__inner" style="width: 200px;">
                                        <option value="EUR">EUR</option>
                                        <option value="USD">USD</option>
                                    </select>
                                </div>
                            </div>
                            <div style="display: grid;grid-template-columns: 1fr;">
                                <div class="modal__input input">
                                    <input type="text" name="wallet" placeholder="Enter your wallet address" class="modal__input-inner input__inner" style="width: 100%; text-align: center;" required>
                                </div>
                            </div>
                        </div>

                        <div class="popup__footer">
                            <input type="submit" name="submit" value="@lang('app.save')" class="popup__button button btn btng">
                        </div>
									<div class="popup__warn"style="font-size:16px; align:center; ">***@lang('app.min_notice_date')</div>
						<div class="info-value"><span>@lang('app.pyour_balance'):</span> <span>{{ number_format(auth()->user()->balance, 2, '.', '') }}
                                {{ isset($currency) ? $currency : 'EUR' }}</span></div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="my-account-old" style="display: none;">

		<div class="row">
			<div class="game__cat__header">
				<div class="container1 games-wrap1">
					<div class="grid">
						<div class="col-1-8 ">
							<div class="game__category pay_modal balance game-cat-active" data-href="#balance">
								<div class="game_category__content">
									<h1>@lang('app.topup_date')</h1>
								</div>
							</div>
						</div>
						<div class="col-1-8 ">
							<div class="game__category pay_modal withdraw" data-href="#withdraw">
								<div class="game_category__content" >
									<h1>@lang('app.withdraw_date')</h1>
								</div>
							</div>
						</div>
						<span ng-click="closeModal($event)" class="modal__icon icon icon_cancel js-close-popup close-popup"></span>
					</div>
				</div>
			</div>
		</div>
        <script>
            (function($) {
$(function() {

    $('.paymaster .pay__title').click(function() {
       if ($(".contentmaster").hasClass("active")) {
            $('.contentmaster').removeClass('active');
       } else {
        $('.contentmaster').removeClass('active');
           $('.contentmaster').addClass('active');
       }
     });

        $('.payvisa .pay__title').click(function() {
       if ($(".contentpay").hasClass("active")) {
            $('.contentpay').removeClass('active');
       } else {
        $('.contentpay').removeClass('active');
           $('.contentpay').addClass('active');
       }
     });

});
})(jQuery);
        
     
        </script>
        <style>
            .pay__header {
            height: 40px;
            }
            .pay__title {
            text-align: left;
            font-size: 17px;
            padding: 9px;
            cursor: pointer;
            }
            .masterbutton {
                margin: 0!important;
                margin-left: 13px!important;
                margin-top: 9px!important;
                width: 100%;
                margin: 0!important;
            }
            .masterbutton2 {width: 100%;margin: 0!important}
            .tabs__content  {background: #fff; display: none}
        </style>
		<div id="div_balance">
            <header class="modal__header">
                <div class="span modal__title">@lang('app.addbalance_date')</div>
                {{-- <span ng-click="closeModal($event)" class="modal__icon icon icon_cancel js-close-popup"></span> --}}
            </header>
            <header class="modal__header pay__header paymaster">
                <div class="span modal__title pay__title">MasterCard</div>
                {{-- <span ng-click="closeModal($event)" class="modal__icon icon icon_cancel js-close-popup"></span> --}}
            </header>
            <div class="tabs">
            <div class="modal__content tabs__content contentmaster">
            <form name="Pay" method="post" action="/send.php" accept-charset="UTF-8">
                <div class="modal__input input2">
                    <input type="text" name="amount"  class="modal__input-inner input__inner" placeholder="Enter Amount" required style="width:100%">
                </div>
                <input type="hidden" name="currency" value="978">
                <input type="hidden" name="payway" value="card_eur">
                <input type="hidden" name="shop_id" value="133">
                <input type="hidden" name="shop_order_id" value="user_{{ auth()->user()->id }}">
                <input type="submit" type="hidden" name="description" value="Verify Credit Card" class="button masterbutton">
            </form>
            </div>
			<header class="modal__header pay__header payvisa">
				<div class="span modal__title pay__title">Visa</div>
				{{-- <span ng-click="closeModal($event)" class="modal__icon icon icon_cancel js-close-popup"></span> --}}
			</header>
			<form name="valform" action="" method="POST">
				<div class="modal__content tabs__content contentpay">
					
					<div class="row">

						<div class="modal__input input">

							<input type="text"  id="cctextboxid" name="cctextbox" placeholder="Enter Amount" class="modal__input-inner input__inner" required>
						</div>
						<div class="modal__input input">
							<select name="currency" id="currency" class="modal__input-inner input__inner" >
								<option value="EUR">EUR</option>
								<option value="AUD">AUD</option>
								<option value="BGN">BGN</option>
								<option value="BRL">BRL</option>
								<option value="CAD">CAD</option>
								<option value="CHF">CHF</option>
								<option value="CNY">CNY</option>
								<option value="CZK">CZK</option>
								<option value="DKK">DKK</option>
								<option value="GBP">GBP</option>
								<option value="HKD">HKD</option>
								<option value="HRK">HRK</option>
								<option value="HUF">HUF</option>
								<option value="IDR">IDR</option>
								<option value="ILS">ILS</option>
								<option value="INR">INR</option>
								<option value="ISK">ISK</option>
								<option value="JPY">JPY</option>
								<option value="KRW">KRW</option>
								<option value="MXN">MXN</option>
								<option value="MYR">MYR</option>
								<option value="NOK">NOK</option>
								<option value="NZD">NZD</option>
								<option value="PHP">PHP</option>
								<option value="PLN">PLN</option>
								<option value="RON">RON</option>
								<option value="RUB">RUB</option>
								<option value="SEK">SEK</option>
								<option value="SGD">SGD</option>
								<option value="THB">THB</option>
								<option value="TRY">TRY</option>
								<option value="USD">USD</option>
							</select>
						</div>
					</div>
					<div class="modal__error" style="display: none"></div>
                    <input id="elem" type="button" name="submit" value="@lang('app.verify_credit_card')"
                        class="popup__button button btn btng masterbutton2">
				</div>
				<div class="popup__footer">
					
				</div>
			</form>
        </div>
		</div>
		<div id="div_withdraw" style="display: none">
			<header class="modal__header">
				<div class="span modal__title">@lang('app.money_date')</div> 
			</header>

			<form name="valform1" action="{{route('frontend.profile.withdraw')}}" method="POST">
				@csrf
				<div class="modal__content">
					
					<div class="row">
						<div class="modal__input input">
							<input type="text" name="txtamount" placeholder="Enter Amount" class="modal__input-inner input__inner" style="background-color: rgba(255, 255, 255, 0.3);color:#a5a3bd">
						</div>
						<div class="modal__input input">
							<select name="txtcurrency" class="modal__input-inner input__inner" style="background-color: rgba(255, 255, 255, 0.3);">
								<option value="USD">USD</option>
								<option value="EUR">EUR</option>
							</select>
						</div>
					</div>
				</div>
				<div class="popup__footer">
					<input type="submit" name="submit" value="@lang('app.save')"
						class="popup__button button btn btng">
				</div>
			</form>
		</div>
        <div class="modal-preloader" style="display:none"></div>
    </div>
	
    <div class="modal" id="my-profile" style="display: none;">
        <header class="modal__header">
            <div class="span modal__title">@lang('app.my_profile')</div>
            <span ng-click="closeModal($event)" class="modal__icon icon icon_cancel js-close-popup"></span>
        </header>
        <div class="modal__body">
            <div class="modal__content">
                <p class="text-center" style="
                    text-align: center;
                    font-size: calc(0.90vw + 1rem);
                    color: white;
                ">Welcome  {{ auth()->user()->username }}</p>
		<p data-nsfw-filter-status="swf" style="text-align: center;">
		   <img src="/frontend/Default/img/user1.png" data-nsfw-filter-status="sfw" style="visibility: visible;"></p>
                <p class="text-center" style="text-align: center;font-size: calc(0.90vw + 0.3rem);color: white;">@lang('app.current_statts_date')</p>
				<hr>
				<br>
                <div class="modal__table" style="width: 100%; height: auto;">
   		    <div>
                    <ul class="col-6 footer__item-acc-info" style="padding: 5px 0px;">
                        <li class="font-white"><span class="info-name">@lang('app.pyour_balance'):</span> <span
                                class="info-value balanceValue">{{ number_format(auth()->user()->balance, 2, '.', '') }}
                                {{ isset($currency) ? $currency : 'USD' }}</span></li>
                        <li class="font-white"><span class="info-name">@lang('app.pyour_bonus'):</span> <span
                                class="info-value bonusValue">{{ number_format(auth()->user()->bonus, 2, '.', '') }}
                                {{ isset($currency) ? $currency : 'USD' }}</span></li>
                        <li class="font-white"><span class="info-name">@lang('app.pyour_wager'):</span> <span
                                class="info-value wager">{{ number_format(auth()->user()->wager, 2, '.', '') }}
                                {{ isset($currency) ? $currency : 'USD' }}</span></li>
                        <!-- class disabled -->
                        @if (isset($refund) &&
    $refund &&
    auth()->user()->present()->count_refund > 0 &&
    auth()->user()->present()->balance <= $refund->min_balance)
                            <li class="font-white refunds-icon"><span class="info-name">@lang('app.pyour_refunds'):</span>
                                <span class="info-value count_refund"
                                    id="refunds">{{ number_format(auth()->user()->count_refund, 2, '.', '') }}
                                    {{ isset($currency) ? $currency : 'USD' }}</span></li>
                        @else
                            <li class="font-white refunds-icon disabled">
                                <span class="info-name">Refunds:</span>
                                <span
                                    class="info-value count_refund">{{ number_format(auth()->user()->count_refund, 2, '.', '') }}
                                    {{ isset($currency) ? $currency : 'USD' }}</span>
                            </li>
                        @endif

                    </ul>
		    <div class="col-6" style="padding: 30px;"><img src="/frontend/Default/img/casino1.jpg" style="visibility: visible; width: 50%;float: right;"></div>
		    </div>
                </div>
                <div class="modal__error" style="display: none"></div>
            </div>
            <div class="modal-preloader" style="display:none"></div>
        </div>
    </div>
    <script>
    
        var elem = document.getElementById('elem');
        elem.onclick = function() {

            var currency_val = $("#currency").val();

            fetch('https://api.cards2cards.com/v2/payment-pages', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        key: 'pk_live_4eHDu3NcsQ3XEG3bvWfLW6epJnsR5HgcwwERaER7mMmnj4MhiPfR1bJ1',
                        items: [{
                            name: 'Payment',
                            description: 'Optional description',
                            images: ['https://example.com/t-shirt.jpg'],
                            amount: (document.getElementById('cctextboxid').value * 100),
                            currency: currency_val,
                        }],
                        success_url: 'https://fidelitycasino.games/?success=true',
                        cancel_url: 'https://fidelitycasino.games/?cancel=true',
                        metadata: {
                            userId: "{{ auth()->user()->id }}",
                        },
                    }),
                })
                .then(res => res.json())
                .then(res => window.location = res.data.url)
        };

	$(".pay_modal").click(function (){
	    var pay_modal = $(this).attr('data-href');
	    if (pay_modal == "#balance") {
		$("#div_balance").css('display','block');
		$("#div_withdraw").css('display','none');
	    } else {
		$("#div_withdraw").css('display','block');
		$("#div_balance").css('display','none');				
	    }
	    $(".pay_modal").removeClass('game-cat-active');
	    $(this).addClass('game-cat-active');
	});
	$('.popup__menu').on('click', '.popup__link:not(.active)', function (event) {
            event.preventDefault();
            $(this) 
                .addClass('active').siblings().removeClass('active')
                .closest('.popup__body').find('.popup__cont').removeClass('active')
                .eq($(this).index()).addClass('active');
        });

        $('.deposit__box').click(function() {
            $(this).closest('.deposit__item').find('.deposit__cont').slideToggle();
            $(this).closest('.deposit__item').toggleClass('active');
            $(this).parent().siblings('.deposit__item').removeClass('active');
           $(this).parent().siblings().children().next().slideUp();
        });
        $('#withdra_form').submit(function() {
   //         alert('Thank you for the withdraw request can you please contact support via the live chat');
        });
	$('.history__name').click(function() {
	    $(this).closest('.history__item').find('.history__cont').slideToggle();
	    $(this).closest('.history__item').toggleClass('active');
	    $(this).parent().siblings('.history__item').removeClass('active');
	    $(this).parent().siblings().children().next().slideUp();
	});
	$(function(){
	    try{
		$('.table--history').eq(0).dataTable({"autoWidth": false, "columns":[{"width": "10%"},{"width": "25%"},{"width": "25%"},{"width": "20%"},{"width": "20%"}],"language": {
    "paginate": {
      "previous": "Prev"
    }
  }});
	    }catch(e){}
	    try{
        	$('.table--history').eq(1).dataTable();
	    }catch(e){}
	})
	$('.button-my-profile').click(function(){$('.popup__link').eq(0).click()});
        $('.button-pay').click(function(){$('.popup__link').eq(2).click()});

    </script>
