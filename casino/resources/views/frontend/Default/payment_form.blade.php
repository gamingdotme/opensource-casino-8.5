@extends('frontend.Default.layouts.app')
@section('page-title', 'Payment form')
@section('add-main-class', 'main-redirect')
@section('add-header-class', 'main-redirect')
@php
    $currency = auth()->user()->present()->shop ? auth()->user()->present()->shop->currency : '';
@endphp



@section('content')

    @include('frontend.Default.partials.header_logged')
	<div class="modal" id="payment-form" style="display: block;">
        <header class="modal__header">
            <div class="span modal__title">Payment</div>
            <span ng-click="closeModal($event)" class="modal__icon icon icon_cancel js-close-popup"></span>
        </header>
		<div class="modal__body">
            <div class="modal__content">
				<div class="redirect" style="background-image: url('/frontend/Default/img/_src/redirected-bg.png');background-size:cover;">
					<h1 class="redirect__title">
						You will be rediracted to
						<span class="redirect__time">paymant page in 5-7 second!</span>

					</h1>

					@if( is_array($data) )
					<form action="{{ $data['action'] }}" method="{{ $data['method'] }}" id="payment_form" >
						@foreach($data['fields'] AS $field=>$value)
							<input type="hidden" name="{{ $field }}" value="{{ $value }}">
						@endforeach
						<button type="submit" class="btn btn--redirect button button-neutral" >OK</button>
					</form>
					@else
						{!! $data !!}
					@endif
				</div>
				<div class="modal__error" style="display: none"></div>
            </div>
            <div class="modal-preloader" style="display:none"></div>
        </div>
	</div>

@endsection

@section('footer')
	@include('frontend.Default.partials.footer')
@endsection

@section('scripts')
	@include('frontend.Default.partials.scripts')
@endsection
