@extends('backend.layouts.app')

@section('page-title', 'Support')
@section('page-heading', 'Support')

@section('content')

	<section class="content-header">
		@include('backend.partials.messages')
	</section>

	<section class="content">
		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title">Support</h3>
                @permission('tickets.add')
				<div class="pull-right box-tools">
					<a href="{{ route('backend.support.create') }}" class="btn btn-block btn-primary btn-sm">NEW TICKET</a>
				</div>
                @endpermission
			</div>
			<div class="box-body">
				<div class="table-responsive">
					<table class="table table-bordered table-striped">
						<thead>
						<tr>
							<th>ID</th>
							<th>Theme</th>
							<th>Client</th>
							<th>Status</th>
							<th>Date</th>
							<th>Action</th>
						</tr>
						</thead>
						<tbody>
						@if (count($tickets))
							@foreach ($tickets as $ticket)
								@include('backend.tickets.partials.row')
							@endforeach
						@else
							<tr><td colspan="6">No data available in table</td></tr>
						@endif
						</tbody>
						<thead>
						<tr>
							<th>ID</th>
							<th>Theme</th>
							<th>Client</th>
							<th>Status</th>
							<th>Date</th>
							<th>Action</th>
						</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>

	</section>


@stop

@section('scripts')

@stop
