@extends('frontend.Default.layouts.app')
@section('page-title', 'Bonus conditions')

@section('content')

    @php
        if(Auth::check()){
            $currency = auth()->user()->present()->shop ? auth()->user()->present()->shop->currency : '';
        } else{
            $currency = '';
        }
    @endphp

    @include('frontend.Default.partials.header')

    <div class="bonus-conditions">
        <div class="container">
            <h1 class="bonus-conditions__title">
                <span class="accent">Bonus</span>
                conditions
            </h1>

            <div class="bonus-conditions__info">
                <div class="bonus-conditions__info-row">
                    <p class="text">
                        Each of the listed bonuses is available in all countries of the world with the exception of the following: Bulgaria, Bosnia and Herzegovina, Hungary, Greece, Georgia, Indonesia, Kazakhstan, Macedonia, Moldova, Serbia, Slovakia, Sierra Leone, Philippines, Croatia, Sweden.
                    </p>
                    <p class="text">
                        All terms and dates of bonuses and promotions are indicated in UTC.
                    </p>
                    <p class="text">
                        It is forbidden to use bonus funds exclusively for passing bonus stages. For example, when the bonus or cash is only used to go through the bonus stages (for example, collecting 9 out of 10 coins to receive a bonus in the game), and then the final stages (for example, a game to get the final coin to get 10 out of 10 coins to open the bonus ) culminate in real money bets where bonus funds have been canceled, lost or wagered and converted into cash. All winnings generated during this game may be void.
                    </p>
                    <p class="text">
                        Welcome package, reloads and other deposit bonuses are not available for players from Finland. However, they can take full advantage of the VIP program and personal VIP bonuses, weekly and daily tournaments, lotteries and other regular promotions.
                    </p>
                    <p class="text">You can read the general conditions for granting bonuses and other conditions here.</p>
                </div>
                <div class="bonus-conditions__info-row">
                    <h2 class="bonus-conditions__info-title">
                        <span class="accent">General terms and conditions of welcome bonuses</span>
                    </h2>
                    <ul class="bonus-conditions__list">
                        <li>Each available currency has a fixed equivalent in relation to other available currencies when calculating bet limits, deposits and winnings (hereinafter - the equiva                                  lent). Current equivalents for 75 RUB: 4.5 PLN; 1 USD; 1 EUR; 1.5 CAD; 10 NOK.</li>
                        <li>The minimum deposit amount to receive a bonus is RUB 1,500 (or equivalent).</li>
                        <li>The bonus must be wagered at least 50 times before funds are available for withdrawal.</li>
                        <li>The bonus is valid for 7 days.</li>
                        <li>The player has the right to cancel the bonus before the start of wagering. If the deposit bonus is canceled, free spins will also not be credited to the game account,  since they are directly part of the deposit bonus.</li>
                        <li>The maximum allowed bet for wagering the bonus is 400 RUB. The maximum bet limit (400 RUB) includes doubling the bets after the end of the game round, as well  as bonus rounds (purchased within the game).</li>
                        <li>It is forbidden to postpone any game rounds, including free spins and bonus rounds, for the time when you no longer need to wager the bonus, and / or make a new deposit (s) while you have active free spins and bonus rounds. If a player is suspected of such actions, all bonuses and winnings received as a result of such actions may be canceled.</li>
                    </ul>
                </div>

                @foreach($bonuses AS $item)

                    @if( !$item['is_first'] )
                        @if($item['type'] == 'happyhour')
                            Happy Hour<br>
                            All deposits will be multiplied by {{ $item['data']->multiplier }}<br>
                            Time {{ \VanguardLTE\HappyHour::$values['time'][$item['data']->time] }}
                        @elseif($item['type'] == 'progress')
                            Progress Bonuses<br>
                            Minimal amount of deposit is {{ $item['data']->sum }} {{ $currency }}<br>
                            Bonus {{ $item['data']->bonus }} {{ $currency }}
                        @elseif($item['type'] == 'invite')
                            Invite Friends<br>
                            For each friend get a bonus {{ $item['data']->sum }} {{ $currency }}<br>
                            A friend will receive a bonus of {{ $item['data']->sum_ref }} {{ $currency }}
                        @elseif($item['type'] == 'sms_bonus')
                            SMS Bonuses<br>
                            If you forgot about us for 5 days<br>
                            We will send you a bonus 5 {{ $currency }}
                        @elseif($item['type'] == 'welcome_bonus')
                            Welcome Bonuses {{ $item['data']->pay }}<br>
                            Minimal amount of deposit is {{ $item['data']->sum }} {{ $currency }} <br>
                            Bonus {{ $item['data']->bonus }} {{ $currency }}
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
