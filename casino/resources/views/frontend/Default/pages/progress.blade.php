@extends('frontend.Default.layouts.app')
@section('page-title', 'Progress')

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


    <div class="progress">
        <div class="container">
            <h1 class="progress__title"><span class="accent">PROGRESS</span> PROGRAM</h1>
            <p class="progress__subtitle">Play, Level Up, Get Rewards!</p>
            <p class="progress__text">
                When playing slots, you’ll accumulate points and guarantee yourself increasingly larger rewards for opening new statuses. These rewards are an addition to your in-game wins. To see all the rewards available, check out the table below. Here we go!
            </p>
            <div class="progress__block">

                @if($progress && count($progress))
                @foreach($progress AS $item)

                    <div class="progress__item @if(auth()->user()->rating == $item->rating) active @endif">
                        <div class="progress__icon">
                            @php $badge = strlen($item->rating) == 1 ? '0'  . $item->rating : $item->rating; @endphp
                            <img src="/frontend/Default/img/badges128x128/badge-{{ $badge }}.png" alt="">
                        </div>
                        <div class="progress__top">
                            <span class="progress__label level">{{ $item->rating }} level</span>
                            <span class="progress__label cr">{{ number_format($item->bonus, 0,""," ") }} {{ $currency }}</span>
                        </div>
                        <ul class="progress__list">
                            <li class="progress__list-item">
                                <span>Money In</span>
                                {{ number_format($item->sum, 0,""," ") }}
                            </li>
                            <li class="progress__list-item">
                                <span>Type</span>
                                {{ __('app.' . $item->type) }}
                            </li>
                            <li class="progress__list-item">
                                <span>Bid Amount</span>
                                {{ $item->spins }}
                            </li>
                            <li class="progress__list-item">
                                <span>Minimal Bet</span>
                                {{ $item->bet }}
                            </li>
                        </ul>
                    </div>

                @endforeach
                    @endif

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
