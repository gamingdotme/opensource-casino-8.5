<footer class="footer">
    <div class="container">
        <div class="footer__block">
            <div class="footer__item footer__item--left">
                <div class="footer__item-acc">
                    <div class="footer__item-acc-img">
                        <a href="javascript:;" data-name="modal-info" class="modal-btn">
                            <!-- <img src="/frontend/Default/img/badges64x64/badge-{{ auth()->user()->badge() }}.png" class="rating" > -->
                            <img src="/back/img/1.png" class="rating" >
                        </a>
                        <div class="footer__item-acc-rating">{{ auth()->user()->username }}</div>
						<a href="javascript:;" data-name="modal-edit-profile" class="modal-btn footer__item-acc-rating">
							Edit Profile
                        </a>
                    </div>
					
                    <ul class="footer__item-acc-info">
                        <li><span class="info-name">Balance:</span> <span class="info-value balanceValue">{{ number_format(auth()->user()->balance, 2,".","") }} {{ $currency }}</span></li>
                        <li><span class="info-name">Bonus:</span> <span class="info-value bonusValue">{{ number_format(auth()->user()->bonus, 2,".","") }} {{ $currency }}</span></li>
                        <li><span class="info-name">Wager:</span> <span class="info-value wager">{{ number_format(auth()->user()->wager, 2,".","") }} {{ $currency }}</span></li>
                        <!-- class disabled -->
                        @if ( isset($refund) && $refund && auth()->user()->present()->count_refund > 0 && auth()->user()->present()->balance <= $refund->min_balance )
                            <li class="refunds-icon"><span class="info-name">Refunds:</span> <span class="info-value count_refund" id="refunds">{{ number_format(auth()->user()->count_refund, 2,".","") }} {{ $currency }}</span></li>
                        @else
                            <li class="refunds-icon disabled">
                                <span class="info-name">Refunds:</span>
                                <span class="info-value count_refund" >{{ number_format(auth()->user()->count_refund, 2,".","") }} {{ $currency }}</span>
                            </li>
                        @endif

                    </ul>
                </div>
            </div>
			
            <div class="footer__item">
                <div class="footer__item-tabs">
					{{-- <a href="{{route('frontend.pincode.list')}}" class="footer__item-tab">
                        <div class="footer__item-tab-img">
                            <img src="/frontend/Default/img/svg/kassa.svg" alt="">
                        </div>
                        <span class="footer__item-tab-title">Pincodes</span>
                    </a> --}}

                    <a href="javascript:;" data-name="modal-kassa" class="footer__item-tab modal-btn">
                        <div class="footer__item-tab-img">
                            <img src="/frontend/Default/img/svg/kassa.svg" alt="">
                        </div>
                        <span class="footer__item-tab-title">Add Credit</span>
                    </a>
					
                    <a href="javascript:;"
                       @if( auth()->user()->phone_verified )
                           @if(auth()->user()->agreed)
                                data-name="modal-invite-1"
                           @else(!auth()->user()->agreed)
                                data-name="modal-invite"
                           @endif
                       @else
                            data-name="modal-invite-3"
                       @endif
                       class="footer__item-tab modal-btn">
                        <div class="footer__item-tab-img">
                            <img src="/frontend/Default/img/svg/sms.svg" alt="">
                        </div>
                        <span class="footer__item-tab-title">Invite Friends</span>
                    </a>
					
                    <a href="javascript:;" data-name="modal-loot" class="footer__item-tab modal-btn">
                        <div class="footer__item-tab-img">
                            <img src="/frontend/Default/img/svg/coleso.svg" alt="">
                        </div>
                        <span class="footer__item-tab-title">Loot Boxes</span>
                    </a>
                    <a href="javascript:;" data-name="modal-bonus" class="footer__item-tab modal-btn">
                        <div class="footer__item-tab-img">
                            <img src="/frontend/Default/img/_src/lootbox.png" alt="">
                        </div>
                        <span class="footer__item-tab-title">Pay and Play</span>
                    </a>
                </div>
            </div>

            <div class="footer__item footer__item--right">
                <div class="footer__item-search">
                    <span class="search-wrap"><input type="text" placeholder="Search Game" class="search"></span>
                </div>
                <a href="{{ route('frontend.auth.logout') }}" class="btn btn--logout">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 22">
                        <path d="M8,21H3a2,2,0,0,1-2-2V3A2,2,0,0,1,3,1H8"/>
                        <polyline points="15 15 19 11 15 7"/><line x1="19" y1="11" x2="7" y2="11"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</footer>

