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
                <div class="heading">@lang('app.terminal') Details</div>
            </div>
            <div class="col-md-6 text-right">
                <a type="button" class="btn btn-primary text-uppercase text-white" data-toggle="modal"
                    data-target="#terminalAdd">
                    <i class="fa fa-plus-square"></i> @lang('app.add_new_terminal')</a>
            </div>
        </div>
    </div>
    <div class="mt-2">
        <div class="terminalsummary">
            <table class="table vm">
                <tr>
                    <td>
                        <div>
                            <p class="usrimg"><img src="/back/img/10.png" alt=""></p>
                            <p class="usrname">{{$response['terminal']->username}}</p>
                        </div>
                    </td>
                    <td>
                        <div>
                            <p>Balance</p>
                            <p>{{ number_format(floatval($response['terminal']->balance), 2, '.', '') }}</p>
                        </div>
                    </td>
                    <td>
                        <div>
                            <p>Total In</p>
                            <p>{{ number_format(floatval($response['terminal']->total_in), 2, '.', '') }}</p>
                        </div>
                    </td>
                    <td>
                        <div>
                            <p>Total Out</p>
                            <p>{{ number_format(floatval($response['terminal']->total_out), 2, '.', '') }}</p>
                        </div>
                    </td>
                    <td>
                        <div>
                            <p>Total</p>
                            <p>{{ number_format(floatval($response['terminal']->count_balance), 2, '.', '') }}</p>
                        </div>
                    </td>
                    @if (Auth::user()->hasRole('admin'))
                    <td>
                        <p>
                            <a type="button" class="btn btn-success text-uppercase fw-bold text-white"
                                data-toggle="modal" data-target="#addCredit">
                                <i class="fa fa-plus-square"></i> Add
                            </a>
                        </p>
                        <p>
                            <a type="button" class="btn btn-danger text-uppercase fw-bold text-white"
                                data-toggle="modal" data-target="#outCredit">
                                <i class="fa fa-minus-square"></i> Out
                            </a></p>
                    </td>
                    @endif
                </tr>
            </table>
        </div>
    </div>

    <div class="mt-1">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a id="details-tab" class="fw-bold" data-toggle="tab" href="#details" aria-expanded="false">
                        Edit @lang('app.terminal') </a>
                </li>
                <li>
                    <a id="authentication-tab" class="fw-bold" data-toggle="tab" href="#login-details"
                        aria-expanded="true">
                        Activity </a>
                </li>
                <li>
                    <a id="authentication-tab" class="fw-bold" data-toggle="tab" href="#ticketDetails"
                        aria-expanded="true">
                        Tickets </a>
                </li>
            </ul>

            <div class="tab-content" id="nav-tabContent">
                <!-- Edit user -->
                <div class="tab-pane active terminaldetails " id="details">
                    <form action="" method="POST">
                        @csrf
                        <table class="table vm">
                            <tr>
                                <td>Shops</td>
                                <td class="w300"><input type="text" name="name" disabled class="form-control"
                                        value="{{$response['shop']->name}}"></td>

                                <td class="text-right">Username</td>
                                <td class=""><input type="text" name="username" class="form-control w250"
                                        value="{{$response['terminal']->username}}"></td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>
                                    <select name="status" class="form-control w250">
                                        @foreach ($response['statuses'] as $status)
                                        <option value="{{$status}}"
                                            <?=($response['terminal']->status==$status)?'selected':''?>>{{$status}}
                                        </option>
                                        @endforeach
                                    </select>
                                <td class="text-right">Language</td>
                                <td>
                                    <select name="language" class="form-control w250">
                                        @foreach ($response['langs'] as $language=>$value)
                                        <option value="{{$language}}"
                                            <?=($response['terminal']->language==$language)?'selected':''?>>
                                            {{$language}}
                                        </option>
                                        @endforeach
                                    </select>
                            </tr>
                            <tr>
                                <td>Password</td>
                                <td colspan="3"><input type="text" name="password" class="form-control w200"
                                        value="{{$response['terminal']->password}}"></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td colspan="3">
                                    <button type="submit" class="btn btn-primary" id="update-details-btn">
                                        Update @lang('app.terminal') </button>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>

                <!-- Activity -->
                <div class="tab-pane" id="login-details">
                    @if (count($response['userActivity'])>0)
                    <table class="table text-center table-bordered vm">
                        <thead>
                            <td>Date</td>
                            <td>IP Address</td>
                            <td>Country</td>
                            <td>City</td>
                            <td>Device</td>
                            <td>OS</td>
                            <td>Browser</td>
                            <td class="text-left">User Agent</td>
                        </thead>
                        <tbody>
                            @foreach ($response['userActivity'] as $item)
                            <tr>
                                <td>{{$item->created_at}}</td>
                                <td>{{$item->ip_address}}</td>
                                <td>{{$item->country}}</td>
                                <td>{{$item->city}}</td>
                                <td>{{$item->device}}</td>
                                <td>{{$item->os}}</td>
                                <td>{{$item->browser}}</td>
                                <td>{{$item->user_agent}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <p class="noData">No activity from this user yet.</p>
                    @endif
                </div>

                <!-- Tickets -->
                <div class="tab-pane ticketDetails" id="ticketDetails">
                    @if (count($response['payTickets'])>0)
                    <table class="table text-center table-bordered vm">
                        <thead>
                            <td class="text-left">PIN</td>
                            <td class="w150">Amount</td>
                            <td class="w150">Status</td>
                            <td class="w150">Updated On</td>
                            <td class="w150">Created On</td>
                        </thead>
                        <tbody>
                            @foreach ($response['payTickets'] as $item)
                            <tr>
                                <td class="fw-bold text-left fs-20">{{$item->ticket_pin}}</td>
                                <td class="fs-20 fw-bold">
                                    {{ number_format(floatval($item->ticket_amount), 2, '.', '') }}</td>
                                <td>
                                    <span
                                        class="<?=($item->ticket_status==1)?'success':'pending'?>"><?=($item->ticket_status==1)?'Success':'Pending'?></span>
                                </td>
                                <td>{{$item->updated_at}}</td>
                                <td>{{$item->created_at}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <p class="noData">No tickets from this user yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    @include('backend.terminal.modals.add_credit')
    @include('backend.terminal.modals.out_credit')
    @include('backend.terminal.modals.terminal_add')
</section>
@stop

@section('scripts')
<script>
    var triggerTabList = [].slice.call(document.querySelectorAll('#myTab a'))
triggerTabList.forEach(function (triggerEl) {
  var tabTrigger = new bootstrap.Tab(triggerEl)

  triggerEl.addEventListener('click', function (event) {
    event.preventDefault()
    tabTrigger.show()
  })
})
</script>
@stop