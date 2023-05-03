@extends('backend.layouts.app')

@section('page-title', trans('app.edit_article'))
@section('page-heading', $article->title)

@section('content')

<section class="content-header">
@include('backend.partials.messages')
</section>

    <section class="content">
      <div class="box box-default">
		{!! Form::open(['route' => array('backend.article.update', $article->id), 'files' => true, 'id' => 'user-form']) !!}
        <div class="box-header with-border">
          <h3 class="box-title">@lang('app.edit_article')</h3>
        </div>

        <div class="box-body">
          <div class="row">
            @include('backend.articles.partials.base', ['edit' => true])
          </div>
        </div>

        <div class="box-footer">
        <button type="submit" class="btn btn-primary">
            @lang('app.edit_article')
        </button>
        <a href="{{ route('backend.article.delete', $article->id) }}"
           class="btn btn-danger"
           data-method="DELETE"
           data-confirm-title="@lang('app.please_confirm')"
           data-confirm-text="@lang('app.are_you_sure_delete_article')"
           data-confirm-delete="@lang('app.yes_delete_him')">
            Delete Article
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