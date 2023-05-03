<!DOCTYPE html>
<html lang="en">

<head>
<meta name="csrf-token" content="{{ csrf_token() }}">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>@yield('page-title') - {{ settings('app_name') }}</title>


    <!-- <link rel="stylesheet" href="/back/dist/css/AdminLTE.min.css"> -->

<link rel="stylesheet" href="/back/dist/css/skins/_all-skins.min.css">

    <link rel="stylesheet" href="/back/bower_components/morris.js/morris.css">
    <link rel="stylesheet" href="/back/bower_components/jvectormap/jquery-jvectormap.css">
    <link rel="stylesheet" href="/back/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="/back/bower_components/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="/back/bower_components/bootstrap-daterangepicker/daterangepicker.css">

    <link rel="stylesheet" href="/back/bower_components/bootstrap-daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="/back/bower_components/bootstrap-tagsinput/bootstrap-tagsinput.css">
    <link rel="stylesheet" href="/back/bower_components/croppie/croppie.css">
    <link rel="stylesheet" href="/back/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
    <link rel="stylesheet" href="/back/bower_components/select2/dist/css/select2.css">
    <link rel="stylesheet" href="/back/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">

     <!-- iCheck for checkboxes and radio inputs -->
     <link rel="stylesheet" href="/back/plugins/iCheck/all.css">

<!-- <link rel="stylesheet" href="/back/dist/css/new.css"> -->

<!-- Font Awesome -->
    <!-- <link rel="stylesheet" href="css/font-awesome.min.css"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Bootstrap core CSS -->
    <link href="/user/css/bootstrap.min.css" rel="stylesheet">
    <!-- Material Design Bootstrap -->
    <link href="/user/css/mdb.min.css" rel="stylesheet">
    <!-- Your custom styles (optional) -->
    <link href="/user/css/style.min.css" rel="stylesheet">
    <link href="/user/scss/style.css" rel="stylesheet">

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->

<style>
.swal2-close:focus{
    border: 0px;
    outline: none;
}
    .swal2-modal {
    position: relative;
    width: auto;
    margin: 10px;
}
@media (min-width: 768px){
    .swal2-modal {
        width: 600px;
        margin: 30px auto;
    }
}

    </style>
</head>

