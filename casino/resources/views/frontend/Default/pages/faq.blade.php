@extends('frontend.Default.layouts.app')
@section('page-title', 'FAQ')

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


    <div class="faq">
        <div class="container">
            <h1 class="faq__title"><span class="accent">Frequently</span> Asked Questions</h1>

            <ul class="faq__block accordion">
                @if( count($faqs) )
                    @foreach($faqs AS $faq)
                        <li class="faq__item accordion__item">
                            <h2 class="faq__item-title accordion__trigger">{{ $faq->question }} <span class="accordion__trigger-icon"></span></h2>
                            <div class="faq__content accordion__content">
                                <p class="faq__item-text">{!! $faq->answer !!}</p>
                            </div>
                        </li>
                    @endforeach
                @endif
            </ul>

        </div>
    </div>










@endsection

@section('footer')
	@include('frontend.Default.partials.footer')
@endsection

@section('scripts')
	@include('frontend.Default.partials.scripts')
@endsection
