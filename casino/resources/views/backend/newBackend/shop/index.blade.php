@extends('backend.newBackend.layouts.layout')
@section('content')


    <ul class="breadcrumb" style="margin-bottom: 0px;">
        <li class="breadcrumb-item"><a href="netpos" data-original-title="" title="">Home</a></li>
        <li class="breadcrumb-item"><a href="netpos" data-original-title="" title="">Refresh</a></li>
        <div class="arrowcss hidden-sm-down">
            <span style="float: right; font-size: 20px; margin-top: -15px; padding-top: 9px;" class="hidden-xs-down ">
                <a href="#" id="right_arrow" class="none_none btn2" data-original-title="" title=""
                    style="display: inline;">
                    <i class="os-icon os-icon-arrow-right2"></i>
                </a>
                <a href="#" id="left_arrow" class="none_none btn2" data-original-title="" title="" style="display: none;">
                    <i class="os-icon os-icon-arrow-left"></i>
                </a>
            </span>
        </div>
    </ul>
    <div class="content-panel-toggler"><i class="os-icon os-icon-grid-squares-22"></i><span>Sidebar</span>
    </div>
    <div class="content-i">
        <style>
            .table td,
            .table th {
                padding: .25rem;

            }

        </style>
        <div class="modal-body none_none" id="printDivVoucher">
            <style>
                @page {
                    size: 88mm 0mm;
                    margin: 6mm;
                    padding: 0;
                    border: none;
                    border-collapse: collapse;
                }

                @media print {

                    .pstyle2 {
                        margin-top: 60px;
                    }

                    .pstyle3 {
                        margin-top: -15px;
                    }

                    .pstyle4 {
                        margin-top: 15px;
                    }

                    .pstyle5 {
                        padding-top: 50px;
                    }

                    #section-to-print,
                    #section-to-print * {
                        visibility: visible;
                        font-size: 22px;
                        padding-top: 0px;
                    }

                    #section-to-print {
                        position: absolute;
                        left: 0;
                        top: 0;
                    }

                    .printbotom222 {
                        padding-bottom: 25mm;
                    }

                    .printbotom {
                        display: none;
                    }

                    .center_div {
                        display: none;
                    }
                }

                .button2 {
                    display: inline;
                    *display: inline;
                    zoom: 1;
                    padding: 6px 20px;
                    margin: 0;
                    cursor: pointer;
                    border: 1px solid #bbb;
                    overflow: visible;
                    font: bold 13px arial, helvetica, sans-serif;
                    text-decoration: none;
                    white-space: nowrap;
                    color: #555;

                    background-color: #ddd;
                    background-image: -webkit-gradient(linear, left top, left bottom, from(rgba(255, 255, 255, 1)), to(rgba(255, 255, 255, 0)));
                    background-image: -webkit-linear-gradient(top, rgba(255, 255, 255, 1), rgba(255, 255, 255, 0));
                    background-image: -moz-linear-gradient(top, rgba(255, 255, 255, 1), rgba(255, 255, 255, 0));
                    background-image: -ms-linear-gradient(top, rgba(255, 255, 255, 1), rgba(255, 255, 255, 0));
                    background-image: -o-linear-gradient(top, rgba(255, 255, 255, 1), rgba(255, 255, 255, 0));
                    background-image: linear-gradient(top, rgba(255, 255, 255, 1), rgba(255, 255, 255, 0));

                    -webkit-transition: background-color .2s ease-out;
                    -moz-transition: background-color .2s ease-out;
                    -ms-transition: background-color .2s ease-out;
                    -o-transition: background-color .2s ease-out;
                    transition: background-color .2s ease-out;
                    background-clip: padding-box;
                    /* Fix bleeding */
                    -moz-border-radius: 3px;
                    -webkit-border-radius: 3px;
                    border-radius: 3px;
                    -moz-box-shadow: 0 1px 0 rgba(0, 0, 0, .3), 0 2px 2px -1px rgba(0, 0, 0, .5), 0 1px 0 rgba(255, 255, 255, .3) inset;
                    -webkit-box-shadow: 0 1px 0 rgba(0, 0, 0, .3), 0 2px 2px -1px rgba(0, 0, 0, .5), 0 1px 0 rgba(255, 255, 255, .3) inset;
                    box-shadow: 0 1px 0 rgba(0, 0, 0, .3), 0 2px 2px -1px rgba(0, 0, 0, .5), 0 1px 0 rgba(255, 255, 255, .3) inset;
                    text-shadow: 0 1px 0 rgba(255, 255, 255, .9);

                    -webkit-touch-callout: none;
                    -webkit-user-select: none;
                    -khtml-user-select: none;
                    -moz-user-select: none;
                    -ms-user-select: none;
                    user-select: none;
                    height: 50px;
                    width: 100px;
                    text-align: center;
                    padding: 25px;
                    font-size: 40px;
                }

                .none_none {
                    display: none;
                }

                .center_div {
                    margin: auto;
                    width: 50%;
                    padding: 10px;
                }

            </style>
        </div>
        <div class="content-box">
            <div class="element-wrapper">
                <div class="element-box-tp">
                    <div class="table-responsive">
                        <div id="users_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">

                            <div class="row">
                                <div class="col-sm-12">
                                    <table class="table table-hover table-bordered dataTable no-footer" id="users"
                                        width="100%" role="grid" aria-describedby="users_info" style="width: 100%;">
                                        <thead>
                                            <tr role="row">
                                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 179px;">
                                                    Name </th>
                                                <th class="sorting_disabled text-center" rowspan="1" colspan="1"
                                                    style="width: 68px;">Credits</th>
                                                <th class="hidden-lg-down text-center sorting_disabled d-none d-xl-table-cell d-lg-table-cell d-md-table-cell"
                                                    rowspan="1" colspan="1" style="width: 136px;">Bonus</th>
                                            
                                                <th style="min-width: 130px; width: 132px;"
                                                    class="text-center sorting_disabled" rowspan="1" colspan="1">In
                                                    /Out</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-5"></div>
                                <div class="col-sm-7"></div>
                            </div>
                            <div class="top"></div>
                          
                            <div class="clear"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-panel rightBarLogs" id="rightBarLogs" style="padding-top: 0px; padding:0px;">
            <div class="content-panel-close"><i class="os-icon os-icon-close"></i></div>
            <div id="logs">
                <div class="content-i content-i-2">
                    <div class="element-wrapper element-wrapper-2">
                        <div class="rowrightdiv">
                            <div class="col-sm-12 b-r b-b">
                                <div class="el-tablo centered el-tabloPiso">
                                    <div class="value text-primary top_credits">
                                        
                    @if( Auth::user()->hasRole(['cashier']) )
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
                                    <div class="label">Credits</div>
                                </div>
                            </div>
                            <div class="col-sm-4 b-r b-b">
                                <div class="el-tablo centered el-tabloPiso">
                                    <div class="value text-success statsTop top_in">{{$total_in}}</div>
                                    <div class="label">In</div>
                                </div>
                            </div>
                            <div class="col-sm-4 b-r b-b">
                                <div class="el-tablo centered el-tabloPiso">
                                    <div class="value text-danger statsTop top_out">{{$total_out}}</div>
                                    <div class="label">Out</div>
                                </div>
                            </div>
                            <div class="col-sm-4 b-r b-b">
                                <div class="el-tablo centered el-tabloPiso">
                                    <div class="value text-primary statsTop top_total">{{$total_in - $total_out}}</div>
                                    <div class="label">Total</div>
                                </div>
                            </div>
                        
                       
                            
                        </div>
                    </div>
                </div>
                <div class="logs_2" style="padding-top: 15px;">

                    @foreach ($newStats as $stat)
                    @include('backend.newbackend.stats.row')
                @endforeach
               
                </div>
            </div>
        </div>
    </div>

@endsection
