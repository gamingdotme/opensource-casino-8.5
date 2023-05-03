@extends('backend.layouts.app')

@section('page-title', trans('app.game_stats'))
@section('page-heading', trans('app.game_stats'))

@section('content')

	<section class="content-header">
		@include('backend.partials.messages')
	</section>

	<section class="content">
		<form action="" method="GET">
			<div class="box box-danger collapsed-box game_stat_show">
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
								<label>@lang('app.game')</label>
								<input type="text" class="form-control" name="game" value="{{ Request::get('game') }}">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>@lang('app.user')</label>
								<input type="text" class="form-control" name="user" value="{{ Request::get('user') }}">
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>@lang('app.balance_from')</label>
								<input type="text" class="form-control" name="balance_from" value="{{ Request::get('balance_from') }}">
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>@lang('app.balance_to')</label>
								<input type="text" class="form-control" name="balance_to" value="{{ Request::get('balance_to') }}">
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
								<label>@lang('app.win_from')</label>
								<input type="text" class="form-control" name="win_from" value="{{ Request::get('win_from') }}">
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>@lang('app.win_to')</label>
								<input type="text" class="form-control" name="win_to" value="{{ Request::get('win_to') }}">
							</div>
						</div>
						<div class="col-md-6">
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
						<div class="col-md-12">
							<div class="form-group">
								@php
									$filter = ['' => '---'];
                                    $shifts = \VanguardLTE\OpenShift::where('shop_id', Auth::user()->shop_id)->orderBy('start_date', 'DESC')->get();
                                    if( count($shifts) ){
                                        foreach($shifts AS $shift){
                                            $filter[$shift->id] = $shift->id . ' - ' . $shift->start_date;
                                        }
                                    }
								@endphp
								<label>@lang('app.shifts')</label>
								{!! Form::select('shifts', $filter, Request::get('shifts'), ['id' => 'shifts', 'class' => 'form-control']) !!}
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
				<h3 class="box-title">@lang('app.game_stats')</h3>
			</div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
					<thead>
					<tr>
						<th>@lang('app.game')</th>
						<th>@lang('app.user')</th>
						<th>@lang('app.balance')</th>
						<th>@lang('app.bet')</th>
						<th>@lang('app.win')</th>
						@if(auth()->user()->hasRole('admin'))
						<th>@lang('app.in_game')</th>
						<th>@lang('app.in_jpg')</th>
							<th>@lang('app.profit')</th>
						@endif
						<th>@lang('app.denomination')</th>
						<th>@lang('app.slots')</th>					
						<th>@lang('app.fish')</th>
						<th>@lang('app.table_bank')</th>
						<th>@lang('app.little')</th>
						<th>@lang('app.bonus')</th>
						<th>@lang('app.total')</th>
						<th>@lang('app.date')</th>
					</tr>
					</thead>
					<tbody>
					@if (count($game_stat))
						@foreach ($game_stat as $stat)
							@include('backend.games.partials.row_stat')
						@endforeach
					@else
						<tr><td colspan="16">@lang('app.no_data')</td></tr>
					@endif
					</tbody>
					<thead>
					<tr>
						<th>@lang('app.game')</th>
						<th>@lang('app.user')</th>
						<th>@lang('app.balance')</th>
						<th>@lang('app.bet')</th>
						<th>@lang('app.win')</th>
						@if(auth()->user()->hasRole('admin'))
						<th>@lang('app.in_game')</th>
						<th>@lang('app.in_jpg')</th>
							<th>@lang('app.profit')</th>
						@endif
						<th>@lang('app.denomination')</th>
						<th>@lang('app.slots')</th>						
						<th>@lang('app.fish')</th>
						<th>@lang('app.table_bank')</th>
						<th>@lang('app.little')</th>
						<th>@lang('app.bonus')</th>
						<th>@lang('app.total')</th>
						<th>@lang('app.date')</th>
					</tr>
					</thead>
                            </table>
                        </div>


						@php
						$urlParams = '?';
						foreach(request()->all() AS $key=>$value){
							if($key != 'page'){
							    $urlParams .= '&' . $key . '=' . $value;
							}
						}
						@endphp

						{!! \VanguardLTE\Lib\Pagination::paging($count, $perPage, $page, route('backend.game_stat').$urlParams, '&page', 9) !!}


                    </div>			
		</div>
	</section>

@stop

@section('scripts')
	<script>
		$('#stats-table').dataTable();
		$(function() {
			$('input[name="dates"]').daterangepicker({
				timePicker: true,
				timePicker24Hour: true,
				startDate: moment().subtract(30, 'day'),
				endDate: moment().add(7, 'day'),

				locale: {
					format: 'YYYY-MM-DD HH:mm'
				}
			});
			$('.btn-box-tool').click(function(event){
				if( $('.game_stat_show').hasClass('collapsed-box') ){
					$.cookie('game_stat_show', '1');
				} else {
					$.removeCookie('game_stat_show');
				}
			});

			if( $.cookie('game_stat_show') ){
				$('.game_stat_show').removeClass('collapsed-box');
				$('.game_stat_show .btn-box-tool i').removeClass('fa-plus').addClass('fa-minus');
			}
		});
	</script>
@stop