<div class="overlay"></div>

<!-- MODAL-EDIT PROFILE -->
<div class="modal modal-edit-profile">
    <div class="modal__body">
        <div class="modal__content">
            <h3 class="modal__title">Edit Profile</h3>
            <div class="modal__table">
				{!! Form::open(['route' => ['frontend.profile.update.details'], 'method' => 'POST', 'id' => 'details-form']) !!}
                <div class="modal__table-wrap"  data-simplebar2 id="myEl">
					<div class="footer__item-acc">
						<div class="footer__item-acc-img">
							<img src="/back/img/1.png" class="rating" >
							<div class="footer__item-acc-rating">{{ auth()->user()->username }}</div>
						</div>
						
						<ul class="footer__item-acc-info">
							<li><span class="info-name">Balance:</span> <span class="info-value balanceValue">{{ number_format(auth()->user()->balance, 2,".","") }} {{ $currency }}</span></li>
							<li><span class="info-name">Bonus:</span> <span class="info-value bonusValue">{{ number_format(auth()->user()->bonus, 2,".","") }} {{ $currency }}</span></li>
							<li><span class="info-name">Wager:</span> <span class="info-value wager">{{ number_format(auth()->user()->wager, 2,".","") }} {{ $currency }}</span></li>
							<!-- class disabled -->
							@if ( isset($refund) && $refund && auth()->user()->present()->count_refund > 0 && auth()->user()->present()->balance <= $refund->min_balance )
								<li class="refunds-icon"><span class="info-name">Refunds:</span> <span class="info-value count_refund" id="refunds">{{ number_format(auth()->user()->count_refund, 2,".","") }} {{ $currency }}</span></li>
							@else
								<li class="refunds-icon disabled">
									<span class="info-name">Refunds:</span>
									<span class="info-value count_refund" >{{ number_format(auth()->user()->count_refund, 2,".","") }} {{ $currency }}</span>
								</li>
							@endif

						</ul>

						<button type="submit" class="btn btn-sm btn-primary" style="padding: 0px;line-height: 0px;height: 40px;width: 78px;">Buy Pins</button>					
					</div>
					<br><hr><br>
					<div class="box-body box-profile">

						<div class="modal__kassa-row">
							<label>@lang('app.username')</label>
							<input type="text" class="form-control" disabled readonly placeholder="(@lang('app.optional'))" value="{{ auth()->user()->username }}">
						</div><br>

						@if( auth()->user()->email != '' )
						<div class="modal__kassa-row">
							<label>@lang('app.email')</label>
							<input type="email" class="form-control" disabled readonly placeholder="(@lang('app.optional'))" value="{{ auth()->user()->email }}">
						</div> <br>
						@endif
						
						<?php 
						
						$langs = [];
						foreach( glob(resource_path() . '/lang/*', GLOB_ONLYDIR) as $fileinfo )
						{
							$dirname = basename($fileinfo);
							$langs[$dirname] = $dirname;
						}
						?>
						<div class="modal__kassa-row">
							<label>@lang('app.lang')</label>
							{!! Form::select('language', $langs, auth()->user()->language, ['class' => 'form-control']) !!}
						</div> <br>

						<div class="modal__kassa-row">
							<label>{{ trans('app.password') }}</label>
							<input type="password" class="form-control" id="old_password" name="old_password" placeholder="@lang('app.leave_blank_if_you_dont_want_to_change')" autocomplete="off" autosuggestion="off">
						</div> <br>
						
						<div class="modal__kassa-row">
							<label>{{ trans('app.new_password') }}</label>
							<input type="password" class="form-control" id="password" name="password" placeholder="@lang('app.leave_blank_if_you_dont_want_to_change')" autocomplete="off">
						</div> <br>

						<div class="modal__kassa-row">
							<label>{{ trans('app.confirm_new_password') }}</label>
							<input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="@lang('app.leave_blank_if_you_dont_want_to_change')" autocomplete="off">
						</div>
					</div>
					<div>
						<button type="submit" class="btn btn-primary" id="update-details-btn">
							@lang('app.update_profile')
						</button>
					</div>
                </div>
				{!! Form::close() !!}
            </div>
            <span class="close-btn">
				<img src="/frontend/Default/img/_src/close.svg" alt="">
			</span>
        </div>
    </div>
