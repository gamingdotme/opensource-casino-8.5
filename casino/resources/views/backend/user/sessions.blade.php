@extends('backend.layouts.app')

@section('page-title', trans('app.active_sessions'))
@section('page-heading', trans('app.active_sessions'))

@section('content')

<section class="content-header">
@include('backend.partials.messages')
</section>

    <section class="content">
	<div class="box box-primary">
	<div class="box-header with-border">
	<h3 class="box-title">@lang('app.sessions') - {{ $user->present()->username }}</h3>
	</div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
	<thead>
	<tr>
	<th>IP</th>

		<th>@lang('app.country')</th>
		<th>@lang('app.city')</th>
		<th>@lang('app.os')</th>
		<th>@lang('app.device')</th>
		<th>@lang('app.browser')</th>
		<th>@lang('app.last_activity')</th>
	<th>@lang('app.action')</th>
	</tr>
	</thead>
	<tbody>
                        @if (count($sessions))
                            @foreach ($sessions as $session)
	<tr>
                                    <td>{{ $session->ip_address }} </td>



		<td>{{ $session->country ?: trans('app.unknown') }}</td>
		<td>{{ $session->city ?: trans('app.unknown') }}</td>
		<td>{{ $session->os ?: trans('app.unknown') }}</td>
		<td>{{ $session->device ?: trans('app.unknown') }}</td>
		<td>{{ $session->browser ?: trans('app.unknown') }}</td>
		<td>{{ \Carbon\Carbon::createFromTimestamp($session->last_activity)->format(config('app.date_time_format')) }}</td>

                                    <td class="text-center">
                                        <a href="{{ isset($profile) ? route('backend.profile.sessions.invalidate', $session->id) : route('backend.user.sessions.invalidate', [$user->id, $session->id]) }}"
                                           class="btn btn-block btn-danger btn-xs"
                                           data-method="DELETE"
                                           data-confirm-title="@lang('app.please_confirm')"
                                           data-confirm-text="@lang('app.are_you_sure_invalidate_session')"
                                           data-confirm-delete="@lang('app.yes_proceed')">
                                            @lang('app.invalidate_session')
                                        </a>
                                    </td>
	</tr>
                            @endforeach
                        @endif
	</tbody>
	<thead>
	<tr>
		<th>IP</th>

		<th>@lang('app.country')</th>
		<th>@lang('app.city')</th>
		<th>@lang('app.os')</th>
		<th>@lang('app.device')</th>
		<th>@lang('app.browser')</th>
		<th>@lang('app.last_activity')</th>
		<th>@lang('app.action')</th>
	</tr>
	</thead>
                            </table>
                        </div>
                    </div>
	</div>
    </section>

@stop