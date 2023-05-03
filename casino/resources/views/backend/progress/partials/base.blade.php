<div class="col-md-6">
    <div class="form-group">
        <label for="nominal">@lang('app.rating')</label>
        <input type="number" step="0.0000001" class="form-control" id="rating" name="rating" placeholder="" required value="{{ $edit ? $progress->rating : '' }}" disabled>
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label for="nominal">@lang('app.sum')</label>
        <input type="number" step="0.0000001" class="form-control" id="sum" name="sum" placeholder="" required value="{{ $edit ? $progress->sum : '' }}">
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label for="status">@lang('app.type')</label>
        {!! Form::select('type', ['one_pay' => __('app.one_pay'), 'sum_pay' => __('app.sum_pay')], $edit ? $progress->type : 'sum_pay', ['class' => 'form-control', 'id' => 'type']) !!}
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        <label for="nominal">@lang('app.spins')</label>
        <input type="number" step="0.0000001" class="form-control" id="spins" name="spins" placeholder="" required value="{{ $edit ? $progress->spins : '' }}">
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label for="nominal">@lang('app.bet')</label>
        <input type="number" step="0.0000001" class="form-control" id="bet" name="bet" placeholder="" required value="{{ $edit ? $progress->bet : '' }}">
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        <label for="bonus">@lang('app.bonus')</label>
        <input type="number" step="0.0000001" class="form-control" id="bonus" name="bonus" placeholder="" required value="{{ $edit ? $progress->bonus : '' }}">
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label>@lang('app.day')</label>
        @php
            $days = array_combine(\VanguardLTE\Progress::$values['day'], \VanguardLTE\Progress::$values['day']);
        @endphp
        {!! Form::select('day', $days, $edit ? $progress->day : '', ['class' => 'form-control']) !!}
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label for="min">@lang('app.min')</label>
        <input type="number" step="0.0000001" class="form-control" id="min" name="min" required value="{{ $edit ? $progress->min : '' }}">
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label for="max">@lang('app.max')</label>
        <input type="number" step="0.0000001" class="form-control" id="max" name="max" required value="{{ $edit ? $progress->max : '' }}">
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label>@lang('app.percent')</label>
        @php
            $percents = array_combine(\VanguardLTE\Progress::$values['percent'], \VanguardLTE\Progress::$values['percent']);
        @endphp
        {!! Form::select('percent', $percents, $edit ? $progress->percent : '', ['class' => 'form-control']) !!}
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label for="nominal">@lang('app.min_balance')</label>
        <input type="number" step="0.0000001" class="form-control" id="min_balance" name="min_balance" required value="{{ $edit ? $progress->min_balance : '' }}">
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label>@lang('app.wager')</label>
        {!! Form::select('wager', \VanguardLTE\Progress::$values['wager'], $edit ? $progress->wager : old('wager'), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        <label>@lang('app.days_active')</label>
        @php
            $days_active = array_combine(\VanguardLTE\Progress::$values['days_active'], \VanguardLTE\Progress::$values['days_active']);
        @endphp
        {!! Form::select('days_active', $days_active, $edit ? $progress->days_active : '', ['class' => 'form-control']) !!}
    </div>
</div>
