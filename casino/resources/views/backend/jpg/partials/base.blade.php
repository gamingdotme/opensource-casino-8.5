<div class="col-md-6">
    <div class="form-group">
        <label>@lang('app.name')</label>
        <input type="text" class="form-control" id="name" name="name" placeholder="@lang('app.name')" required value="{{ $edit ? $jackpot->name : '' }}">
    </div>
</div>
@if(auth()->user()->hasRole('admin') )
    <div class="col-md-6">
        <div class="form-group">
            <label>@lang('app.balance')</label>
            <input type="number" step="0.0000001" class="form-control" id="balance" name="balance" placeholder="0.00" @if(!auth()->user()->hasRole('admin'))disabled @endif value="{{ $edit ? $jackpot->balance : '' }}" >
        </div>
    </div>
@endif

@if( auth()->user()->hasPermission('jpgame.edit') )
<div class="col-md-6">
    <div class="form-group">
        <label>@lang('app.start_balance')</label>
        {!! Form::select('start_balance', ['' => '---'] + \VanguardLTE\JPG::$values['start_balance'], $edit ? '' : 1, ['class' => 'form-control']) !!}
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        <label>@lang('app.trigger')</label>
        {!! Form::select('pay_sum', ['' => '---'] + \VanguardLTE\JPG::$values['pay_sum'], $edit ? '' : 0, ['class' => 'form-control']) !!}
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        <label>@lang('app.percent')</label>
        @php
            $percents = array_combine(\VanguardLTE\JPG::$values['percent'], \VanguardLTE\JPG::$values['percent']);
        @endphp
        {!! Form::select('percent', ['' => '---'] + $percents, '', ['class' => 'form-control']) !!}
    </div>
</div>
@endif

<div class="col-md-6">
    <div class="form-group">
        <label>@lang('app.user')</label>
        {!! Form::select('user_id', ['' => '---'] + $users, $edit ? $jackpot->user_id : 0, ['class' => 'form-control select2', 'style' => 'width: 100%;']) !!}
    </div>
</div>
