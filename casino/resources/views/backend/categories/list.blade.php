@extends('backend.layouts.app')

@section('page-title', trans('app.categories'))
@section('page-heading', trans('app.categories'))

@section('content')

	<section class="content-header">
		@include('backend.partials.messages')
	</section>

	<section class="content">
		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title">@lang('app.categories')</h3>
				<div class="pull-right box-tools">
					<a href="{{ route('backend.category.create') }}" class="btn btn-block btn-primary btn-sm">@lang('app.add')</a>
				</div>
			</div>
			<div class="box-body">
				<div class="table-responsive">
					<table class="table table-bordered table-striped">
						<thead>
						<tr>
							<th>@lang('app.name')</th>
							<th>@lang('app.position')</th>
							<th>@lang('app.href')</th>
							<th>@lang('app.count')</th>
						</tr>
						</thead>
						<tbody>
						@if (count($categories))
							@foreach ($categories as $category)
								@include('backend.categories.partials.row', ['base' => true])
								@foreach ($category->inner as $category)
									@include('backend.categories.partials.row', ['base' => false])
								@endforeach
							@endforeach
						@else
							<tr><td colspan="4">@lang('app.no_data')</td></tr>
						@endif
						</tbody>
						<thead>
						<tr>
							<th>@lang('app.name')</th>
							<th>@lang('app.position')</th>
							<th>@lang('app.href')</th>
							<th>@lang('app.count')</th>
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
		$('#categories-table').dataTable();
		$("#view").change(function () {
			$("#users-form").submit();
		});
	</script>
@stop
