@extends('backend.layouts.app')

@section('page-title', trans('app.terminal'))
@section('page-heading', trans('app.terminal'))

@section('content')

<section class="content-header">
    @include('backend.partials.messages')
</section>

<section class="content">
    <div class="subuheader">
        <div class="row">
            <div class="col-md-6">
                <div class="heading">@lang('app.terminal')</div>
            </div>
            <div class="col-md-6 text-right">
                <a type="button" class="btn btn-primary text-uppercase text-white" data-toggle="modal"
                    data-target="#terminalAdd">
                    <i class="fa fa-plus-square"></i> @lang('app.add_new_terminal') </a>
                </a>
            </div>
        </div>
    </div>
    <div class="tableList">
        <table class="table bg-white">
            <thead>
                <tr>
                    <th scope="col">Username</th>
                    <th scope="col">Status</th>
                    <th scope="col">Tickets</th>
                    <th scope="col">Balance</th>
                    <th scope="col">Rating</th>
                    <th scope="col">TB</th>
                    <th scope="col">PB</th>
                    <th scope="col">DE</th>
                    <th scope="col">IF</th>
                    <th scope="col">HH</th>
                    <th scope="col">Refund</th>
                </tr>
            </thead>
            <tbody>
                @if (count($response['terminals'])>0)
                @foreach ($response['terminals'] as $item)
                <tr>
                    <td>
                        <a class="fw-bold" href="{{url('backend/terminal/details/'.encoded($item->id))}}">
                            {{$item->username}}
                        </a>
                    </td>
                    <td>
                        @if(!VanguardLTE\Http\Controllers\Web\Backend\Helper::is_online($item->last_online))
                        <small><i class="fa fa-circle text-red"></i> Offline</small>
                        @else
                        <small><i class="fa fa-circle text-green"></i> Online</small>
                        @endif
                    </td>
                    <td><a href="#" class="btn btn-xs btn-default detailsTicket" data-id="">Details</a></td>
                    <td>{{ number_format(floatval($item->balance), 2, '.', '') }}</td>
                    <td>{{$item->rating}}</td>
                    <td>{{ number_format(floatval($item->count_tournaments), 2, '.', '') }}</td>
                    <td>{{ number_format(floatval($item->count_progress), 2, '.', '') }}</td>
                    <td>{{ number_format(floatval($item->count_daily_entries), 2, '.', '') }}</td>
                    <td>{{ number_format(floatval($item->count_invite), 2, '.', '') }}</td>
                    <td>{{ number_format(floatval($item->count_happyhours), 2, '.', '') }}</td>
                    <td>{{ number_format(floatval($item->count_refunds), 2, '.', '') }}</td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="11">
                        <div class="noData">No terminal found!</div>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
        <div class="">
            {{ $response['terminals']->appends(request()->input())->links() }}
            <div class="clear"></div>
        </div>
    </div>
</section>
<!-- Modals -->
@include('backend.terminal.modals.terminal_add')
@stop

@section('scripts')
<script>
    $(document).on('click','.detailsTicket',function(e){
        $.ajax({
            type:'POST',
            url:'{{env('APP_URL')}}/meta-stone-details',
            data:{
                "name":gname
            },
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success:function(response){
                console.log(response);    
            }
        });
    });
</script>
@stop