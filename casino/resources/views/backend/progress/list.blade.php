@extends('backend.layouts.app')

@section('page-title', trans('app.progress'))
@section('page-heading', trans('app.progress'))

@section('content')

	<section class="content-header">
		@include('backend.partials.messages')
	</section>

	<section class="content">

		<form action="" method="GET">
			<div class="box box-danger collapsed-box pin_show">
				<div class="box-header with-border">
					<h3 class="box-title">@lang('app.filter')</h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
					</div>
				</div>
				<div class="box-body">
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label>@lang('app.sum_from')</label>
								<input type="text" class="form-control" name="sum_from" value="{{ Request::get('sum_from') }}">
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>@lang('app.sum_to')</label>
								<input type="text" class="form-control" name="sum_to" value="{{ Request::get('sum_to') }}">
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>@lang('app.spins_from')</label>
								<input type="text" class="form-control" name="spins_from" value="{{ Request::get('spins_from') }}">
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>@lang('app.spins_to')</label>
								<input type="text" class="form-control" name="spins_to" value="{{ Request::get('spins_to') }}">
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								<label>@lang('app.bet_from')</label>
								<input type="text" class="form-control" name="bet_from" value="{{ Request::get('bet_from') }}">
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>@lang('app.bet_to')</label>
								<input type="text" class="form-control" name="bet_to" value="{{ Request::get('bet_to') }}">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>@lang('app.type')</label>
								{!! Form::select('type', ['' => __('app.all'), 'one_pay' => __('app.one_pay'), 'sum_pay' => __('app.sum_pay')], Request::get('type'), ['id' => 'type', 'class' => 'form-control']) !!}
							</div>
						</div>
					</div>
				</div>
				<div class="box-footer">
					<button type="submit" class="btn btn-primary">
						@lang('app.filter')
					</button>
				</div>
			</div>
		</form>

		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title">@lang('app.progress')</h3>
                <div class="pull-right box-tools">
                    @permission('progress.edit')
                    @if($shop)
                        @if( $shop->progress_active )
                            <a href="{{ route('backend.progress.status', 'disable') }}" class="btn btn-danger btn-sm">@lang('app.disable')</a>
                        @else
                            <a href="{{ route('backend.progress.status', 'activate') }}" class="btn btn-success btn-sm">@lang('app.active')</a>
                        @endif
                    @endif
                    @endpermission
                </div>
			</div>
			<div class="box-body">
				<table class="table table-bordered table-striped">
					<thead>
					<tr>
						<th>@lang('app.rating')</th>
						<th>@lang('app.sum')</th>
						<th>@lang('app.type')</th>
						<th>@lang('app.spins')</th>
						<th>@lang('app.bet')</th>

						<th>@lang('app.bonus')</th>
						<th>@lang('app.day')</th>
						<th>@lang('app.min')</th>
						<th>@lang('app.max')</th>
						<th>@lang('app.percent')</th>
						<th>@lang('app.min_balance')</th>
						<th>@lang('app.wager')</th>
                        <th>@lang('app.days_active')</th>
					</tr>
					</thead>
					<tbody>
					@if (count($progress))
						@foreach ($progress as $item)
							@include('backend.progress.partials.row')
						@endforeach
					@else
						<tr><td colspan="13">@lang('app.no_data')</td></tr>
					@endif
					</tbody>
					<thead>
					<tr>
						<th>@lang('app.rating')</th>
						<th>@lang('app.sum')</th>
						<th>@lang('app.type')</th>
						<th>@lang('app.spins')</th>
						<th>@lang('app.bet')</th>

						<th>@lang('app.bonus')</th>
						<th>@lang('app.day')</th>
						<th>@lang('app.min')</th>
						<th>@lang('app.max')</th>
						<th>@lang('app.percent')</th>
						<th>@lang('app.min_balance')</th>
						<th>@lang('app.wager')</th>
                        <th>@lang('app.days_active')</th>
					</tr>
					</thead>
				</table>
			</div>
		</div>
	</section>

@stop

@section('scripts')
    <script>
		$(function() {
			//$('#progress-table').dataTable();
		});

    </script>
@stop
