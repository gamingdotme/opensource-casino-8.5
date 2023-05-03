@extends('backend.layouts.user')

@section('page-title', trans('app.statistics'))
@section('page-heading', trans('app.statistics'))

@section('content')
@if(auth()->user()->hasRole(['admin', 'agent']))
		<div class="row">
			<div class="col-lg-3 col-xs-6">
				<!-- small box -->
				<div class="small-box bg-light-blue">
					<div class="inner">
						<h3>{{ number_format( $stats['total_agent'], 2 ) }}</h3>
						<p>@lang('app.credit_agents')</p>
					</div>
					<div class="icon">
						<i class="fa fa-sitemap"></i>
					</div>
				</div>
			</div>

			<div class="col-lg-3 col-xs-6">
				<!-- small box -->
				<div class="small-box bg-red">
					<div class="inner">
						<h3>{{ number_format( $stats['total_distributor'], 2 ) }}</h3>
						<p>@lang('app.credit_distributors')</p>
					</div>
					<div class="icon">
						<i class="fa fa-area-chart"></i>
					</div>
				</div>
			</div>

			<div class="col-lg-3 col-xs-6">
				<!-- small box -->
				<div class="small-box bg-red">
					<div class="inner">
						<h3>{{ number_format( $stats['total_credit'], 2 ) }}</h3>
						<p>@lang('app.credit_shops')</p>
					</div>
					<div class="icon">
						<i class="fa fa-area-chart"></i>
					</div>
				</div>
			</div>

			<div class="col-lg-3 col-xs-6">
				<!-- small box -->
				<div class="small-box bg-red">
					<div class="inner">
						<h3>{{ number_format( $stats['total_money'], 2 ) }}</h3>
						<p>@lang('app.total_money') {{ number_format( $stats['pay_out'], 2 ) }}%</p>
					</div>
					<div class="icon">
						<i class="fa fa-area-chart"></i>
					</div>
				</div>
			</div>

		</div>
