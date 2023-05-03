@extends('backend.layouts.app')

@section('page-title', trans('app.jpg'))
@section('page-heading', trans('app.jpg'))

@section('content')

	<section class="content-header">
		@include('backend.partials.messages')
	</section>

	<section class="content">
		<form action="{{ route('backend.jpgame.global') }}" method="POST" class="pb-2 mb-3 border-bottom-light">
		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title">@lang('app.jpg')</h3>
				<div class="pull-right box-tools">
					<input type="hidden" value="<?= csrf_token() ?>" name="_token">
                    @if( auth()->user()->hasRole('admin') || auth()->user()->hasPermission('jpgame.edit') )
					<button class="btn btn-block btn-primary btn-sm" type="submit">@lang('app.change')</button>
                    @endif
				</div>
			</div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
					<thead>
					<tr>
						<th>@lang('app.id')</th>
						<th>@lang('app.name')</th>
						<th>@lang('app.balance')</th>
						<th>@lang('app.start_balance')</th>
						<th>@lang('app.trigger')</th>
						<th>@lang('app.percent')</th>
						<th>@lang('app.user')</th>
                        @if( auth()->user()->hasRole('admin') || auth()->user()->hasPermission('jpgame.edit') )
						<th>
							<label class="checkbox-container">
								<input type="checkbox" class="checkAll">
								<span class="checkmark"></span>
							</label>
						</th>
                        @endif
					</tr>
					</thead>
					<tbody>
					@if (count($jackpots))
						@foreach ($jackpots as $jackpot)
							@include('backend.jpg.partials.row')
						@endforeach
					@else
						<tr><td colspan="9">@lang('app.no_data')</td></tr>
					@endif
					</tbody>
					<thead>
					<tr>
						<th>@lang('app.id')</th>
						<th>@lang('app.name')</th>
						<th>@lang('app.balance')</th>
						<th>@lang('app.start_balance')</th>
						<th>@lang('app.trigger')</th>
						<th>@lang('app.percent')</th>
						<th>@lang('app.user')</th>
                        @if( auth()->user()->hasRole('admin') || auth()->user()->hasPermission('jpgame.edit') )
						<th>
							<label class="checkbox-container">
								<input type="checkbox" class="checkAll">
								<span class="checkmark"></span>
							</label>
						</th>
                        @endif
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
		$('#jackpots-table').dataTable();
	</script>
@stop
