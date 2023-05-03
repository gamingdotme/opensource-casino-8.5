@extends('backend.layouts.app')

@section('page-title', trans('app.roles'))
@section('page-heading', $edit ? $role->name : trans('app.create_new_role'))

@section('content')

<section class="content-header">
@include('backend.partials.messages')
</section>

    <section class="content">
            @if ($edit)
            {!! Form::open(['route' => ['backend.role.update', $role->id], 'method' => 'PUT', 'id' => 'role-form']) !!}
            @else
            {!! Form::open(['route' => 'backend.role.store', 'id' => 'role-form']) !!}
             @endif
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">{{ $edit ? trans('app.edit_role') : trans('app.add_role') }}</h3>
        </div>

        <div class="box-body">
          <div class="row">

            <div class="col-md-6">
              <div class="form-group">
                <label>@lang('app.slug')</label>
                <input type="text" class="form-control" id="slug" name="slug" placeholder="@lang('app.role_name')" value="{{ $edit ? $role->slug : old('slug') }}">
			  </div>
            </div>
			
            <div class="col-md-6">
              <div class="form-group">
                <label>@lang('app.name')</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="@lang('app.name')" value="{{ $edit ? $role->name : old('name') }}">
			  </div>
            </div>
			
            <div class="col-md-6">
              <div class="form-group">
                <label>@lang('app.level')</label>
                <input type="text" class="form-control" id="level" name="level" placeholder="@lang('app.level')" value="{{ $edit ? $role->level : old('level') }}">
			  </div>
            </div>
			
            <div class="col-md-6">
              <div class="form-group">
                <label>@lang('app.description')</label>
                <textarea rows="2" name="description" id="description" class="form-control">{{ $edit ? $role->description : old('description') }}</textarea>
			  </div>
            </div>

          </div>
        </div>

        <div class="box-footer">
            <button type="submit" class="btn btn-primary">
                {{ $edit ? trans('app.edit_role') : trans('app.add_role') }}
            </button>
        </div>
      </div>
            {!! Form::close() !!}
    </section>

@stop