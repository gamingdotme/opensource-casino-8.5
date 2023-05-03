@extends('backend.layouts.app')

@section('page-title', trans('app.articles'))
@section('page-heading', trans('app.articles'))

@section('content')

	<section class="content-header">
		@include('backend.partials.messages')
	</section>

	<section class="content">
		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title">@lang('app.articles')</h3>
				<div class="pull-right box-tools">
					<a href="{{ route('backend.article.create') }}" class="btn btn-block btn-primary btn-sm">@lang('app.add')</a>
				</div>
			</div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
					<thead>
					<tr>
						<th>@lang('app.id')</th>
						<th>@lang('app.title')</th>
						<th>@lang('app.date')</th>
					</tr>
					</thead>
					<tbody>
					@if (count($articles))
						@foreach ($articles as $article)
							@include('backend.articles.partials.row')
						@endforeach
					@else
						<tr><td colspan="3">@lang('app.no_data')</td></tr>
					@endif
					</tbody>
					<thead>
					<tr>
						<th>@lang('app.id')</th>
						<th>@lang('app.title')</th>
						<th>@lang('app.date')</th>
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
		$('#articles-table').dataTable();
		$("#status").change(function () {
			$("#users-form").submit();
		});
	</script>
@stop