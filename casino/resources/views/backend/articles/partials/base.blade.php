

<div class="col-md-12">
    <div class="form-group">
        <label>@lang('app.title')</label>
        <input type="text" class="form-control" id="title" name="title" required value="{{ $edit ? $article->title : '' }}">
    </div>
</div>

<div class="col-md-12">
    <div class="form-group">
        <label>@lang('app.keywords')</label>
        <input class="form-control" id="keywords" name="keywords" value="{{ $edit ? $article->keywords : '' }}" data-role="tagsinput">
    </div>
</div>

<div class="col-md-12">
    <div class="form-group">
        <label>@lang('app.description')</label>
        <input class="form-control" id="description" name="description" value="{{ $edit ? $article->description : '' }}">
    </div>
</div>

<div class="col-md-12">
    <div class="form-group">
        <label>@lang('app.text')</label>
        <textarea class="form-control" id="editor" name="text">{{ $edit ? $article->text : '' }}</textarea>
    </div>
</div>

