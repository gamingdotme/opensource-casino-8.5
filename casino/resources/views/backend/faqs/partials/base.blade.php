

<div class="col-md-12">
    <div class="form-group">
        <label>@lang('app.question')</label>
        <input type="text" class="form-control" id="question" name="question" required value="{{ $edit ? $faq->question : '' }}">
    </div>
</div>


<div class="col-md-12">
    <div class="form-group">
        <label>@lang('app.answer')</label>
        <textarea class="form-control" id="editor" name="answer">{{ $edit ? $faq->answer : '' }}</textarea>
    </div>
</div>

<div class="col-md-12">
    <div class="form-group">
        <label>@lang('app.rank')</label>
        <input type="text" class="form-control" id="rank" name="rank" required value="{{ $edit ? $faq->rank : '' }}">
    </div>
</div>