<body class="grey lighten-3">

    <!--Main Navigation-->
    <header>
    <div class="sidebar-fixed navbar navbar-dark">
            <div class="logo-w">
                <a class="logo" href="#" data-original-title="" title="">
                    <img src="/user/img/logo.png"></a>
            </div>

            <div class="logged-user-w">
                <div class="logged-user-i">
                    <div class="logged-user-name">{{ auth()->user()->username }}</div>
                    <div class="logged-user-role">{{ auth()->user()->role->name }}</div>
                    @if( Auth::user()->hasRole(['distributor']) )
                    <div class="logged-user-name">
                    Balance: {{ auth()->user()->balance }}
                    </div>
                    @endif
                    <div>
                    <a href="javascript:;" data-toggle="modal" data-target="#openChangeModal">
				<i class="fa fa-circle text-success"></i>
				@if(Auth::user()->shop) {{ Auth::user()->shop->name }} @else @lang('app.no_shop') @endif
			</a>
                    </div>
                </div>
            </div>
            <!-- Collapse -->
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- Links -->
            <div class="desktop-menu collapse navbar-collapse" id="navbarSupportedContent">

                <!-- Left -->

                <ul class="navbar-nav mr-auto">
                @permission('dashboard')
                    <li class="nav-item {{ Request::is('backend') ? 'active' : ''  }}">
                        <a href="{{ route('backend.dashboard') }}" class="nav-link">
                        <div class="icon-w">
                            <i class="fa fa-home"></i>
                        </div>
                        <span class="text-uppercase">@lang('app.dashboard')</span>
                        </a>
                    </li>
                    @endpermission

                    @permission('users.manage')
                    <li class="nav-item {{ Request::is('backend/user*') ? 'active' : ''  }}">
                        <a href="{{ route('backend.user.list') }}" class="nav-link">
                        <div class="icon-w">
                            <i class="fa fa-users"></i>
                            </div>
                            <span class="text-uppercase">@lang('app.users')</span>
                        </a>
                    </li>
                    @endpermission

                    @permission('users.tree')
                    <li class="nav-item {{ Request::is('backend/tree*') ? 'active' : ''  }}">
                        <a href="{{ route('backend.user.tree') }}" class="nav-link waves-effect">
                        <div class="icon-w">
                            <i class="fa fa-users"></i>
                        </div>
                            <span class="text-uppercase">{{ \VanguardLTE\Role::where('id', auth()->user()->role_id - 1)->first()->name }} @lang('app.tree')</span>
                        </a>
                    </li>
                    @endpermission

                    @permission('shops.manage')
                    <li class="nav-item {{ Request::is('backend/shops*') ? 'active' : ''  }}">
                        <a href="{{ route('backend.shop.list') }}" class="nav-link waves-effect">
                        <div class="icon-w">

                            <i class="fa fa-users"></i>
                        </div>
                            <span class="text-uppercase">@lang('app.shops')</span>
                        </a>
                    </li>
                    @endpermission

                    @permission('categories.manage')
                    @if( !(auth()->check() && auth()->user()->shop_id == 0 && auth()->user()->role_id < 6) )
                    <li class="{{ Request::is('backend/category*') ? 'nav-item active' : 'nav-item'  }}">
                        <a href="{{ route('backend.category.list') }}" class="nav-link waves-effect">
                            <i class="fa fa-server"></i>
                            <span>@lang('app.categories')</span>
                        </a>
                    </li>
                    @endif
                    @endpermission

                    @permission('refunds.manage')
                    @if( !(auth()->check() && auth()->user()->shop_id == 0) )
                    <li class="{{ Request::is('backend/refunds*') ? 'nav-item active' : 'nav-item'  }}">
                        <a href="{{ route('backend.refunds.list') }}" class="nav-link waves-effect">
                            <div class="icon-w">
                                <i class="fa fa-server"></i>
                            </div>
                            <span class="text-uppercase">@lang('app.refunds')</span>
                        </a>
                    </li>
                    @endif
                    @endpermission

                    @permission('happyhours.manage')
                    @if( !(auth()->check() && auth()->user()->shop_id == 0 && auth()->user()->role_id < 6) )
                    <li class="{{ Request::is('backend/happyhours*') ? 'nav-item active' : 'nav-item'  }}">
                        <a href="{{ route('backend.happyhour.list') }}" class="nav-link waves-effect">
                            <i class="fa fa-server"></i>
                            <span>@lang('app.happyhours')</span>
                        </a>
                    </li>
                    @endif
                    @endpermission

                    @php $jpgame_enabled = false; @endphp
                    @if($jpgame_enabled)
                    @permission('jpgame.manage')
                    @if( !(auth()->check() && auth()->user()->shop_id == 0 && auth()->user()->role_id < 6) )
                    <li class="{{ Request::is('backend/jpgame*') ? 'nav-item active' : 'nav-item'  }}">
                        <a href="{{ route('backend.jpgame.list') }}" class="nav-link waves-effect">
                            <i class="fa  fa-money"></i>
                            <span>@lang('app.jpg')</span>
                        </a>
                    </li>
                    @endif
                    @endpermission
                    @endif

                    @permission('pincodes.manage')
                    <li class="{{ Request::is('backend/pincodes*') ? 'nav-item active' : 'nav-item'  }}">
                        <a href="{{ route('backend.pincode.list') }}" class="nav-link waves-effect">
                            <i class="fa fa-server"></i>
                            <span>@lang('app.pincodes')</span>
                        </a>
                    </li>
                    @endpermission

                    @permission('games.manage')
                    @if( !(auth()->check() && auth()->user()->shop_id == 0 && auth()->user()->role_id < 6) )
                    <li class="nav-item  {{ (Request::is('backend/game') || Request::is('backend/game/*')) ? 'active' : ''  }}">
                        <a href="{{ route('backend.game.list') }}" class="nav-link waves-effect">
                            <i class="fa fa-gamepad"></i>
                            <span>@lang('app.games')</span>
                        </a>
                    </li>
                    @endif
                    @endpermission

                    @if (
                        Auth::user()->hasPermission('stats.live') ||
                        Auth::user()->hasPermission('stats.pay') ||
                        Auth::user()->hasPermission('stats.game') ||
                        Auth::user()->hasPermission('stats.bank') ||
                        Auth::user()->hasPermission('stats.shop') ||
                        Auth::user()->hasPermission('stats.shift')
                    )

                    <li class="has-sub-menu {{ Request::is('backend/live*') || Request::is('backend/statistics*') || Request::is('backend/game_stat*') || Request::is('backend/shop_stat') || Request::is('backend/shift_stat') || Request::is('backend/bank_stat') ? 'active' : '' }}">
                        <a href="#menu1sub1sub1" class="list-group-item list-head" data-toggle="collapse" aria-expanded="false">
                            <div class="icon-w">
                            <i class="fa fa-database"></i>
                            </div>
                            <span class="text-uppercase">Stats</span>

                        </a>
                        <div class="menu-down collapse" id="menu1sub1sub1">

                        @permission('stats.live')
                        <a  href="{{ route('backend.live_stat') }}" class="list-group-item" data-parent="#menu1sub1sub1">
                            @lang('app.live_stats')
                        </a>
                        @endpermission

                            @permission('stats.pay')
                            <a  href="{{ route('backend.transactions') }}" class="list-group-item" data-parent="#menu1sub1sub1">
                                @lang('app.statistics')
                            </a>
                            @endpermission

                            @permission('stats.game')
                            <a  href="{{ route('backend.game_stat') }}" class="list-group-item" data-parent="#menu1sub1sub1">
                                @lang('app.game_stats')
                            </a>
                            @endpermission

                            @permission('stats.bank')
                                <a  href="{{ route('backend.bank_stat') }}" class="list-group-item" data-parent="#menu1sub1sub1">
                                    @lang('app.bank_stats')
                                </a>
                            @endpermission

                            @permission('stats.shop')
                                <a href="{{ route('backend.shop_stat') }}" class="list-group-item" data-parent="#menu1sub1sub1">
                                    @lang('app.shop_stats')
                                </a>
                            @endpermission

                            @permission('stats.shift')
                                <a href="{{ route('backend.shift_stat') }}" class="list-group-item" data-parent="#menu1sub1sub1">
                                    @lang('app.shift_stats')
                                </a>
                            @endpermission

                                        </div>

                    </li>

                    @endif

                    @permission('users.activity')
                    <li class="nav-item {{ Request::is('backend/activity*') ? 'active' : ''  }}">
                        <a href="{{ route('backend.activity.index') }}" class="nav-link waves-effect">
                            <div class="icon-w">
                                <i class="fa fa-server"></i>
                            </div>
                            <span class="text-uppercase">@lang('app.activity_log')</span>
                        </a>
                    </li>
                    @endpermission

                    @permission('permissions.manage')
                    @if( !(auth()->check() && auth()->user()->shop_id == 0 && auth()->user()->role_id < 6) )
                    <li  class="nav-item {{ Request::is('backend/permission*') ? 'active' : '' }}">
                        <a href="{{ route('backend.permission.index') }}" class="nav-link waves-effect">
                            <i class="fa fa-circle-o"></i>
                            <span>@lang('app.permissions')</span>
                        </a>
                    </li>
                    @endif
                    @endpermission

                    @permission('settings.generator')
                    <li class="nav-item {{ Request::is('backend/generator*') ? 'active' : ''  }}">
                        <a href="{{ route('backend.settings.generator') }}" class="nav-link waves-effect">
                            <i class="fa fa-server"></i>
                            <span>@lang('app.api_generator')</span>
                        </a>
                    </li>
                    @endpermission

                    @permission('api.manage')
                    <li class="nav-item {{ Request::is('backend/api*') ? 'active' : ''  }}">
                        <a href="{{ route('backend.api.list') }}" class="nav-link waves-effect">
                            <i class="fa fa-circle-o"></i>
                            <span>@lang('app.api_keys')</span>
                        </a>
                    </li>
                    @endpermission

                    @permission('settings.general')
                    @if( !(auth()->check() && auth()->user()->shop_id == 0 && auth()->user()->role_id < 6) )
                    <li class="nav-item {{ Request::is('backend/settings') ? 'active' : ''  }}">
                        <a href="{{ route('backend.settings.general') }}" class="nav-link waves-effect">
                            <i class="fa fa-circle-o"></i>
                            <span>@lang('app.settings')</span>
                        </a>
                    </li>
                    @endif
                    @endpermission

                    @permission('helpers.manage')
                    <li class="nav-item {{ Request::is('backend/info*') ? 'active' : ''  }}">
                        <a href="{{ route('backend.info.list') }}" class="nav-link waves-effect">
                            <i class="fa fa-circle-o"></i>
                            <span>@lang('app.info')</span>
                        </a>
                    </li>
                    @endpermission


                    @permission('users.add')
                    @if(auth()->user()->hasRole('distributor'))
                    <li class="nav-item {{ Request::is('backend/user*') ? 'active' : ''  }}">
                        <a href="{{ route('backend.shop.create') }}" class="nav-link waves-effect">
                            <div class="icon-w">
                                <i class="fa fa-user-circle"></i>
                            </div>
                            <span class="font-weight-bold text-danger text-uppercase ">+New User</span>
                        </a>
                    </li>
                    @else
                    <li class="nav-item {{ Request::is('backend/user*') ? 'active' : ''  }}">
                        <a href="{{ route('backend.user.create') }}" class="nav-link waves-effect">
                            <div class="icon-w">
                                <i class="fa fa-user-circle"></i>
                            </div>
                            <span class="font-weight-bold text-danger text-uppercase">+New User</span>
                        </a>
                    </li>
                @endif
						@endpermission

                <li class="nav-item {{ Request::is('backend/user/*/profile') ? 'active' : ''  }}">
                    <a href="{{ route('backend.user.edit', auth()->user()->present()->id) }}" class="nav-link waves-effect">
                        <div class="icon-w">
                            <i class="fa fa-edit"></i>
                        </div>
                        <span class="text-uppercase"> @lang('app.my_profile')</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('backend.auth.logout') }}" class="nav-link waves-effect">
                        <div class="icon-w">
                            <i class="fa fa-sign-out"></i>
                        </div>
                        <span class="text-uppercase">@lang('app.logout')</span>
                    </a>
                </li>







                    <div class="side-menu-magic">
                        <p>© 2014 - 2021 Copyright</p>
                    </div>

                </ul>

            </div>
        </div>

        <!-- Sidebar -->



        </div>
        <!-- Sidebar -->

    </header>
    <!--Main Navigation-->

    <!--Main layout-->
    <main class="content-w pt-2">
        <div class="navbar navbar-expand-lg ">
            <div class="container-fluid">
            <ul class="breadcrumb" style="margin-bottom: 0px;">
                        <li class="breadcrumb-item"><a href="{{ route(auth()->user()->hasRole('distributor') ? 'backend.dashboard' :'backend.user.list') }}" data-original-title="" title="">Home</a>
                        </li>
                        <li class="breadcrumb-item"><a href="{{ route(auth()->user()->hasRole('distributor') ? 'backend.dashboard' :'backend.user.list') }}" data-original-title=""
                                title="">Refresh</a></li>
                        <div class="arrowcss hidden-sm-down">
                            <span style="float: right; font-size: 20px; margin-top: -15px; padding-top: 9px;"
                                class="hidden-xs-down ">
                                <a href="#" id="right_arrow" class="none_none btn2" data-original-title="" title=""
                                    style="display: inline;">
                                    <i class="os-icon os-icon-arrow-right2"></i>
                                </a>
                                <a href="#" id="left_arrow" class="none_none btn2" data-original-title="" title=""
                                    style="display: none;">
                                    <i class="os-icon os-icon-arrow-left"></i>
                                </a>
                            </span>
                        </div>
                    </ul>
            </div>
        </div>
        <div class="container-fluid mt-5">
        @yield('content')
        </div>
    </main>
    <!--Main layout-->
    <div class="modal fade" id="openChangeModal"  role="dialog" aria-hidden="true" >
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">@lang('app.shops')</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
            </div>
			<form action="{{ route('backend.profile.setshop') }}" method="POST">
				<div class="modal-body">
					<div class="form-group">
						{!! Form::select('shop_id',
                            (Auth::user()->hasRole(['admin','agent']) ? [0 => __('app.no_shop')] : [])
                            +
                            Auth::user()->shops_array(), Auth::user()->shop_id, ['class' => 'form-control select2', 'style' => 'width: 100%;']) !!}
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('app.close')</button>
					<button type="submit" class="btn btn-primary">@lang('app.change')</button>
				</div>
			</form>
        </div>
    </div>
