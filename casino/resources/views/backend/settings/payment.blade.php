@extends('backend.layouts.app')

@section('page-title', trans('app.general_settings'))
@section('page-heading', trans('app.general_settings'))

@section('content')

    <section class="content-header">
        @include('backend.partials.messages')
    </section>

    <section class="content">

        <div class="box box-default">
            {!! Form::open(['route' => ['backend.settings.list.update', 'payment'], 'id' => 'general-settings-form']) !!}
            <div class="box-header with-border">
                <h3 class="box-title">@lang('app.general_settings')</h3>
            </div>

            <div class="box-body">
                <div class="row">

                    <div class="col-md-6">

                        <div class="form-group">
                            <label>@lang('app.default_currency')</label>
                            @php
                                $currencies = array_combine(\VanguardLTE\Shop::$values['currency'], \VanguardLTE\Shop::$values['currency']);
                            @endphp
                            {!! Form::select('default_currency', $currencies, settings('default_currency', 'USD'), ['class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            <label>@lang('app.minimum_payment_amount')</label>
                            <input type="number" step="0.0000001" name="minimum_payment_amount" class="form-control" value="{{ settings('minimum_payment_amount', 0) }}">
                        </div>

                        <div class="form-group">
                            <label>@lang('app.maximum_payment_amount')</label>
                            <input type="number" step="0.0000001" name="maximum_payment_amount" class="form-control" value="{{ settings('maximum_payment_amount', 10000) }}">
                        </div>


                    </div>

                    <div class="col-md-6">

                        <div class="form-group">
                            <label>
                                IK
                            </label>
                            {!! Form::select('payment_interkassa', ['0' => __('app.no'), '1' => __('app.yes')], settings('payment_interkassa'), ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group">
                            <label>
                                CB
                            </label>
                            {!! Form::select('payment_coinbase', ['0' => __('app.no'), '1' => __('app.yes')], settings('payment_coinbase'), ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group">
                            <label>
                                BP
                            </label>
                            {!! Form::select('payment_btcpayserver', ['0' => __('app.no'), '1' => __('app.yes')], settings('payment_btcpayserver'), ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group">
                            <label>
                                Pin
                            </label>
                            {!! Form::select('payment_pin', ['0' => __('app.no'), '1' => __('app.yes')], settings('payment_pin'), ['class' => 'form-control']) !!}
                        </div>


                    </div>

                </div>

                <hr>

                <h4>Interkassa</h4>
                <div class="row">
                    @foreach(config('payments.interkassa.fields') AS $field)
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ $field }}</label>
                                <input type="text" class="form-control" name="system[interkassa][{{ $field }}][{{ auth()->user()->shop_id }}]" value="{{ \VanguardLTE\Lib\Setting::get_value('interkassa', $field, auth()->user()->shop_id) }}">
                            </div>
                        </div>
                    @endforeach
                </div>

                <h4>Coinbase</h4>
                <div class="row">
                    @foreach(config('payments.coinbase.fields') AS $field)
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ $field }}</label>
                                <input type="text" class="form-control" name="system[coinbase][{{ $field }}][{{ auth()->user()->shop_id }}]" value="{{ \VanguardLTE\Lib\Setting::get_value('coinbase', $field, auth()->user()->shop_id) }}">
                            </div>
                        </div>
                    @endforeach
                </div>

                <h4>BtcPayServer</h4>
                <div class="row">
                    @foreach(config('payments.btcpayserver.fields') AS $field)
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ $field }}</label>
                                <input type="text" class="form-control" name="system[btcpayserver][{{ $field }}][{{ auth()->user()->shop_id }}]" value="{{ \VanguardLTE\Lib\Setting::get_value('btcpayserver', $field, auth()->user()->shop_id) }}">
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>

            <div class="box-footer">
                <button type="submit" class="btn btn-primary">
                    @lang('app.edit_settings')
                </button>



            </div>
            {{ Form::close() }}
        </div>
    </section>

@stop
