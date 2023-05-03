@extends('backend.layouts.app')

@section('page-title', trans('app.tournaments'))
@section('page-heading', trans('app.tournaments'))

@section('content')

	<section class="content-header">
		@include('backend.partials.messages')
	</section>

	<section class="content">


		<form action="" method="GET">
			<div class="box box-danger collapsed-box tournament_show">
				<div class="box-header with-border">
					<h3 class="box-title">@lang('app.filter')</h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
					</div>
				</div>
				<div class="box-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>@lang('app.search')</label>
								<input type="text" class="form-control" name="search" value="{{ Request::get('search') }}">
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label> @lang('app.date_start')</label>
								<div class="input-group">
									<button type="button" class="btn btn-default pull-right" id="daterange-btn">
										<span><i class="fa fa-calendar"></i> {{ Request::get('dates_view') ?: __('app.date_start_picker') }}</span>
										<i class="fa fa-caret-down"></i>
									</button>
								</div>
								<input type="hidden" id="dates_view" name="dates_view" value="{{ Request::get('dates_view') }}">
								<input type="hidden" id="dates" name="dates" value="{{ Request::get('dates') }}">
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>@lang('app.date_end')</label>
								<div class="input-group">
									<button type="button" class="btn btn-default pull-right" id="end_daterange-btn">
										<span><i class="fa fa-calendar"></i> {{ Request::get('end_dates_view') ?: __('app.date_end_picker') }}</span>
										<i class="fa fa-caret-down"></i>
									</button>
								</div>
								<input type="hidden" id="end_dates_view" name="end_dates_view" value="{{ Request::get('end_dates_view') }}">
								<input type="hidden" id="end_dates" name="end_dates" value="{{ Request::get('end_dates') }}">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>@lang('app.type')</label>
								{!! Form::select('type',  ['' => '---'] + \VanguardLTE\Tournament::$values['type'], Request::get('type'), ['class' => 'form-control']) !!}
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
								<label>@lang('app.prize_from')</label>
								<input type="text" class="form-control" name="prize_from" value="{{ Request::get('prize_from') }}">
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>@lang('app.prize_to')</label>
								<input type="text" class="form-control" name="prize_to" value="{{ Request::get('prize_to') }}">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>@lang('app.status')</label>
								{!! Form::select('status', ['' => '---'] + \VanguardLTE\Tournament::$values['status'],Request::get('status'), ['class' => 'form-control']) !!}
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
				<h3 class="box-title">@lang('app.tournaments')</h3>
				@permission('tournaments.add')
				<div class="pull-right box-tools">
					<a href="{{ route('backend.tournament.create') }}" class="btn btn-block btn-primary btn-sm">@lang('app.add')</a>
				</div>
				@endpermission
			</div>
			<div class="box-body">
				<div class="table-responsive">
					<table class="table table-bordered table-striped">
						<thead>
						<tr>
							<th>@lang('app.id')</th>
							<th>@lang('app.name')</th>
							<th>@lang('app.start')</th>
							<th>@lang('app.end')</th>
							<th>@lang('app.type')</th>
							<th>@lang('app.prize')</th>
							<th>@lang('app.bet')</th>
							<th>@lang('app.spins')</th>
							<th>@lang('app.wager')</th>
							<th>@lang('app.status')</th>
						</tr>
						</thead>
						<tbody>
						@if (count($tournaments))
							@foreach ($tournaments as $tournament)
								@include('backend.tournaments.partials.row')
							@endforeach
						@else
							<tr><td colspan="10">@lang('app.no_data')</td></tr>
						@endif
						</tbody>
						<thead>
						<tr>
							<th>@lang('app.id')</th>
							<th>@lang('app.name')</th>
							<th>@lang('app.start')</th>
							<th>@lang('app.end')</th>
							<th>@lang('app.type')</th>
							<th>@lang('app.prize')</th>
							<th>@lang('app.bet')</th>
							<th>@lang('app.spins')</th>
							<th>@lang('app.wager')</th>
							<th>@lang('app.status')</th>
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

		$(function() {

			$('.datepicker').daterangepicker({
				"locale": {
					format: 'YYYY-MM-DD'
				},
				"startDate": "{{ date('Y-m-d') }}",
				"endDate": "{{ date('Y-m-d', time() + 31*24*60*60) }}"
			});

			$('.btn-box-tool').click(function(event){
				if( $('.tournament_show').hasClass('collapsed-box') ){
					$.cookie('tournament_show', '1');
				} else {
					$.removeCookie('tournament_show');
				}
			});

			if( $.cookie('tournament_show') ){
				$('.tournament_show').removeClass('collapsed-box');
				$('.tournament_show .btn-box-tool i').removeClass('fa-plus').addClass('fa-minus');
			}
		});
	</script>
@stop