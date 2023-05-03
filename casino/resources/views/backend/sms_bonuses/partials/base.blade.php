<div class="col-md-6">
    <div class="form-group">
        <label>@lang('app.days')</label>
        @php
            $days = array_combine(\VanguardLTE\SMSBonus::$values['days'], \VanguardLTE\SMSBonus::$values['days']);
        @endphp
        {!! Form::select('days', $days, $edit ? $sms_bonus->days : old('days'), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        <label>@lang('app.bonus')</label>
        @php
            $bonuses = array_combine(\VanguardLTE\SMSBonus::$values['bonus'], \VanguardLTE\SMSBonus::$values['bonus']);
        @endphp
        {!! Form::select('bonus', $bonuses, $edit ? $sms_bonus->bonus : old('bonus'), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        <label>@lang('app.wager')</label>
        {!! Form::select('wager', \VanguardLTE\SMSBonus::$values['wager'], $edit ? $sms_bonus->wager : old('wager'), ['class' => 'form-control']) !!}
    </div>
</div>

