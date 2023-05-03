@extends('backend.layouts.user')

@section('page-title', trans('app.users'))
@section('page-heading', trans('app.users'))
<style>
    .content-w table.dataTable th, .content-w table.dataTable td {
    font-size: 14px !important;
}
</style>
@section('content')
<!--Grid row-->
<div class="row wow fadeIn">

<!--Grid column-->
<div class="col-md-9 mb-4">
<section class="content-header">
		@include('backend.partials.messages')
	</section>
    <div class="content-box">
        <div class="element-wrapper">
            <div class="element-box-tp">
                <div class="table-responsive">
                    <div id="users_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-hover table-bordered dataTable no-footer" id="users" width="100%" role="grid" aria-describedby="users_info" style="width: 100%;">
                                    <thead>
                                        <tr role="row">
                                            <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 285px;">@lang('app.name')</th>
                                            @permission('users.balance.manage')
                                            <th class="sorting_disabled text-center" rowspan="1" colspan="1" style="width: 96px;">@lang('app.balance')</th>
                                            <th class="hidden-lg-down text-center sorting_disabled d-none d-xl-table-cell d-lg-table-cell d-md-table-cell" rowspan="1" colspan="1" style="width: 206px;">Game</th>
                                            <th style="min-width: 130px; width: 132px;" class="text-center sorting_disabled" rowspan="1" colspan="1">@lang('app.pay_in') /@lang('app.pay_out')
                                            </th>
                                            @endpermission

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($users))
                                            @foreach ($users as $user)
                                                @include('backend.user.partials.row-cashier')
                                            @endforeach
                                        @else
                                            <tr><td colspan="4">@lang('app.no_data')</td></tr>
                                        @endif
                                    </tbody>
                                </table>
                                {{$users->links()}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Grid column-->  

<!--Grid column-->
<div class="col-md-3 mb-4">

    <div class="content-panel rightBarLogs" id="rightBarLogs" style="padding-top: 0px; padding:0px;">
        <div class="content-panel-close"><i class="os-icon os-icon-close"></i></div>
        <div id="logs">
            <div class="content-i content-i-2">
                <div class="element-wrapper element-wrapper-2">
                    <div class="rowrightdiv text-center">
                        @if(auth()->user()->hasRole('cashier') && $openshift = \VanguardLTE\OpenShift::where(['shop_id' => auth()->user()->shop_id, 'end_date' => NULL])->first())
                        @php $summ = \VanguardLTE\User::where(['shop_id' => auth()->user()->shop_id, 'role_id' => 1])->sum('balance'); @endphp
                        <div class="col-sm-12 b-r b-b">
                            <div class="el-tablo centered el-tabloPiso">
                                <div class="value text-primary top_credits">


                                @if( Auth::user()->hasRole(['cashier', 'manager']) )
                            @php
                                $shop = \VanguardLTE\Shop::find( auth()->user()->present()->shop_id );
                                echo $shop?number_format($shop->balance,2,".",""):0;
                            @endphp
                        @if( auth()->user()->present()->shop )
                            {{ auth()->user()->present()->shop->currency }}
                        @endif
                        @else
                            {{ number_format(auth()->user()->present()->balance,2,".","") }}
                            @if( auth()->user()->present()->shop )
                                {{ auth()->user()->present()->shop->currency }}
                            @endif
                        @endif
                                </div>
                                <div class="label">@lang('app.balance')</div>
                            </div>
                        </div>

                        <div class="col-sm-4 b-r b-b">
                            <div class="el-tablo centered el-tabloPiso">
                                <div class="value text-success statsTop top_in">{{ number_format($openshift->money_in, 2, ".", "") }}</div>
                                <div class="label">@lang('app.in')</div>
                            </div>
                        </div>
                        <div class="col-sm-4 b-r b-b">
                            <div class="el-tablo centered el-tabloPiso">
                                <div class="value text-danger statsTop top_out">{{ number_format ($openshift->money_out, 2, ".", "") }}</div>
                                <div class="label">@lang('app.out')</div>
                            </div>
                        </div>
                        <div class="col-sm-4 b-r b-b">
                            <div class="el-tablo centered el-tabloPiso">
                            @php
								$total = $openshift->money_in - $openshift->money_out;
							@endphp
                                <div class="value text-primary statsTop top_total">{{ number_format ($total, 2, ".", "") }}</div>
                                <div class="label">@lang('app.total')</div>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="rowrightdiv text-center mt-4">
                        <div class="col-md-12">
                    <a class="btn btn-success" href="{{ route('backend.start_shift') }}"> @lang('app.start_shift')</a></li>
                        </div>
                    </div>
                </div>
            </div>
            <div class="logs_2" style="padding-top: 15px;"></div>
        </div>
    </div>


</div>
<!--Grid column-->

</div>
<!--Grid row-->
<div class="modal fade" id="openAddModal" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('backend.user.balance.update') }}" method="POST">
                    <div class="modal-header">
                        <h4 class="modal-title">@lang('app.balance') @lang('app.pay_in')</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="fa fa-close"></i></button>
                    </div>
                    <div class="modal-body">
						@if($happyhour && auth()->user()->hasRole('cashier'))
							<div class="alert alert-success">
								<h4>@lang('app.happyhours')</h4>
								<p> @lang('app.all_player_deposits') {{ $happyhour->multiplier }}</p>
							</div>
						@endif
						<div class="form-group">
							<label for="OutSum">@lang('app.sum')</label>
							<input type="text" class="form-control" id="OutSum" name="summ" placeholder="@lang('app.sum')" required>
							<input type="hidden" name="type" value="add">
							<input type="hidden" id="AddId" name="user_id">
							<input type="hidden" name="_token" value="{{ csrf_token() }}">
						</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">@lang('app.close')</button>
                        <button type="submit" class="btn btn-primary">@lang('app.pay_in')</button>
                    </div>
                </form>
			</div>
		</div>
	</div>

	<div class="modal fade" id="openOutModal" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
                <form action="{{ route('backend.user.balance.update') }}" method="POST" id="outForm">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('app.balance') @lang('app.pay_out')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-close"></i></span></button>
                </div>
                <div class="modal-body">
                        <div class="form-group">
                            <label for="OutSum">@lang('app.sum')</label>
                            <input type="text" class="form-control" id="OutSum" name="summ" required autofocus>
                            <input type="hidden" name="type" value="out">
                            <input type="hidden" id="outAll" name="all" value="0">
                            <input type="hidden" id="OutId" name="user_id">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">@lang('app.close')</button>
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

		$(function() {

		var table = $('#users-table').dataTable();
		$("#view").change(function () {
			$("#shops-form").submit();
		});

		$("#filter").detach().appendTo("div.toolbar");


		$("#status").change(function () {
			$("#users-form").submit();
		});
		$("#role").change(function () {
			$("#users-form").submit();
		});
		$('.addPayment').click(function(event){
            console.log("🚀 ~ file: list2.blade.php ~ line 225 ~ $ ~ event", event)
			if( $(event.target).is('.newPayment') ){
				var id = $(event.target).attr('data-id');
			}else{
				var id = $(event.target).parents('.newPayment').attr('data-id');
			}
			$('#AddId').val(id);

		});

		$('.outPayment').click(function(event){
			if( $(event.target).is('.newPayment') ){
				var id = $(event.target).attr('data-id');
			}else{
				var id = $(event.target).parents('.newPayment').attr('data-id');
			}
			$('#OutId').val(id);
			$('#outAll').val('');
		});

		$('#doOutAll').click(function () {
			$('#outAll').val('1');
			$('form#outForm').submit();
		});


		$('.btn-box-tool').click(function(event){
			if( $('.users_show').hasClass('collapsed-box') ){
				$.cookie('users_show', '1');
			} else {
				$.removeCookie('users_show');
			}
		});

		if( $.cookie('users_show') ){
			$('.users_show').removeClass('collapsed-box');
			$('.users_show .btn-box-tool i').removeClass('fa-plus').addClass('fa-minus');
		}
		});
	</script>
@stop
