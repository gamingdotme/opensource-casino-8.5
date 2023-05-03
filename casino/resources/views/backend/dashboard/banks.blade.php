@extends('backend.layouts.app')

@section('page-title', trans('app.banks'))
@section('page-heading', trans('app.banks'))

@section('content')

    <section class="content-header">
        @include('backend.partials.messages')
    </section>

    <section class="content">

            <form action="" id="games-form" method="GET">
                <div class="box box-danger collapsed-box banks_show">
                    <div class="box-header with-border">
                        <h3 class="box-title">@lang('app.filter')</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('app.search')</label>
                                    <input type="text" class="form-control" name="search" value="{{ Request::get('search') }}" placeholder="@lang('app.banks')">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('app.percent')</label>
                                    {!! Form::select('percent', ['' => '---'] + \VanguardLTE\Shop::$values['percent_labels'], Request::get('percent'), ['class' => 'form-control']) !!}
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('app.slots') From</label>
                                    <input type="text" class="form-control" name="slots_from" value="{{ Request::get('slots_from') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('app.slots') To</label>
                                    <input type="text" class="form-control" name="slots_to" value="{{ Request::get('slots_to') }}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('app.little') From</label>
                                    <input type="text" class="form-control" name="little_from" value="{{ Request::get('little_from') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('app.little') To</label>
                                    <input type="text" class="form-control" name="little_to" value="{{ Request::get('little_to') }}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('app.table_bank') From</label>
                                    <input type="text" class="form-control" name="table_from" value="{{ Request::get('table_from') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('app.table_bank') To</label>
                                    <input type="text" class="form-control" name="table_to" value="{{ Request::get('table_to') }}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('app.fish') From</label>
                                    <input type="text" class="form-control" name="fish_from" value="{{ Request::get('fish_from') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('app.fish') To</label>
                                    <input type="text" class="form-control" name="fish_to" value="{{ Request::get('fish_to') }}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('app.bonus') From</label>
                                    <input type="text" class="form-control" name="bonus_from" value="{{ Request::get('bonus_from') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('app.bonus') To</label>
                                    <input type="text" class="form-control" name="bonus_to" value="{{ Request::get('bonus_to') }}">
                                </div>
                            </div>


                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('app.rtp') From</label>
                                    <input type="text" class="form-control" name="rtp_from" value="{{ Request::get('rtp_from') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('app.rtp') To</label>
                                    <input type="text" class="form-control" name="rtp_to" value="{{ Request::get('rtp_to') }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('app.order')</label>
                                    {!! Form::select('sort_order', ['' => '---', 'asc' => 'Low', 'desc' => 'High'], $savedSortOrder, ['class' => 'form-control']) !!}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('app.order')</label>
                                    {!! Form::select('sort_field',
                                        [
                                            '' => '---',
                                            'percent' => 'Percent',
                                            'rtp' => 'RTP',
                                            'slots' => 'Slots',
                                            'little' => 'Little',
                                            'table_bank' => 'Table',
                                            'fish' => 'Fish',
                                            'bonus' => 'Bonus',
                                            'total' => 'Total',
                                        ],
                                    $savedSortFiled, ['class' => 'form-control']) !!}
                                </div>
                            </div>

                        </div>
                        <div class="row">




                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">
                            @lang('app.filter')
                        </button>
                        <a href="?clear" class="btn btn-default">
                            @lang('app.clear')
                        </a>

                    </div>
                </div>
            </form>

            <form action="{{ route('backend.banks.update') }}" method="POST" class="pb-2 mb-3 border-bottom-light">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">@lang('app.banks')</h3>
                        @if( auth()->user()->hasRole('admin') )
                        <div class="pull-right box-tools">
                            <input type="hidden" value="<?= csrf_token() ?>" name="_token">
                            <button class="btn btn-block btn-primary btn-sm" type="submit">@lang('app.change')</button>
                        </div>
                        @endif
                    </div>
                    <div class="box-body">


                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>@lang('app.name')</th>
                                    <th>@lang('app.credit')</th>
                                    <th>@lang('app.percent')</th>
                                    <th>@lang('app.rtp')</th>
                                    <th>@lang('app.slots')</th>
                                    <th>@lang('app.little')</th>
                                    <th>@lang('app.table_bank')</th>
                                    <th>@lang('app.fish')</th>
                                    <th>@lang('app.bonus')</th>
                                    <th>@lang('app.total')</th>
                                    <th>
                                        <label class="checkbox-container">
                                            <input type="checkbox" class="checkAll">
                                            <span class="checkmark"></span>
                                        </label>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                @if (count($banks))
                                    @foreach ($banks as $bank)
                                        @if($bank->shop)
                                        <tr>
                                            <td>{{ $bank->shop ? $bank->shop->name : 'No shop' }}</td>
                                            <td>{{ $bank->shop ? $bank->shop->balance : '' }}</td>
                                            <td>{{ $bank->shop ? $bank->shop->get_percent_label($bank->shop->percent) : '' }}</td>
                                            <td>{{ $bank->get_rtp() }}</td>
                                            <td>{{ $bank->slots }}</td>
                                            <td>{{ $bank->little }}</td>
                                            <td>{{ $bank->table_bank }}</td>
                                            <td>{{ $bank->fish }}</td>
                                            <td>{{ $bank->bonus }}</td>
                                            <td>{{ $bank->total() }}</td>
                                            <td>
                                                <label class="checkbox-container">
                                                    <input type="checkbox" name="checkbox[{{ $bank->shop_id }}]">
                                                    <span class="checkmark"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                @else
                                    <tr><td colspan="11">@lang('app.no_data')</td></tr>
                                @endif
                                </tbody>
                                <thead>
                                <tr>
                                    <th>@lang('app.name')</th>
                                    <th>@lang('app.credit')</th>
                                    <th>@lang('app.percent')</th>
                                    <th>@lang('app.rtp')</th>
                                    <th>@lang('app.slots')</th>
                                    <th>@lang('app.little')</th>
                                    <th>@lang('app.table_bank')</th>
                                    <th>@lang('app.fish')</th>
                                    <th>@lang('app.bonus')</th>
                                    <th>@lang('app.total')</th>
                                    <th>
                                        <label class="checkbox-container">
                                            <input type="checkbox" class="checkAll">
                                            <span class="checkmark"></span>
                                        </label>
                                    </th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </form>



    </section>

@stop

@section('scripts')

    <script>

        $("#filter").detach().appendTo("div.toolbar");


        $('.btn-box-tool').click(function(event){
            if( $('.banks_show').hasClass('collapsed-box') ){
                $.cookie('banks_show', '1');
            } else {
                $.removeCookie('banks_show');
            }
        });

        if( $.cookie('banks_show') ){
            $('.banks_show').removeClass('collapsed-box');
            $('.banks_show .btn-box-tool i').removeClass('fa-plus').addClass('fa-minus');
        }

    </script>
@stop