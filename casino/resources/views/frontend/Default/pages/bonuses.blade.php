@extends('frontend.Default.layouts.app')
@section('page-title', 'Bonus')

@section('add-main-class', 'main-pt')

@section('content')


	@php
        if(Auth::check()){
            $currency = auth()->user()->present()->shop ? auth()->user()->present()->shop->currency : '';
        } else{
            $currency = '';
        }
	@endphp

    @include('frontend.Default.partials.header')

    <div class="bonus-page">
        <div class="container">

            @foreach($bonuses AS $item)

                @if( $item['is_first'] )
                    @if($item['type'] == 'happyhour')
                        <div class="bonus-page__banner bonus-banner" style="background-image: url('/frontend/Default/img/_src/banner-bg.png')">
                            <div class="bonus-banner__text">
                                <span class="bonus-banner__label">Happy Hour</span>
                                <h2 class="bonus-banner__title">
                                    <span class="accent">Bonus up to $ </span>
                                    Multiplier {{ $item['data']->multiplier }} <br>
                                    Wager {{ $item['data']->wager }} <br>
                                    Time {{ \VanguardLTE\HappyHour::$values['time'][$item['data']->time] }}
                                </h2>
                            </div>
                            <a href="{{ route('frontend.bonus.conditions') }}" class="bonus-banner__icon">
                                <svg xmlns="http://www.w3.org/2000/svg"   viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="8"></line></svg>
                            </a>
                        </div>
                    @elseif($item['type'] == 'progress')
                        <div class="bonus-page__banner bonus-banner" style="background-image: url('/frontend/Default/img/_src/banner-bg.png')">
                            <div class="bonus-banner__text">
                                <span class="bonus-banner__label">Progress Bonus</span>
                                <h2 class="bonus-banner__title">
                                    <span class="accent">Bonus up to $ {{ $item['data']->bonus }}</span>
                                    Bonus {{ $item['data']->bonus }} {{ $currency }} x{{ $item['data']->wager }}
                                    Payment Sum {{ __('app.' . $item['data']->type) }} {{ $item['data']->sum }}{{ $currency }}
                                    Spins {{ $item['data']->spins }} <br>
                                    Bet {{ $item['data']->bet }} <br>
                                    Rating {{ $item['data']->rating }} <br>
                                    Day {{ $item['data']->day }} <br>
                                    Percent {{ $item['data']->percent }} <br>
                                    Min Balance {{ $item['data']->min_balance }}<br>
                                    Min {{ $item['data']->min }}<br>
                                    Max {{ $item['data']->max }}
                                </h2>
                            </div>
                            <a href="{{ route('frontend.bonus.conditions') }}" class="bonus-banner__icon">
                                <svg xmlns="http://www.w3.org/2000/svg"   viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="8"></line></svg>
                            </a>
                        </div>
                    @elseif($item['type'] == 'invite')
                        <div class="bonus-page__banner bonus-banner" style="background-image: url('/frontend/Default/img/_src/banner-bg.png')">
                            <div class="bonus-banner__text">
                                <span class="bonus-banner__label">Invite Friends</span>
                                <h2 class="bonus-banner__title">
                                    <span class="accent">Bonus up to $ {{ $item['data']->sum }}</span>
                                    Bonus {{ $item['data']->sum }} {{ $currency }}
                                    Friend Bonus {{ $item['data']->sum_ref }} {{ $currency }}
                                    Sum {{ $item['data']->min_amount }} {{ $currency }}
                                    x{{ $item['data']->wager }} <br>
                                    Waiting Time {{ $item['data']->waiting_time }} days
                                </h2>
                            </div>
                            <a href="{{ route('frontend.bonus.conditions') }}" class="bonus-banner__icon">
                                <svg xmlns="http://www.w3.org/2000/svg"   viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="8"></line></svg>
                            </a>
                        </div>
                    @elseif($item['type'] == 'sms_bonus')
                        <div class="bonus-page__banner bonus-banner" style="background-image: url('/frontend/Default/img/_src/banner-bg.png')">
                            <div class="bonus-banner__text">
                                <span class="bonus-banner__label">SMS Bonus</span>
                                <h2 class="bonus-banner__title">
                                    <span class="accent">Bonus up to $ </span>
                                    @foreach($item['data'] AS $res)
                                        Days {{ $res->days }}  {{ $res->bonus }} {{ $currency }} x{{ $res->wager }} <br>
                                    @endforeach
                                </h2>
                            </div>
                            <a href="{{ route('frontend.bonus.conditions') }}" class="bonus-banner__icon">
                                <svg xmlns="http://www.w3.org/2000/svg"   viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="8"></line></svg>
                            </a>
                        </div>
                    @elseif($item['type'] == 'welcome_bonus')
                        <div class="bonus-page__banner bonus-banner" style="background-image: url('/frontend/Default/img/_src/banner-bg.png')">
                            <div class="bonus-banner__text">
                                <span class="bonus-banner__label">Welcome Bonus</span>
                                <h2 class="bonus-banner__title">
                                    <span class="accent">Bonus up to {{ $item['data']->bonus }} {{ $currency }}</span>
                                        Minimal amount of deposit is {{ $item['data']->sum }} {{ $currency }} <br />
                                        bonus {{ $item['data']->bonus }} {{ $currency }}
                                </h2>
                            </div>
                            <a href="{{ route('frontend.bonus.conditions') }}" class="bonus-banner__icon">
                                <svg xmlns="http://www.w3.org/2000/svg"   viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="8"></line></svg>
                            </a>
                        </div>
                    @endif
                @endif
            @endforeach

            <div class="bonus-page__cards bonus-cards">

                @foreach($bonuses AS $item)

                    @if( !$item['is_first'] )
                        @if($item['type'] == 'happyhour')
                            <div class="bonus-cards__item">
                                <a href="{{ route('frontend.bonus.conditions') }}" class="bonus-cards__icon">
                                    <svg xmlns="http://www.w3.org/2000/svg"   viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="8"></line></svg>
                                </a>
                                <div class="bonus-cards__img">
                                    <img src="/frontend/Default/img/_src/WelcomeBonus3.png" alt="">
                                </div>
                                <div class="bonus-cards__info">
                                    <span class="bonus-cards__label">Happy Hour</span>
                                    <p class="bonus-cards__title">
                                        <span class="accent">All deposits will be multiplied by {{ $item['data']->multiplier }}</span>
                                        Time {{ \VanguardLTE\HappyHour::$values['time'][$item['data']->time] }}
                                    </p>
                                </div>
                            </div>
                        @elseif($item['type'] == 'progress')
                            <div class="bonus-cards__item">
                                <a href="{{ route('frontend.bonus.conditions') }}" class="bonus-cards__icon">
                                    <svg xmlns="http://www.w3.org/2000/svg"   viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="8"></line></svg>
                                </a>
                                <div class="bonus-cards__img">
                                    <img src="/frontend/Default/img/_src/ProgressBonus.png" alt="">
                                </div>
                                <div class="bonus-cards__info">
                                    <span class="bonus-cards__label">Progress Bonus</span>
                                    <p class="bonus-cards__title">
                                        <span class="accent">Minimal amount of deposit is {{ $item['data']->sum }} {{ $currency }}</span>
                                        Bonus {{ $item['data']->bonus }} {{ $currency }}
                                    </p>
                                </div>
                            </div>
                        @elseif($item['type'] == 'invite')

                            <div class="bonus-cards__item">
                                <a href="{{ route('frontend.bonus.conditions') }}" class="bonus-cards__icon">
                                    <svg xmlns="http://www.w3.org/2000/svg"   viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="8"></line></svg>
                                </a>
                                <div class="bonus-cards__img">
                                    <img src="/frontend/Default/img/_src/InviteFriends.png" alt="">
                                </div>
                                <div class="bonus-cards__info">
                                    <span class="bonus-cards__label">Invite Friends</span>
                                    <p class="bonus-cards__title">
                                        <span class="accent">For each friend get a bonus {{ $item['data']->sum }} {{ $currency }}</span>
                                        A friend will receive a bonus of {{ $item['data']->sum_ref }} {{ $currency }}
                                    </p>
                                </div>
                            </div>


                        @elseif($item['type'] == 'sms_bonus')
                            <div class="bonus-cards__item">
                                <a href="{{ route('frontend.bonus.conditions') }}" class="bonus-cards__icon">
                                    <svg xmlns="http://www.w3.org/2000/svg"   viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="8"></line></svg>
                                </a>
                                <div class="bonus-cards__img">
                                    <img src="/frontend/Default/img/_src/SMSBonus.png" alt="">
                                </div>
                                <div class="bonus-cards__info">
                                    <span class="bonus-cards__label">SMS Bonus</span>
                                    <p class="bonus-cards__title">
                                        <span class="accent">If you forgot about us for 5 days</span>
                                        We will send you a bonus 5 {{ $currency }}
                                    </p>
                                </div>
                            </div>

                        @elseif($item['type'] == 'welcome_bonus')

                            <div class="bonus-cards__item">
                                <a href="{{ route('frontend.bonus.conditions') }}" class="bonus-cards__icon">
                                    <svg xmlns="http://www.w3.org/2000/svg"   viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="8"></line></svg>
                                </a>
                                <div class="bonus-cards__img">
                                    <img src="/frontend/Default/img/_src/WelcomeBonus{{ $item['data']->pay }}.png" alt="">
                                </div>
                                <div class="bonus-cards__info">
                                    <span class="bonus-cards__label">Welcome Bonus</span>
                                    <p class="bonus-cards__title">
                                        <span class="accent">Minimal amount of deposit is {{ $item['data']->sum }} {{ $currency }} </span>
                                        Bonus {{ $item['data']->bonus }} {{ $currency }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    @endif
                @endforeach

            </div>
        </div>
    </div>

@endsection

@section('footer')
	@include('frontend.Default.partials.footer')
@endsection

@section('scripts')
	@include('frontend.Default.partials.scripts')
@endsection
