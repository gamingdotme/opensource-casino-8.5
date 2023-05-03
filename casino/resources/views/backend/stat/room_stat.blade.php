@extends('backend.layouts.app')

@section('page-title', trans('app.room_stats'))
@section('page-heading', trans('app.room_stats'))

@section('content')

	<section class="content-header">
		@include('backend.partials.messages')
	</section>

	<section class="content">
		<form action="" method="GET">
			<div class="box box-danger collapsed-box room_stat_show">
				<div class="box-header with-border">
					<h3 class="box-title">Filter</h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
					</div>
				</div>
				<div class="box-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Name</label>
								{!! Form::select('type', $rooms, Request::get('name'), ['id' => 'name', 'class' => 'form-control']) !!}
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>User</label>
								<input type="text" class="form-control" name="user" value="{{ Request::get('user') }}">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>Type</label>
								{!! Form::select('type', ['' => 'All', 'add' => 'Add', 'out' => 'Out'], Request::get('type'), ['id' => 'type', 'class' => 'form-control']) !!}
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>Sum From</label>
								<input type="text" class="form-control" name="sum_from" value="{{ Request::get('sum_from') }}">
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>Sum To</label>
								<input type="text" class="form-control" name="sum_to" value="{{ Request::get('sum_to') }}">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>Date</label>
								<input type="text" class="form-control" name="dates" value="{{ Request::get('dates') }}">
							</div>
						</div>
						<div class="col-md-12">
							<div class="form-group">
								@php
									$filter = ['' => '---'];
                                    $shifts = \VanguardLTE\OpenShift::where('room_id', Auth::user()->room_id)->orderBy('start_date', 'DESC')->get();
                                    if( count($shifts) ){
                                        foreach($shifts AS $shift){
                                            $filter[$shift->id] = $shift->id . ' - ' . $shift->start_date;
                                        }
                                    }
								@endphp
								<label>Shifts</label>
								{!! Form::select('shifts', $filter, Request::get('shifts'), ['id' => 'shifts', 'class' => 'form-control']) !!}
							</div>
						</div>
					</div>
				</div>
				<div class="box-footer">
					<button type="submit" class="btn btn-primary">
						Filter
					</button>
				</div>
			</div>
		</form>

		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title">@lang('app.room_stats')</h3>
			</div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
					<thead>
					<tr>
						<th>Name</th>
						<th>User</th>
						<th>Sum</th>
						<th>Date</th>
					</tr>
					</thead>
					<tbody>
					@if (count($rooms_stat))
						@foreach ($rooms_stat as $stat)
							@include('backend.stat.partials.row_room_stat')
						@endforeach
					@else
						<tr><td colspan="4">No data available in table</td></tr>
					@endif
					</tbody>
					<thead>
					<tr>
						<th>Name</th>
						<th>User</th>
						<th>Sum</th>
						<th>Date</th>
					</tr>
					</thead>
                            </table>
                        </div>
                        {{ $rooms_stat->appends(Request::except('page'))->links() }}						
                    </div>
		</div>
	</section>

@stop

@section('scripts')
	<script>
		$(function() {
			$('#stat-table').dataTable();
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
				if( $('.room_stat_show').hasClass('collapsed-box') ){
					$.cookie('room_stat_show', '1');
				} else {
					$.removeCookie('room_stat_show');
				}
			});

			if( $.cookie('room_stat_show') ){
				$('.room_stat_show').removeClass('collapsed-box');
				$('.room_stat_show .btn-box-tool i').removeClass('fa-plus').addClass('fa-minus');
			}
		});
	</script>
@stop