</div>
<!-- MODAL-EDIT PROFILE END-->

<!-- MODAL-BONUS -->
<div class="modal modal-bonus modal-pin">
    <div class="modal__body">
        <div class="modal__content">
            <div class="modal__invite">
                <h3 class="modal__title">Please Enter your BTC or preferred Crypto Payment</h3>
                <p class="modal__text">YOu can pay for Play credits via your preferred Crypto Currencies:
Please pay usign the form below!</p>
                <button class="btn modal-close">Close</button>
            </div>
            <span class="close-btn">
					<img src="/frontend/Default/img/_src/close.svg" alt="">
				</span>
        </div>
    </div>
</div>
<!-- MODAL-BONUS END -->

<!-- MODAL-LOOT -->
<div class="modal modal-loot">
    <div class="modal__body">
        <div class="modal__content">
            <h3 class="modal__title">My lootboxes</h3>
            <span class="modal__subtitle">Availables lootboxes</span>
            <div class="modal-slider-loot">
                <div class="modal__slider">
                    <div class="modal__slider-slide">
                        <div class="modal__slider-item">
                            <div class="modal__slider-row">
                                <div class="modal__slider-img">
                                    <img src="/frontend/Default/img/badges64x64/badge-01.png" alt="">
                                    <span class="modal__slider-text">+1 Box Cammon</span>
                                </div>
                                <a href="javascript:;" class="btn">Open</a>
                            </div>
                        </div>
                        
                        <div class="modal__slider-item">
                            <div class="modal__slider-row">
                                <div class="modal__slider-img">
                                    <img src="/frontend/Default/img/badges64x64/badge-01.png" alt="">
                                    <span class="modal__slider-text">+1 Box Cammon</span>
                                </div>
                                <a href="javascript:;" class="btn">Open</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <span class="close-btn">
					<img src="/frontend/Default/img/_src/close.svg" alt="">
				</span>
        </div>
    </div>
</div>
<!-- MODAL-LOOT END -->



