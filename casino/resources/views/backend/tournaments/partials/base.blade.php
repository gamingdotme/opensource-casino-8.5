<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>@lang('app.name')</label>
            <input type="text" class="form-control" name="name" value="{{ $edit ? $tournament->name : old('name') }}" required @if($denied) disabled @endif>
        </div>
        <div class="form-group">
            <label>@lang('app.start')</label>
            <div class="input-group date">
                <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                </div>
                <input type="text" name="start" id="start" class="form-control pull-right datepicker" required value="{{ $edit ? $tournament->start : old('start') }}" @if($denied) disabled @endif>
            </div>
        </div>
        <div class="form-group">
            <label>@lang('app.end')</label>
            <div class="input-group date">
                <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                </div>
                <input type="text" name="end" id="end" class="form-control pull-right datepicker" required value="{{ $edit ? $tournament->end : old('end') }}" @if($denied) disabled @endif>
            </div>
        </div>
        <div class="form-group">
            <label>@lang('app.type')</label>
            {!! Form::select('type', \VanguardLTE\Tournament::$values['type'], $edit ? $tournament->type : old('type'), ['class' => 'form-control', 'disabled' => $denied ? true:false]) !!}
        </div>

        <div class="form-group">
            <label>@lang('app.bet')</label>
            @php
                $bets = array_combine(\VanguardLTE\Tournament::$values['bet'], \VanguardLTE\Tournament::$values['bet']);
            @endphp
            {!! Form::select('bet', $bets, $edit ? $tournament->bet : old('bet'), ['class' => 'form-control', 'disabled' => $denied ? true:false]) !!}
        </div>
        <div class="form-group">
            <label>@lang('app.spins')</label>
            @php
                $spins = array_combine(\VanguardLTE\Tournament::$values['spins'], \VanguardLTE\Tournament::$values['spins']);
            @endphp
            {!! Form::select('spins', $spins, $edit ? $tournament->spins : old('spins'), ['class' => 'form-control', 'disabled' => $denied ? true:false]) !!}
        </div>

        <div class="form-group">
            <label for="device"> @lang('app.categories')</label>
            <select class="form-control select2" name="categories[]" multiple="multiple" id="categories" style="width: 100%;" @if($denied) disabled @endif>
                <option value="0" {{ ((old('categories') && in_array(0, old('categories')) ) || ($edit && in_array(0, $cats) )) ? 'selected':'' }}>All</option>
                @foreach ($categories as $key=>$category)
                    <option value="{{ $category->id }}"
                            {{
    ((old('categories') && in_array($category->id, old('categories')) )  || ($edit && in_array($category->id, $cats) ))
    ? 'selected':'' }}
                    >{{ $category->title }}</option>
                    @foreach ($category->inner as $inner)
                        <option value="{{ $inner->id }}"
                                {{
    (( old('categories') && in_array($inner->id, old('categories')) || ( $edit && in_array($inner->id, $cats) )) ) ? 'selected':''

    }}>{{ $inner->title }}</option>
                    @endforeach
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>@lang('app.games')</label>
            {!! Form::select('games[]', $games, ($edit && $tournament->games_selected) ? $gams : old('games'), ['id' => 'games', 'class' => 'form-control select2', 'multiple' => 'multiple', 'style' => 'width: 100%;', 'disabled' => $denied ? true:false]) !!}
        </div>

        <div class="form-group">
            <label>@lang('app.bots')</label>
            @php
                $bots = array_combine(\VanguardLTE\Tournament::$values['bots'], \VanguardLTE\Tournament::$values['bots']);
            @endphp
            {!! Form::select('bots', $bots, $edit ? $tournament->bots : old('bots'), ['class' => 'form-control', 'disabled' => $denied ? true:false]) !!}
        </div>
        <div class="form-group">
            <label>@lang('app.bots_time')</label>
            @php
                $bots_time = array_combine(\VanguardLTE\Tournament::$values['bots_time'], \VanguardLTE\Tournament::$values['bots_time']);
            @endphp
            {!! Form::select('bots_time', $bots_time, $edit ? $tournament->bots_time : old('bots_time'), ['class' => 'form-control', 'disabled' => $denied ? true:false]) !!}
        </div>
        <div class="form-group">
            <label>@lang('app.bots_step')</label>
            {!! Form::select('bots_step', \VanguardLTE\Tournament::$values['bots_step'], $edit ? $tournament->bots_step : old('bots_step'), ['class' => 'form-control', 'disabled' => $denied ? true:false]) !!}
        </div>
        <div class="form-group">
            <label>@lang('app.bots_limit')</label>
            @php
                $bots_limit = array_combine(\VanguardLTE\Tournament::$values['bots_limit'], \VanguardLTE\Tournament::$values['bots_limit']);
            @endphp
            {!! Form::select('bots_limit', $bots_limit, $edit ? $tournament->bots_limit : old('bots'), ['class' => 'form-control', 'disabled' => $denied ? true:false]) !!}
        </div>
        <div class="form-group">
            <label>@lang('app.wager')</label>
            {!! Form::select('wager', \VanguardLTE\Tournament::$values['wager'], $edit ? $tournament->wager : old('wager'), ['class' => 'form-control', 'disabled' => $denied ? true:false]) !!}
        </div>
        <div class="form-group">
            <label>@lang('app.image')</label>
            @if($edit && $tournament->image != '')
                <img src="{{ '/storage/tournaments/' . $tournament->image }}" style="width: 100%;">
            @endif
            <input type="file" class="form-control" name="image" value="{{ $edit ? $tournament->image : old('image') }}" @if($denied) disabled @endif>
        </div>
        <div class="form-group">
            <label>@lang('app.text')</label>
            <textarea class="form-control" id="editor" name="description"  >{{ $edit ? $tournament->description : old('description') }}</textarea>
        </div>
        <div class="form-group">
            <label>@lang('app.repeat_days')</label>
            @php
                $repeat_days = array_combine(\VanguardLTE\Tournament::$values['repeat_days'], \VanguardLTE\Tournament::$values['repeat_days']);
            @endphp
            {!! Form::select('repeat_days', ['' => '---'] + $repeat_days, $edit ? $tournament->repeat_days : old('repeat_days'), ['class' => 'form-control', 'disabled' => $denied ? true:false]) !!}
        </div>
        <div class="form-group">
            <label>@lang('app.repeat_number')</label>
            @php
                $repeat_number = array_combine(\VanguardLTE\Tournament::$values['repeat_number'], \VanguardLTE\Tournament::$values['repeat_number']);
            @endphp
            {!! Form::select('repeat_number', ['' => '---'] + $repeat_number, $edit ? $tournament->repeat_number : old('repeat_number'), ['class' => 'form-control', 'disabled' => $denied ? true:false]) !!}
        </div>

    </div>
    <div class="col-md-6">
        <div id="prizes">

            @php $count = 0; @endphp

            @if(old('prize'))
                @foreach(old('prize') AS $prize)
                    @php $count++; @endphp
                    <div class="prize_item">
                        <div class="form-group">
                            <label>@lang('app.prize') {{ $count }}</label>
                            <div class="input-group">
                                <input type="number" step="0.0000001" name="prize[]" class="form-control" required value="{{ $prize }}" @if($denied) disabled @endif>
                                <div class="input-group-btn">
                                    <button type="button" class="btn btn-info delete_prize">-</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                @if($edit)
                    @if($tournament->prizes )
                        @foreach($tournament->prizes AS $prize)
                            @php $count++; @endphp
                            <div class="prize_item">
                                <div class="form-group">
                                    <label>@lang('app.prize') {{ $count }}</label>
                                    <div class="input-group">
                                        <input type="number" step="0.0000001" name="prize[]" class="form-control" required value="{{ $prize->prize }}" @if($denied) disabled @endif>
                                        <div class="input-group-btn">
                                            <button type="button" class="btn btn-info delete_prize">-</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                @endif
            @endif

            @if($count < 10)
                @for($i=0; $i<(10-$count); $i++)
                    <div class="prize_item">
                        <div class="form-group">
                            <label>@lang('app.prize') {{ $i+$count+1 }}</label>
                            <div class="input-group">
                                <input type="number" step="0.0000001" name="prize[]" class="form-control" required value="" @if($denied) disabled @endif>
                                <div class="input-group-btn">
                                    <button type="button" class="btn btn-info delete_prize">-</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endfor
            @endif
        </div>
    </div>
</div>
