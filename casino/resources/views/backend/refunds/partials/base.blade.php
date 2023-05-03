<div class="col-md-6">
    <div class="form-group">
        <label>@lang('app.min_pay')</label>
        <input type="text" class="form-control" name="min_pay" placeholder="@lang('app.min_pay')" required value="{{ $edit ? $refund->min_pay : '' }}">
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        <label>@lang('app.max_pay')</label>
        <input type="text" class="form-control" name="max_pay" placeholder="@lang('app.max_pay')" required value="{{ $edit ? $refund->max_pay : '' }}">
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        <label>@lang('app.percent')</label>
        @php
            $percents = array_combine(\VanguardLTE\Refund::$values['percent'], \VanguardLTE\Refund::$values['percent']);
        @endphp
        {!! Form::select('percent', $percents, $edit ? $refund->percent : '', ['class' => 'form-control']) !!}
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        <label>@lang('app.min_balance')</label>
        <input type="text" class="form-control" name="min_balance" placeholder="@lang('app.min_balance')" required value="{{ $edit ? $refund->min_balance : 0 }}">
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        <label>@lang('app.status')</label>
        {!! Form::select('status', ['0' => __('app.disabled'), '1' => __('app.active')], $edit ? $refund->status : 1, ['id' => 'status', 'class' => 'form-control']) !!}
    </div>
</div>