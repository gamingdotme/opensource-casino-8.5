@extends('backend.layouts.user')

@section('page-title', trans('app.shop_stats'))
@section('page-heading', trans('app.shop_stats'))

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
                                <h5 class="element-inner-header">@lang('app.shop_stats')</h5>
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

                        <div class="card-body">
                            <form action="" method="GET" class="collapse" id="collapseExample">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('app.name')</label>
                                            {!! Form::select('type', $shops, Request::get('name'), ['id' => 'name', 'class' => 'form-control']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('app.user')</label>
                                            <input type="text" class="form-control" name="user" value="{{ Request::get('user') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('app.type')</label>
                                            {!! Form::select('type', ['' => 'All', 'add' => 'Add', 'out' => 'Out'], Request::get('type'), ['id' => 'type', 'class' => 'form-control']) !!}
                                        </div>
                                    </div>
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
                                        <th>@lang('app.name')</th>
                                        <th>@lang('app.in')</th>
                                        <th>@lang('app.out')</th>
                                        <th>@lang('app.user')</th>
                                        <th>@lang('app.date')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @if (count($shops_stat))
                                    @foreach ($shops_stat as $stat)
                                        @include('backend.stat.partials.row_shop_stat')
                                    @endforeach
                                @else
                                    <tr><td colspan="4">@lang('app.no_data')</td></tr>
                                @endif
                                </tbody>
                                <thead>
                                <tr>
                                    <th>@lang('app.name')</th>
                                    <th>@lang('app.in')</th>
                                    <th>@lang('app.out')</th>
                                    <th>@lang('app.user')</th>
                                    <th>@lang('app.date')</th>
                                </tr>
                                </thead>
                            </table>
                            {{ $shops_stat->appends(Request::except('page'))->links() }}
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
				if( $('.shop_stat_show').hasClass('collapsed-box') ){
					$.cookie('shop_stat_show', '1');
				} else {
					$.removeCookie('shop_stat_show');
				}
			});

			if( $.cookie('shop_stat_show') ){
				$('.shop_stat_show').removeClass('collapsed-box');
				$('.shop_stat_show .btn-box-tool i').removeClass('fa-plus').addClass('fa-minus');
			}
		});
	</script>
@stop