<!-- MODAL-KASSA -->
<div class="modal modal-kassa">
    <div class="modal__body">
        <div class="modal__content">
            <h3 class="modal__title">Enter the Purchased PIN Code to add credit!</h3>
            <div class="modal__kassa">

                <div class="modal__kassa-row">
                    @if(
                    setting('payment_interkassa') &&
                    !(
                        !settings('interkassa_shop_id_shop_' . auth()->user()->shop_id)
                        ||
                        !settings('interkassa_token_shop_' . auth()->user()->shop_id)
                    )
                    )

                        @php $interkassa = \VanguardLTE\Lib\Interkassa::get_systems(auth()->user()->id, auth()->user()->shop_id); @endphp

                        @if( isset($interkassa['success']) && count($interkassa['systems']) )
                            @foreach($interkassa['systems'] AS $system)

                                {!! Form::open(['route' => 'frontend.balance.post', 'method' => 'POST']) !!}
                                <div class="modal__kassa-row-item">
                                    <div class="modal__kassa-row-img">
                                        <img src="/frontend/Default/img/_src/logo-{{ $system['ps'] }}.png" alt="">
                                    </div>
                                    <div class="modal__kassa-row-input">
                                        <input type="text" placeholder="Enter the amount" name="summ">
                                    </div>
                                    <div class="modal__kassa-row-btn">
                                        <input type="hidden" name="system" value="interkassa_{{ $system['als'] }}">
                                        <button class="btn">Pay</button>
                                    </div>
                                </div>
                                {!! Form::close() !!}

                            @endforeach
                        @endif

                    @endif
                    @if(
                        setting('payment_coinbase') &&
                        !(
                           !settings('coinbase_api_key_shop_' . auth()->user()->shop_id)
                            ||
                            !settings('coinbase_webhook_key_shop_' . auth()->user()->shop_id)
                        )
                    )
                        {!! Form::open(['route' => 'frontend.balance.post', 'method' => 'POST']) !!}
                        <div class="modal__kassa-row-item">
                            <div class="modal__kassa-row-img">
                                <img src="/frontend/Default/img/_src/logo-kassa-2.png" alt="">
                            </div>
                            <div class="modal__kassa-row-input">
                                <input type="text" placeholder="Enter the amount" name="summ">
                            </div>
                            <div class="modal__kassa-row-btn">
                                <input type="hidden" name="system" value="coinbase">
                                <button class="btn">Pay</button>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    @endif
                    @if(
                        setting('payment_btcpayserver') &&
                        !(
                            !settings('btcpayserver_server_shop_' . auth()->user()->shop_id)
                            ||
                            !settings('btcpayserver_store_id_shop_' . auth()->user()->shop_id)
                        )
                    )

                        {!! Form::open(['route' => 'frontend.balance.post', 'method' => 'POST']) !!}
                        <div class="modal__kassa-row-item">
                            <div class="modal__kassa-row-img">
                                <img src="/frontend/Default/img/_src/logo-kassa-3.png" alt="">
                            </div>
                            <div class="modal__kassa-row-input">
                                <input type="text" placeholder="Enter the amount" name="summ">
                            </div>
                            <div class="modal__kassa-row-btn">
                                <input type="hidden" name="system" value="btcpayserver">
                                <button class="btn">Pay</button>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    @endif
                    @if( setting('payment_pin') )
                        <div class="modal__kassa-row-item">
                            <div class="modal__kassa-row-img">
                                <img src="/frontend/Default/img/_src/logo-kassa-4.png" alt="">
                            </div>
                            <div class="modal__kassa-row-input">
                                <input type="text" placeholder="Enter the amount" id="inputPin">
                            </div>
                            <div class="modal__kassa-row-btn">
                                <a href="javascript:;" class="btn" id="send">Enter PIN</a>
                            </div>
                        </div>
                    @endif
					&nbsp;
					<table class="table table-bordered">
                        <thead>
                        <tr>
                            <td width="15%">#</td>
                            <td>Pincode</td>
                            <td>Total for this PIN code!</td>
                        </tr>
                        </thead>
                        <tbody>
						@php $pincodes = \VanguardLTE\Pincode::where(['user_id' => auth()->user()->id])->get(); @endphp
                        @if(!$pincodes->isEmpty())
							@php $i=1; @endphp
                            @foreach($pincodes AS $item)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $item->code }}</td>
                                    <td>{{ $item->nominal }}</td>  
                                </tr>
                            @endforeach
						@else 
							<tr>
								<td colspan=3>Please Purchase a PIN code!</td>
							</tr>
                        @endif 
                        </tbody>
                    </table>

					
                </div>

            </div>
            <span class="close-btn">
					<img src="/frontend/Default/img/_src/close.svg" alt="">
				</span>
        </div>
    </div>
</div>
<!-- MODAL-KASSA END -->

<!-- MODAL-INVITE -->
<div class="modal modal-invite modal-pin">
    <div class="modal__body">
        <div class="modal__content">
            <div class="modal__invite">
                <h3 class="modal__title">Invite friends</h3>
                <span class="modal__subtitle">You paraticipate in a referal program</span>
                <p class="modal__text">
                    Welcome to the all-new {{ config('app.name') }} Referral Reward program!
                    <br>
                    <br>
                    Join the {{ config('app.name') }} community of over 10.000 players who share a passion for fun games,
                    spectacular HD graphics and promotional offers!
                    <br>
                    <br>
                    It’s easy: Invite your Friends and earn UNLIMITED Bonus Play! The {{ config('app.name') }} Referral Rewards
                    program wars designed with YOU in mind. Earn as much as you can for free by welcoming new players to the {{ config('app.name') }} community. Every referral earns you ${{ isset($invite) && $invite->sum }} in Bonus Rewards!
                    Plus, every person you invite gets a free ${{ isset($invite) && $invite->sum_ref }} Bonus when they use purchase code for first-time deposit of ${{ isset($invite) && $invite->min_amount }}!
                    <br>
                    <br>
                    We kept things simple for the best possible experience for you and those you refer.
                    You'll love the ability to privately track your Bonuses in your account directly from
                    your phone! Let it bulld up or play whenever you like!
                    <br>
                    <br>
                    {{ config('app.name') }} REFERRAL REWARDS - The Gateway to Unlimited Bonus Play!
                    It's that simple Play. Invite. Earn. Repeat!
                </p>
                <a href="{{ route('frontend.profile.agree') }}" class="btn modal-close">Ok</a>
            </div>
            <span class="close-btn">
					<img src="/frontend/Default/img/_src/close.svg" alt="">
				</span>
        </div>
    </div>
