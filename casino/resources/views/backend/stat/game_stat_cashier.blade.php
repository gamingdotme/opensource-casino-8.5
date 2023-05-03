@extends('backend.layouts.user')

@section('page-title', trans('app.game_stats'))
@section('page-heading', trans('app.game_stats'))

@section('content')
<div class="row wow fadeIn">

                <div class="col-lg-12">
                    <section class="content-header">
                        @include('backend.partials.messages')
                    </section>
                <div class="element-wrapper">
                        <div class="element-box">

    <div class="element-info mb-3">
                                <div class="element-info-with-icon">
                                    <div class="element-info-icon">
                                        <div class="fa fa-pie-chart"></div>
                                    </div>
                                    <div class="element-info-text">
                                        <h5 class="element-inner-header">@lang('app.game_stats')</h5>
                                        <div class="element-inner-desc text-primary">
                                        </div>
                                    </div>
                                </div>
                            </div>


                                <div class="p-1" >
                                    <div class="card-header p-2">
                                    <div class="d-flex justify-content-between">
                                        <h5>@lang('app.filter')</h5>
                                        <button class="btn btn-sm btn-link" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                    </div>

<div class="card-body ">

<form action="" method="GET" class="collapse" id="collapseExample">
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
								<label>@lang('app.date')</label>
								<input type="text" class="form-control" name="dates" value="{{ Request::get('dates') }}">
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
					@if( Auth::user()->hasRole('admin') )
						<a href="{{ route('backend.game_stat.clear') }}"
						   class="btn btn-danger"
						   data-method="DELETE"
						   data-confirm-title="Please Confirm"
						   data-confirm-text="Are you sure that you want to delete all logs?"
						   data-confirm-delete="Yes, delete all!">
							@lang('app.delete_logs')
						</a>
					@endif
				</div>

</form>
</div>
</div>

        <div class="col-sm-12 table-responsive p-0">
                                <div id="transactions_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4  p-0 m-0 ">




                    <table class="table table-striped table-bordered table-sm dataTable no-footer">
                    <thead>
					<tr>
						<th>@lang('app.game')</th>
						<th>@lang('app.user')</th>
						<th>@lang('app.balance')</th>
						<th>@lang('app.bet')</th>
						<th>@lang('app.win')</th>
						<th>@lang('app.in_game')</th>
						<th>@lang('app.in_jps')</th>
						<th>@lang('app.in_jpg')</th>
						@if(auth()->user()->hasRole('admin'))
							<th>@lang('app.profit')</th>
						@endif
						<th>@lang('app.denomination')</th>
						<th>@lang('app.date')</th>
					</tr>
					</thead>
					<tbody>
					@if (count($game_stat))
						@foreach ($game_stat as $stat)
							@include('backend.games.partials.row_stat')
						@endforeach
					@else
						<tr><td colspan="11">@lang('app.no_data')</td></tr>
					@endif
					</tbody>
					<thead>
					<tr>
						<th>@lang('app.game')</th>
						<th>@lang('app.user')</th>
						<th>@lang('app.balance')</th>
						<th>@lang('app.bet')</th>
						<th>@lang('app.win')</th>
						<th>@lang('app.in_game')</th>
						<th>@lang('app.in_jps')</th>
						<th>@lang('app.in_jpg')</th>
						@if(auth()->user()->hasRole('admin'))
							<th>@lang('app.profit')</th>
						@endif
						<th>@lang('app.denomination')</th>
						<th>@lang('app.date')</th>
					</tr>
					</thead>
                    </table>
                    {{ $game_stat->appends(Request::except('page'))->links()}}
            </div>

        </div>

</div>
</div>
</div>
</div>





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
