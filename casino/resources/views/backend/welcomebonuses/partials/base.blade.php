<div class="col-md-6">
    <div class="form-group">
        <label>@lang('app.sum')</label>
        <input type="number" step="0.0000001" class="form-control" name="sum" value="{{ $edit ? $welcome_bonus->sum : old('sum') }}" required >
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label>@lang('app.type')</label>
        {!! Form::select('type', \VanguardLTE\WelcomeBonus::$values['type'], $edit ? $welcome_bonus->type : old('type'), ['class' => 'form-control', 'disabled' => true]) !!}
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label>@lang('app.bonus')</label>
        @php
            $bonuses = array_combine(\VanguardLTE\WelcomeBonus::$values['bonus'], \VanguardLTE\WelcomeBonus::$values['bonus']);
        @endphp
        {!! Form::select('bonus', $bonuses, $edit ? $welcome_bonus->bonus : old('bonus'), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        <label>@lang('app.wager')</label>
        {!! Form::select('wager', \VanguardLTE\WelcomeBonus::$values['wager'], $edit ? $welcome_bonus->wager : old('wager'), ['class' => 'form-control']) !!}
    </div>
</div>