</div>

<div class="hiddendiv common"></div>
    <!--Main layout-->

    <!-- SCRIPTS -->
    <!-- JQuery -->
    <script type="text/javascript" src="/user/js/jquery-3.4.1.min.js"></script>
    <!-- Bootstrap tooltips -->
    <script type="text/javascript" src="/user/js/popper.min.js"></script>
    <!-- Bootstrap core JavaScript -->
    <script type="text/javascript" src="/user/js/bootstrap.min.js"></script>
    <!-- MDB core JavaScript -->
    <script type="text/javascript" src="/user/js/mdb.min.js"></script>
    <!-- Initializations -->
    <script type="text/javascript">
        // Animations initialization
        new WOW().init();
    </script>

<script src="/back/bower_components/jquery-ui/jquery-ui.min.js"></script>
<script>
    var timezon = '{{ date_default_timezone_get() }}';
    $.widget.bridge('uibutton', $.ui.button);
</script>
<script src="/back/bower_components/raphael/raphael.min.js"></script>
<script src="/back/bower_components/morris.js/morris.min.js"></script>
<script src="/back/bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
<script src="/back/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="/back/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<script src="/back/bower_components/jquery-knob/dist/jquery.knob.min.js"></script>
<script src="/back/bower_components/moment/min/moment.min.js"></script>
<script src="/back/bower_components/moment/min/moment-timezone-with-data-1970-2030.min.js"></script>

