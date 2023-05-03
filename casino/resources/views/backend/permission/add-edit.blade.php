@extends('backend.layouts.app')

@section('page-title', trans('app.permissions'))
@section('page-heading', $edit ? $permission->name : trans('app.create_new_permission'))

@section('content')

    <section class="content-header">
        @include('backend.partials.messages')
    </section>

    <section class="content">
        @if ($edit)
            {!! Form::open(['route' => ['backend.permission.update', $permission->id], 'method' => 'PUT', 'id' => 'permission-form']) !!}
        @else
            {!! Form::open(['route' => 'backend.permission.store', 'id' => 'permission-form']) !!}
        @endif
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">{{ $edit ? trans('app.edit_permission') : trans('app.add_permission') }}</h3>
            </div>

            <div class="box-body">
                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>@lang('app.slug')</label>
                            <input type="text" class="form-control" name="slug" placeholder="@lang('app.slug')" value="{{ $edit ? $permission->slug : old('slug') }}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>@lang('app.name')</label>
                            <input type="text" class="form-control" name="name" placeholder="@lang('app.permission_name')" value="{{ $edit ? $permission->name : old('name') }}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>@lang('app.description')</label>
                            <textarea rows="2" name="description" class="form-control">{{ $edit ? $permission->description : old('description') }}</textarea>
                        </div>
                    </div>

                </div>
            </div>

            <div class="box-footer">
                <button type="submit" class="btn btn-primary">
                    {{ $edit ? trans('app.edit_permission') : trans('app.add_permission') }}
                </button>
                @if ($edit && $permission->removable)
                    <a href="{{ route('backend.permission.delete', $permission->id) }}"
                       class="btn btn-danger"
                       data-method="DELETE"
                       data-confirm-title="@lang('app.please_confirm')"
                       data-confirm-text="@lang('app.are_you_sure_delete_permission')"
                       data-confirm-delete="@lang('app.yes_delete_it')">
                        Delete Permission
                    </a>
                @endif
            </div>
        </div>
        {!! Form::close() !!}
    </section>

@stop