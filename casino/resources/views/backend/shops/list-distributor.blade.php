@extends('backend.layouts.user')

@section('page-title', trans('app.shops'))
@section('page-heading', trans('app.shops'))
<style>
    .content-w table.dataTable th, .content-w table.dataTable td {
    font-size: 12px !important;
}
</style>
@section('content')
<div class="row wow fadeIn">

                <div class="col-md-9 mb-4">
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
                <div class="element-info-text d-flex justify-content-between w-100">
                    <h5 class="element-inner-header">@lang('app.shops')</h5>
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

                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('app.name')</label>
                                    <input type="text" class="form-control" name="name" value="{{ Request::get('name') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('app.credit_from')</label>
                                    <input type="text" class="form-control" name="credit_from" value="{{ Request::get('credit_from') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('app.credit_to')</label>
                                    <input type="text" class="form-control" name="credit_to" value="{{ Request::get('credit_to') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('app.frontend')</label>
                                    <input type="text" class="form-control" name="frontend" value="{{ Request::get('frontend') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('app.percent_from')</label>
                                    <input type="text" class="form-control" name="percent_from" value="{{ Request::get('percent_from') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('app.percent_to')</label>
                                    <input type="text" class="form-control" name="percent_to" value="{{ Request::get('percent_to') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('app.order')</label>
                                    @php
                                        $orders = array_combine(array_merge([''], \VanguardLTE\Shop::$values['orderby']), array_merge([''], \VanguardLTE\Shop::$values['orderby']));
                                    @endphp
                                    {!! Form::select('order', $orders, Request::get('status'), ['id' => 'order', 'class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('app.currency')</label>
                                    @php
                                        $currencies = array_combine(\VanguardLTE\Shop::$values['currency'], \VanguardLTE\Shop::$values['currency']);
                                    @endphp
                                    {!! Form::select('currency', $currencies, Request::get('currency'), ['id' => 'currency', 'class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('app.categories')</label>
                                    {!! Form::select('categories[]', $categories->pluck('title','id'), Request::get('categories'), ['id' => 'type', 'class' => 'form-control select2', 'multiple' => true, 'style' => 'width: 100%;']) !!}

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('app.status')</label>
                                    {!! Form::select('status', ['' => __('app.all'), '1' => __('app.active'), '0' => __('app.disabled')], Request::get('status'), ['id' => 'type', 'class' => 'form-control']) !!}
                                </div>
                            </div>

                            @if(auth()->user()->hasRole('admin'))
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('app.agents') & @lang('app.distributors')</label>
                                        {!! Form::select('users', ['' => '---'] + $agents + $distributors, Request::get('users'), ['id' => 'users', 'class' => 'form-control select2']) !!}
                                    </div>
                                </div>
                            @endif

                            @if(auth()->user()->hasRole(['agent']))
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('app.distributors')</label>
                                        {!! Form::select('users', ['' => '---'] + $distributors, Request::get('users'), ['id' => 'users', 'class' => 'form-control select2']) !!}
                                    </div>
                                </div>
                            @endif

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




                    <table class="table table-striped table-bordered table-sm dataTable no-footer" id="stat-table">
                    <thead>
					<tr>
						<th>@lang('app.name')</th>
						<!-- <th>@lang('app.go_to_shop')</th> -->
						<th>@lang('app.distributor')</th>
						<th>@lang('app.id')</th>
						<th>@lang('app.credit')</th>
						<th>@lang('app.percent')</th>
						<th>@lang('app.frontend')</th>
						<th>@lang('app.currency')</th>
						<th>@lang('app.order')</th>
						<th>@lang('app.status')</th>
						<th>@lang('app.pay_in')</th>
						<th>@lang('app.pay_out')</th>
					</tr>
					</thead>
					<tbody>
					@if (count($shops))
						@foreach ($shops as $shop)
							@include('backend.shops.partials.row-distributor')
						@endforeach
					@else
						<tr><td colspan="11">@lang('app.no_data')</td></tr>
					@endif
					</tbody>
					<thead>
					<tr>
						<th>@lang('app.name')</th>
						<!-- <th>@lang('app.go_to_shop')</th> -->
						<th>@lang('app.distributor')</th>
						<th>@lang('app.id')</th>
						<th>@lang('app.credit')</th>
						<th>@lang('app.percent')</th>
						<th>@lang('app.frontend')</th>
						<th>@lang('app.currency')</th>
						<th>@lang('app.order')</th>
						<th>@lang('app.status')</th>
						<th>@lang('app.pay_in')</th>
						<th>@lang('app.pay_out')</th>
					</tr>
					</thead>
                            </table>

							{{ $shops->links() }}
                        </div>
                    </div>

</div>
</div>
</div>
<div class="col-md-3 mb-4 text-center">
<div class="content-panel rightBarLogs" id="rightBarLogs" style="padding-top: 0px; padding:0px;">
                        <div class="content-panel-close"><i class="os-icon os-icon-close"></i></div>
                        <div id="logs">
                            <div class="content-i content-i-2">
                                <div class="element-wrapper element-wrapper-2">
                                    <div class="rowrightdiv">
                                        <div class="col-sm-12 b-r b-b">
                                            <div class="el-tablo centered el-tabloPiso">
                                                <div class="value text-primary top_credits">{{ number_format( $stats['credit'], 2 ) }}</div>
                                                <div class="label">@lang('app.total_credit')</div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 b-r b-b">
                                            <!-- small box -->
                                            <div class="el-tablo centered el-tabloPiso">
                                                <div class="value text-success statsTop top_in">
                                                    {{ $stats['shops'] }}
                                                </div>
                                                <div class="label">
                                                    @lang('app.total_shops')
                                                </div>
                                            </div>
                                        </div>
                                        <!-- ./col -->
                                        @if(auth()->user()->hasRole('admin'))
                                        <div class="col-sm-12 b-r b-b">
                                            <!-- small box -->
                                            <div class="el-tablo centered el-tabloPiso">
                                                <div class="value text-success statsTop top_in">
                                                    {{ ($stats['agents']) }}
                                                </div>
                                                <div class="label">
                                                    @lang('app.total_agents')
                                                </div>
                                            </div>
                                        </div>
                                        <!-- ./col -->
                                        @endif
                                        @if(auth()->user()->hasRole(['admin','agent']))
                                        <div class="col-sm-12 b-r b-b">
                                            <!-- small box -->
                                            <div class="el-tablo centered el-tabloPiso">
                                                <div class="value text-success statsTop top_in">
                                                    {{ ($stats['distributors']) }}
                                                </div>
                                                <div class="label">
                                                    @lang('app.total_distributors')
                                                </div>
                                            </div>
                                        </div>
                                        <!-- ./col -->
                                        @endif
                                        @if(auth()->user()->hasRole(['agent','distributor']))
                                            <div class="col-sm-12 b-r b-b">
                                                <!-- small box -->
                                                <div class="el-tablo centered el-tabloPiso">
                                                    <div class="value text-success statsTop top_in">
                                                        {{ ($stats['managers']) }}
                                                    </div>
                                                    <div class="label">
                                                        @lang('app.total_managers')
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- ./col -->
                                        @endif
                                        @if(auth()->user()->hasRole(['distributor','manager']))
                                            <div class="col-sm-12 b-r b-b">
                                                <!-- small box -->
                                                <div class="el-tablo centered el-tabloPiso">
                                                    <div class="value text-success statsTop top_in">
                                                        {{ ($stats['cashiers']) }}
                                                    </div>
                                                    <div class="label">
                                                        @lang('app.total_cashiers')
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- ./col -->
                                        @endif
                                        @if(auth()->user()->hasRole(['manager','cashier']))
                                            <div class="col-sm-12 b-r b-b">
                                                <!-- small box -->
                                                <div class="el-tablo centered el-tabloPiso">
                                                    <div class="value text-success statsTop top_in">
                                                        {{ ($stats['users']) }}
                                                    </div>
                                                    <div class="label">
                                                        @lang('app.total_users')
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- ./col -->
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="logs_2" style="padding-top: 15px;"></div>
                        </div>
                    </div>
</div>
</div>

	<!-- Modal -->
	<div class="modal fade" id="openAddModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form action="{{ route('backend.shop.balance') }}" method="POST">
					<div class="modal-header">
						<h4 class="modal-title">@lang('app.balance') @lang('app.pay_in')</h4>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true"><i class="fa fa-close"></i></span></button>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label for="AddSum">@lang('app.sum')</label>
							<input type="text" class="form-control" id="AddSum" name="summ" placeholder="@lang('app.sum')" required>
							<input type="hidden" name="type" value="add">
							<input type="hidden" id="AddId" name="shop_id">
							<input type="hidden" name="_token" value="{{ csrf_token() }}">
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('app.close')</button>
						<button type="submit" class="btn btn-primary">@lang('app.pay_in')</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div class="modal fade" id="openOutModal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form action="{{ route('backend.shop.balance') }}" method="POST" id="outForm">
                    <div class="modal-header">
                        <h4 class="modal-title">@lang('app.balance') @lang('app.pay_out')</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="fa fa-close"></i></span></button>
                    </div>
					<div class="modal-body">
						<div class="form-group">
							<label for="OutSum">@lang('app.sum')</label>
							<input type="text" class="form-control" id="OutSum" name="summ" placeholder="@lang('app.sum')" required>
							<input type="hidden" id="outAll" name="all" value="0">
							<input type="hidden" name="type" value="out">
							<input type="hidden" id="OutId" name="shop_id">
							<input type="hidden" name="_token" value="{{ csrf_token() }}">
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('app.close')</button>
						<button type="button" class="btn btn-danger" id="doOutAll">@lang('app.pay_out') @lang('app.all')</button>
						<button type="submit" class="btn btn-primary">@lang('app.pay_out')</button>
					</div>
				</form>
			</div>
		</div>
	</div>

@stop

@section('scripts')
	<script>
		$('#shops-table').dataTable();
		$("#view").change(function () {
			$("#shops-form").submit();
		});
		$('.addPayment').click(function(event){
			console.log($(event.target));
			var item = $(event.target).hasClass('addPayment') ? $(event.target) : $(event.target).parent();
			var id = item.attr('data-id');
            console.log("🚀 ~ file: list-distributor.blade.php ~ line 375 ~ $ ~ id", id)
			$('#AddId').val(id);
		});

		$('.outPayment').click(function(event){
			console.log($(event.target));
			var item = $(event.target).hasClass('outPayment') ? $(event.target) : $(event.target).parent();
			var id = item.attr('data-id');
			$('#OutId').val(id);
			$('#outAll').val('0');
		});


		$('#doOutAll').click(function () {
			$('#outAll').val('1');
			$('form#outForm').submit();
		});

		$('.btn-box-tool').click(function(event){
			if( $('.shops_show').hasClass('collapsed-box') ){
				$.cookie('shops_show', '1');
			} else {
				$.removeCookie('shops_show');
			}
		});

		if( $.cookie('shops_show') ){
			$('.shops_show').removeClass('collapsed-box');
			$('.shops_show .btn-box-tool i').removeClass('fa-plus').addClass('fa-minus');
		}
	</script>
@stop
