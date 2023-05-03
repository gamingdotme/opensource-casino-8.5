

<div class="col-md-12">
    <div class="form-group">
        <label>@lang('app.title')</label>
        <input type="text" class="form-control" id="title" name="title" required value="{{ $edit ? $rule->title : '' }}">
    </div>
</div>



<div class="col-md-12">
    <div class="form-group">
        <label>@lang('app.text')</label>
        <textarea class="form-control" id="editor" name="text">{{ $edit ? $rule->text : '' }}</textarea>
    </div>
</div>

