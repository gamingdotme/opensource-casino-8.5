@extends('backend.layouts.app')

@section('page-title', trans('app.add_tournament'))
@section('page-heading', trans('app.add_tournament'))

@section('content')

    <section class="content-header">
        @include('backend.partials.messages')
    </section>

    <section class="content">
        {!! Form::open(['route' => 'backend.tournament.store', 'files' => true, 'id' => 'tournament-form']) !!}
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">@lang('app.add_tournament')</h3>
                <div class="pull-right box-tools">
                    <a href="javascript:;" class="btn btn-block btn-primary btn-sm" id="addPrize">@lang('app.add') @lang('app.prize')</a>
                </div>
            </div>

            <div class="box-body">
                @include('backend.tournaments.partials.base', ['edit' => false])
            </div>

            <div class="box-footer">
                <button type="submit" class="btn btn-primary">
                    @lang('app.add_tournament')
                </button>
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
