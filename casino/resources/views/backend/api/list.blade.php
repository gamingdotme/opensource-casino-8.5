@extends('backend.layouts.app')

@section('page-title', trans('app.api_keys'))
@section('page-heading', trans('app.api_keys'))

@section('content')

	<section class="content-header">
		@include('backend.partials.messages')
	</section>

	<section class="content">
		<div class="box box-danger collapsed-box api_show">
			<div class="box-header with-border">
				<h3 class="box-title">@lang('app.filter')</h3>
				<div class="box-tools pull-right">
					<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
				</div>
			</div>
			<div class="box-body">
				<div class="row">
					<form action="" method="GET" id="users-form" >
						<div class="col-md-6">
							<input type="text" class="form-control" name="search" value="{{ Request::get('search') }}" placeholder="@lang('app.search_for_users')">
						</div>
						<div class="col-md-6">
							{!! Form::select('status', ['' => 'All', '0' => 'Disabled', '1' => 'Active'], Request::get('status'), ['id' => 'status', 'class' => 'form-control input-solid']) !!}
						</div>
					</form>
				</div>
			</div>
		</div>

		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title">@lang('app.api_keys')</h3>
				<div class="pull-right box-tools">
					@permission('api.add')
					<a href="{{ route('backend.api.create') }}" class="btn btn-block btn-primary btn-sm">@lang('app.add')</a>
					@endpermission
				</div>
			</div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
					<thead>
					<tr>
						<th>@lang('app.api_key')</th>
						<th>@lang('app.api_ip')</th>
						<th>@lang('app.shop')</th>
						<th>@lang('app.status')</th>
					</tr>
					</thead>
					<tbody>
					@if (count($api))
						@foreach ($api as $api_item)
							@include('backend.api.partials.row')
						@endforeach
					@else
						<tr><td colspan="4">@lang('app.no_data')</td></tr>
					@endif
					</tbody>
					<thead>
					<tr>
						<th>@lang('app.api_key')</th>
						<th>@lang('app.api_ip')</th>
						<th>@lang('app.shop')</th>
						<th>@lang('app.status')</th>
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
			var table = $('#api-table').DataTable({
				orderCellsTop: true,
				dom: '<"toolbar">frtip',

			});

			$("#filter").detach().appendTo("div.toolbar");

			$("#status").change(function () {
				$("#users-form").submit();
			});

			$('.btn-box-tool').click(function(event){
				if( $('.api_show').hasClass('collapsed-box') ){
					$.cookie('api_show', '1');
				} else {
					$.removeCookie('api_show');
				}
			});

			if( $.cookie('api_show') ){
				$('.api_show').removeClass('collapsed-box');
				$('.api_show .btn-box-tool i').removeClass('fa-plus').addClass('fa-minus')
			}
		});
	</script>
@stop