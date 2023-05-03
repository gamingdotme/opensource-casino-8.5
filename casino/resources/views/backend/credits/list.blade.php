@extends('backend.layouts.app')

@section('page-title', trans('app.credits'))
@section('page-heading', trans('app.credits'))

@section('content')

	<section class="content-header">
		@include('backend.partials.messages')
	</section>

	<section class="content">

		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title">@lang('app.buy') @lang('app.credit')</h3>
				@if( auth()->user()->hasRole('admin') )
				<div class="pull-right box-tools">
					<a href="{{ route('backend.credit.create') }}" class="btn btn-block btn-primary btn-sm">@lang('app.add')</a>
				</div>
				@endif
			</div>
			<div class="box-body">
				<table class="table table-bordered table-striped">
					<thead>
					<tr>
						<th>@lang('app.credit')</th>
						<th>@lang('app.price')</th>
						<th>@lang('app.action')</th>
					</tr>
					</thead>
					<tbody>
					@if (count($credits))
						@foreach ($credits as $credit)
							@include('backend.credits.partials.row')
						@endforeach
					@else
						<tr><td colspan="4">@lang('app.no_data')</td></tr>
					@endif
					</tbody>
					<thead>
					<tr>
						<th>@lang('app.credit')</th>
						<th>@lang('app.active')</th>
						<th>@lang('app.action')</th>
					</tr>
					</thead>
				</table>
			</div>
		</div>
	</section>

@stop

@section('scripts')

@stop