@endif
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
                            <h5 class="element-inner-header">@lang('app.statistics')</h5>
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
                            <div class="box box-danger collapsed-box shift_stat_show">
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
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>@lang('app.type')</label>
                                                    {!! Form::select('system', $systems, Request::get('system'), ['id' => 'system', 'class' => 'form-control']) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>@lang('app.role')</label>
                                                    {!! Form::select('role', ['' => '---'] + $roles, Request::get('role'), ['id' => 'role', 'class' => 'form-control']) !!}
                                                </div>
                                            </div>
                                            @if(auth()->user()->hasRole(['admin', 'agent', 'distributor']))
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>@lang('app.credit_in') @lang('app.from')</label>
                                                    <input type="text" class="form-control" name="credit_in_from" value="{{ Request::get('credit_in_from') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>@lang('app.credit_in') @lang('app.to')</label>
                                                    <input type="text" class="form-control" name="credit_in_to" value="{{ Request::get('credit_in_to') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>@lang('app.credit_out') @lang('app.from')</label>
                                                    <input type="text" class="form-control" name="credit_out_from" value="{{ Request::get('credit_out_from') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>@lang('app.credit_out') @lang('app.to')</label>
                                                    <input type="text" class="form-control" name="credit_out_to" value="{{ Request::get('credit_out_to') }}">
                                                </div>
                                            </div>
                                            @endif
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>@lang('app.money_in') @lang('app.from')</label>
                                                    <input type="text" class="form-control" name="money_in_from" value="{{ Request::get('money_in_from') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>@lang('app.money_in') @lang('app.to')</label>
                                                    <input type="text" class="form-control" name="money_in_to" value="{{ Request::get('money_in_to') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>@lang('app.money_out') @lang('app.from')</label>
                                                    <input type="text" class="form-control" name="money_out_from" value="{{ Request::get('money_out_from') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>@lang('app.money_out') @lang('app.to')</label>
                                                    <input type="text" class="form-control" name="money_out_to" value="{{ Request::get('money_out_to') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    @php
                                                        $filter = ['' => '---'];
                                                        $shifts = \VanguardLTE\OpenShift::orderBy('start_date', 'DESC')->get();
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
                        </div>
                </div>

                <div class="col-sm-12 table-responsive p-0">
        <div id="transactions_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4  p-0 m-0 ">
            <table class="table table-striped table-bordered table-sm dataTable no-footer">
            <thead>
					<tr>
						@if(auth()->user()->hasRole(['admin']))
							<th>@lang('app.admin')</th>
						@endif
						@if(auth()->user()->hasRole(['admin', 'agent']))
							<th>@lang('app.agent')</th>
						@endif
						@if(auth()->user()->hasRole(['admin', 'agent', 'distributor']))
							<th>@lang('app.distributor')</th>
						@endif
						@if(auth()->user()->hasRole(['admin', 'agent', 'distributor']))
							<th>@lang('app.shop')</th>
						@endif
						<th>@lang('app.cashier')</th>
						<th>@lang('app.type')</th>
						<th>@lang('app.user')</th>
						@if(auth()->user()->hasRole(['admin', 'agent']))
							<th>@lang('app.agent') @lang('app.in')</th>
							<th>@lang('app.agent') @lang('app.out')</th>
						@endif
						@if(auth()->user()->hasRole(['admin', 'agent', 'distributor']))
							<th>@lang('app.distributor') @lang('app.in')</th>
							<th>@lang('app.distributor') @lang('app.out')</th>
						@endif
						@if(auth()->user()->hasRole(['admin']))
							<th>@lang('app.type') @lang('app.in')</th>
							<th>@lang('app.type') @lang('app.out')</th>
						@endif
						@if(auth()->user()->hasRole(['admin', 'agent', 'distributor']))
							<th>@lang('app.credit') @lang('app.in')</th>
							<th>@lang('app.credit') @lang('app.out')</th>
						@endif
						<th>@lang('app.money') @lang('app.in')</th>
						<th>@lang('app.money') @lang('app.out')</th>
						<th>@lang('app.date')</th>
					</tr>
					</thead>
					<tbody>
					@if (count($transactions))
						@foreach ($transactions as $transaction)
							@include('backend.stat.partials.transaction_stat')
						@endforeach
					@else
						<tr><td colspan="18">@lang('app.no_data')</td></tr>
					@endif
					</tbody>
					<thead>
					<tr>
						@if(auth()->user()->hasRole(['admin']))
							<th>@lang('app.admin')</th>
						@endif
						@if(auth()->user()->hasRole(['admin', 'agent']))
							<th>@lang('app.agent')</th>
						@endif
						@if(auth()->user()->hasRole(['admin', 'agent', 'distributor']))
							<th>@lang('app.distributor')</th>
						@endif
						@if(auth()->user()->hasRole(['admin', 'agent', 'distributor']))
							<th>@lang('app.shop')</th>
						@endif
						<th>@lang('app.cashier')</th>
						<th>@lang('app.type')</th>
						<th>@lang('app.user')</th>
						@if(auth()->user()->hasRole(['admin', 'agent']))
							<th>@lang('app.agent') @lang('app.in')</th>
							<th>@lang('app.agent') @lang('app.out')</th>
						@endif
						@if(auth()->user()->hasRole(['admin', 'agent', 'distributor']))
							<th>@lang('app.distributor') @lang('app.in')</th>
							<th>@lang('app.distributor') @lang('app.out')</th>
						@endif
						@if(auth()->user()->hasRole(['admin']))
							<th>@lang('app.type') @lang('app.in')</th>
							<th>@lang('app.type') @lang('app.out')</th>
						@endif
						@if(auth()->user()->hasRole(['admin', 'agent', 'distributor']))
							<th>@lang('app.credit') @lang('app.in')</th>
							<th>@lang('app.credit') @lang('app.out')</th>
						@endif
						<th>@lang('app.money') @lang('app.in')</th>
						<th>@lang('app.money') @lang('app.out')</th>
						<th>@lang('app.date')</th>
					</tr>
					</thead>
            </table>
            @php
							$urlParams = '?';
                            foreach(request()->all() AS $key=>$value){
                                if($key != 'page'){
                                    $urlParams .= '&' . $key . '=' . $value;
                                }
                            }
						@endphp

						{!! \VanguardLTE\Lib\Pagination::paging($count, $perPage, $page, route('backend.transactions').$urlParams, '&page', 9) !!}

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
