@extends('backend.layouts.user')

@section('page-title', trans('app.live_stats'))
@section('page-heading', trans('app.live_stats'))

@section('content')

    <script src="https://cdn.jsdelivr.net/npm/socket.io-client@2/dist/socket.io.js"></script>
    <script src="https://cdn.jsdelivr.net/vue/1.0.24/vue.js"></script>

    <div class="row wow fadeIn">

                <div class="col-lg-12">
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
                                    <div class="element-info-text">
                                        <h5 class="element-inner-header">@lang('app.live_stats')</h5>
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
            <div class="col-md-12">
                <div class="form-group">
                    <label>@lang('app.type')</label>
                    {!! Form::select('type', ['' => 'All', 'PayStat' => 'Pay Stats', 'StatGame' => 'Game Stats', 'BankStat' => 'Bank Stats', 'ShopStat' => 'Shop Stats'], $filter, ['id' => 'type', 'class' => 'form-control']) !!}
                </div>
            </div>
        </div>
    </div>
    <div class="box-footer">
        <button type="submit" class="btn btn-primary">
            @lang('app.filter')
        </button>
        <button type="button" class="btn btn-danger" id="stopActivity">
            @lang('app.stop')
        </button>
        <button type="button" class="btn btn-success" id="startActivity">
            @lang('app.start')
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
                            <th>@lang('app.game')</th>
                            <th>@lang('app.user')</th>
                            <th>@lang('app.system')</th>
                            <th>@lang('app.in')</th>
                            <th>@lang('app.out')</th>
                            <th>@lang('app.balance')</th>
                            <th>@lang('app.bet')</th>
                            <th>@lang('app.win')</th>
                            <th>@lang('app.old')</th>
                            <th>@lang('app.new')</th>
                            <th>@lang('app.in_game')</th>
                            <th>@lang('app.in_jps')</th>
                            <th>@lang('app.in_jpg')</th>
                            <th>@lang('app.profit')</th>
                            <th>@lang('app.date')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if (count($statistics))
                            @foreach ($statistics as $stat)
                                @include('backend.stat.partials.row_live_stat')
                            @endforeach
                        @else
                            <tr><td colspan="15">@lang('app.no_data')</td></tr>
                        @endif
                        </tbody>
                        <thead>
                        <tr>
                            <th>@lang('app.name')</th>
                            <th>@lang('app.game')</th>
                            <th>@lang('app.user')</th>
                            <th>@lang('app.system')</th>
                            <th>@lang('app.in')</th>
                            <th>@lang('app.out')</th>
                            <th>@lang('app.balance')</th>
                            <th>@lang('app.bet')</th>
                            <th>@lang('app.win')</th>
                            <th>@lang('app.old')</th>
                            <th>@lang('app.new')</th>
                            <th>@lang('app.in_game')</th>
                            <th>@lang('app.in_jps')</th>
                            <th>@lang('app.in_jpg')</th>
                            <th>@lang('app.profit')</th>
                            <th>@lang('app.date')</th>
                        </tr>
                        </thead>
                    </table>
            </div>

        </div>

</div>
</div>
</div>
</div>

@stop

@section('scripts')
    <script>
        $(function() {

            var table = $('#stat-table');
            //var datatable = table.DataTable();

            $('#stopActivity').click(function(){
                $.cookie('stopActivity', '1'), { expires: 7 };
                $('#startActivity').css('display', '');
                $('#stopActivity').css('display', 'none');
            });

            $('#startActivity').click(function(){
                $.removeCookie('stopActivity');
                $('#startActivity').css('display', 'none');
                $('#stopActivity').css('display', '');
            });

            if($.cookie('stopActivity')){
                $('#startActivity').css('display', '');
                $('#stopActivity').css('display', 'none');
            } else{
                $('#startActivity').css('display', 'none');
                $('#stopActivity').css('display', '');
            }

            @php

                $users = Auth::user()->hierarchyUsers();

                $filter = Request::get('type')?:'';

                echo 'var users = [' . implode(',', $users) . '];';
                echo 'var filter = "' . $filter . '";';

            @endphp

            var socket = io.connect("{{  Request::server('SERVER_ADDR') }}:3000", {reconnect: true});
            var prior = ['ShopStat', 'BankStat'];

            var availible = {
                'StatGame': {{ intval(Auth::user()->hasPermission('stats.live')) }},
                'ShopStat': {{ intval(Auth::user()->hasPermission('stats.shop')) }},
                'BankStat': {{ intval(Auth::user()->hasPermission('stats.bank')) }},
                'PayStat': {{ intval(Auth::user()->hasPermission('stats.pay')) }},
            };

            socket.on('connect', function(){
                socket.on('Lives:NewLive', function(msg){

                    console.log(msg);
                    console.log($.inArray(parseInt(msg['shop_id']), [{{ implode(',', auth()->user()->availableShops()) }}]));

                    if( !$.cookie('stopActivity') && availible[msg['type']] &&
                        $.inArray(parseInt(msg['shop_id']), [{{ implode(',', auth()->user()->availableShops()) }}]) > -1 &&
                        !(!users.includes(msg['user_id']) && msg['type'] == 'ShopStat' ) &&
                        msg['domain'] == '{{ request()->getHost() }}'
                    ){

                        if( filter == '' || (filter != '' && filter == msg['type']) ){
                            $('#stat-table tbody').prepend(
                                '<tr>'+
                                '<td>' + msg['Name'] + '</td>'+
                                '<td>' + msg['Game'] + '</td>'+
                                '<td>' + msg['User'] + '</td>'+
                                '<td>' + msg['System'] + '</td>'+
                                '<td>' + msg['In'] + '</td>'+
                                '<td>' + msg['Out'] + '</td>'+
                                '<td>' + msg['Balance'] + '</td>'+
                                '<td>' + msg['Bet'] + '</td>'+
                                '<td>' + msg['Win'] + '</td>'+
                                '<td>' + msg['Old'] + '</td>'+
                                '<td>' + msg['New'] + '</td>'+
                                '<td>' + msg['IN_GAME'] + '</td>'+
                                '<td>' + msg['IN_JPS'] + '</td>'+
                                '<td>' + msg['IN_JPG'] + '</td>'+
                                '<td>' + msg['Profit'] + '</td>'+
                                '<td>' + msg['Date'] + '</td>'+
                                '</tr>'
                            );
                            remove();
                        }
                    }
                });
            });


            function remove(){
                var table = $('#stat-table tbody');
                var trs = table.find('tr');
                if( trs.length > 100){
                    for(var i=100; i<trs.length; i++){
                        table.children().slice(i).detach();
                    }
                }
            }


            $('input[name="dates"]').daterangepicker({
                timePicker: true,
                timePicker24Hour: true,
                startDate: moment().subtract(30, 'day'),
                endDate: moment().add(7, 'day'),

                locale: {
                    format: 'YYYY-MM-DD HH:mm'
                }
            });
            $('.btn-box-tool').click(function(event){
                if( $('.shop_stat_show').hasClass('collapsed-box') ){
                    $.cookie('shop_stat_show', '1');
                } else {
                    $.removeCookie('shop_stat_show');
                }
            });

            if( $.cookie('shop_stat_show') ){
                $('.shop_stat_show').removeClass('collapsed-box');
                $('.shop_stat_show .btn-box-tool i').removeClass('fa-plus').addClass('fa-minus');
            }
        });
    </script>
@stop
