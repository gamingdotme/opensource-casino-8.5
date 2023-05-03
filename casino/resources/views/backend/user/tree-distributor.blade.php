@extends('backend.layouts.user')

@section('page-title', $role->name .' '. trans('app.tree'))
@section('page-heading', $role->name .' '. trans('app.tree'))
<style>
    .content-w table.dataTable th, .content-w table.dataTable td {
    font-size: 14px !important;
}
</style>
@section('content')
<div class="row wow fadeIn">

<div class="col-lg-12">
    <section class="content-header">
        @include('backend.partials.messages')
    </section>
    <div class="element-wrapper">
        <div class="element-box">
        <div class="element-info">
                <div class="element-info-with-icon">
                    <div class="element-info-icon">
                        <i class="fa fa-users"></i>
                    </div>
                    <div class="element-info-text">
                        <h5 class="element-inner-header">{{ $role->name }} @lang('app.tree')</h5>
                    </div>
                </div>
            </div>
            <!-- <div class="row"> -->
                <div class="col-sm-12 table-responsive p-0">
                    <div id="transactions_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4  p-0 m-0 ">
                    <table class="table table-striped table-bordered table-sm dataTable no-footer">
                        <thead>
                        <tr>
                            @if( auth()->user()->hasRole(['admin','agent']) )
                                <th>@lang('app.agent')</th>
                            @endif
                            <th>@lang('app.distributor')</th>
                            <th>@lang('app.shop')</th>
                            <th>@lang('app.manager')</th>
                            <th>@lang('app.cashier')</th>
                            <th>@lang('app.user')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if (count($users))
                            @foreach ($users as $user)
                                <tr>
                                @if($user->hasRole('agent'))
                                    <td rowspan="{{ $user->getRowspan() }}">
                                        <a href="{{ route('backend.user.edit', $user->id) }}">
                                            {{ $user->username ?: trans('app.n_a') }}
                                        </a>
                                    </td>
                                    @if( $distributors = $user->getInnerUsers() )
                                        @foreach($distributors AS $distributor)
                                            @include('backend.user.partials.distributor')
                                        @endforeach
                                    @else
                                        <td colspan="5"></td></tr><tr></tr><tr>
                                    @endif
                                @endif
                                @if($user->hasRole('distributor'))
                                    @include('backend.user.partials.distributor', ['distributor' => $user])
                                @endif
                                </tr>
                            @endforeach
                        @else
                            <tr><td colspan="6">@lang('app.no_data')</td></tr>
                        @endif
                        </tbody>
                        <thead>
                        <tr>
                            @if( auth()->user()->hasRole(['admin','agent']) )
                                <th>@lang('app.agent')</th>
                            @endif
                            <th>@lang('app.distributor')</th>
                            <th>@lang('app.shop')</th>
                            <th>@lang('app.manager')</th>
                            <th>@lang('app.cashier')</th>
                            <th>@lang('app.user')</th>
                        </tr>
                        </thead>
                    </table>
                    </div>
                </div>
            <!-- </div> -->

</div>
</div>
</div>
</div>


@stop

@section('scripts')
    <script>


    </script>
@stop
