@extends('backend.layouts.app')

@section('page-title', trans('app.roles'))
@section('page-heading', trans('app.roles'))

@section('content')

<section class="content-header">
@include('backend.partials.messages')
</section>

	<section class="content">
    <div class="box box-primary">
    <div class="box-header with-border">
         <h3 class="box-title">@lang('app.roles')</h3>
			<div class="pull-right box-tools">
                <a href="{{ route('backend.role.create') }}" class="btn btn-block btn-primary btn-sm">@lang('app.add')</a>
			</div>
    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
						<thead>
						<tr>
							<th>@lang('app.slug')</th>
							<th>@lang('app.name')</th>
							<th>@lang('app.level')</th>
							<th>@lang('app.users_with_this_role')</th>
						</tr>
						</thead>
                        <tbody>
                        @if (count($roles))
                            @foreach ($roles as $role)
                                <tr>
                                    <td>{{ $role->slug }}</td>
                                    <td><a href="{{ route('backend.role.edit', $role->id) }}">{{ $role->name }}</a></td>
                                    <td>{{ $role->level }}</td>
                                    <td>{{ $role->users_count }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4"><em>@lang('app.no_records_found')</em></td>
                            </tr>
                        @endif
                        </tbody>
						<thead>
						<tr>
							<th>@lang('app.slug')</th>
							<th>@lang('app.name')</th>
							<th>@lang('app.level')</th>
							<th>@lang('app.users_with_this_role')</th>
						</tr>
						</thead>
                            </table>
                        </div>
                    </div>
	</div>
	
	</section>
	
@stop