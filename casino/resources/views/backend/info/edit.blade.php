@extends('backend.layouts.app')

@section('page-title', trans('app.edit_info'))
@section('page-heading', $info->title)

@section('content')

    <section class="content-header">
        @include('backend.partials.messages')
    </section>

    <section class="content">

        {!! Form::open(['route' => array('backend.info.update', $info->id), 'files' => true, 'id' => 'user-form']) !!}
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">@lang('app.edit_info')</h3>
            </div>

            <div class="box-body">
                @include('backend.info.partials.base', ['edit' => true])
            </div>

            <div class="box-footer">
                <button type="submit" class="btn btn-primary">
                    @lang('app.edit_info')
                </button>

                <a href="{{ route('backend.info.delete', $info->id) }}"
                   class="btn btn-danger"
                   data-method="DELETE"
                   data-confirm-title="@lang('app.please_confirm')"
                   data-confirm-text="@lang('app.are_you_sure_delete_info')"
                   data-confirm-delete="@lang('app.yes_delete_him')">
                    @lang('app.delete_info')
                </a>
            </div>
        </div>
        {!! Form::close() !!}

    </section>

@stop

@section('scripts')
    <script>
        initSample();
    </script>
@stop
