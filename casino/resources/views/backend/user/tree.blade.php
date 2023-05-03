@extends('backend.layouts.app')

@section('page-title', $role->name .' '. trans('app.tree'))
@section('page-heading', $role->name .' '. trans('app.tree'))

@section('content')

    <section class="content-header">
        @include('backend.partials.messages')
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">{{ $role->name }} @lang('app.tree')</h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">

                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            @if( auth()->user()->hasRole(['admin','agent']) )
                                <th>@lang('app.agent')</th>
                            @endif
                            <th>@lang('app.distributor')</th>
                            <th>@lang('app.shop')</th>
                            <th>@lang('app.manager')</th>
                            <th>@lang('app.cashier')</th>
                            <th>@lang('app.user')</th>
                        </tr>
                        </thead>
                        <tbody>

                        <tr>
                        @if( auth()->user()->hasRole(['admin','agent']) )
                            @if( isset($tree['agents']) && count($tree['agents']) )
                                @foreach($tree['agents'] AS $agent_id=>$agent)
                                    <td rowspan="{{ $agent['rowspan'] }}">
                                        <a href="{{ $agent['href'] }}">
                                            {{ $agent['text'] }}
                                        </a>
                                        @if( isset($agent['balance']) )
                                            <p>@lang('app.balance'): {{ number_format($agent['balance'], 2, '.', '') }}</p>
                                        @endif
                                    </td>

                                    @if( count($agent['distributors']) )
                                        @foreach($agent['distributors'] AS $distributor_id=>$distributor)
                                            @include('backend.user.partials.distributor2')
                                        @endforeach
                                    @else
                                        <td colspan="5"></td></tr><tr></tr><tr>
                                    @endif
                                @endforeach
                            @else
                                <td colspan="6">@lang('app.no_data')</td>
                            @endif
                        @endif

                            @if( auth()->user()->hasRole(['distributor']) )
                                @include('backend.user.partials.distributor2', ['distributor' => $tree['distributor']])
                            @endif
                        </tr>

                        </tbody>
                        <thead>
                        <tr>
                            @if( auth()->user()->hasRole(['admin','agent']) )
                                <th>@lang('app.agent')</th>
                            @endif
                            <th>@lang('app.distributor')</th>
                            <th>@lang('app.shop')</th>
                            <th>@lang('app.manager')</th>
                            <th>@lang('app.cashier')</th>
                            <th>@lang('app.user')</th>
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


    </script>
@stop
