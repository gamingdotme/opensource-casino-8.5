
<div class="row">

    <div class="col-md-4">
        <div class="form-group">
            <label for="title">@lang('app.gamebank')</label>
            {!! Form::select('gamebank', $game->gamebankNames, $edit ? $game->gamebank : '', ['class' => 'form-control', 'required' => true]) !!}
        </div>
    </div>

    <div class="col-md-4">
        @if (!$edit || $game->rezerv !== '')
            <div class="form-group">
                <label for="rezerv">@lang('app.doubling')</label>
                {!! Form::select('rezerv', $game->get_values('rezerv'), $edit ? $game->rezerv : '', ['class' => 'form-control', 'required' => true]) !!}
            </div>
        @endif
    </div>
    <div class="col-md-4">
        @if (!$edit || $game->cask !== '')
            <div class="form-group">
                <label for="cask">@lang('app.health')</label>
                {!! Form::select('cask', $game->get_values('cask'), $edit ? $game->cask : '', ['class' => 'form-control', 'required' => true]) !!}
            </div>
        @endif
    </div>


</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="title">@lang('app.jpg')</label>
            {!! Form::select('jpg_id', ['' => '---'] + $jpgs, $edit ? $game->jpg_id : '', ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label for="title">@lang('app.labels')</label>
            {!! Form::select('label', ['' => '---'] + $game->labels, $edit ? $game->label : '', ['class' => 'form-control']) !!}
        </div>
    </div>
</div>


<ul class="list-group list-group-unbordered">
    <li class="list-group-item">
        @foreach([1,3,5,7,9,10] AS $index)
            <div class="row">
                @foreach(\VanguardLTE\Game::$values['random_keys'] AS $random_key=>$values)
                    @php
                        $key = 'lines_percent_config_spin';
                        $array_key = 'line_spin[line'.$index.']['.$random_key.']';
                        $value = $game->get_line_value($game->$key, 'line'.$index, $random_key, true);
                    @endphp

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>L {{ $index }} - {{ $values[0] }}, {{ $values[1] }}</label>
                            {!! Form::select($array_key, $game->get_values('random_values', false, $edit ? $value: false), $edit ? $value : '', ['class' => 'form-control', 'required' => true]) !!}
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </li>
</ul>

<ul class="list-group list-group-unbordered">
    <li class="list-group-item">
        @foreach([1,3,5,7,9,10] AS $index)
            <div class="row">
                @foreach(\VanguardLTE\Game::$values['random_keys'] AS $random_key=>$values)
                    @php
                        $key = 'lines_percent_config_spin_bonus';
                        $array_key = 'line_spin_bonus[line'.$index.'_bonus]['.$random_key.']';
                        $value = $game->get_line_value($game->$key, 'line'.$index.'_bonus', $random_key, true);
                    @endphp

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>L {{ $index }} Bonus - {{ $values[0] }}, {{ $values[1] }}</label>
                            {!! Form::select($array_key, $game->get_values('random_values', false, $edit ? $value: false), $edit ? $value : '', ['class' => 'form-control', 'required' => true]) !!}
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </li>
</ul>







<div class="row">

    @if ($edit)
        <div class="col-md-12 mt-2">
            <button type="submit" class="btn btn-primary" id="update-details-btn">
                @lang('app.edit_game')
            </button>
        </div>
    @endif
</div>