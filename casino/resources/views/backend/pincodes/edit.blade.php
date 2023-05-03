@extends('backend.layouts.app')

@section('page-title', trans('app.edit_pincode'))
@section('page-heading', $pincode->title)

@section('content')

    <section class="content-header">
        @include('backend.partials.messages')
    </section>

    <section class="content">

        {!! Form::open(['route' => array('backend.pincode.update', $pincode->id), 'files' => true, 'id' => 'user-form']) !!}
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">@lang('app.pincode_details')</h3>
            </div>

            <div class="box-body">
                <div class="row">

                    @include('backend.pincodes.partials.base', ['edit' => true])

                </div>
            </div>

            <div class="box-footer">
                <button type="submit" class="btn btn-primary">
                        @lang('app.edit_pincode')
                </button>

                @permission('pincodes.delete')
                    <a href="{{ route('backend.pincode.delete', $pincode->id) }}"
                       class="btn btn-danger"
                       data-method="DELETE"
                       data-confirm-title="@lang('app.please_confirm')"
                       data-confirm-text="@lang('app.are_you_sure_delete_pincode')"
                       data-confirm-delete="@lang('app.yes_delete_him')">
                        @lang('app.delete_pincode')
                    </a>
                @endpermission
            </div>
        </div>
        {!! Form::close() !!}

    </section>

@stop

@section('scripts')
    <script>
        $(function() {
            $('[data-mask]').inputmask({
                mask: "****-****-****-****-****",
                definitions: {'5': {validator: "[0-9A-Z]"}}
            });
        });
    </script>
@stop