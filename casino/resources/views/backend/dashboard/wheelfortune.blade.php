@extends('backend.layouts.app')

@section('page-title', trans('app.wheelfortune'))
@section('page-heading', trans('app.wheelfortune'))

@section('content')

    <section class="content-header">
        @include('backend.partials.messages')
    </section>

    <section class="content">
            {!! Form::open(['route' => 'backend.wheelfortune.update']) !!}
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">{{ trans('app.wheelfortune') }}</h3>
                <div class="pull-right box-tools">
                    @permission('wheelfortune.manage')
                    @if( auth()->user()->shop )
                        @if( auth()->user()->shop->wheelfortune_active )
                            <a href="{{ route('backend.wheelfortune.status', 'disable') }}" class="btn btn-danger btn-sm">@lang('app.disable')</a>
                        @else
                            <a href="{{ route('backend.wheelfortune.status', 'activate') }}" class="btn btn-success btn-sm">@lang('app.active')</a>
                        @endif
                    @endif
                    @endpermission
                </div>
            </div>

            @php
                $wh1 = array_combine(\VanguardLTE\WheelFortune::$values['wh1'], \VanguardLTE\WheelFortune::$values['wh1']);
                $wh2 = array_combine(\VanguardLTE\WheelFortune::$values['wh2'], \VanguardLTE\WheelFortune::$values['wh2']);
            @endphp

            <div class="box-body">
                <div class="row">
                    @for($i=1; $i<=7; $i++)
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('app.wh')1 {{ $i }}</label>
                                {!! Form::select('wh1_'.$i, \VanguardLTE\WheelFortune::$values['wh1'], $wheelfortune->{'wh1_'.$i}, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    @endfor
                </div>

                <hr>

                <div class="row">
                    @for($i=1; $i<=8; $i++)
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('app.wh')2 {{ $i }}</label>
                                {!! Form::select('wh2_'.$i, \VanguardLTE\WheelFortune::$values['wh1'], $wheelfortune->{'wh2_'.$i} , ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    @endfor
                </div>

                <hr>

                <div class="row">
                    @for($i=1; $i<=16; $i++)
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('app.wh')3 {{ $i }}</label>
                                {!! Form::select('wh3_'.$i, \VanguardLTE\WheelFortune::$values['wh2'], $wheelfortune->{'wh3_'.$i}, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    @endfor
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>@lang('app.wager')</label>
                            {!! Form::select('wager', \VanguardLTE\WheelFortune::$values['wager'], $wheelfortune->wager, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status">@lang('app.status')</label>
                            {!! Form::select('status', [__('app.disabled'), __('app.active')], $wheelfortune->status, ['class' => 'form-control', 'id' => 'status']) !!}
                        </div>
                    </div>
                </div>

            </div>

            <div class="box-footer">
                <button type="submit" class="btn btn-primary">
                    @lang('app.edit_wheelfortune')
                </button>
            </div>
        </div>
        {!! Form::close() !!}
    </section>

@stop
