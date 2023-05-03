@extends('backend.layouts.app')

@section('page-title', trans('app.jpg'))
@section('page-heading', trans('app.jpg'))

@section('content')

    <section class="content-header">
        @include('backend.partials.messages')
    </section>

    <section class="content">

        <form action="{{ route('backend.jpgame.global_update') }}" method="POST" class="pb-2 mb-3 border-bottom-light">
            <div class="box box-danger ">
                <div class="box-header with-border">
                    <input type="hidden" value="<?= csrf_token() ?>" name="_token">
                    <input type="hidden" value="{{ implode(',', $ids) }}" name="ids">
                    <h3 class="box-title">@lang('app.jpg')</h3>
                </div>
                <div class="box-body">
                    <div class="row">

                        <div class="col-md-12">
                            <ul>
                                @foreach($jackpots AS $jackpot)
                                    <li>{{ $jackpot->name }}</li>
                                @endforeach
                            </ul>
                        </div>

                        @if(auth()->user()->hasRole('admin') )
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('app.balance')</label>
                                    <input type="number" step="0.0000001" class="form-control" id="balance" name="balance" placeholder="">
                                </div>
                            </div>
                        @endif

                        @if( auth()->user()->hasPermission('jpgame.edit') )
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('app.start_balance')</label>
                                {!! Form::select('start_balance', ['' => '---'] + \VanguardLTE\JPG::$values['start_balance'], '', ['class' => 'form-control']) !!}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('app.trigger')</label>
                                {!! Form::select('pay_sum', ['' => '---'] + \VanguardLTE\JPG::$values['pay_sum'], '', ['class' => 'form-control']) !!}
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

                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">
                        @lang('app.jpg')
                    </button>
                </div>
            </div>
        </form>



    </section>

@stop

@section('scripts')
@stop
