<ul class="list-group list-group-unbordered">
    <li class="list-group-item">
        @foreach([1,3,5,7,9,10] AS $index)
            <div class="row">
                @foreach(\VanguardLTE\Game::$values['random_keys'] AS $random_key=>$values)
                    @php
                        $key = 'lines_percent_config_bonus';
                        $array_key = 'line_bonus[line'.$index.']['.$random_key.']';
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
                        $key = 'lines_percent_config_bonus_bonus';
                        $array_key = 'line_bonus_bonus[line'.$index.'_bonus]['.$random_key.']';
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


<ul class="list-group list-group-unbordered">
    <li class="list-group-item">
        <div class="row">
            @foreach([1,2,3] AS $index)
                @php
                    $key = 'chanceFirepot'.$index;
                @endphp
                <div class="col-md-6">
                    <div class="form-group">
                        <label>ChanceFirepot {{ $index }}</label>
                        {!! Form::select($key, $game->get_values($key, true, $edit ? $game->$key: false), $edit ? $game->$key : '', ['class' => 'form-control', 'required' => true]) !!}
                    </div>
                </div>
                @php
                    $key = 'fireCount'.$index;
                @endphp
                <div class="col-md-6">
                    <div class="form-group">
                        <label>FireCount {{ $index }}</label>
                        {!! Form::select($key, $game->get_values($key, true, $edit ? $game->$key: false), $edit ? $game->$key : '', ['class' => 'form-control', 'required' => true]) !!}
                    </div>
                </div>
            @endforeach
        </div>

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