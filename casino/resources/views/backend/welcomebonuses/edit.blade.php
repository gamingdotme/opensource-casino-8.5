@extends('backend.layouts.app')

@section('page-title', trans('app.edit_welcome_bonus'))
@section('page-heading', $welcome_bonus->title)

@section('content')

<section class="content-header">
@include('backend.partials.messages')
</section>

    <section class="content">

      <div class="box box-default">
		{!! Form::open(['route' => array('backend.welcome_bonus.update', $welcome_bonus->id), 'files' => true, 'id' => 'user-form']) !!}
        <div class="box-header with-border">
          <h3 class="box-title">@lang('app.edit_welcome_bonus')</h3>
        </div>

        <div class="box-body">
          <div class="row">
            @include('backend.welcomebonuses.partials.base', ['edit' => true])
          </div>
        </div>

        <div class="box-footer">
        <button type="submit" class="btn btn-primary">
            @lang('app.edit_welcome_bonus')
        </button>
		@permission('welcome_bonuses.delete')
        <a href="{{ route('backend.welcome_bonus.delete', $welcome_bonus->id) }}"
           class="btn btn-danger"
           data-method="DELETE"
           data-confirm-title="@lang('app.please_confirm')"
           data-confirm-text="@lang('app.are_you_sure_delete_welcome_bonus')"
           data-confirm-delete="@lang('app.yes_delete_him')">
            @lang('app.delete_welcome_bonus')
        </a>
		@endpermission
        </div>
		{!! Form::close() !!}
      </div>
    </section>

@stop