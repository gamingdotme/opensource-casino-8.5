@extends('backend.layouts.app')

@section('page-title', trans('app.info'))
@section('page-heading', trans('app.info'))

@section('content')

	<section class="content-header">
		@include('backend.partials.messages')
	</section>

	<section class="content">


		<form action="" method="GET">
			<div class="box box-danger collapsed-box info_show">
				<div class="box-header with-border">
					<h3 class="box-title">@lang('app.filter')</h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
					</div>
				</div>
				<div class="box-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>@lang('app.search')</label>
								<input type="text" class="form-control" name="search" value="{{ Request::get('search') }}">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>@lang('app.role')</label>
								{!! Form::select('role', ['' => __('app.all')] + array_combine($roles, $roles), Request::get('role'), ['id' => 'role', 'class' => 'form-control']) !!}
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


		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title">Helper</h3>
				<div class="pull-right box-tools">
					<a href="{{ route('backend.info.create') }}" class="btn btn-block btn-primary btn-sm">@lang('app.add')</a>
				</div>
			</div>
			<div class="box-body">
				<div class="table-responsive">
					<table class="table table-bordered table-striped">
						<thead>
						<tr>
							<th>@lang('app.title')</th>
							<th>@lang('app.roles')</th>
							<th>@lang('app.days')</th>
						</tr>
						</thead>
						<tbody>
						@if (count($info))
							@foreach ($info as $info_item)
								@include('backend.info.partials.row')
							@endforeach
						@else
							<tr><td colspan="3">@lang('app.no_data')</td></tr>
						@endif
						</tbody>
						<thead>
						<tr>
							<th>@lang('app.title')</th>
							<th>@lang('app.roles')</th>
							<th>@lang('app.days')</th>
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

		$(function() {
			$('.btn-box-tool').click(function(event){
				if( $('.info_show').hasClass('collapsed-box') ){
					$.cookie('info_show', '1');
				} else {
					$.removeCookie('info_show');
				}
			});

			if( $.cookie('info_show') ){
				$('.info_show').removeClass('collapsed-box');
				$('.info_show .btn-box-tool i').removeClass('fa-plus').addClass('fa-minus');
			}
		});
	</script>
@stop
