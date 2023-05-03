@extends('backend.layouts.user')

@section('page-title', trans('app.edit_shop'))
@section('page-heading', $shop->title)

@section('content')
    <div class="row wow fadeIn">
        <div class="col-lg-12">
            <section class="content-header">
                @include('backend.partials.messages')
            </section>
            <div class="element-wrapper">
                <div class="element-box">
                    <div class="element-info mb-3">
                        <div class="element-info-with-icon">
                            <div class="element-info-icon">
                                <div class="fa fa-pie-chart"></div>
                            </div>
                            <div class="element-info-text">
                                <h5 class="element-inner-header">@lang('app.edit_shop')</h5>
                                <div class="element-inner-desc text-primary">
                                </div>
                            </div>
                        </div>
                    </div>
                    <section class="content">

        {!! Form::open(['route' => array('backend.shop.update', $shop->id), 'files' => true, 'id' => 'user-form']) !!}
        <div class="box box-default">


            <div class="box-body">

                    @include('backend.shops.partials.base', ['edit' => true])

            </div>

            <div class="box-footer">
                <button type="submit" class="btn btn-primary">
                    @lang('app.edit_shop')
                </button>

                @if( Auth::user()->hasRole(['admin','agent']) )
                    <a href="{{ route('backend.shop.hard_delete', $shop->id) }}"
                       class="btn btn-danger"
                       data-method="DELETE"
                       data-confirm-title="@lang('app.please_confirm')"
                       data-confirm-text="@lang('app.are_you_sure_delete_shop')"
                       data-confirm-delete="@lang('app.yes_delete_him')">
                        @lang('app.hard_delete')
                    </a>
                @endif

                @if( Auth::user()->hasRole('distributor') && count(Auth::user()->shops()) > 1 )
                <a href="{{ route('backend.shop.delete', $shop->id) }}"
                   class="btn btn-danger"
                   data-method="DELETE"
                   data-confirm-title="@lang('app.please_confirm')"
                   data-confirm-text="@lang('app.are_you_sure_delete_shop')"
                   data-confirm-delete="@lang('app.yes_delete_him')">
                    @lang('app.delete_shop')
                </a>
                @endif


            </div>
        </div>
        {!! Form::close() !!}

    </section>
                </div>
            </div>
        </div>
    </div>



@stop