</div>
<!-- MODAL-INVITE END -->

<!-- MODAL-INFO -->
<div class="modal modal-info">
    <div class="modal__body">
        <div class="modal__content">
            <h3 class="modal__title">Your Stats</h3>
            <div class="modal__table">
                <div class="modal__table-wrap"  data-simplebar2 id="myEl">
                    <table class="table">
                        <thead>
                        <tr>
                            <td></td>
                            <td>Sum</td>
                            <td>Type</td>
                            <td>Spins</td>
                            <td>Bet</td>
                        </tr>
                        </thead>
                        <tbody>
                        @php $progress = \VanguardLTE\Progress::where('shop_id', auth()->user()->shop_id)->orderBy('rating')->get() @endphp
                        @if($progress)
                            @foreach($progress AS $item)
                                <tr>
                                    <td class="big-td"><img src="/frontend/Default/img/badges64x64/badge-{{ $item->badge() }}.png" alt="">Range #{{ $item->rating }}</td>
                                    <td>{{ $item->sum }}</td>
                                    <td>@if( $item->type == 'sum_pay' ) Pay Sum @else One Pay @endif</td>
                                    <td>{{ $item->spins }}</td>
                                    <td>{{ $item->bet }}</td>
                                </tr>
                            @endforeach
                        @endif



                        </tbody>
                    </table>
                </div>

            </div>
            <span class="close-btn">
					<img src="/frontend/Default/img/_src/close.svg" alt="">
				</span>
        </div>
    </div>
</div>
<!-- MODAL-INFO END-->

<!-- MODAL-INVITE-1 -->
<div class="modal modal-invite-1 modal-pin">
    <div class="modal__body">
        <div class="modal__content">
            <h3 class="modal__title">Invite friends</h3>
            <div class="modal__invite-block">
                <div class="modal__invite-item">

                    @if( count(auth()->user()->rewards()))

                        <div class="modal__invite-title">
                            Invited friends
                        </div>
                        <div class="modal__invite-phones">
                            @foreach(auth()->user()->rewards() AS $reward)
                                @if($reward->referral)
                                <div class="modal__invite-row" id="reward{{ $reward->id }}">
                                    <div class="modal__invite-info">
                                        <div class="modal__invite-date">{{ $reward->created_at->format(config('app.date_format')) }}</div>
                                        <span class="modal__invite-valid">Until {{ \Carbon\Carbon::parse($reward->until)->format(config('app.date_format')) }}</span>
                                        <div class="modal__invite-phones-value">
                                            @if( auth()->user()->id == $reward->user_id )
                                                {{ $reward->referral->formatted_phone() }}
                                            @else
                                                My Bonus
                                            @endif
                                                <span class="error-message"></span>
                                        </div>
                                    </div>
                                    @if( $reward->activated )
                                    <div class="modal__invite-phones-btn">
                                        <a href="javascript:;" class="btn take_reward" data-id="{{ $reward->id }}">Take bonus</a>
                                    </div>
                                    @endif
                                </div>
                                @endif
                            @endforeach
                        </div>
                    @else



                        <div class="modal__invite-title">
                            You have no invitees yet
                        </div>
                        <div class="modal__invite-subtitle">
                            Enter the phone and  invite a friends
                        </div>
                        <div class="modal__invite-place" >
                        </div>

                    @endif
                </div>
                <div class="modal__invite-item">
                    <div class="modal__invite-label">
                        Enter phone number
                    </div>
                    <div class="modal__invite-input">
                        <input type="text" class="loginInput " autocomplete='off' id="inputPhone">
                        <span class="error-message"></span>
                    </div>
                    <div class="modal__invite-pin">
                        <form name='PINform' class="form-pin">
                            <input type='button' class='PINbutton' name='1' value='1'/>
                            <input type='button' class='PINbutton' name='2' value='2'/>
                            <input type='button' class='PINbutton' name='3' value='3'/>
                            <input type='button' class='PINbutton' name='4' value='4'/>
                            <input type='button' class='PINbutton' name='5' value='5'/>
                            <input type='button' class='PINbutton' name='6' value='6'/>
                            <input type='button' class='PINbutton' name='7' value='7'/>
                            <input type='button' class='PINbutton' name='8' value='8'/>
                            <input type='button' class='PINbutton' name='9' value='9'/>
                            <input type='button' class='buttonClear clear' value='C'  onClick=clearForm(this); />
                            <input type='button' class='PINbutton' name='0' value='0' />
                            <input type='button' class='buttonClear backspace' value='X' onClick=backspace(this); />
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal__invite-btn">
                <a href="javascript:;" class="btn" id="sendPhone">Invite</a>
            </div>
            <span class="close-btn">
					<img src="/frontend/Default/img/_src/close.svg" alt="">
				</span>
        </div>
    </div>
