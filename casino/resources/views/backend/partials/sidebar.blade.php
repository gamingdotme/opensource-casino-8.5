<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->

        <div class="user-panel">
            <div class="pull-left image">
				<img src="/back/img/12.png" class="img-circle">
				
                <!--<img src="/back/img/{{ auth()->user()->present()->role_id }}.png" class="img-circle">-->
            </div>
            <div class="pull-left info">
                <p>Balance: <span class="activeBalance">

                    @if( auth()->user()->hasRole(['cashier', 'manager']) )
                            @php
                                $shop = \VanguardLTE\Shop::find( auth()->user()->present()->shop_id );
                                echo $shop?number_format($shop->balance,2,".",""):0;
                            @endphp
                            @if( auth()->user()->present()->shop )
                                {{ auth()->user()->present()->shop->currency }}
                            @endif
                        @else
                            {{ number_format(auth()->user()->present()->balance,2,".","") }}
                            @if( auth()->user()->present()->shop )
                                {{ auth()->user()->present()->shop->currency }}
                            @endif
                        @endif
                </span>
                </p>

                <a href="javascript:;" data-toggle="modal" data-target="#openChangeModal">
                    <i class="fa fa-circle text-success"></i>
                    @if(auth()->user()->shop) {{ auth()->user()->shop->name }} @else @lang('app.no_shop') @endif
                </a>

            </div>
        </div>
        <!-- search form -->

        @if( auth()->user()->hasRole('admin') )
            <form action="{{ route('backend.search') }}" method="get" class="sidebar-form">
                <div class="input-group">
                    <input type="text" name="q" class="form-control" placeholder="@lang('app.search')">
                    <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat">
                  <i class="fa fa-search"></i>
                </button>
              </span>
                </div>
            </form>
        @endif

    <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">

            <li class="header">@lang('app.main_navigation')</li>

            @permission('dashboard')
            <li class="{{ Request::is('backend') ? 'active' : ''  }}">
                <a href="{{ route('backend.dashboard') }}">
                    <i class="fa fa-home"></i>
                    <span>@lang('app.dashboard')</span>
                </a>
            </li>
            @endpermission

            @permission('users.manage')
            <li class="{{ Request::is('backend/user*') ? 'active' : ''  }}">
                <a href="{{ route('backend.user.list') }}">	
                    <i class="fa fa-user"></i>
                    <span>@lang('app.users')</span>
                </a>
            </li>
            @endpermission
			
			@permission('users.manage')
            <li class="{{ Request::is('backend/terminal*') ? 'active' : ''  }}">
                <a href="{{ url('/backend/terminal') }}">
                   <i class='fa fa-desktop' style='color:red'></i>
                    <span>@lang('app.terminal')</span>
                </a>
            </li>
            @endpermission

            @if (
                auth()->user()->hasRole('admin') ||
                auth()->user()->hasPermission('settings.payment')
            )
            <li class="{{ Request::is('backend/withdraw*') ? 'active' : ''  }}">
                <a href="{{ url('/backend/withdraw') }}">
                   <i class='fa fa-money'></i>
                    <span>@lang('app.pyour_withdraw')</span>
                </a>
            </li>
			@endif

            @permission('users.manage')
            <li class="{{ Request::is('backend/atm*') ? 'active' : ''  }}">
                <a href="{{ url('/backend/atm') }}">
                    <i class="fa fa-credit-card"></i>
                    <span>@lang('app.atm')</span>
                </a>
            </li>
            @endpermission

            @permission('users.tree')
            <li class="{{ Request::is('backend/tree*') ? 'active' : ''  }}">
                <a href="{{ route('backend.user.tree') }}">
                    <i class="fa fa-tree"></i>
                    <span>{{ \VanguardLTE\Role::where('id', auth()->user()->role_id - 1)->first()->name }} @lang('app.tree')</span>
                </a>
            </li>
            @endpermission

            @permission('shops.manage')
            <li class="{{ Request::is('backend/shops*') ? 'active' : ''  }}">
                <a href="{{ route('backend.shop.list') }}">
                    <i class="fa fa-bank"></i>
                    <span>@lang('app.shops')</span>
                </a>
            </li>
            @endpermission

            @permission('tournaments.manage')
            @if( !(auth()->check() && auth()->user()->shop_id == 0 ) )
                <li class="{{ Request::is('backend/tournaments*') ? 'active' : ''  }}">
                    <a href="{{ route('backend.tournament.list') }}">
                        <i class="fa fa-trophy"></i>
                        <span>@lang('app.tournaments')</span>
                    </a>
                </li>
            @endif
            @endpermission

            @if( auth()->user()->hasRole('admin') )
                <li class="{{ Request::is('backend/category*') ? 'active' : ''  }}">
                    <a href="{{ route('backend.category.list') }}">
                        <i class="fa fa-bars"></i>
                        <span>@lang('app.categories')</span>
                    </a>
                </li>
            @endif


            @if (
                auth()->user()->hasPermission('happyhours.manage') ||
                auth()->user()->hasPermission('progress.manage') ||
                auth()->user()->hasPermission('invite.manage') ||
                auth()->user()->hasPermission('sms_bonuses.manage') ||
                auth()->user()->hasPermission('welcome_bonuses.manage') ||
                auth()->user()->hasPermission('wheelfortune.manage')
            )
                @if( !(auth()->check() && auth()->user()->shop_id == 0 && auth()->user()->role_id < 6 ) )
                    <li class="treeview {{ Request::is('backend/happyhours*') || Request::is('backend/progress*') || Request::is('backend/invite*') || Request::is('backend/welcome_bonuses*') || Request::is('backend/smsbonuses*') || Request::is('backend/wheelfortune*') ? 'active' : '' }}">
                        <a href="#">
                            <i class="fa fa-diamond"></i>
                            <span>Bonuses</span>
                            <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                        </a>
                        <ul class=" treeview-menu" id="bonuses-dropdown">

                            @permission('happyhours.manage')
                            <li class="{{ Request::is('backend/happyhours*') ? 'active' : ''  }}">
                                <a href="{{ route('backend.happyhour.list') }}">
                                    <i class="fa fa-circle-o"></i>
                                    <span>@lang('app.happyhours')</span>
                                </a>
                            </li>
                            @endpermission

                            @permission('progress.manage')
                            <li class="{{ Request::is('backend/progress*') ? 'active' : ''  }}">
                                <a href="{{ route('backend.progress.list') }}">
                                    <i class="fa fa-circle-o"></i>
                                    <span>@lang('app.progress')</span>
                                </a>
                            </li>
                            @endpermission

                            @permission('invite.manage')
                            <li class="{{ Request::is('backend/invite*') ? 'active' : ''  }}">
                                <a href="{{ route('backend.invites') }}">
                                    <i class="fa  fa-circle-o"></i>
                                    <span>@lang('app.invite')</span>
                                </a>
                            </li>
                            @endpermission

                            @permission('welcome_bonuses.manage')
                            <li class="{{ Request::is('backend/welcome_bonuses*') ? 'active' : ''  }}">
                                <a href="{{ route('backend.welcome_bonus.list') }}">
                                    <i class="fa  fa-circle-o"></i>
                                    <span>@lang('app.welcome_bonuses')</span>
                                </a>
                            </li>
                            @endpermission

                            @permission('sms_bonuses.manage')
                            <li class="{{ Request::is('backend/smsbonuses*') ? 'active' : ''  }}">
                                <a href="{{ route('backend.sms_bonus.list') }}">
                                    <i class="fa  fa-circle-o"></i>
                                    <span>@lang('app.sms_bonuses')</span>
                                </a>
                            </li>
                            @endpermission

                            @permission('wheelfortune.manage')
                            <li class="{{ Request::is('backend/wheelfortune*') ? 'active' : ''  }}">
                                <a href="{{ route('backend.wheelfortune') }}">
                                    <i class="fa  fa-circle-o"></i>
                                    <span>@lang('app.wheelfortune')</span>
                                </a>
                            </li>
                            @endpermission

                        </ul>
                    </li>
                @endif
            @endif
						  


            @permission('jpgame.manage')
            @if( !(auth()->check() && auth()->user()->shop_id == 0 && auth()->user()->role_id < 6) )
                <li class="{{ Request::is('backend/jpgame*') ? 'active' : ''  }}">
                    <a href="{{ route('backend.jpgame.list') }}">
                        <i class="fa  fa-heartbeat"></i>
                        <span>@lang('app.jpg')</span>
                    </a>
                </li>
            @endif
            @endpermission



            @permission('pincodes.manage')
            @if( !(auth()->check() && auth()->user()->shop_id == 0 ) )
                <li class="{{ Request::is('backend/pincodes*') ? 'active' : ''  }}">
                    <a href="{{ route('backend.pincode.list') }}">
                        <i class="fa fa-qrcode"></i>
                        <span>@lang('app.pincodes')</span>
                    </a>
                </li>
            @endif
            @endpermission

            @permission('games.manage')
            @if( !(auth()->check() && auth()->user()->shop_id == 0 && auth()->user()->role_id < 6 ) )
                <li class="{{ (Request::is('backend/game') || Request::is('backend/game/*')) ? 'active' : ''  }}">
                    <a href="{{ route('backend.game.list') }}">
                        <i class="fa fa-gamepad"></i>
                        <span>@lang('app.games')</span>
                    </a>
                </li>
            @endif
            @endpermission

            @if (
                auth()->user()->hasPermission('stats.pay') ||
                auth()->user()->hasPermission('stats.game') ||
                auth()->user()->hasPermission('stats.shift')
            )

                <li class="treeview {{ Request::is('backend/transactions*') || Request::is('backend/game_stat*') || Request::is('backend/shift_stat') ? 'active' : '' }}">
                    <a href="#">
                        <i class="fa fa-area-chart"></i>
                        <span>Stats</span>
                        <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                    </a>
                    <ul class=" treeview-menu" id="stats-dropdown">

                        @permission('stats.pay')
                        <li class="{{ Request::is('backend/transactions*') ? 'active' : ''  }}">
                            <a  href="{{ route('backend.transactions') }}">
                                <i class="fa fa-circle-o"></i>
                                @lang('app.statistics')
                            </a>
                        </li>
                        @endpermission

                        @permission('stats.game')
                        <li class="{{ Request::is('backend/game_stat') ? 'active' : ''  }}">
                            <a  href="{{ route('backend.game_stat') }}">
                                <i class="fa fa-circle-o"></i>
                                @lang('app.game_stats')
                            </a>
                        </li>
                        @endpermission

                        @permission('stats.shift')
                        <li class="{{ Request::is('backend/shift_stat') ? 'active' : ''  }}">
                            <a href="{{ route('backend.shift_stat') }}">
                                <i class="fa fa-circle-o"></i>
                                @lang('app.shift_stats')
                            </a>
                        </li>
                        @endpermission
                    </ul>
                </li>

            @endif

            @if (
                auth()->user()->hasPermission('activity.system') ||
                auth()->user()->hasPermission('activity.user')
            )
                <li class="treeview {{ Request::is('backend/activity*') ? 'active' : '' }}">
                    <a href="#">
                        <i class="fa fa-bar-chart"></i>
                        <span>@lang('app.activity_log')</span>
                        <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                    </a>
                    <ul class=" treeview-menu" id="stats-dropdown">
                        <li class="{{ Request::is('backend/activity') ? 'active' : ''  }}">
                            <a href="{{ route('backend.activity.index') }}">
                                <i class="fa fa-circle-o"></i>
                                <span>@lang('app.all')</span>
                            </a>
                        </li>
                        @permission('activity.system')
                        <li class="{{ Request::is('backend/activity/system') ? 'active' : ''  }}">
                            <a href="{{ route('backend.activity.system', 'system') }}">
                                <i class="fa fa-circle-o"></i>
                                <span>@lang('app.system_data')</span>
                            </a>
                        </li>
                        @endpermission
                        @permission('activity.user')
                        <li class="{{ Request::is('backend/activity/user') ? 'active' : ''  }}">
                            <a href="{{ route('backend.activity.user', 'user') }}">
                                <i class="fa fa-circle-o"></i>
                                <span>@lang('app.user_data')</span>
                            </a>
                        </li>
                        @endpermission
                    </ul>
                </li>
            @endif

            @if( auth()->user()->hasRole('admin') )
                <li  class="{{ Request::is('backend/permission*') ? 'active' : '' }}">
                    <a href="{{ route('backend.permission.index') }}">
                        <i class="fa fa-bell-slash"></i>
                        <span>@lang('app.permissions')</span>
                    </a>
                </li>
            @endif

            @permission('api.manage')
            @if( !(auth()->check() && auth()->user()->shop_id == 0 ) )
                <li class="{{ Request::is('backend/api*') ? 'active' : ''  }}">
                    <a href="{{ route('backend.api.list') }}">
                        <i class="fa fa-key"></i>
                        <span>@lang('app.api_keys')</span>
                    </a>
                </li>
            @endif
            @endpermission

            @if (
                auth()->user()->hasRole('admin')
            )

                <li class="treeview {{ Request::is('backend/info*') || Request::is('backend/articles*') ||
                    Request::is('backend/rules*') || Request::is('backend/faq*') ? 'active' : '' }}">
                    <a href="#">
                        <i class="fa fa-comments-o"></i>
                        <span>@lang('app.pages')</span>
                        <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                    </a>
                    <ul class=" treeview-menu" id="stats-dropdown">

                        <li class="{{ Request::is('backend/info*') ? 'active' : ''  }}">
                            <a href="{{ route('backend.info.list') }}">
                                <i class="fa fa-circle-o"></i>
                                <span>@lang('app.info')</span>
                            </a>
                        </li>
                        <li class="{{ Request::is('backend/articles*') ? 'active' : ''  }}">
                            <a href="{{ route('backend.article.list') }}">
                                <i class="fa fa-circle-o"></i>
                                <span>@lang('app.articles')</span>
                            </a>
                        </li>
                        <li class="{{ Request::is('backend/rules*') ? 'active' : ''  }}">
                            <a href="{{ route('backend.rule.list') }}">
                                <i class="fa fa-circle-o"></i>
                                <span>@lang('app.rules')</span>
                            </a>
                        </li>
                        <li class="{{ Request::is('backend/faq*') ? 'active' : ''  }}">
                            <a href="{{ route('backend.faq.list') }}">
                                <i class="fa fa-circle-o"></i>
                                <span>@lang('app.faqs')</span>
                            </a>
                        </li>
                    </ul>
                </li>

            @endif

            @if( auth()->user()->hasRole('admin'))
                <li  class="{{ Request::is('backend/sms_mailings*') ? 'active' : '' }}">
                    <a href="{{ route('backend.sms_mailing.list') }}">
                        <i class="fa fa-commenting"></i>
                        <span>SMS Mailings</span>
                    </a>
                </li>
            @endif

            @permission('tickets.manage')
            <li class="{{ Request::is('backend/support*') ? 'active' : ''  }}">
                <a href="{{ route('backend.support.index') }}">
                    <i class="fa fa-support"></i>
                    <span>Support</span>
                    @if( auth()->user()->hasRole('admin') )
                        @if($count = \VanguardLTE\Ticket::where('status', 'awaiting')->count() )
                            <span class="pull-right-container">
                            <span class="label label-primary pull-right">{{ $count }}</span>
                        </span>
                        @endif
                    @else
                        @if($count = \VanguardLTE\Ticket::where(['status' => 'answered', 'user_id' => auth()->user()->id])->count() )
                            <span class="pull-right-container">
                            <span class="label label-primary pull-right">{{ $count }}</span>
                        </span>
                        @endif
                    @endif
                </a>
            </li>
            @endpermission

            @if( auth()->user()->hasRole('admin'))
                <li class="{{ Request::is('backend/banks*') ? 'active' : ''  }}">
                    <a href="{{ route('backend.banks') }}">
                        <i class="fa fa-refresh"></i>
                        <span>@lang('app.banks')</span>
                    </a>
                </li>
            @endif

            @if( auth()->user()->hasRole('admin'))
                <li class="{{ Request::is('backend/securities*') ? 'active' : ''  }}">
                    <a href="{{ route('backend.securities') }}">
                        <i class="fa  fa-user-secret"></i>
                        <span>@lang('app.security')</span>
                    </a>
                </li>
            @endif

            @if (
                auth()->user()->hasRole('admin') ||
                auth()->user()->hasPermission('settings.payment')
            )
                <li class="treeview {{
    Request::is('backend/settings/general') || Request::is('backend/settings/securities') ||
    Request::is('backend/settings/sms') || Request::is('backend/settings/payment') ||
    Request::is('backend/settings/banks') || Request::is('backend/settings/categories') ||
    Request::is('backend/settings/games') || Request::is('backend/settings/auth') ||
    Request::is('backend/settings/bonuses')
  ? 'active' : '' }}">
                    <a href="#">
                        <i class="fa fa-cog fa-spin fa-fw" style='font-size:16px;color:red'></i>
                        <span>@lang('app.settings')</span>
                        <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                    </a>
                    <ul class=" treeview-menu" id="stats-dropdown">

                        @if( auth()->user()->hasRole('admin') )
                            <li class="{{ Request::is('backend/settings/general') ? 'active' : ''  }}">
                                <a  href="{{ route('backend.settings.list', 'general') }}">
                                    <i class="fa fa-circle-o"></i>
                                    @lang('app.general')
                                </a>
                            </li>
                            <li class="{{ Request::is('backend/settings/securities') ? 'active' : ''  }}">
                                <a  href="{{ route('backend.settings.list', 'securities') }}">
                                    <i class="fa fa-circle-o"></i>
                                    @lang('app.securities')
                                </a>
                            </li>
                            <li class="{{ Request::is('backend/settings/sms') ? 'active' : ''  }}">
                                <a  href="{{ route('backend.settings.list', 'sms') }}">
                                    <i class="fa fa-circle-o"></i>
                                    @lang('app.sms')
                                </a>
                            </li>
                        @endif

                        @permission('settings.payment')
                        <li class="{{ Request::is('backend/settings/payment') ? 'active' : ''  }}">
                            <a  href="{{ route('backend.settings.list', 'payment') }}">
                                <i class="fa fa-circle-o"></i>
                                @lang('app.payment')
                            </a>
                        </li>
                        @endpermission


                        @if( auth()->user()->hasRole('admin') )
                            <li class="{{ Request::is('backend/settings/banks') ? 'active' : ''  }}">
                                <a  href="{{ route('backend.settings.list', 'banks') }}">
                                    <i class="fa fa-circle-o"></i>
                                    @lang('app.banks')
                                </a>
                            </li>
                            <li class="{{ Request::is('backend/settings/categories') ? 'active' : ''  }}">
                                <a  href="{{ route('backend.settings.list', 'categories') }}">
                                    <i class="fa fa-circle-o"></i>
                                    @lang('app.categories')
                                </a>
                            </li>
                            <li class="{{ Request::is('backend/settings/games') ? 'active' : ''  }}">
                                <a  href="{{ route('backend.settings.list', 'games') }}">
                                    <i class="fa fa-circle-o"></i>
                                    @lang('app.games')
                                </a>
                            </li>
                            <li class="{{ Request::is('backend/settings/auth') ? 'active' : ''  }}">
                                <a  href="{{ route('backend.settings.list', 'auth') }}">
                                    <i class="fa fa-circle-o"></i>
                                    @lang('app.auth')
                                </a>
                            </li>
                        @endif


                    </ul>
                </li>
            @endif



        </ul>
		
		

           		</br>
								
				@if( auth()->user()->hasRole('agent') )
                <li  class="{{ Request::is('backend/user/create*') ? 'active' : '' }}">
                    <a href="/backend/user/create">
                        <i class="fa fa-plus" style="font-size:18px;color:#fb7f83"></i>
                        <span>&nbsp;&nbsp;&nbsp;&nbsp; New Operator</span>
                    </a>
                </li>
            @endif
			
			@if( auth()->user()->hasRole('distributor') )
                <li  class="{{ Request::is('backend/user/create*') ? 'active' : '' }}">
                    <a href="/backend/user/create">
                        <i class="fa fa-plus" style="font-size:18px;color:#fb7f83"></i>
                        <span>&nbsp;&nbsp;&nbsp;&nbsp; New Manager</span>
                    </a>
                </li>
           </br>
            @endif
			
			@if( auth()->user()->hasRole('distributor') )
                <li  class="{{ Request::is('backend/shops/create*') ? 'active' : '' }}">
                    <a href="/backend/shops/create">
                        <i class="fa fa-plus" style="font-size:18px;color:#fb7f83"></i>
                        <span>&nbsp;&nbsp;&nbsp;&nbsp; New Shop</span>
                    </a>
                </li>
			</br>
            @endif
            
			@if( auth()->user()->hasRole('manager') )
                <li  class="{{ Request::is('backend/user/create*') ? 'active' : '' }}">
                    <a href="/backend/user/create">
                        <i class="fa fa-plus" style="font-size:18px;color:#fb7f83"></i>
                        <span>&nbsp;&nbsp;&nbsp;&nbsp; New Cashier</span>
                    </a>
                </li>
              </br>
            @endif
			
			@if( auth()->user()->hasRole('cashier') )
                <li  class="{{ Request::is('backend/user/create*') ? 'active' : '' }}">
                    <a href="/backend/user/create">
                        <i class="fa fa-plus" style="font-size:18px;color:#fb7f83"></i>
                        <span>&nbsp;&nbsp;&nbsp;&nbsp; New Player</span>
                    </a>
                </li>
              </br>
            @endif
			
