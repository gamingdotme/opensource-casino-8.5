<div class="col-md-6">
    <div class="form-group">
        <label>@lang('app.title')</label>
        <input type="text" class="form-control" name="title" placeholder="@lang('app.title')" required value="{{ $edit ? $category->title : '' }}">
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label>@lang('app.position')</label>
        <input type="number" step="0.0000001" class="form-control" name="position" placeholder="@lang('app.position')" required value="{{ $edit ? $category->position : '' }}">
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label>@lang('app.parent')</label>
        {!! Form::select('parent', $categories, $edit?$category->parent:0, ['id' => 'parent', 'class' => 'form-control input-solid']) !!}
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label>@lang('app.href')</label>
        <input type="text" class="form-control" name="href" placeholder="@lang('app.href')" required value="{{ $edit ? $category->href : '' }}">
    </div>
</div>