</div>
<!-- MODAL-INVITE-1 END -->

<!-- MODAL-INVITE-2 -->
<div class="modal modal-invite-2 modal-pin">
    <div class="modal__body">
        <div class="modal__content">
            <h3 class="modal__title">Invite friends</h3>
            <div class="modal__invite-block">
                <div class="modal__invite-item">
                    <div class="modal__invite-title">
                        Bonus Conditions
                    </div>
                    <div class="modal__invite-subtitle">
                        Enter the phone and  invite a friend
                    </div>
                    <p class="modal__invite-text">
                        Let's have some fun!
                        <br>
                        <br>
                        Here are a few helpful tips:
                        <br>
                        <br>
                        After confirming your own valid mobile phone number, proceed on to the next easy steps
                        <br>
                        <br>
                        * Invite Friends you know and trust. They must be 18 years or older to participate.
                        * Monitor your Referral Reward Bonuses on your device to see when those you refer complete the easy steps in their test message invite.
                        <br>
                        <br>
                        It's that simple Play. Invite. Earn. Repeat!
                    </p>
                </div>
                <div class="modal__invite-item">
                    <div class="modal__invite-label">
                        Enter verification code on SMS
                    </div>
                    <div class="modal__invite-input">
                        <input type="text" class="loginInput bonus-input" placeholder="X  X  X  X"  autocomplete='off' id="myCode">
                        <span class="error-message"></span>
                    </div>
                    <div class="modal__invite-pin">
                        <form name='PINform' class="form-pin">
                            <input type='button' class='PINbutton' name='1' value='1'/>
                            <input type='button' class='PINbutton' name='2' value='2'/>
                            <input type='button' class='PINbutton' name='3' value='3'/>
                            <input type='button' class='PINbutton' name='4' value='4'/>
                            <input type='button' class='PINbutton' name='5' value='5'/>
                            <input type='button' class='PINbutton' name='6' value='6'/>
                            <input type='button' class='PINbutton' name='7' value='7'/>
                            <input type='button' class='PINbutton' name='8' value='8'/>
                            <input type='button' class='PINbutton' name='9' value='9'/>
                            <input type='button' class='buttonClear clear' value='C'  onClick=clearForm(this); />
                            <input type='button' class='PINbutton' name='0' value='0' />
                            <input type='button' class='buttonClear backspace' value='X' onClick=backspace(this); />
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal__invite-btn">
                <a href="javascript:;" class="btn" id="ckeckCode">Send code</a>
            </div>
            <span class="close-btn">
					<img src="/frontend/Default/img/_src/close.svg" alt="">
				</span>
        </div>
    </div>
</div>
<!-- MODAL-INVITE-2 END-->


