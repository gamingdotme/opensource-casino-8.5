@extends('system.layouts.errors')

@section('title', __('app.license_error'))

@section('content')
	<div class="title">@lang('app.license_error')</div>
	<div class="reason">@lang('app.license_text') <a href="{{ route('frontend.new_license') }}">@lang('app.the_page')</a>.</div>
@stop