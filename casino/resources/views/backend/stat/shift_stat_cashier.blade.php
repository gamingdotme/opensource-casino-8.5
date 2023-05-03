@extends('backend.layouts.user')

@section('page-title', trans('app.shift_stats'))
@section('page-heading', trans('app.shift_stats'))

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
                                        <h5 class="element-inner-header">@lang('app.shift_stats')</h5>
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
                                                        <label>@lang('app.user')</label>
                                                        <input type="text" class="form-control" name="user" value="{{ Request::get('user') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>@lang('app.date_start')</label>
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
                                        </div>

                                    </form>
                                </div>
                                </div>

        <div class="col-sm-12 table-responsive p-0">
                                <div id="transactions_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4  p-0 m-0 ">




                    <table class="table table-striped table-bordered table-sm dataTable no-footer">
                    <thead>
					<tr>
						@if(!auth()->user()->hasRole('cashier'))
							<th>@lang('app.shift')</th>
						@endif
						<th>Open Shift</th>
						<th>@lang('app.date_start')</th>
						<th>@lang('app.date_end')</th>
						@if(!auth()->user()->hasRole('cashier'))
							<th>@lang('app.credit')</th>
							<th>@lang('app.in')</th>
							<th>@lang('app.out')</th>
						@endif
						<th>@lang('app.total') Credit</th>
						@permission('games.in_out')
							<th>@lang('app.banks')</th>
						@endpermission
						@if(!auth()->user()->hasRole('cashier'))
						<th>@lang('app.returns')</th>
					    @endif
						<th>User @lang('app.balance')</th>
						<th>@lang('app.in')</th>
						<th>@lang('app.out')</th>
						<th>@lang('app.total') Money</th>
						@if(auth()->user()->hasRole('admin'))
							<th>@lang('app.profit')</th>
						@endif
					</tr>
					</thead>
					<tbody>
					@if (count($open_shift))
						@foreach ($open_shift as $num=>$stat)
							@include('backend.stat.partials.row_shift_stat')
						@endforeach
					@else
						<tr><td colspan="15">@lang('app.no_data')</td></tr>
					@endif
					</tbody>
					<thead>
					<tr>
						@if(!auth()->user()->hasRole('cashier'))
							<th>@lang('app.shift')</th>
						@endif
						<th>Open Shift</th>
						<th>@lang('app.date_start')</th>
						<th>@lang('app.date_end')</th>
						@if(!auth()->user()->hasRole('cashier'))
							<th>@lang('app.credit')</th>
							<th>@lang('app.in')</th>
							<th>@lang('app.out')</th>
						@endif
						<th>@lang('app.total') Credit</th>
						@permission('games.in_out')
							<th>@lang('app.banks')</th>
						@endpermission
						@if(!auth()->user()->hasRole('cashier'))
						<th>@lang('app.returns')</th>
					    @endif
						<th>User @lang('app.balance')</th>
						<th>@lang('app.in')</th>
						<th>@lang('app.out')</th>
						<th>@lang('app.total') Money</th>
						@if(auth()->user()->hasRole('admin'))
							<th>@lang('app.profit')</th>
						@endif
					</tr>
					</thead>
                    </table>
                    {{ $open_shift->appends(Request::except('page'))->links() }}
            </div>

        </div>

</div>
</div>
</div>
</div>






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
				if( $('.shift_stat_show').hasClass('collapsed-box') ){
					$.cookie('shift_stat_show', '1');
				} else {
					$.removeCookie('shift_stat_show');
				}
			});

			if( $.cookie('shift_stat_show') ){
				$('.shift_stat_show').removeClass('collapsed-box');
				$('.shift_stat_show .btn-box-tool i').removeClass('fa-plus').addClass('fa-minus');
			}
		});
	</script>
@stop
