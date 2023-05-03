<header class="main-header">
    <!-- Logo -->
    <a class="logo" href="{{ url('/') }}">
        <span class="logo-mini"><b>G</b></span>
        <span class="logo-lg"><b>{{ settings('app_name') }}</b></span>
    </a>

    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">@lang('app.toggle_navigation')</span>
        </a>

<div class="navbar-custom-menu">
   <ul class="nav navbar-nav">

@if (session()->exists('beforeUser'))
      <li class="dropdown tasks-menu">
         <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-repeat text-aqua"></i></a>
         <ul class="dropdown-menu">
            <li class="header"><b>{{ auth()->user()->username }}</b></li>
            <li>
               <ul class="menu">
                    <li><a href="{{ route('backend.user.back_login') }}"> Back Login</a></li>
               </ul>
            </li>
         </ul>
      </li>
@endif

    @php
        $open_shift = \VanguardLTE\OpenShift::where(['shop_id' => auth()->user()->shop_id, 'end_date' => NULL])->first();
    @endphp
	@if (Auth::user()->hasRole(['cashier']))
      <li class="dropdown tasks-menu">
         <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-time text-aqua"></i></a>
         <ul class="dropdown-menu">
            <li class="header"><b>{{ auth()->user()->username }}</b></li>
            <li>
               <ul class="menu">
                        @if($open_shift)
                           <li><a href="#" data-toggle="modal" data-target="#openShiftModal"> @lang('app.start_shift')</a></li>
                        @else
                           <li><a href="{{ route('backend.start_shift') }}"> @lang('app.start_shift')</a></li>
                        @endif
               </ul>
            </li>
         </ul>
      </li>
	@endif

    <li class="dropdown tasks-menu">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-off text-aqua"></i></a>
        <ul class="dropdown-menu">
            <li class="header"><b>{{ auth()->user()->username }}</b></li>
            <li>
                <ul class="menu">

                    @if (config('session.driver') == 'database')
                        <li><a href="{{ route('backend.profile.sessions') }}"> @lang('app.active_sessions')</a></li>
                    @endif



                    @if( auth()->user()->hasRole(['admin', 'agent']) )
                        <li><a href="{{ route('backend.credit.list') }}"> @if(auth()->user()->hasRole('admin')) @lang('app.edit_credit') @else @lang('app.buy_credit') @endif </a></li>
                    @endif

                    <li><a href="{{ route('backend.user.edit', auth()->user()->present()->id) }}"> @lang('app.my_profile')</a></li>
                    <li><a href="{{ route('backend.auth.logout') }}"> @lang('app.logout')</a></li>

                </ul>
            </li>
        </ul>
    </li>

   </ul>
</div>
    </nav>
</header>
<!-- Left side column. contains the logo and sidebar -->

@if (Auth::user()->hasRole(['cashier']))

    <div class="modal fade" id="openShiftModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('backend.start_shift') }}" method="GET" id="outForm">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">@lang('app.start_shift')</h4>
                    </div>
                    <div class="modal-body">
					    @if($open_shift)
                        @php
                            $money = $open_shift->users;
                            if($open_shift->end_date == NULL){
                                $money = $open_shift->get_money();
                            }

                            $payout = $open_shift->money_in > 0 ? ($open_shift->money_out / $open_shift->money_in) * 100 : 0;
                            $date = \Carbon\Carbon::now()->format(config('app.date_time_format'));

                        @endphp
                        <table class="table table-striped">
                            <tr><td>Start:</td><td> {{ $open_shift->start_date }}</td></tr>
                            <tr><td>Money: </td><td> {{ $money }}</td></tr>
                            <tr><td>In:</td><td> {{ $open_shift->money_in }}</td></tr>
                            <tr><td>Out: </td><td>{{ $open_shift->money_out }}</td></tr>
                            <tr><td>Total: </td><td>{{ $open_shift->money_in - $open_shift->money_out }}</td></tr>
                            <tr><td>Transfers:</td><td> {{ $open_shift->transfers }}</td></tr>
                            <tr><td>Pay Out:</td><td> {{ $payout }}</td></tr>
                        </table>
                        @else
                            <p>@lang('app.shift_not_opened')</p>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">@lang('app.close')</button>
                        <a href="{{ route('backend.start_shift.print') }}" target="_blank" class="btn btn-success">@lang('app.print')</a>
                        <button type="submit" class="btn btn-primary">@lang('app.ok')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @endif
