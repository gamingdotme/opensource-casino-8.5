@extends('backend.layouts.user')

@section('page-title', trans('app.dashboard'))
@section('page-heading', trans('app.dashboard'))
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
        <div class="content-box">
            <div class="element-wrapper">
                <div class="element-box-tp">
                    <div class="row">
                        @permission('stats.pay')
                        <div class="col-md-6 mb-4">
                            <h6 class="text-primary">@lang('app.latest_pay_stats')</h6>
                            <div class="table-responsive">
                            <table class="table table-hover table-bordered dataTable no-footer">
                            <thead>
                                <tr>
                                    <th>@lang('app.cashier')</th>
                                    <th>@lang('app.money_in')</th>
                                    <th>@lang('app.money_out')</th>
                                    <th>@lang('app.date')</th>
                                </tr>
                                </thead>

                                <tbody>

                                @if (count($statistics))
                                    @foreach ($statistics as $stat)
                                        <tr>
                                            <td>
                                            @if( $stat->payeer && $stat->payeer->hasRole(['cashier']))
                                                <a href="{{ route('backend.user.edit', $stat->payeer->id)  }}">
                                                    {{ $stat->payeer->username }}
                                                </a>
                                            @endif

                                            </td>
                                            <td>
                                                @if ($stat->add->money_in != NULL)
                                                    <span class="text-green">{{ $stat->add->money_in }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($stat->add->money_out != NULL)
                                                    <span class="text-red">{{ $stat->add->money_out }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $stat->created_at->format(config('app.time_format')) }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="4">@lang('app.no_data')</td></tr>
                                @endif

                                </tbody>
                            </table>
                            </div>

                        </div>
                        @endpermission
                        @permission('stats.game')
                        <div class="col-md-6 mb-4">
                            <h6 class="text-primary">@lang('app.latest_game_stats')</h6>
                            <div class="table-responsive">
                            <table class="table table-hover table-bordered dataTable no-footer">
                            <thead>
                                <tr>
                                    <th>@lang('app.game')</th>
                                    <th>@lang('app.user')</th>
                                    <th>@lang('app.balance')</th>
                                    <th>@lang('app.bet')</th>
                                    <th>@lang('app.win')</th>
                                    <th>@lang('app.date')</th>
                                </tr>
                                </thead>

                                <tbody>

                                @if (count($gamestat))
                                    @foreach ($gamestat as $stat)
                                        <tr>
                                            <td>
                                                <a href="{{ route('backend.game_stat', ['game' => $stat->game])  }}">
                                                    {{ $stat->game }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('backend.game_stat', ['user' => $stat->user ? $stat->user->username : ''])  }}">
                                                    {{ $stat->user ? $stat->user->username : '' }}
                                                </a>
                                            </td>
                                            <td>{{ $stat->balance }}</td>
                                            <td>{{ $stat->bet }}</td>
                                            <td>{{ $stat->win }}</td>
                                            <td>{{ date(config('app.time_format'), strtotime($stat->date_time)) }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="6">@lang('app.no_data')</td></tr>
                                @endif

                                </tbody>
                            </table>
                            </div>

                        </div>
                        @endpermission
                        @permission('shops.manage')
                        <div class="col-md-6 mb-4">
                        <h6 class="text-primary">@lang('app.latest_shops')</h6>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered dataTable no-footer">
                            <thead>
                                <tr>
                                    <th>@lang('app.name')</th>
                                    <th>@lang('app.credit')</th>
                                    <th>@lang('app.percent')</th>
                                    <th>@lang('app.frontend')</th>
                                    <th>@lang('app.currency')</th>
                                    <th>@lang('app.status')</th>
                                </tr>
                                </thead>

                                <tbody>

                                @if (count($shops))
                                    @foreach ($shops as $shop)
                                        <tr>
                                            <td>
                                                <a href="{{ route('backend.shop.edit', $shop->id)  }}">
                                                    {{ $shop->name }}
                                                </a>
                                            </td>

                                            <td>{{ $shop->balance }}</td>
                                            <td>{{ $shop->percent }}</td>
                                            <td>{{ $shop->frontend }}</td>

                                            <td>{{ $shop->currency }}</td>
                                            <td>
                                                @if($shop->is_blocked)
                                                    <small><i class="fa fa-circle text-red"></i></small>
                                                @else
                                                    <small><i class="fa fa-circle text-green"></i></small>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="6">@lang('app.no_data')</td></tr>
                                @endif

                                </tbody>
                            </table>
                        </div>
                        </div>
                        @endpermission
                        @permission('stats.shift')
                            <div class="col-md-12 mb-4">
                                <h6 class="text-primary">Latest @lang('app.shift_stats')</h6>
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered dataTable no-footer">
                                    <thead>
                                <tr>
                                    @if(!auth()->user()->hasRole('cashier'))
                                        <th>@lang('app.shift')</th>
                                    @endif
                                    <th>@lang('app.user')</th>
                                    <th>@lang('app.start')</th>
                                    <th>@lang('app.end')</th>
                                    @if(!auth()->user()->hasRole('cashier'))
                                        <th>@lang('app.credit')</th>
                                        <th>@lang('app.in')</th>
                                        <th>@lang('app.out')</th>
                                    @endif
                                    <th>@lang('app.total')</th>
                                    @permission('games.in_out')
                                    <th>@lang('app.bank')</th>
                                    @endpermission
                                    <th>@lang('app.jackpots')</th>
                                    @if(!auth()->user()->hasRole('cashier'))
                                        <th>@lang('app.refunds')</th>
                                    @endif
                                    <th>@lang('app.money')</th>
                                    <th>@lang('app.in')</th>
                                    <th>@lang('app.out')</th>
                                    <th>@lang('app.total')</th>
                                    <th>@lang('app.transfers')</th>
                                    <th>@lang('app.payout')</th>
                                </tr>
                                </thead>

                                <tbody>

                                @if (count($open_shift))
                                    @foreach ($open_shift as $num=>$stat)
                                        <tr>
                                            @if(!auth()->user()->hasRole('cashier'))
                                                <td>{{ $stat->id }}</td>
                                            @endif
                                            <td>{{ $stat->user->username }}</td>
                                            <td>{{ date(config('app.date_time_format'), strtotime($stat->start_date)) }}</td>
                                            <td>{{ $stat->end_date ? date(config('app.date_time_format'), strtotime($stat->end_date)) : '' }}</td>
                                            @if(!auth()->user()->hasRole('cashier'))
                                                <td>{{ $stat->balance }}</td>
                                                <td>{{ $stat->balance_in }}</td>
                                                <td>{{ $stat->balance_out }}</td>
                                            @endif
                                            <td>{{ number_format ($stat->balance + $stat->balance_in - $stat->balance_out, 4, ".", "") }}</td>
                                            @permission('games.in_out')
                                            @php
                                                $banks = !$stat->end_date ? $stat->banks() : $stat->last_banks;
                                            @endphp

                                            <td>{{ number_format ($banks, 4, ".", "") }}</td>
                                            @endpermission
                                            <td>{{ number_format (!$stat->end_date ? $stat->get_jpg() : $stat->jpg, 4, ".", "") }}</td>
                                            @if(!auth()->user()->hasRole('cashier'))
                                                <td>
                                                    @if( $stat->end_date == NULL )
                                                        {{ $stat->refunds() }}
                                                    @else
                                                        {{ $stat->last_refunds }}
                                                    @endif
                                                </td>
                                            @endif
                                            @php
                                                $money = $stat->users;
                                                if($stat->end_date == NULL){
                                                    $money = $stat->get_money();
                                                }
                                            @endphp

                                            <td>{{ $money }}</td>
                                            <td>{{ $stat->money_in }}</td>
                                            <td>{{ $stat->money_out }}</td>

                                            @php
                                                $total = $stat->money_in - $stat->money_out;
                                            @endphp

                                            <td>{{ number_format ($total, 4, ".", "") }}</td>
                                            <td>{{ $stat->transfers }}</td>

                                            @php
                                                $payout = $stat->money_in > 0 ? ($stat->money_out / $stat->money_in) * 100 : 0;
                                            @endphp
                                            <td>{{ number_format ($payout, 4, ".", "") }}</td>

                                        </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="17">@lang('app.no_data')</td></tr>
                                @endif

                                </tbody>
                                    </table>
                                </div>
                            </div>
                        @endpermission

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="content-panel rightBarLogs" id="rightBarLogs" style="padding-top: 0px; padding:0px;">
            <div class="content-panel-close"><i class="os-icon os-icon-close"></i></div>
            <div id="logs">
                <div class="content-i content-i-2">
                    <div class="element-wrapper element-wrapper-2">
                        <div class="rowrightdiv text-center">
                            <div class="col-sm-12 b-r b-b">
                                <div class="el-tablo centered el-tabloPiso">
                                    <div class="value text-primary top_credits">
                                        {{ number_format($stats['total']) }}
                                    </div>
                                    <div class="label">@lang('app.total_users')</div>
                                </div>
                            </div>

                            <div class="col-sm-12 b-r b-b">
                                <div class="el-tablo centered el-tabloPiso">
                                    <div class="value text-success statsTop top_in">{{ number_format($stats['new']) }}</div>
                                    <div class="label">@lang('app.new_users_this_month')</div>
                                </div>
                            </div>
                            <div class="col-sm-12 b-r b-b">
                                <div class="el-tablo centered el-tabloPiso">
                                    <div class="value text-danger statsTop top_out">{{ number_format($stats['banned']) }}</div>
                                    <div class="label">@lang('app.banned_users')</div>
                                </div>
                            </div>
                            <div class="col-sm-12 b-r b-b">
                                <div class="el-tablo centered el-tabloPiso">

                                    <div class="value text-primary statsTop top_total">{{ number_format($stats['games']) }}</div>
                                    <div class="label">@lang('app.games')</div>
                                </div>
                            </div>
                        </div>
                        <div class="rowrightdiv text-center mt-4">
                            <div class="col-md-12">
                        <a class="btn btn-success" href="{{ route('backend.start_shift') }}"> @lang('app.start_shift')</a>
                            </div>
                            @if( Auth::user()->shop )
                        @if( Auth::user()->shop->is_blocked )
                            @permission('shops.unblock')
                            <div class="col-md-12"><a class="btn btn-danger" href="{{ route('backend.settings.shop_unblock') }}"
                                   data-method="PUT"
                                   data-confirm-title="@lang('app.please_confirm')"
                                   data-confirm-text="@lang('app.are_you_sure_unblock_shop')"
                                   data-confirm-delete="@lang('app.unblock')"
                                > UnBlock Shop</a></div>
                            @endpermission
                        @else
                            @permission('shops.block')
                            <div class="col-md-12"><a class="btn btn-danger" href="{{ route('backend.settings.shop_block') }}"
                                   data-method="PUT"
                                   data-confirm-title="@lang('app.please_confirm')"
                                   data-confirm-text="@lang('app.are_you_sure_block_shop')"
                                   data-confirm-delete="@lang('app.block')"
                                > Block Shop</a></div>
                            @endpermission
                        @endif
                    @endif
				@if( Auth::user()->hasRole('distributor') && auth()->user()->present()->shop)

                            <div class="col-md-12">
                            <a class="btn btn-link" href="{{ route('backend.shop.action', [auth()->user()->present()->shop, 'jpg_out']) }}"
                    data-method="DELETE"
                    data-confirm-title="@lang('app.please_confirm')"
                    data-confirm-text="{{ auth()->user()->present()->shop->name }} / @lang('app.jpg_out')"
                    data-confirm-delete="@lang('app.yes_delete_him')"> @lang('app.jpg_out')</a>
                            </div>
                            <div class="col-md-12">
                            <a class="btn btn-link" href="{{ route('backend.shop.action', [auth()->user()->present()->shop, 'games_out']) }}"
                    data-method="DELETE"
                    data-confirm-title="@lang('app.please_confirm')"
                    data-confirm-text="{{ auth()->user()->present()->shop->name }} / @lang('app.games_out')"
                    data-confirm-delete="@lang('app.yes_delete_him')"> @lang('app.games_out')</a>
                            </div>
                            <div class="col-md-12">
                            <a class="btn btn-link" href="{{ route('backend.shop.action', [auth()->user()->present()->shop, 'return_out']) }}"
                    data-method="DELETE"
                    data-confirm-title="@lang('app.please_confirm')"
                    data-confirm-text="{{ auth()->user()->present()->shop->name }} / @lang('app.returns_out')"
                    data-confirm-delete="@lang('app.yes_delete_him')"> @lang('app.returns_out')</a>
                            </div>
				@endif

                        </div>
                    </div>
                </div>
                <div class="logs_2" style="padding-top: 15px;"></div>
            </div>
        </div>
    </div>
</div>


@stop

@section('scripts')
    {!! HTML::script('/back/dist/js/pages/dashboard.js') !!}
@stop
