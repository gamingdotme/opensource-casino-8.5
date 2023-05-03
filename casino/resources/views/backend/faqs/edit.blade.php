@extends('backend.layouts.app')

@section('page-title', trans('app.edit_faq'))
@section('page-heading', $faq->title)

@section('content')

    <section class="content-header">
        @include('backend.partials.messages')
    </section>

    <section class="content">
        <div class="box box-default">
            {!! Form::open(['route' => array('backend.faq.update', $faq->id), 'files' => true, 'id' => 'user-form']) !!}
            <div class="box-header with-border">
                <h3 class="box-title">@lang('app.edit_faq')</h3>
            </div>

            <div class="box-body">
                <div class="row">
                    @include('backend.faqs.partials.base', ['edit' => true])
                </div>
            </div>

            <div class="box-footer">
                <button type="submit" class="btn btn-primary">
                    @lang('app.edit_faq')
                </button>
                <a href="{{ route('backend.faq.delete', $faq->id) }}"
                   class="btn btn-danger"
                   data-method="DELETE"
                   data-confirm-title="@lang('app.please_confirm')"
                   data-confirm-text="@lang('app.are_you_sure_delete_faq')"
                   data-confirm-delete="@lang('app.yes_delete_him')">
                    @lang('app.delete_faq')
                </a>
            </div>
            {!! Form::close() !!}
        </div>
    </section>

@stop

@section('scripts')
    <script>
        initSample();
    </script>
@stop
