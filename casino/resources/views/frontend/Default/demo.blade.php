@extends('frontend.Default.layouts.app')
@section('page-title', 'Free DEMO')

@section('add-main-class', 'main-redirect')
@section('add-header-class', 'main-redirect')

@php

    $refund = false;
    if( auth()->user()->shop && auth()->user()->shop->progress_active ){
        $refund = \VanguardLTE\Progress::where(['shop_id' => auth()->user()->shop_id, 'rating' => auth()->user()->rating])->first();
    }

    $currency = auth()->user()->present()->shop ? auth()->user()->present()->shop->currency : '';
@endphp


@section('content')

    @include('frontend.Default.partials.header')

	<div class="redirect" style="background-image: url('/frontend/Default/img/_src/redirected-bg.png')">
		<h1 class="redirect__title">
			Free Demo
			<span class="redirect__time">Demo will be created in 5-7 second!</span>

		</h1>

		<a href="javascript:;" class="btn btn--redirect" id="free_demo">Get Free Demo</a>

	</div>

@endsection

@section('footer')
	@include('frontend.Default.partials.footer')
@endsection

@section('scripts')
	@include('frontend.Default.partials.scripts')
@endsection
