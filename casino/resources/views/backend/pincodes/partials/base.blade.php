<div class="col-md-6">
    <div class="form-group">
        <label for="code">@lang('app.pincode')</label>
        <input type="text" class="form-control" id="code" name="code" placeholder="" data-inputmask="'mask': '&&&&-&&&&-&&&&-&&&&-&&&&'" data-mask required value="{{ $edit ? $pincode->code : '' }}" {{ $edit ? 'disabled' : '' }}>
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label for="nominal">@lang('app.nominal')</label>
        <input type="number" step="0.0000001" class="form-control" id="nominal" name="nominal" placeholder="" required value="{{ $edit ? $pincode->nominal : '' }}" {{ $edit ? 'disabled' : '' }}>
    </div>
</div>
@if($edit)
<div class="col-md-6">
    <div class="form-group">
        <label for="status">@lang('app.status')</label>
        {!! Form::select('status', [__('app.disabled'), __('app.active')], $edit ? $pincode->status : 1, ['class' => 'form-control', 'id' => 'status']) !!}
    </div>
</div>
    @endif
