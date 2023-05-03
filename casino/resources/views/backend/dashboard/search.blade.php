@extends('backend.layouts.app')

@section('page-title', trans('app.search'))
@section('page-heading', trans('app.search'))

@section('content')

    <section class="content-header">
        @include('backend.partials.messages')
    </section>

    <section class="content">

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">{{ __('app.search_results', ['query' => $query]) }}</h3>
            </div>
        </div>



        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">@lang('app.users')</h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>@lang('app.username')</th>
                            @if(auth()->user()->hasRole('admin'))
                                <th>Log In</th>
                            @endif

                            <th>@lang('app.balance')</th>

                            <th>@lang('app.rating')</th>
                            <th>@lang('app.tb')</th>
                            <th>@lang('app.pb')</th>
                            <th>@lang('app.de')</th>
                            <th>@lang('app.if')</th>
                            <th>@lang('app.hh')</th>
                            <th>@lang('app.refund')</th>
                            <th>@lang('app.wb')</th>
                            <th>@lang('app.sb')</th>

                            <th>@lang('app.pay_in')</th>
                            <th>@lang('app.pay_out')</th>

                            <th>@lang('app.shop')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if (count($users))
                            @foreach ($users as $user)
                                @include('backend.user.partials.row', ['show_shop' => true])
                            @endforeach
                        @else
                            <tr><td colspan="15">@lang('app.no_data')</td></tr>
                        @endif
                        </tbody>
                        <thead>
                        <tr>
                            <th>@lang('app.username')</th>
                            @if(auth()->user()->hasRole('admin'))
                                <th>Log In</th>
                            @endif

                            <th>@lang('app.balance')</th>

                            <th>@lang('app.rating')</th>
                            <th>@lang('app.tb')</th>
                            <th>@lang('app.pb')</th>
                            <th>@lang('app.de')</th>
                            <th>@lang('app.if')</th>
                            <th>@lang('app.hh')</th>
                            <th>@lang('app.refund')</th>
                            <th>@lang('app.wb')</th>
                            <th>@lang('app.sb')</th>

                            <th>@lang('app.pay_in')</th>
                            <th>@lang('app.pay_out')</th>

                            <th>@lang('app.shop')</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">@lang('app.pay_stats')</h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            @if(auth()->user()->hasRole(['admin']))
                                <th>@lang('app.admin')</th>
                            @endif
                            @if(auth()->user()->hasRole(['admin', 'agent']))
                                <th>@lang('app.agent')</th>
                            @endif
                            @if(auth()->user()->hasRole(['admin', 'agent', 'distributor']))
                                <th>@lang('app.distributor')</th>
                            @endif
                            @if(auth()->user()->hasRole(['admin', 'agent', 'distributor']))
                                <th>@lang('app.shop')</th>
                            @endif
                            <th>@lang('app.cashier')</th>
                            <th>@lang('app.type')</th>
                            <th>@lang('app.user')</th>
                            @if(auth()->user()->hasRole(['admin', 'agent']))
                                <th>@lang('app.agent') @lang('app.in')</th>
                                <th>@lang('app.agent') @lang('app.out')</th>
                            @endif
                            @if(auth()->user()->hasRole(['admin', 'agent', 'distributor']))
                                <th>@lang('app.distributor') @lang('app.in')</th>
                                <th>@lang('app.distributor') @lang('app.out')</th>
                            @endif
                            @if(auth()->user()->hasRole(['admin']))
                                <th>@lang('app.type') @lang('app.in')</th>
                                <th>@lang('app.type') @lang('app.out')</th>
                            @endif
                            @if(auth()->user()->hasRole(['admin', 'agent', 'distributor']))
                                <th>@lang('app.credit') @lang('app.in')</th>
                                <th>@lang('app.credit') @lang('app.out')</th>
                            @endif
                            <th>@lang('app.money') @lang('app.in')</th>
                            <th>@lang('app.money') @lang('app.out')</th>
                            <th>@lang('app.date')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if (count($pay_stats))
                            @foreach ($pay_stats as $transaction)
                                @include('backend.stat.partials.transaction_stat')
                            @endforeach
                        @else
                            <tr><td colspan="18">@lang('app.no_data')</td></tr>
                        @endif
                        </tbody>
                        <thead>
                        <tr>
                            @if(auth()->user()->hasRole(['admin']))
                                <th>@lang('app.admin')</th>
                            @endif
                            @if(auth()->user()->hasRole(['admin', 'agent']))
                                <th>@lang('app.agent')</th>
                            @endif
                            @if(auth()->user()->hasRole(['admin', 'agent', 'distributor']))
                                <th>@lang('app.distributor')</th>
                            @endif
                            @if(auth()->user()->hasRole(['admin', 'agent', 'distributor']))
                                <th>@lang('app.shop')</th>
                            @endif
                            <th>@lang('app.cashier')</th>
                            <th>@lang('app.type')</th>
                            <th>@lang('app.user')</th>
                            @if(auth()->user()->hasRole(['admin', 'agent']))
                                <th>@lang('app.agent') @lang('app.in')</th>
                                <th>@lang('app.agent') @lang('app.out')</th>
                            @endif
                            @if(auth()->user()->hasRole(['admin', 'agent', 'distributor']))
                                <th>@lang('app.distributor') @lang('app.in')</th>
                                <th>@lang('app.distributor') @lang('app.out')</th>
                            @endif
                            @if(auth()->user()->hasRole(['admin']))
                                <th>@lang('app.type') @lang('app.in')</th>
                                <th>@lang('app.type') @lang('app.out')</th>
                            @endif
                            @if(auth()->user()->hasRole(['admin', 'agent', 'distributor']))
                                <th>@lang('app.credit') @lang('app.in')</th>
                                <th>@lang('app.credit') @lang('app.out')</th>
                            @endif
                            <th>@lang('app.money') @lang('app.in')</th>
                            <th>@lang('app.money') @lang('app.out')</th>
                            <th>@lang('app.date')</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>


        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">@lang('app.game_stats')</h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                        <tr>
                            <th>@lang('app.game')</th>
                            <th>@lang('app.user')</th>
                            <th>@lang('app.balance')</th>
                            <th>@lang('app.bet')</th>
                            <th>@lang('app.win')</th>
                            @if(auth()->user()->hasRole('admin'))
                                <th>@lang('app.in_game')</th>
                                <th>@lang('app.in_jpg')</th>
                                <th>@lang('app.profit')</th>
                            @endif
                            <th>@lang('app.denomination')</th>
                            <th>@lang('app.slots')</th>
                            <th>@lang('app.fish')</th>
                            <th>@lang('app.table_bank')</th>
                            <th>@lang('app.little')</th>
                            <th>@lang('app.bonus')</th>
                            <th>@lang('app.total')</th>
                            <th>@lang('app.date')</th>
                        </tr>
                        </tr>
                        </thead>
                        <tbody>
                        @if (count($game_stats))
                            @foreach ($game_stats as $stat)
                                @include('backend.games.partials.row_stat', ['show_shop' => true])
                            @endforeach
                        @else
                            <tr><td colspan="16">@lang('app.no_data')</td></tr>
                        @endif
                        </tbody>
                        <thead>
                        <tr>
                        <tr>
                            <th>@lang('app.game')</th>
                            <th>@lang('app.user')</th>
                            <th>@lang('app.balance')</th>
                            <th>@lang('app.bet')</th>
                            <th>@lang('app.win')</th>
                            @if(auth()->user()->hasRole('admin'))
                                <th>@lang('app.in_game')</th>
                                <th>@lang('app.in_jpg')</th>
                                <th>@lang('app.profit')</th>
                            @endif
                            <th>@lang('app.denomination')</th>
                            <th>@lang('app.slots')</th>
                            <th>@lang('app.fish')</th>
                            <th>@lang('app.table_bank')</th>
                            <th>@lang('app.little')</th>
                            <th>@lang('app.bonus')</th>
                            <th>@lang('app.total')</th>
                            <th>@lang('app.date')</th>
                        </tr>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>







    </section>

    <div class="modal fade" id="openAddModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('backend.user.balance.update') }}" method="POST">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">@lang('app.balance') @lang('app.pay_in')</h4>
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
                <form action="{{ route('backend.user.balance.update') }}" method="POST">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">@lang('app.balance') @lang('app.pay_out')</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="OutSum">@lang('app.sum')</label>
                            <input type="text" class="form-control" id="OutSum" name="summ" placeholder="@lang('app.sum')" required>
                            <input type="hidden" name="type" value="out">
                            <input type="hidden" id="OutId" name="user_id">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">@lang('app.close')</button>
                        <button type="submit" class="btn btn-primary">@lang('app.pay_out')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@stop

@section('scripts')
    <script>

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

    </script>
@stop
