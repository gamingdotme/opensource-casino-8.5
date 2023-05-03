@extends('backend.layouts.app')

@section('page-title', trans('app.choose_payment_system'))
@section('page-heading', trans('app.choose_payment_system'))

@section('content')
    <section class="content-header">
        @include('backend.partials.messages')
    </section>
    <section class="content">

        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">@lang('app.choose_payment_system')</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                    @if( settings('payment_interkassa') && \VanguardLTE\Lib\Setting::is_available('interkassa', 0) )
                        @if( isset($interkassa['success']) && count($interkassa['systems']) )
                            @foreach($interkassa['systems'] AS $system)
                                <a href="{{ route('backend.credit.payment', ['credit' => $credit->id, 'system' => 'interkassa_'.$system['als']]) }}" class="btn btn-success">{{ ucfirst($system['ps'])  }} {{ mb_strtoupper($system['curAls']) }}</a>
                            @endforeach
                        @endif
                    @endif

                    @if( settings('payment_coinbase') && \VanguardLTE\Lib\Setting::is_available('coinbase', 0) )
                        <a href="{{ route('backend.credit.payment', ['credit' => $credit->id, 'system' => 'coinbase']) }}" class="btn btn-success">@lang('app.coinbase')</a>
                    @endif

                    @if( settings('payment_btcpayserver') && \VanguardLTE\Lib\Setting::is_available('btcpayserver', 0) )
                        <a href="{{ route('backend.credit.payment', ['credit' => $credit->id, 'system' => 'btcpayserver']) }}" class="btn btn-success">@lang('app.btcpayserver')</a>
                    @endif
                    </div>
                </div>
            </div>
        </div>

    </section>
@stop

@section('scripts')
@stop