<script src="/back/bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
<script src="/back/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="/back/bower_components/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
<script src="/back/bower_components/bootstrap-tagsinput/bootstrap-tagsinput.min.js"></script>
<script src="/back/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<script src="/back/dist/js/adminlte.js"></script>
    <!-- SweetAlert -->
    <link href="/back/bower_components/sweetalert2/bootstrap-4.css" rel="stylesheet">
<!-- <link rel="stylesheet" href="/back/dist/css/additional.css"> -->
<script src="/back/bower_components/sweetalert2/sweetalert2.js"></script>
<script src="/back/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<script src="/back/bower_components/fastclick/lib/fastclick.js"></script>
<script src="/back/bower_components/croppie/croppie.js"></script>
<script src="/back/bower_components/select2/dist/js/select2.js"></script>

<script src="/back/js/delete.handler.js"></script>
<script src="/back/bower_components/jquery-validation/jquery.validate.min.js"></script>
<script src="/back/bower_components/jquery-validation/additional-methods.min.js"></script>

<script src="/back/plugins/jquery-cookie/jquery.cookie.min.js"></script>
<!-- DataTables -->
<script src="/back/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="/back/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>

<!-- iCheck 1.0.1 -->
<script src="/back/plugins/iCheck/icheck.min.js"></script>

<!-- InputMask -->
<script src="/back/plugins/input-mask/jquery.inputmask.js"></script>
<script src="/back/plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
<script src="/back/plugins/input-mask/jquery.inputmask.extensions.js"></script>
<script src="/back/dist/js/demo.js"></script>


@yield('scripts')
</body>

</html>
