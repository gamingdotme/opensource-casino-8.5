@extends('backend.layouts.app')

@section('page-title', __('app.edit_sms_mailing'))
@section('page-heading', $mailing->name)

@section('content')

    <section class="content-header">
        @include('backend.partials.messages')
    </section>

    <section class="content">
        <div class="box box-danger">
            {!! Form::open(['route' => array('backend.sms_mailing.update', $mailing->id), 'files' => true, 'id' => 'user-form']) !!}
            <div class="box-header with-border">
                <h3 class="box-title">@lang('app.edit_sms_mailing')</h3>
            </div>
            <div class="box-body">
                @include('backend.sms_mailings.partials.base', ['edit' => true])
            </div>

            <div class="box-footer">
                @if( \Carbon\Carbon::now()->diffInSeconds(\Carbon\Carbon::parse($mailing->date_start), false) > 0 )
                <button type="submit" class="btn btn-primary">
                    @lang('app.edit')
                </button>
                @endif
                <a href="{{ route('backend.sms_mailing.delete', $mailing->id) }}"
                   class="btn btn-danger"
                   data-method="DELETE"
                   data-confirm-title="@lang('app.please_confirm')"
                   data-confirm-text="@lang('app.are_you_sure_delete_sms_mailing')"
                   data-confirm-delete="@lang('app.yes_delete_him')">
                    Delete SMS Mailing
                </a>
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
