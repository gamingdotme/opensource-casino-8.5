@extends('backend.layouts.app')

@section('page-title', trans('app.edit_tournament'))
@section('page-heading', $tournament->name)

@section('content')
    <section class="content-header">
        @include('backend.partials.messages')
    </section>
    <section class="content">
        {!! Form::open(['route' => array('backend.tournament.update', $tournament->id), 'files' => true, 'id' => 'tournament-form']) !!}
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">@lang('app.edit_tournament')</h3>
                <div class="pull-right box-tools">
                    <a href="javascript:;" class="btn btn-block btn-primary btn-sm" id="addPrize">@lang('app.add') @lang('app.prize')</a>
                </div>
            </div>
            <div class="box-body">

                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a data-toggle="tab" href="#details">@lang('app.tournament_details')</a>
                        </li>
                        <li >
                            <a data-toggle="tab" href="#stats">@lang('app.tournament_stats')</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="nav-tabContent">
                        <div class=" active tab-pane" id="details">
                            @include('backend.tournaments.partials.base', ['edit' => true])
                        </div>
                        <div class="tab-pane" id="stats">

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>@lang('app.login')</th>
                                        <th>@lang('app.is_bot')</th>
                                        <th>@lang('app.points')</th>
                                        <th>@lang('app.prize')</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if( count($tournament->stats) )
                                        @php $index=1; @endphp
                                        @foreach($tournament->stats->where('prize_id', '!=', 0)->sortBy('prize_id') AS $stat)
                                            <tr>
                                                <td>{{ $index }}</td>
                                                <td>{{ $stat->is_bot ? $stat->bot->username : $stat->user->username }}</td>
                                                <td>{{ $stat->is_bot ? __('app.yes') : __('app.no') }}</td>
                                                <td>{{ $stat->points }}</td>
                                                <td>{{ $stat->prize ? number_format($stat->prize->prize, 2,".","") : '' }}</td>
                                            </tr>
                                            @php $index++; @endphp
                                        @endforeach
                                        @foreach($tournament->stats->where('prize_id', '=', 0)->sortByDesc('points') AS $stat)
                                            <tr>
                                                <td>{{ $index }}</td>
                                                <td>{{ $stat->is_bot ? $stat->bot->username : $stat->user->username }}</td>
                                                <td>{{ $stat->is_bot ? __('app.yes') : __('app.no') }}</td>
                                                <td>{{ $stat->points }}</td>
                                                <td>{{ $stat->prize ? number_format($stat->prize->prize, 2,".","") : '' }}</td>
                                            </tr>
                                            @php $index++; @endphp
                                        @endforeach
                                    @else
                                        <tr><td colspan="5">@lang('app.no_data')</td></tr>
                                    @endif
                                    </tbody>
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>@lang('app.login')</th>
                                        <th>@lang('app.is_bot')</th>
                                        <th>@lang('app.points')</th>
                                        <th>@lang('app.prize')</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>


            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-primary">
                    @lang('app.edit_tournament')
                </button>
                @permission('tournaments.delete')
                <a href="{{ route('backend.tournament.delete', $tournament->id) }}"
                   class="btn btn-danger"
                   data-method="DELETE"
                   data-confirm-title="@lang('app.please_confirm')"
                   data-confirm-text="@lang('app.are_you_sure_delete_tournament')"
                   data-confirm-delete="@lang('app.yes_delete_him')">
                    @lang('app.delete_tournament')
                </a>
                @endpermission
            </div>
        </div>
        {!! Form::close() !!}
    </section>
@stop

@section('scripts')
    <script>
        $('.datepicker').datetimepicker({
            format: 'YYYY-MM-DD HH:mm',
            timeZone: '{{ config('app.timezone') }}'
        });

        $('#addPrize').click(function(){
            $('#prizes').append(
                '<div class="prize_item">\n' +
                '   <div class="form-group">\n' +
                '       <label></label>\n' +
                '       <div class="input-group">\n' +
                '           <input type="number" step="0.0000001" name="prize[]" class="form-control" required>\n' +
                '           <div class="input-group-btn">\n' +
                '               <button type="button" class="btn btn-info delete_prize">-</button>\n' +
                '           </div>\n' +
                '       </div>\n' +
                '   </div>\n' +
                '</div>'
            );
            change_prize_labels();
        });

        $( "#prizes" ).on( "click", '.delete_prize', function(event) {
            $(event.target).parents('.prize_item').remove();
            change_prize_labels();
        });

        $('#categories').on('select2:select', function (e) {
            var selected = $('#categories').val();
            if (selected.length === 0) {}
            get_games(selected);
        });
        $('#categories').on('select2:unselect', function (e) {
            var selected = $('#categories').val();
            get_games(selected);
        });

        function get_games(selected) {
            $.getJSON('{{ route('backend.tournament.games') }}', {'id' : selected}, function(data){
                $('#games').empty().trigger("change");
                if (Object.keys(data).length > 0) {
                    $.each(data, function( index, value ) {
                        $('#games').append(new Option(value, index, false, false)).trigger('change');
                    });
                }
            });
        }

        function change_prize_labels() {
            $( "#prizes label" ).each(function( index ) {
                $( this ).text(  "@lang('app.prize') " + (parseInt(index) + 1) );
            });
        }

    </script>
@stop
