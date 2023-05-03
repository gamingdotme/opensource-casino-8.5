@extends('backend.layouts.app')

@section('page-title', trans('app.refunds'))
@section('page-heading', trans('app.refunds'))

@section('content')

	<section class="content-header">
		@include('backend.partials.messages')
	</section>

	<section class="content">
		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title">@lang('app.refunds')</h3>
			</div>
			<div class="box-body">
				<div class="table-responsive">
					<table class="table table-bordered table-striped">
						<thead>
						<tr>
							<th>@lang('app.min_pay')</th>
							<th>@lang('app.max_pay')</th>
							<th>@lang('app.percent')</th>
							<th>@lang('app.min_balance')</th>
							<th>@lang('app.status')</th>
						</tr>
						</thead>
						<tbody>
						@if (count($refunds))
							@foreach ($refunds as $refund)
								@include('backend.refunds.partials.row', ['base' => true])
							@endforeach
						@else
							<tr><td colspan="5">@lang('app.no_data')</td></tr>
						@endif
						</tbody>
						<thead>
						<tr>
							<th>@lang('app.min_pay')</th>
							<th>@lang('app.max_pay')</th>
							<th>@lang('app.percent')</th>
							<th>@lang('app.min_balance')</th>
							<th>@lang('app.status')</th>
						</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>
	</section>

@stop