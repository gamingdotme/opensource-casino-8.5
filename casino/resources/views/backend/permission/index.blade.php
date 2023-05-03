@extends('backend.layouts.app')

@section('page-title', trans('app.permissions'))
@section('page-heading', trans('app.permissions'))

@section('content')

    <section class="content-header">
        @include('backend.partials.messages')
    </section>

    <section class="content">

        <div class="box box-primary">
            <div class="box-header with-border">
                <h2 class="box-title">@lang('app.permissions')</h2>

            </div>
        </div>

        {!! Form::open(['route' => 'backend.permission.save', 'class' => 'mb-4']) !!}


        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title">
                    @lang('app.permissions')
                </h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-pills nav-stacked">
                                @foreach($data AS $key=>$permissions)
                                    @if( count($permissions) )
                                        <li @if ($loop->first) class="active" @endif><a href="#permissions_{{ $key }}" data-toggle="tab" aria-expanded="false">@lang('app.permissions_' . $key)</a></li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="tab-content">

                            @foreach($data AS $key=>$permissions)
                                @if( count($permissions) )
                                    <div class="tab-pane @if ($loop->first) active @endif" id="permissions_{{ $key }}">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                <tr>
                                                    <th>@lang('app.name')</th>
                                                    @foreach ($roles as $role)
                                                        @if( !in_array($role->id, [1,6]) )
                                                            <th>{{ $role->name }}</th>
                                                        @endif
                                                    @endforeach
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @if (count($permissions))
                                                    @foreach ($permissions as $permission)
                                                        <tr>
                                                            <td>{{ $permission->name ?: $permission->name }}</td>

                                                            @foreach ($roles as $role)
                                                                @if( !in_array($role->id, [1,6]) )
                                                                    <td>

                                                                        @if(
                                                                (in_array($role->id, [2,3,4]) && in_array($permission->id, [11,95,131])  )
                                                                    ||
                                                                (in_array($role->id, [2,3]) && in_array($permission->id, [60,130,14,81,82,83,84,85,86,87,88,89,132,133,134,135,136,137,138,139])  )
                                                                    ||
                                                                ( $role->id == 2 && in_array($permission->id, [56,57,58,59,103,104,105,106,121,90,122,80,114,115,116,117,118,119,10,123,8,98,96,97,102,127,128,36,38,39,125]))
                                                                )
                                                                            <label class="checkbox-container" for="cb-{{ $role->id }}-{{ $permission->id }}">
                                                                                {!!
                                                                                Form::checkbox(
                                                                                    "roles_temp[{$role->id}][]",
                                                                                    $permission->id,
                                                                                    $role->hasOnePermission($permission->id),
                                                                                    [
                                                                                        'id' => "cb-{$role->id}-{$permission->id}",
                                                                                        'disabled' => 'disabled'
                                                                                    ]
                                                                                )
                                                                                !!}

                                                                                {!!
                                                                            Form::checkbox(
                                                                                "roles[{$role->id}][]",
                                                                                $permission->id,
                                                                                $role->id == 6 ? true: $role->hasOnePermission($permission->id),
                                                                                [
                                                                                    'id' => "cb-{$role->id}-{$permission->id}",
                                                                                    'style' => 'display: none;'
                                                                                ]
                                                                            )
                                                                            !!}


                                                                                <span class="checkmark"></span>
                                                                            </label>

                                                                        @else

                                                                            <label class="checkbox-container" for="cb-{{ $role->id }}-{{ $permission->id }}">
                                                                                {!!
                                                                                Form::checkbox(
                                                                                    "roles[{$role->id}][]",
                                                                                    $permission->id,
                                                                                    $role->hasOnePermission($permission->id),
                                                                                    [
                                                                                        'id' => "cb-{$role->id}-{$permission->id}"
                                                                                    ]
                                                                                )
                                                                                !!}
                                                                                <span class="checkmark"></span>
                                                                            </label>

                                                                        @endif

                                                                    </td>
                                                                @else
                                                                    {!!
                                                                            Form::checkbox(
                                                                                "roles[{$role->id}][]",
                                                                                $permission->id,
                                                                                $role->id == 6 ? true: $role->hasOnePermission($permission->id),
                                                                                [
                                                                                    'id' => "cb-{$role->id}-{$permission->id}",
                                                                                    'style' => 'display: none;'
                                                                                ]
                                                                            )
                                                                            !!}
                                                                @endif
                                                            @endforeach
                                                        </tr>
                                                    @endforeach
                                                @endif
                                                </tbody>
                                                <thead>
                                                <tr>
                                                    <th>@lang('app.name')</th>
                                                    @foreach ($roles as $role)
                                                        @if( !in_array($role->id, [1,6]) )
                                                            <th>{{ $role->name }}</th>
                                                        @endif
                                                    @endforeach
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>

                                        @foreach ($hidden as $permission)
                                            @foreach ($roles as $role)
                                                {!!
                                                            Form::checkbox(
                                                                "roles[{$role->id}][]",
                                                                $permission->id,
                                                                $role->id == 6 ? true: $role->hasOnePermission($permission->id),
                                                                [
                                                                    'class' => 'custom-control-input',
                                                                    'id' => "cb-{$role->id}-{$permission->id}",
                                                                    'style' => 'display: none;'
                                                                ]
                                                            )
                                                            !!}
                                            @endforeach
                                        @endforeach

                                        <button type="submit" class="btn btn-primary">
                                            @lang('app.save_permissions')
                                        </button>

                                    </div>
                                @endif
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-primary">
                    @lang('app.save_permissions')
                </button>
            </div>
        </div>


        {!! Form::close() !!}

    </section>

@stop
