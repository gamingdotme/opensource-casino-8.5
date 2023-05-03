<div class="col-md-6">
    <div class="form-group">
        <label for="code">@lang('app.credit')</label>
        <input type="text" class="form-control" id="credit" name="credit" placeholder="" required value="{{ $edit ? $credit->credit : '' }}" >
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label for="nominal">@lang('app.price')</label>
        <input type="text" class="form-control" id="price" name="price" placeholder="" required value="{{ $edit ? $credit->price : '' }}" >
    </div>
</div>
