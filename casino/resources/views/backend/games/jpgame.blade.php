@extends('backend.layouts.app')

@section('page-title', 'Edit JPG')
@section('page-heading', 'Edit JPG')

@section('content')

    <section class="content-header">
        @include('backend.partials.messages')
    </section>

    <section class="content">

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> Edit JPG</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12" >
                        {!! Form::open(['route' => ['backend.jpgame.update'], 'method' => 'POST']) !!}
                        @include('backend.games.partials.jackpot')
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop

@section('scripts')
@stop