<!-- MODAL-INVITE-3 -->
<div class="modal modal-invite-3 modal-pin">
    <div class="modal__body">
        <div class="modal__content">
            <h3 class="modal__title">Invite friends</h3>
            <div class="modal__invite-block">
                <div class="modal__invite-item">
                    <div class="modal__invite-title">
                        Bonus Conditions
                    </div>
                    <p class="modal__invite-text">
                        Let's have some fun!
                        <br>
                        <br>
                        Here are a few helpful tips:
                        <br>
                        <br>
                        After confirming your own valid mobile phone number, proceed on to the next easy steps
                        <br>
                        <br>
                        * Invite Friends you know and trust. They must be 18 years or older to participate.
                        * Monitor your Referral Reward Bonuses on your device to see when those you refer complete the easy steps in their test message invite.
                        <br>
                        <br>
                        It's that simple Play. Invite. Earn. Repeat!
                    </p>
                </div>
                <div class="modal__invite-item">
                    <div class="modal__invite-label">
                        Enter phone number
                    </div>
                    <div class="modal__invite-input">
                        <input type="text" class="loginInput " autocomplete='off' id="myPhone">
                        <span class="error-message"></span>
                    </div>
                    <div class="modal__invite-pin">
                        <form name='PINform' class="form-pin">
                            <input type='button' class='PINbutton' name='1' value='1'/>
                            <input type='button' class='PINbutton' name='2' value='2'/>
                            <input type='button' class='PINbutton' name='3' value='3'/>
                            <input type='button' class='PINbutton' name='4' value='4'/>
                            <input type='button' class='PINbutton' name='5' value='5'/>
                            <input type='button' class='PINbutton' name='6' value='6'/>
                            <input type='button' class='PINbutton' name='7' value='7'/>
                            <input type='button' class='PINbutton' name='8' value='8'/>
                            <input type='button' class='PINbutton' name='9' value='9'/>
                            <input type='button' class='buttonClear clear' value='C'  onClick=clearForm(this); />
                            <input type='button' class='PINbutton' name='0' value='0' />
                            <input type='button' class='buttonClear backspace' value='X' onClick=backspace(this); />
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal__invite-btn">
                <a href="javascript:;" class="btn" id="verifyMyPhone">Send code</a>
            </div>
            <span class="close-btn">
					<img src="/frontend/Default/img/_src/close.svg" alt="">
				</span>
        </div>
    </div>
</div>
<!-- MODAL-INVITE-3 END-->

<!-- POPUPS -->

<div class="popup popup-1">
    <div class="popup__body">
        <div class="popup__content" style="background-image: url('/frontend/Default/img/_src/popup-bg-1.png')">
            <div class="popup__value" data-attr="$8">$8</div>
            <div class="popup__info">
                <div class="popup__title">
                    Congratulations!
                    <span>You got to balance</span>
                </div>
                <a href="javascript:;" class="btn popup-btn">Ok</a>
            </div>
            <span class="close-btn">
					<img src="/frontend/Default/img/_src/close.svg" alt="">
				</span>
        </div>
    </div>
</div>

<div class="popup popup-2">
    <div class="popup__body">
        <div class="popup__content">
            <div class="popup__bg" style="background-image: url('/frontend/Default/img/_src/popup-bg-2.png')"></div>
            <div class="popup__value">
                <img src="/frontend/Default/img/badges320x320/badge-15.png" alt="">
            </div>
            <div class="popup__info">
                <div class="popup__title">
                    Congratulations!
                    <span>You hotel was awarded a news star</span>
                </div>
                <div class="popup__prize">
                    You prize: <span>LootBox Bare</span>
                </div>
                <a href="javascript:;" class="btn popup-btn">Got it</a>
            </div>
            <span class="close-btn">
					<img src="/frontend/Default/img/_src/close.svg" alt="">
				</span>
        </div>
    </div>
</div>
<!-- /.MAIN -->


<div class="modal modal-notifications">
    <div class="modal__body">
        <div class="modal__content">
            <h3 class="modal__title">Notifications</h3>
            <div class="modal__notifications-block">
                @if(isset ($errors) && count($errors) > 0)
                    <div class="alert alert-danger">
                        <h4>@lang('app.error')</h4>
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif
                @if(Session::get('success', false))
                    <?php $data = Session::get('success'); ?>
                    @if (is_array($data))
                        @foreach ($data as $msg)
                            <div class="alert alert-success">
                                <h4>@lang('app.success')</h4>
                                <p>{{ $msg }}</p>
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-success">
                            <h4>@lang('app.success')</h4>
                            <p>{{ $data }}</p>
                        </div>
                    @endif
                @endif
            </div>
            <span class="close-btn">
					<img src="/frontend/Default/img/_src/close.svg" alt="">
				</span>
        </div>
    </div>
</div>





<!-- /.MAIN -->