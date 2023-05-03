@extends('backend.layouts.app')

@section('page-title', trans('app.welcome_bonuses'))
@section('page-heading', trans('app.welcome_bonuses'))

@section('content')

	<section class="content-header">
		@include('backend.partials.messages')
	</section>

	<section class="content">
		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title">@lang('app.welcome_bonuses')</h3>
                <div class="pull-right box-tools">
                    @permission('welcome_bonuses.edit')
                    @if($shop)
                        @if( $shop->welcome_bonuses_active )
                            <a href="{{ route('backend.welcome_bonus.status', 'disable') }}" class="btn btn-danger btn-sm">@lang('app.disable')</a>
                        @else
                            <a href="{{ route('backend.welcome_bonus.status', 'activate') }}" class="btn btn-success btn-sm">@lang('app.active')</a>
                        @endif
                    @endif
                    @endpermission
                </div>
			</div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
					<thead>
					<tr>
						<th>@lang('app.pay')</th>
						<th>@lang('app.sum')</th>
						<th>@lang('app.type')</th>
						<th>@lang('app.bonus')</th>
						<th>@lang('app.wager')</th>
					</tr>
					</thead>
					<tbody>
					@if (count($welcome_bonuses))
						@foreach ($welcome_bonuses as $welcome_bonus)
							@include('backend.welcomebonuses.partials.row')
						@endforeach
					@else
						<tr><td colspan="6">@lang('app.no_data')</td></tr>
					@endif
					</tbody>
					<thead>
					<tr>
						<th>@lang('app.pay')</th>
						<th>@lang('app.sum')</th>
						<th>@lang('app.type')</th>
						<th>@lang('app.bonus')</th>
						<th>@lang('app.wager')</th>
					</tr>
					</thead>
                            </table>
                        </div>
                    </div>
		</div>
	</section>

@stop

@section('scripts')
	<script>
	</script>
@stop
