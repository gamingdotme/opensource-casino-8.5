@extends('backend.layouts.user')

@section('page-title', trans('app.activity_log'))
@section('page-heading', trans('app.activity_log'))

@section('content')
<div class="row wow fadeIn">
        <div class="col-md-12">
            <section class="content-header">
                @include('backend.partials.messages')
            </section>
            <div class="content-box">
            <div class="element-wrapper">
                <div class="element-box">
                    <div class="element-info mb-3">
                        <div class="element-info-with-icon">
                            <div class="element-info-icon">
                                <div class="fa fa-pie-chart"></div>
                            </div>
                            <div class="element-info-text">
                                <h5 class="element-inner-header">@lang('app.activity_log')</h5>
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
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('app.user')</label>
                                            <input type="text" class="form-control" name="username" value="{{ Request::get('username') }}" placeholder="@lang('app.search_for_users')">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('app.message')</label>
                                            <input type="text" class="form-control" name="search" value="{{ Request::get('search') }}" placeholder="">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('app.ip')</label>
                                            <input type="text" class="form-control" name="ip" value="{{ Request::get('ip') }}" placeholder="">
                                        </div>
                                    </div>
                                </div>
                                <div class="box-footer">
                                    <button type="submit" class="btn btn-primary">
                                        Filter
                                    </button>

                                    @if( Auth::user()->hasRole('admin') )
                                        <a href="{{ route('backend.activity.clear') }}"
                                            class="btn btn-danger"
                                            data-method="DELETE"
                                            data-confirm-title="@lang('app.please_confirm')"
                                            data-confirm-text="@lang('app.are_you_sure_delete_shop')"
                                            data-confirm-delete="@lang('app.yes_i_do')">
                                            Delete Logs
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
                                        @if (isset($adminView))
                                            <th>@lang('app.user')</th>
                                        @endif
                                        <th>@lang('app.ip')</th>
                                        <th>@lang('app.message')</th>
                                        <th>@lang('app.log_time')</th>
                                        <th>@lang('app.more_info')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @if (count($activities))
                                    @foreach ($activities as $activity)
                                        <tr>
                                        @if (isset($adminView))
										<td>
											@if (isset($user))
												{{ $activity->user->username }}
											@else
												<a href="{{ route('backend.activity.user', $activity->user_id) }}">{{ $activity->user->username }}</a>
											@endif
										</td>
									@endif

									<td>
										@if ($activity->old)
											<b>@lang('app.after'): </b><br>
										@endif
											{{ $activity->description }}
										@if ($activity->old)
											<br><b>@lang('app.before'): </b><br>{{ $activity->old }}
										@endif
									</td>
									<td>{{ date(config('app.date_time_format'), strtotime($activity->created_at)) }}</td>
										<td>
											<b> @lang('app.country')</b>: {{ $activity->country }} <br>
											<b> @lang('app.city')</b>: {{ $activity->city }} <br>
											<b> @lang('app.os')</b>: {{ $activity->os }} <br>
											<b> @lang('app.device')</b>: {{ $activity->device }} <br>
											<b> @lang('app.browser')</b>: {{ $activity->browser }} <br>
											<b> @lang('app.ip')</b>: {{ $activity->ip_address }} <br>
											<b> @lang('app.user_agent')</b>: {{ $activity->user_agent }} <br>
										</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="@if (isset($adminView)) 5 @else 4 @endif">@lang('app.no_data')</td></tr>
                                @endif
                                </tbody>
                                <thead>
                                <tr>
                                    @if (isset($adminView))
                                        <th>@lang('app.user')</th>
                                    @endif
                                    <th>@lang('app.ip')</th>
                                    <th>@lang('app.message')</th>
                                    <th>@lang('app.log_time')</th>
                                    <th>@lang('app.more_info')</th>
                                </tr>
                                </thead>
                            </table>
                            {{ $activities->appends(Request::except('page'))->links() }}
                        </div>
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
				if( $('.activity_show').hasClass('collapsed-box') ){
					$.cookie('activity_show', '1');
				} else {
					$.removeCookie('activity_show');
				}
			});

			if( $.cookie('activity_show') ){
				$('.activity_show').removeClass('collapsed-box');
				$('.activity_show .btn-box-tool i').removeClass('fa-plus').addClass('fa-minus');
			}
		});
	</script>
@stop
