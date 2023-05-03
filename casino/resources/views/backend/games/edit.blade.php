@extends('backend.layouts.app')

@section('page-title', trans('app.edit_game'))
@section('page-heading', $game->name)

@section('content')

    <section class="content-header">
        @include('backend.partials.messages')
    </section>

    <section class="content">

        <div class="row">
            <div class="col-md-3">

                <div class="box box-primary">
                    <div class="box-body box-profile">
                        <center>
                            <img class="img-responsive" src="{{ $edit ? '/frontend/Default/ico/' . $game->name . '.jpg' : '' }}" alt="{{ $edit ? $game->name : '' }}">
                        </center>
                        <ul class="list-group list-group-unbordered">
                            <li class="list-group-item">
                                <b>@lang('app.percent')</b> <a class="pull-right">{{ $game->shop? $game->shop->get_percent_label($game->shop->percent):'0' }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>@lang('app.in')</b> <a class="pull-right">{{ $game->stat_in }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>@lang('app.out')</b> <a class="pull-right">{{ $game->stat_out }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>@lang('app.total')</b>
                                <a class="pull-right">
                                    @if(($game->stat_in - $game->stat_out) >= 0)
                                        <span class="text-green">
		@else
                                                <span class="text-red">
		@endif
                                                    {{ number_format(abs($game->stat_in-$game->stat_out), 4, '.', '') }}
		</span>
                                </a>
                            </li>
                            <li class="list-group-item">
                                <b>@lang('app.rtp')</b> <a class="pull-right">{{ $game->stat_in > 0 ? number_format(($game->stat_out / $game->stat_in) * 100, 2, '.', '') : '0.00' }}</a>
                            </li>
                        </ul>

                        <a href="{{ route('backend.game.delete', $game->id) }}" class="btn btn-danger btn-block"
                           data-method="DELETE"
                           data-confirm-title="@lang('app.please_confirm')"
                           data-confirm-text="@lang('app.are_you_sure_delete_game')"
                           data-confirm-delete="@lang('app.yes_delete_him')">
                            <b>DELETE</b></a>
                    </div>
                </div>

                <div class="box box-primary">
                    <div class="box-body">
                        <h4>@lang('app.latest_stats')</h4>

                        <table class="table table-borderless table-striped">
                            <thead>
                            <tr>
                                <th>@lang('app.user')</th>
                                <th>@lang('app.win')</th>
                            </tr>
                            </thead>
                            <tbody>

                            @if (count($game_stat))
                                @foreach ($game_stat as $stat)
                                    <tr>
                                        <td>
                                            <a href="{{ route('backend.game_stat', ['user' => $stat->user->username])  }}">
                                                {{ $stat->user->username }}
                                            </a>
                                        </td>
                                        <td>{{ $stat->win }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr><td colspan="2">@lang('app.no_data')</td></tr>
                            @endif

                            </tbody>
                        </table>

                    </div>
                </div>

            </div>

            <div class="col-md-9" id="colrighttemp">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a id="details-tab"
                               data-toggle="tab"
                               href="#details">
                                @lang('app.game_details')
                            </a>
                        </li>

                            <li>
                                <a id="authentication-tab"
                                   data-toggle="tab"
                                   href="#login-details">
                                    @lang('app.game_settings')
                                </a>
                            </li>

                            <li>
                                <a id="bonus-tab"
                                   data-toggle="tab"
                                   href="#bonus-details">
                                    @lang('app.game_bonuses')
                                </a>
                            </li>

                    </ul>

                    <div class="tab-content" id="nav-tabContent">
                        <div class="active tab-pane" id="details">
                            {!! Form::open(['route' => ['backend.game.update', $game->id], 'method' => 'POST', 'id' => 'details-form']) !!}
                            @include('backend.games.partials.base', ['profile' => false])
                            {!! Form::close() !!}
                        </div>


                        <div class="tab-pane" id="login-details">
                            {!! Form::open(['route' => ['backend.game.update', $game->id], 'method' => 'POST', 'id' => 'login-details-form']) !!}
                            @include('backend.games.partials.match')
                            {!! Form::close() !!}
                        </div>

                        <div class="tab-pane" id="bonus-details">
                            {!! Form::open(['route' => ['backend.game.update', $game->id], 'method' => 'POST', 'id' => 'bonus-details-form']) !!}
                            @include('backend.games.partials.bonus')
                            {!! Form::close() !!}
                        </div>

                    </div>

                </div>



            </div>
        </div>



    </section>









@stop

@section('scripts')
    <script>
        $('.changeAddSum').click(function(event){
            $('#AddSum').val($(event.target).data('value'));
            $('#gamebank_add').submit();
        });
    </script>
@stop