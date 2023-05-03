@extends('backend.layouts.app')

@section('page-title', trans('app.live_stats'))
@section('page-heading', trans('app.live_stats'))

@section('content')

    <script src="https://cdn.jsdelivr.net/npm/socket.io-client@2/dist/socket.io.js"></script>
    <script src="https://cdn.jsdelivr.net/vue/1.0.24/vue.js"></script>

    <section class="content-header">
        @include('backend.partials.messages')
    </section>

    <section class="content">

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">@lang('app.live_stats')</h3>
                <div class="pull-right box-tools">
                    <button type="button" class="btn btn-danger" id="stopActivity">
                        @lang('app.stop')
                    </button>
                    <button type="button" class="btn btn-success" id="startActivity">
                        @lang('app.start')
                    </button>
                </div>
            </div>
            <div class="box-body">

                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="stat-table">
                        <thead>
                        <tr>
                            <th>@lang('app.game')</th>
                            <th>@lang('app.user')</th>
                            <th>@lang('app.balance')</th>
                            <th>@lang('app.bet')</th>
                            <th>@lang('app.win')</th>
                            <th>@lang('app.in_game')</th>
                            <th>@lang('app.in_jpg')</th>
                            <th>@lang('app.profit')</th>
                            <th>@lang('app.slots')</th>
                            <th>@lang('app.bonus')</th>
                            <th>@lang('app.fish')</th>
                            <th>@lang('app.table_bank')</th>
                            <th>@lang('app.little')</th>
                            <th>@lang('app.total')</th>
                            <th>@lang('app.date')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if (count($statistics))
                            @foreach ($statistics as $stat)
                                @include('backend.stat.partials.row_live_stat')
                            @endforeach
                        @else
                            <tr><td colspan="16">@lang('app.no_data')</td></tr>
                        @endif
                        </tbody>
                        <thead>
                        <tr>
                            <th>@lang('app.game')</th>
                            <th>@lang('app.user')</th>
                            <th>@lang('app.balance')</th>
                            <th>@lang('app.bet')</th>
                            <th>@lang('app.win')</th>
                            <th>@lang('app.in_game')</th>
                            <th>@lang('app.in_jpg')</th>
                            <th>@lang('app.profit')</th>
                            <th>@lang('app.slots')</th>
                            <th>@lang('app.bonus')</th>
                            <th>@lang('app.fish')</th>
                            <th>@lang('app.table_bank')</th>
                            <th>@lang('app.little')</th>
                            <th>@lang('app.total')</th>
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

                $server = json_decode(file_get_contents(public_path() . '/socket_config.json'), true);
                $users = Auth::user()->hierarchyUsers();
                $filter = Request::get('type')?:'';

                echo 'var users = [' . implode(',', $users) . '];';
                echo 'var filter = "' . $filter . '";';

            @endphp

            var socket = new WebSocket("{{ $server['prefix_ws'].$server['host_ws'] }}:2053/live");

            var availible = {
                'StatGame': {{ intval(Auth::user()->hasPermission('stats.live')) }},
                'PayStat': {{ intval(Auth::user()->hasPermission('stats.pay')) }},
            };

            socket.onmessage= function(d){
				
			
            
var msg =JSON.parse(d.data);
                    
                    console.log($.inArray(parseInt(msg['shop_id']), [{{ implode(',', auth()->user()->availableShops()) }}]));

       if( !$.cookie('stopActivity') && availible[msg['type']] &&
                        $.inArray(parseInt(msg['shop_id']), [{{ implode(',', auth()->user()->availableShops()) }}]) > -1 &&
                        !(!users.includes(msg['user_id']) && msg['type'] == 'ShopStat' ) &&
                        msg['domain'] == '{{ request()->getHost() }}'
                    ){

                        if( filter == '' || (filter != '' && filter == msg['type']) ){
                            $('#stat-table tbody').prepend(
                                '<tr>'+
                                '<td>' + msg['Game'] + '</td>'+
                                '<td>' + msg['User'] + '</td>'+
                                '<td>' + msg['Balance'] + '</td>'+
                                '<td>' + msg['Bet'] + '</td>'+
                                '<td>' + msg['Win'] + '</td>'+
                                '<td>' + msg['IN_GAME'] + '</td>'+
                                '<td>' + msg['IN_JPG'] + '</td>'+
                                '<td>' + msg['Profit'] + '</td>'+
                                '<td>' + msg['Slots'] + '</td>'+
                                '<td>' + msg['Bonus'] + '</td>'+
                                '<td>' + msg['Fish'] + '</td>'+
                                '<td>' + msg['Table'] + '</td>'+
                                '<td>' + msg['Little'] + '</td>'+
                                '<td>' + msg['Total_bank'] + '</td>'+
                                '<td>' + msg['Date'] + '</td>'+
                                '</tr>'
                            );
                            remove();
                        }
                    }

			 };


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