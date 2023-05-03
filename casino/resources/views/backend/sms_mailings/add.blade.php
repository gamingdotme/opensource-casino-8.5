@extends('backend.layouts.app')

@section('page-title', __('app.add_sms_mailing'))
@section('page-heading', __('app.add_sms_mailing'))

@section('content')

    <section class="content-header">
        @include('backend.partials.messages')
    </section>

    <section class="content">
        <div class="box box-danger">
            {!! Form::open(['route' => 'backend.sms_mailing.store']) !!}
            <div class="box-header with-border">
                <h3 class="box-title">@lang('app.add_sms_mailing')</h3>
            </div>
            <div class="box-body">
                @include('backend.sms_mailings.partials.base', ['edit' => false])
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-primary">
                    @lang('app.add')
                </button>
            </div>
            {!! Form::close() !!}
        </div>
    </section>

@stop

@section('scripts')
    <script>
        $('.datepicker').datetimepicker({
            format: 'YYYY-MM-DD HH:mm',
            timeZone: '{{ config('app.timezone') }}'
        });
    </script>
@stop