</br>
        @if( auth()->user()->shop )
            @if( auth()->user()->shop->is_blocked )
                @permission('shops.unblock')
                <br>
                <a href="{{ route('backend.settings.shop_unblock') }}" class="btn btn-success"
                   style="color: #fff; margin: 0 auto; display: table;"
                   data-method="PUT"
                   data-confirm-title="@lang('app.please_confirm')"
                   data-confirm-text="@lang('app.are_you_sure_unblock_shop')"
                   data-confirm-delete="@lang('app.unblock')"
                > UnBlock Shop</a>
                @endpermission
            @else
                @permission('shops.block')
                <br>
                <a href="{{ route('backend.settings.shop_block') }}" class="btn btn-danger"
                   style="color: #fff; margin: 0 auto; display: table;"
                   data-method="PUT"
                   data-confirm-title="@lang('app.please_confirm')"
                   data-confirm-text="@lang('app.are_you_sure_block_shop')"
                   data-confirm-delete="@lang('app.block')"
                > Block Shop</a>
                @endpermission
            @endif
        @endif
 
        <ul>
            <li>
                <br>
                <a href="javascript:;">
                    <span id="date-part"></span>
                    <span id="time-part"></span>
                </a>
            </li>
        </ul>
		</br>
		@if( auth()->user()->hasRole('cashier') )
<a href="#" class="btn btn-success" style="color: #fff; margin: 0 auto; display: table;" data-toggle="modal" data-target="#openShiftModal"> @lang('app.start_shift')</a>
 @endif
    </section>
</aside>

<div class="modal fade" id="openChangeModal"  role="dialog" aria-hidden="true" >
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('backend.profile.setshop') }}" method="POST">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('app.shops')</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        {!! Form::select('shop_id',
                            (auth()->user()->hasRole(['admin','agent']) ? [0 => __('app.no_shop')] : [])
                            +
                            auth()->user()->shops_array(), auth()->user()->shop_id, ['class' => 'form-control select2', 'style' => 'width: 100%;']) !!}
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
