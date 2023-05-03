@extends('backend.layouts.app')

@section('page-title', __('app.sms_mailings'))
@section('page-heading', __('app.sms_mailings'))

@section('content')

	<section class="content-header">
		@include('backend.partials.messages')
	</section>

	<section class="content">
		<div class="box box-danger collapsed-box sms_mailings_show">
			<div class="box-header with-border">
				<h3 class="box-title">@lang('app.filter')</h3>
				<div class="box-tools pull-right">
					<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
				</div>
			</div>
			<div class="box-body">

				<form action="" method="GET" id="users-form" >
					<div class="row">
						<div class="col-md-6">
							<label>@lang('app.theme')</label>
							<input type="text" class="form-control" name="theme" value="{{ Request::get('theme') }}" placeholder="">
						</div>
						<div class="col-md-6">
							<label>@lang('app.date_start')</label>
							<div class="input-group date">
								<div class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</div>
								<input type="text" name="date_start" class="form-control pull-right datepicker" value="{{ Request::get('date_start') }}">
							</div>
						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-md-12">
							<button type="submit" class="btn btn-primary">
								@lang('app.filter')
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>

		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title">@lang('app.sms_mailings')</h3>
				<div class="pull-right box-tools">
					<a href="{{ route('backend.sms_mailing.create') }}" class="btn btn-block btn-primary btn-sm">@lang('app.add')</a>
				</div>
			</div>
			<div class="box-body">
				<div class="table-responsive">
					<table class="table table-bordered table-striped">
						<thead>
						<tr>
							<th>@lang('app.id')</th>
							<th>@lang('app.theme')</th>
							<th>@lang('app.roles')</th>
							<th>@lang('app.user_statuses')</th>
							<th>@lang('app.date_start')</th>
							<th>@lang('app.messages')</th>
							<th>@lang('app.status')</th>
						</tr>
						</thead>
						<tbody>
						@if (count($mailings))
							@foreach ($mailings as $mailing)
								@include('backend.sms_mailings.partials.row', ['base' => true])
							@endforeach
						@else
							<tr><td colspan="9">@lang('app.no_records_found')</td></tr>
						@endif
						</tbody>
						<thead>
						<tr>
							<th>ID</th>
							<th>Theme</th>
							<th>Roles</th>
							<th>User Statuses</th>
							<th>Date Start</th>
							<th>Messages</th>
							<th>Status</th>
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


		$('.datepicker').daterangepicker({
			"locale": {
				format: 'YYYY-MM-DD'
			},
			"startDate": "{{ date('Y-m-d') }}",
			"endDate": "{{ date('Y-m-d', time() + 31*24*60*60) }}"
		});

		$('.btn-box-tool').click(function(event){
			if( $('.sms_mailings_show').hasClass('collapsed-box') ){
				$.cookie('sms_mailings_show', '1');
			} else {
				$.removeCookie('sms_mailings_show');
			}
		});

		if( $.cookie('sms_mailings_show') ){
			$('.sms_mailings_show').removeClass('collapsed-box');
			$('.sms_mailings_show .btn-box-tool i').removeClass('fa-plus').addClass('fa-minus');
		}

	</script>
@stop
