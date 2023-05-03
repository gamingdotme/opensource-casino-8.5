@extends('backend.layouts.app')

@section('page-title', trans('app.edit_api'))
@section('page-heading', $api->title)

@section('content')

<section class="content-header">
@include('backend.partials.messages')
</section>

    <section class="content">
{!! Form::open(['route' => array('backend.api.update', $api->id), 'files' => true, 'id' => 'user-form']) !!}	
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">@lang('app.create_user')</h3>
        </div>

        <div class="box-body">
          <div class="row">

            @include('backend.api.partials.base', ['edit' => true])

          </div>
        </div>

        <div class="box-footer">
            <button type="submit" class="btn btn-primary">
                @lang('app.edit_api')
            </button>
            @permission('api.delete')
        <a href="{{ route('backend.api.delete', $api->id) }}"
           class="btn btn-danger"
           data-method="DELETE"
           data-confirm-title="@lang('app.please_confirm')"
           data-confirm-text="@lang('app.are_you_sure_delete_api')"
           data-confirm-delete="@lang('app.yes_delete_him')">
            Delete API Key
        </a>
            @endpermission
        </div>
      </div>
{!! Form::close() !!}

@stop

@section('scripts')
    <script>
        $(function() {
            $('#generateKey').click(function(){
                $.ajax({
                    url: "{{ route('backend.api.generate') }}",
                    dataType: 'json',
                    success: function(data){
                        $('#keygen').val( data.key );
                    }
                });
            });
        });
    </script>
@stop