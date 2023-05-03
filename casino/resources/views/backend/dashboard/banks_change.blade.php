@extends('backend.layouts.app')

@section('page-title', trans('app.change'))
@section('page-heading', trans('app.change'))

@section('content')

    <section class="content-header">
        @include('backend.partials.messages')
    </section>

    <section class="content">

        <form action="{{ route('backend.banks.update.do') }}" method="POST" class="pb-2 mb-3 border-bottom-light">
            <div class="box box-danger ">
                <div class="box-header with-border">
                    <input type="hidden" value="<?= csrf_token() ?>" name="_token">
                    <input type="hidden" value="{{ implode(',', $ids) }}" name="ids">
                    <h3 class="box-title">@lang('app.change')</h3>
                </div>
                <div class="box-body">
                    <div class="row">

                        <div class="col-md-12">
                            <ul>
                                @foreach($banks AS $bank)
                                    <li>{{ $bank->shop ? $bank->shop->name : 'No shop' }}</li>
                                @endforeach
                            </ul>
                        </div>

                        @php
                            $percents = array_combine([''] + \VanguardLTE\GameBank::$values['banks'], ['---'] + \VanguardLTE\GameBank::$values['banks']);
                        @endphp

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('app.slots')</label>
                                {!! Form::select('slots', $percents, Request::get('percent'), ['class' => 'form-control']) !!}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('app.little')</label>
                                {!! Form::select('little', $percents, Request::get('percent'), ['class' => 'form-control']) !!}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('app.table_bank')</label>
                                {!! Form::select('table_bank', $percents, Request::get('percent'), ['class' => 'form-control']) !!}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('app.fish')</label>
                                {!! Form::select('fish', $percents, Request::get('percent'), ['class' => 'form-control']) !!}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('app.bonus')</label>
                                {!! Form::select('bonus', $percents, Request::get('percent'), ['class' => 'form-control']) !!}
                            </div>
                        </div>


                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">
                        @lang('app.change')
                    </button>
                </div>
            </div>
        </form>



    </section>

@stop

@section('scripts')


@stop