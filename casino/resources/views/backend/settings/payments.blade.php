@extends('backend.layouts.app')

@section('page-title', trans('app.general_settings'))
@section('page-heading', trans('app.general_settings'))

@section('content')

    <section class="content-header">
        @include('backend.partials.messages')
    </section>

    <section class="content">

        <div class="box box-default">
            {!! Form::open(['route' => 'backend.settings.payment.update', 'id' => 'general-settings-form']) !!}
            <div class="box-header with-border">
                <h3 class="box-title">@lang('app.payment_settings', ['name' => auth()->user()->shop ? auth()->user()->shop->name : '0'])</h3>
            </div>

            <div class="box-body">

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