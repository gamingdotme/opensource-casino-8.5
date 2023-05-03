@extends('backend.layouts.app')

@section('page-title', trans('app.general_settings'))
@section('page-heading', trans('app.general_settings'))

@section('content')

    <section class="content-header">
        @include('backend.partials.messages')
    </section>

    <section class="content">

        <div class="box box-default">
            {!! Form::open(['route' => ['backend.settings.list.update', 'sms'], 'id' => 'general-settings-form']) !!}
            <div class="box-header with-border">
                <h3 class="box-title">@lang('app.general_settings')</h3>
            </div>

            <div class="box-body">
                <div class="row">
                    <div class="col-md-6">

                        <div class="form-group">
                            <label>@lang('app.smsto_client_api_key')</label>
                            <input type="text" class="form-control" name="smsto_client_api_key" value="{{ settings('smsto_client_api_key') }}">
                        </div>

                        <div class="form-group">
                            <label>@lang('app.smsto_client_id')</label>
                            <input type="text" class="form-control" name="smsto_client_id" value="{{ settings('smsto_client_id') }}">
                        </div>

                        <div class="form-group">
                            <label>@lang('app.smsto_client_secret')</label>
                            <input type="text" class="form-control" name="smsto_client_secret" value="{{ settings('smsto_client_secret') }}">
                        </div>

                    </div>
                    <div class="col-md-6">

                        <div class="form-group">
                            <label>@lang('app.smsto_limit')</label>
                            <input type="number" step="0.0000001" class="form-control" name="smsto_limit" value="{{ settings('smsto_limit') }}">
                        </div>

                        <div class="form-group">
                            <label>@lang('app.smsto_time')</label>
                            <input type="number" step="0.0000001" class="form-control" name="smsto_time" value="{{ settings('smsto_time') }}">
                        </div>

                        <div class="form-group">
                            <label>@lang('app.smsto_alert_phone')</label>
                            <div class="input-group">
                                <span class="input-group-addon">+</span>
                                <input type="text" class="form-control onlynumber" name="smsto_alert_phone" value="{{ settings('smsto_alert_phone') }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>@lang('app.smsto_alert_phone') #2</label>
                            <div class="input-group">
                                <span class="input-group-addon">+</span>
                                <input type="text" class="form-control onlynumber" name="smsto_alert_phone_2" value="{{ settings('smsto_alert_phone_2') }}">
                            </div>
                        </div>

                    </div>
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
