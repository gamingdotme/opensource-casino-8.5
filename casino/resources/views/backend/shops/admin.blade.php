@extends('backend.layouts.app')

@section('page-title', trans('app.add_shop'))
@section('page-heading', trans('app.add_shop'))

@section('content')

    <section class="content-header">
        @include('backend.partials.messages')
    </section>

    <section class="content">
        {!! Form::open(['route' => 'backend.shop.admin_store', 'files' => true, 'id' => 'user-form']) !!}

        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">@lang('app.add_shop')</h3>
            </div>

            <div class="box-body">
                @foreach(['5' => 'agent', '4' => 'distributor', 'shop' => 'shop', '3' => 'manager', '2' => 'cashier'] AS $role_id=>$role_name)

                    @if($role_id == 'shop')
                        <h4>@lang('app.shop')</h4>
                        @include('backend.shops.partials.base', ['edit' => false, 'profile' => false, 'balance' => true])
                    @else
                        <h4>{{ strtoupper($role_name) }}</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('app.username')</label>
                                    <input type="text" class="form-control" id="username" name="{{ $role_name }}[username]" placeholder="(@lang('app.optional'))" value="{{ old($role_name)['username'] }}">
                                </div>
                            </div>
                            @if($role_name != 'cashier' && $role_name != 'manager')
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ trans('app.balance') }}</label>
                                        <input type="text" class="form-control" id="balance" name="{{ $role_name }}[balance]" value="{{ old($role_name)['balance']?:0 }}">
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ trans('app.password') }}</label>
                                    <input type="password" class="form-control" id="password" name="{{ $role_name }}[password]" value="{{ old($role_name)['password'] }}">
                                </div>
                            </div>
                        </div>
                    @endif


                    <hr>

                @endforeach


                <h4>@lang('app.users')</h4>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>@lang('app.count')</label>
                            {!! Form::select('users[count]', [1=>1,5=>5,10=>10,25=>25,50=>50,100=>100], old('users')['count'] , ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>@lang('app.balance')</label>
                            <input type="text" class="form-control" id="title" name="users[balance]" value="{{ old('users')['balance']?:0 }}">
                        </div>
                    </div>
                </div>

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">
                        @lang('app.add_shop')
                    </button>
                </div>

            </div>



        </div>

        {!! Form::close() !!}
    </section>

@stop