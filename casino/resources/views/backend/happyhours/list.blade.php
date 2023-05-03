@extends('backend.layouts.app')

@section('page-title', trans('app.happyhours'))
@section('page-heading', trans('app.happyhours'))

@section('content')

	<section class="content-header">
		@include('backend.partials.messages')
	</section>

	<section class="content">
		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title">@lang('app.happyhours')</h3>

				<div class="pull-right box-tools">
                    @permission('happyhours.edit')
                    @if($shop)
                        @if( $shop->happyhours_active )
                            <a href="{{ route('backend.happyhour.status', 'disable') }}" class="btn btn-danger btn-sm">@lang('app.disable')</a>
                        @else
                            <a href="{{ route('backend.happyhour.status', 'activate') }}" class="btn btn-success btn-sm">@lang('app.active')</a>
                        @endif
                    @endif
                    @endpermission
                    @permission('happyhours.add')
					<a href="{{ route('backend.happyhour.create') }}" class="btn btn-primary btn-sm">@lang('app.add')</a>
                    @endpermission
				</div>

			</div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
					<thead>
					<tr>
						<th>@lang('app.id')</th>
						<th>@lang('app.multiplier')</th>
						<th>@lang('app.wager')</th>
						<th>@lang('app.time')</th>
					</tr>
					</thead>
					<tbody>
					@if (count($happyhours))
						@foreach ($happyhours as $happyhour)
							@include('backend.happyhours.partials.row')
						@endforeach
					@else
						<tr><td colspan="4">@lang('app.no_data')</td></tr>
					@endif
					</tbody>
					<thead>
					<tr>
						<th>@lang('app.id')</th>
						<th>@lang('app.multiplier')</th>
						<th>@lang('app.wager')</th>
						<th>@lang('app.time')</th>
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
