@extends('backend.layouts.app')

@section('page-title', trans('app.sms_bonuses'))
@section('page-heading', trans('app.sms_bonuses'))

@section('content')

	<section class="content-header">
		@include('backend.partials.messages')
	</section>

	<section class="content">

		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title">@lang('app.sms_bonuses')</h3>

				<div class="pull-right box-tools">
                    @permission('sms_bonuses.edit')
                    @if($shop)
                        @if( $shop->sms_bonuses_active )
                            <a href="{{ route('backend.sms_bonus.status', 'disable') }}" class="btn btn-danger btn-sm">@lang('app.disable')</a>
                        @else
                            <a href="{{ route('backend.sms_bonus.status', 'activate') }}" class="btn btn-success btn-sm">@lang('app.active')</a>
                        @endif
                    @endif
                    @endpermission
                    @permission('sms_bonuses.add')
					<a href="{{ route('backend.sms_bonus.create') }}" class="btn btn-primary btn-sm">@lang('app.add')</a>
                    @endpermission
				</div>

			</div>
			<div class="box-body">
				<table class="table table-bordered table-striped">
					<thead>
					<tr>
						<th>@lang('app.day')</th>
						<th>@lang('app.bonus')</th>
						<th>@lang('app.wager')</th>
					</tr>
					</thead>
					<tbody>
					@if (count($sms_bonuses))
						@foreach ($sms_bonuses as $sms_bonus)
							@include('backend.sms_bonuses.partials.row')
						@endforeach
					@else
						<tr><td colspan="3">@lang('app.no_data')</td></tr>
					@endif
					</tbody>
					<thead>
					<tr>
						<th>@lang('app.day')</th>
						<th>@lang('app.bonus')</th>
						<th>@lang('app.wager')</th>
					</tr>
					</thead>
				</table>
			</div>
		</div>
	</section>

@stop

@section('scripts')
    <script>

    </script>
@stop
