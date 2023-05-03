<!DOCTYPE html>
<html lang="en">

<head>

	<meta charset="utf-8">

	<title>@yield('page-title') - {{ settings('app_name') }}</title>

	<meta name="viewport" content="width=device-width">

	<link rel="icon" href="/frontend/Tropicoblack/img/favicon.png" >

	<link rel="stylesheet" href="/frontend/Tropicoblack/css/slick.css">
	<link rel="stylesheet" href="/frontend/Tropicoblack/css/styles.min.css">

	<script src="/frontend/Tropicoblack/js/jquery-3.4.1.min.js"></script>

	<link rel="stylesheet" type="text/css" href="/frontend/Tropicoblack/assets/vendors/google/css/roboto.css"/>
	<link rel="stylesheet" type="text/css" href="/frontend/Tropicoblack/assets/vendors/bootstrap/4.3.1/css/bootstrap.min.css"/>
	<link rel="stylesheet" type="text/css" href="/frontend/Tropicoblack/assets/vendors/swiper/4.5.0/css/swiper.min.css"/>
	<link rel="stylesheet" type="text/css" href="/frontend/Tropicoblack/assets/vendors/odometer/0.4.6/css/odometer-theme-default.css"/>
	<link rel="stylesheet" type="text/css" href="/frontend/Tropicoblack/assets/vendors/bootstrap-select/1.13.9/css/bootstrap-select.min.css"/>
	<link rel="stylesheet" type="text/css" href="/frontend/Tropicoblack/assets/vendors/flag-icon-css/3.3.0/css/flag-icon.min.css"/>
	<link rel="stylesheet" type="text/css" href="/frontend/Tropicoblack/assets/vendors/flaticon/flaticon.css"/>
	<link rel="stylesheet" type="text/css" href="/frontend/Tropicoblack/assets/vendors/fontawesome/5.10.1/css/all.min.css"/>


	<link rel="stylesheet" type="text/css" href="/frontend/Tropicoblack/css/alertify.min.css"/>
	<link rel="stylesheet" type="text/css" href="/frontend/Tropicoblack/css/pisoglentis.css"/>
	<link rel="stylesheet" type="text/css" href="/frontend/Tropicoblack/assets/css/main-black.css"/>


</head>

<body data-type="public">
	@yield('content')
<!-- <div class="fullpage" style="height: 100220vh; background-color: black; position:absolute;
    top:0;
    bottom:0;
    left:0;
    right:0;
    z-index: 2222;
    overflow:hidden;"></div> -->
<!-- public, private -->
<!-- SIDEBAR -->
    <nav id="sidebar" class="">
    <div id="dismiss">
        <i class="fas fa-angle-left"></i>
    </div>

    <div class="sidebar-header">
        <h3 class="text-center">

                            <img src="/frontend/Tropicoblack/assets/images/ui/tropicana88_logo-bg.png" alt="logo" class="sidebar-logo"/>


        </h3>
        <div class="container text-center">
            <select class="selectpicker" id="lang" data-width="fit">
                <option  selected
                         data-content='<span class="flag-icon flag-icon-gb"></span>'></option>
                <option  data-content='<span class="flag-icon flag-icon-it"></span>'></option>
                <option  data-content='<span class="flag-icon flag-icon-es"></span>'></option>
            </select>
        </div>
    </div>

    <ul class="list-unstyled components">


						<li data-category="all">
								<a href="javascript: void(0);">
										All
								</a>
						</li>
						@if ($categories && count($categories))
							@foreach($categories AS $index=>$category)

							<li  data-category="{{$category->id}}">
									<a href="javascript: void(0);">
											{{ $category->title }}
									</a>
							</li>

							@endforeach
						@endif




    </ul>
















    <ul class="list-unstyled buttonsGroup-public">
        <li>
            <a href="/logout" class="primaryBtn">
                <i class="fas fa-user-times"></i>
                Logout
            </a>
        </li>
    </ul>
</nav>
<!-- END SIDEBAR -->

<!-- CONTENT -->
<div id="content">
    <!-- NAVBAR -->
    <nav class="navbar position-absolute w-100 p-0" id="slide222">
    <div class="row w-100 m-0">
        <div class="col-2 col-sm-2 text-sm-left text-center">
            <button type="button" id="sidebarCollapse" class="btn primary-btn float-left mt-1">
                <i class="fas fa-align-justify"></i>
            </button>
        </div>
        <div class="d-none d-md-block col-sm-4 p-0 text-center text-sm-left">
            <a href="" class="pl-2">
                                    <img src="/frontend/Tropicoblack/assets/images/ui/tropicana88_logo-bg.png" alt="logo"
                         class="navbar-logo"/>
                                </a>

        </div>














        <div class="col-10 col-sm-6 text-sm-right text-center">
            <div class="btn primary-btn d-inline-block mt-1 user-info">
							<span class="custom-input-group-icon text-left" style="margin-right: 10px;" data-toggle="tooltip"
                                  title="Username">
								<i class="fas fa-user"></i>
								<span id="user-username">{{ Auth::user()->username }}</span>
							</span>
                <span class="custom-input-group-icon text-left" style="margin: 10px;" id="hideMsg" data-toggle="tooltip"
                      title="Balance">
								<i class="fas fa-wallet"></i>
								<span id="user-balance">{{ number_format(Auth::user()->balance, 2,".",",") }} @if( auth()->user()->present()->shop ){{ auth()->user()->present()->shop->currency }}@endif</span>
							</span>
                <input type="hidden" id="hiddenScore" name="hiddenScore" value="0">

                <span class="custom-input-group-icon text-left" style="margin-left: 10px;" data-toggle="tooltip"
                      title="Bonus">
								<i class="fas fa-gift"></i>
								<span id="user-cashback">{{ number_format(Auth::user()->count_return, 2,".",",") }} @if( auth()->user()->present()->shop ){{ auth()->user()->present()->shop->currency }}@endif</span>
							</span>

                            </div>
        </div>

            <div class="d-none d-md-block col-sm-8 p-0 text-center text-sm-left">
            </div>
            <div class="col-12 col-sm-4 text-right bonuslvl d-none">

            </div>
    </div>
</nav>
<!-- END NAVBAR -->

    <div class="row m-0 position-relative">
        <!-- JACKPOT -->
        <div class="row w-100" id="jackpots">
        <div class="col-lg-3 pb-1 my-auto"></div>
        <div class="col-4 col-lg-2 pb-1 my-auto">
            <div class="jackpot-container align-middle w-100 m-auto text-right">
                <img src="/frontend/Tropicoblack/assets/images/ui/jackpot-icon-1.png" alt="jackpot-icon"
                     class="jackpot-icon float-left"/>
                <div class="odometer jackpot-elem1"></div>
            </div>
        </div>
        <div class="col-4 col-lg-2 pb-1 my-auto">
            <div class="jackpot-container align-middle w-100 m-auto text-right">
                <img src="/frontend/Tropicoblack/assets/images/ui/jackpot-icon-2.png" alt="jackpot-icon"
                     class="jackpot-icon float-left"/>
                <div class="odometer jackpot-elem2"></div>
            </div>
        </div>
        <div class="col-4 col-lg-2 pb-1 my-auto">
            <div class="jackpot-container align-middle w-100 m-auto text-right">
                <img src="/frontend/Tropicoblack/assets/images/ui/jackpot-icon-3.png" alt="jackpot-icon"
                     class="jackpot-icon float-left"/>
                <div class="odometer jackpot-elem3"></div>
            </div>
        </div>
        <div class="col-lg-3 pb-1 my-auto"></div>
    </div>
    <!-- END JACKPOT -->
        <!-- BANNERS -->
        <div id="banners"></div>
        <!-- END BANNERS -->
    </div>

    <!-- MAIN MENU -->
    <div class="sticky-top main-bg" style="position: sticky;top: 0;z-index: 1020;">
    <div id="main-menu">
        <nav class="navbar navbar-expand p-0 scrollable-navbar">
            <div>
                <ul class="navbar-nav">
									<li class="nav-item" data-category="all">
											<a class="nav-link text-center" href="javascript: void(0);">
													All
											</a>
									</li>

									@php
									$companies = [
										'wazdan',
                                        'arcade',
                                        'netgame',
                                        'betsoft',
                                        'gdgames',
                                        'cq9',
                                        'playgt',
                                        'vision',
                                        'playngo',
										'ka-gaming',
										'mainama',
										'skywind',
										'pragmatic',
										'novomatic',
										'netent',
										'igrosoft',
										'greentube',
										'casino-technology',
										'aristocrat',
										'amatic',
										'playtech',
										'gamomat',
										'isoftbet',
										'egt'
									];
									@endphp

									@if ($categories && count($categories))
										@foreach($categories AS $index=>$category)
										@if (!in_array($category->href, $companies))
										<li class="nav-item" data-sv="{{$category->href}}" data-category="{{$category->id}}">
												<a class="nav-link text-center" href="javascript: void(0);">
														{{ $category->title }}
												</a>
										</li>
										@endif

										@endforeach
									@endif








                </ul>
            </div>
        </nav>
    </div>

    <div class="container-fluid mt-2">
        <div class="row m-0">
            <div class="col-xl-12 col-lg-9 col-md-0 mb-2 p-0">
							<nav class="navbar navbar-expand p-0 scrollable-navbar" id="vendors">
								<ul class="navbar-nav">
									@if ($categories && count($categories))
										@foreach($categories AS $index=>$category)
										@if (in_array($category->href, $companies))
										<li class="nav-item mr-2 ml-2 box active" data-category="{{$category->id}}">
											<a href="javascript: void(0);"><img style="filter:brightness(0) invert(1);" class="nav-item-img" src="/frontend/Tropicoblack/img/companies/{{$category->href}}.png"></a>
										</li>
										@endif

										@endforeach
									@endif

								</ul>
							</nav>
            </div>
           <!-- <div class="col-xl-2 col-lg-3 col-md-12 mb-2 p-0">
                <div id="searchbar" class="search-box">
                    <input id="search_input" type="text" name="search_input" placeholder=" " class="search-txt"/>
                    
                        <i class="fas fa-search"></i>
                    </a>
                </div>
            </div>-->
        </div>
    </div>
</div>


<script>

	var GLOBAL_GAMES_LIST = [


		@foreach ($games as $key=>$game)
		{
			"game_id":{{$game->id}},
			"launchUrl":"/game/{{$game['name']}}?api_exit=/",
			"providerId":"bomba",
			"categoryName":"{{\VanguardLTE\GameCategory::where('game_id',$game->original_id)->first()->category_id}}",
			"gameName":"{{$game['title']}}",
			"imageUrl":"{{ $game->name ? '/frontend/Tropicoblack/ico/' . $game->name . '.jpg' : '' }}",
			"data-src":"{{ $game->name ? '/frontend/Tropicoblack/ico/' . $game->name . '.jpg' : '' }}",
			"mobileGame": false
		},

		@endforeach

	];

                            var GLOBAL_BANNERS_LIST = {
            slide1: "/frontend/Tropicoblack/assets/images/slides/1.jpg",
            slide2: "/frontend/Tropicoblack/assets/images/slides/2.jpg",
            slide3: "/frontend/Tropicoblack/assets/images/slides/3b.jpg",
            slide4: "/frontend/Tropicoblack/assets/images/slides/4.jpg",
            slide5: "/frontend/Tropicoblack/assets/images/slides/5.jpg",
            slide6: "/frontend/Tropicoblack/assets/images/slides/6.jpg",
            slide7: "/frontend/Tropicoblack/assets/images/slides/7.jpg",
            slide8: "/frontend/Tropicoblack/assets/images/slides/8.jpg",
            slide9: "/frontend/Tropicoblack/assets/images/slides/9.jpg",
            slide10: "/frontend/Tropicoblack/assets/images/slides/10.jpg",
            slide11: "/frontend/Tropicoblack/assets/images/slides/11.jpg"
        };


</script><!-- END MAIN MENU -->

    <div class="container-fluid p-0 mt-2 mb-5">
        <div id="mainContent" class="text-center row m-0"></div>
    </div>

    <!-- FOOTER-->
    <footer>
    <div class="row m-0">
        <div class="col-12 col-md-3 p-0 border-container">
            <h5 class="text-center p-2">&nbsp;</h5>
            <div class="container text-center">
                <img src="/frontend/Tropicoblack/assets/images/ui/devices.png" alt="" class="w-50 mb-1"/>
                <br/>
                <h6>Now available on all devices</h6>
                <h6>Desktop - Tablet - Mobile</h6>
            </div>
            <div class="container">
                <p class="text-justify">
                    For best performance please make sure you have the latest version of Google Chrome.
                </p>
            </div>
            <div class="container">
                <p class="text-justify">
                    All graphic material contained on the website including logos, text, sound, videos, design, photographs are subject to copyright and may not be distributed without explicit written consent of the Casino.
                </p>
            </div>
        </div>
        <div class="col-12 col-md-3 p-0 border-container">
            <h5 class="text-center p-2">Play Safe</h5>
            <div class="container text-center">
                <img src="/frontend/Tropicoblack/assets/images/ui/secure_website.png" alt="" class="w-50 mb-1"/>
                <p class="text-justify">
                    The the Online Casino has a legal permission to conduct online gambling on the basis of the international License.
                    <br/>
                    We understand that ensuring the fair game is one of the most important conditions for the casino operation.
                    <br/>
                    Therefore, we offer you only games from reliable and long proven certified suppliers.
                    <br/>
                    We approach to Responsible Gaming and Player Protection.
                </p>
            </div>
        </div>
        <div class="col-12 col-md-2 p-0 border-container">
            <h5 class="text-center p-2">Game Providers</h5>
            <div class="container">
                <div class="row m-0">
                    <div class="col-6 col-md-12 p-0 text-center">
                        <a href="#">
                            <img src="/frontend/Tropicoblack/assets/images/vendors/shadow/q1x2gaming.png" alt="q1x2gaming"
                                 style="max-width: 100px;"/>
                        </a>
                    </div>
                    <div class="col-6 col-md-12 p-0 text-center">
                        <a href="#">
                            <img src="/frontend/Tropicoblack/assets/images/vendors/shadow/amatic.png" alt="amatic"
                                 style="max-width: 100px;"/>
                        </a>
                    </div>
                    <div class="col-6 col-md-12 p-0 text-center">
                        <a href="#">
                            <img src="/frontend/Tropicoblack/assets/images/vendors/shadow/bomba.png" alt="bomba"
                                 style="max-width: 100px;"/>
                        </a>
                    </div>
                    <div class="col-6 col-md-12 p-0 text-center">
                        <a href="#">
                            <img src="/frontend/Tropicoblack/assets/images/vendors/shadow/egt.png" alt="egt" style="max-width: 100px;"/>
                        </a>
                    </div>
                    <div class="col-6 col-md-12 p-0 text-center">
                        <a href="#">
                            <img src="/frontend/Tropicoblack/assets/images/vendors/shadow/netent.png" alt="netent"
                                 style="max-width: 100px;"/>
                        </a>
                    </div>
                    <div class="col-6 col-md-12 p-0 text-center">
                        <a href="#">
                            <img src="/frontend/Tropicoblack/assets/images/vendors/shadow/novomatic.png" alt="novomatic"
                                 style="max-width: 100px;"/>
                        </a>
                    </div>
                    <div class="col-6 col-md-12 p-0 text-center">
                        <a href="#">
                            <img src="/frontend/Tropicoblack/assets/images/vendors/shadow/spin2win.png" alt="spin2win"
                                 style="max-width: 100px;"/>
                        </a>
                    </div>
                    <div class="col-6 col-md-12 p-0 text-center">
                        <a href="#">
                            <img src="/frontend/Tropicoblack/assets/images/vendors/shadow/wazdan.png" alt="wazdan"
                                 style="max-width: 100px;"/>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-2 p-0 border-container">
            <h5 class="text-center p-2">
               Social Media
            </h5>
            <div class="container text-center">
                <h6>Follow Us On ...</h6>
            </div>
            <div class="row m-0">
                <div class="col-6 p-0 text-center">
                    <a href="#">
                        <img src="/frontend/Tropicoblack/assets/images/ui/facebook.png" alt="facebook" class="p-2"
                             style="max-width: 64px;"/>
                    </a>
                </div>
                <div class="col-6 p-0 text-center">
                    <a href="#">
                        <img src="/frontend/Tropicoblack/assets/images/ui/twitter.png" alt="twitter" class="p-2"
                             style="max-width: 64px;"/>
                    </a>
                </div>
                <div class="col-6 p-0 text-center">
                    <a href="#">
                        <img src="/frontend/Tropicoblack/assets/images/ui/youtube.png" alt="youtube" class="p-2"
                             style="max-width: 64px;"/>
                    </a>
                </div>
                <div class="col-6 p-0 text-center">
                    <a href="#">
                        <img src="/frontend/Tropicoblack/assets/images/ui/instagram.png" alt="instagram" class="p-2"
                             style="max-width: 64px;"/>
                    </a>
                </div>
                <div class="col-6 p-0 text-center">
                    <a href="#">
                        <img src="/frontend/Tropicoblack/assets/images/ui/linkedin.png" alt="linkedin" class="p-2"
                             style="max-width: 64px;"/>
                    </a>
                </div>
                <div class="col-6 p-0 text-center">
                    <a href="#">
                        <img src="/frontend/Tropicoblack/assets/images/ui/pinterest.png" alt="pinterest" class="p-2"
                             style="max-width: 64px;"/>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-2 p-0 border-container">
            <h5 class="text-center p-2">Contact Us</h5>
            <div class="container text-center">
                <h6>24/7 Support</h6>
                <img src="/frontend/Tropicoblack/assets/images/ui/18plus.png" alt="" class="mb-1" style="max-width: 64px;"/>
            </div>
            <div class="container mt-5 text-center">
                <h6>
                    Gambling can be addictive.
                    <br/>
                    Please play responsibly.
                </h6>
            </div>
            <div class="container mt-5 text-center">
                <h6>
                    © 2020 the Casino
                    <br/>
                    All Rights Reserved
                </h6>
            </div>
        </div>
    </div>

<!--   <div class="ft-cookies" id="div-cookies">
        <p>
            We use technical and analytics cookies to ensure that we give you the best experience on our website.
            &nbsp;&nbsp;&nbsp;&nbsp;
            <a class="AcceptCookiesBtn btn primary-btn btn-lg text-uppercase" href="javascript: void(0);"
               onclick="javascript:GLOBALOBJ.methods.acceptCookies();">Accept</a>
        </p>
    </div>-->
</footer>
<!-- END FOOTER-->
</div>
<!-- END CONTENT -->

<!-- HELPERS -->
<!-- BACK TO TOP -->
<a id="back-to-top" href="javascript: void(0);" class="btn primary-btn btn-lg back-to-top" role="button"
   title="Back to Top" data-toggle="tooltip" data-placement="left">
    <span class="fas fa-angle-up"></span>
</a>
<!-- END BACK TO TOP -->
<!-- OVERLAY -->
<div class="overlay"></div>
<!-- END OVERLAY -->
<!-- GAME WINDOW MODAL -->
<div class="modal fade" id="game-window-modal" tabindex="-1" role="dialog" aria-labelledby="game-window-modal-title"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document" id="game-window-modal-dialog">
        <div class="modal-content" id="game-modal-content">
            <div class="modal-header">
                <a style="z-index: 20;" href="javascript: void(0);" id="game-window-modal-fullscreen"
                   class="modal-button" aria-label="Open in Fullscreen" rel="fullscreen">
                    <i class="fas fa-external-link-alt"></i>
                </a>
                <h5 class="modal-title" id="game-window-modal-title"></h5>

                <!-- JACKPOT -->
                    <div class="row w-100" id="jackpots2">
                        <div class="col-lg-3 pb-1 my-auto">
                        </div>
                        <div class="col-4 col-lg-2 pb-1 my-auto">
                            <div class="jackpot-container align-middle w-100 m-auto text-right">
                                <img src="/frontend/Tropicoblack/assets/images/ui/jackpot-icon-1.png" alt="jackpot-icon"
                                     class="jackpot-icon float-left"/>
                                <div class="odometer pisoglentis jackpot-elem1"></div>
                            </div>
                        </div>
                        <div class="col-4 col-lg-2 pb-1 my-auto">
                            <div class="jackpot-container align-middle w-100 m-auto text-right">
                                <img src="/frontend/Tropicoblack/assets/images/ui/jackpot-icon-2.png" alt="jackpot-icon"
                                     class="jackpot-icon float-left"/>
                                <div class="odometer pisoglentis jackpot-elem2"></div>
                            </div>
                        </div>
                        <div class="col-4 col-lg-2 pb-1 my-auto">
                            <div class="jackpot-container align-middle w-100 m-auto text-right">
                                <img src="/frontend/Tropicoblack/assets/images/ui/jackpot-icon-3.png" alt="jackpot-icon"
                                     class="jackpot-icon float-left"/>
                                <div class="odometer pisoglentis jackpot-elem3"></div>
                            </div>
                        </div>
                        <div class="col-lg-3 pb-1 my-auto">
                        </div>
                    </div>
                    <!-- JACKPOT -->
                                <button style="z-index: 20;" type="button" class="close modal-button" data-dismiss="modal"
                        aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>

            </div>

            <!-- BONUS MODAL -->
            <div id="bonusdiv" style="display: none;">
                <div id="bonusinner" style="">
                    <div id="bonusamount"></div>
                </div>
            </div>
            <!-- END BONUS MODAL -->

            <!-- JACKPOT MODAL -->
            <div id="jpdiv" style="display:none;">
                <div id="jpinner">
                    <div id="jpwin" class="center2">

                    </div>
                </div>
            </div>
            <!-- /JACKPOT MODAL -->

            <div class="modal-body">
                <div style="height: 65vh;" id="game-window-modal-frame">
                    <iframe class="modal-iframe" id="game-window-modal-iframe" src=""></iframe>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END GAME WINDOW MODAL -->
<!-- REGISTER MODAL -->
<div class="modal fade" id="register-modal" tabindex="-1" role="dialog" aria-labelledby="register-modal-title"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" id="register-modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="register-modal-title">
                    Register
                </h5>
                <button type="button" class="close modal-button" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body p-3">
                <form id="register-form" action="#" method="post">
                    <div class="custom-input-group mb-2">
                        <span class="custom-input-group-icon"><i class="fas fa-address-card"></i></span>
                        <input id="register-form-first-name" type="text" name="register-form-first-name"
                               placeholder="First Name"/>
                    </div>
                    <div class="custom-input-group mb-2">
                        <span class="custom-input-group-icon"><i class="fas fa-address-card"></i></span>
                        <input id="register-form-last-name" type="text" name="register-form-last-name"
                               placeholder="Last Name"/>
                    </div>
                    <div class="custom-input-group mb-2">
                        <span class="custom-input-group-icon"><i class="fas fa-at"></i></span>
                        <input id="register-form-email" type="text" name="register-form-email" placeholder="Email"/>
                    </div>
                    <div class="custom-input-group mb-2">
                        <span class="custom-input-group-icon"><i class="fas fa-user"></i></span>
                        <input id="register-form-username" type="text" name="register-form-username"
                               placeholder="Username"/>
                    </div>
                    <div class="custom-input-group mb-2">
                        <span class="custom-input-group-icon"><i class="fas fa-key"></i></span>
                        <input id="register-form-password1" type="password" name="register-form-password1"
                               placeholder="Password"/>
                    </div>
                    <div class="custom-input-group mb-2">
                        <span class="custom-input-group-icon"><i class="fas fa-key"></i></span>
                        <input id="register-form-password2" type="password" name="register-form-password2"
                               placeholder="Retype Password"/>
                    </div>
                    <button type="submit" form="register-form" value="Submit" class="btn primary-btn w-100 mt-4">
                        Register
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- END REGISTER MODAL -->
<!-- LOGIN MODAL -->

































</div>
<!-- END LOGIN MODAL -->

<!-- TICKET MODAL -->
<div class="modal fade" id="ticket-modal" tabindex="-1" role="dialog" aria-labelledby="ticket-modal-title"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" id="ticket-modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="login-modal-title">
                    Ticket
                </h5>
                <button type="button" class="close modal-button" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body p-3">
                <div class="w-100 text-center mb-4">
                    <img src="/frontend/Tropicoblack/img/ticket_logo.png" alt="logo"/>
                </div>
                <form id="ticket-form" action="#" method="post">
                    <div class="custom-input-group mb-2">
                        <span class="custom-input-group-icon"><i class="fas fa-user"></i></span>
                        <input id="ticket-form-username" type="text" value="1234"
                               disabled name="ticket-form-username" placeholder="Username"/>
                    </div>
                    <div class="custom-input-group mb-2">
                        <span class="custom-input-group-icon"><i class="fas fa-money-bill-alt"></i></span>
                        <input id="ticket-form-balance" disabled value="" type="text" name="ticket-form-balance"
                               placeholder="Balance"/>
                    </div>
                    <button type="button" form="ticket-form" id="ticket-print-button" value="Submit"
                            class="btn primary-btn w-100 mt-4">
                        Print
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- END TICKET MODAL -->


<!-- HELP MODAL -->
<style>
    .modal-fullscreen .modal-dialog {
        max-width: 100%;
        height: 100vh;
    }

    .modal-full .modal-content {
        width: 100vw;
        height: 100%;
    }
</style>
<div class="modal fade modal-fullscreen " id="help-modal" tabindex="-1" role="dialog"
     aria-labelledby="ticket-modal-title"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-full" role="document" id="ticket-modal-dialog">
        <div class="modal-content modal-content-full">
            <div class="modal-header">
                <h5 class="modal-title" id="login-modal-title">
                    Help
                </h5>
                <button type="button" class="close modal-button" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body p-3">
                <h4></h4>
                Online casino  is one of the most reliable and proven online casinos. You can enjoy a number of popular games in  known from land-based casinos all over the world. Players can choose between classic fruit slot games, theme slots, Roulette, Black Jack, Keno and other games including exclusive brand new games.

                <div class="d-none pt-3" id="bonus1">
                    <h4> Cash back bonus</h4>

                    <span id="perBonus" class="text-danger font-weight-bold"></span>
                    Whenever you deposit an amount, you will get
                    <span id="perBonus2" class="text-danger font-weight-bold"></span>
                    amount added to your bonus box. These credits are added automatically to your balance when your balance drops below 1.00 If you cash out before cash back bonus is activated, the bonus is lost. You can cash out or top up at any time.


                    <span id="bonus2" class="d-none pt-3">
                            <h4 class="pt-3"> Happy hour bonus</h4>
                            Deposit From<br>
                                <span id="dates" class="font-weight-bold text-danger">

                                </span>
                                 and get
                                <span id="hp_bonus" class="font-weight-bold text-danger">

                                </span>
                                bonus.Happy hour bonus is an increased cash back bonus at specific time of the day so on every deposit you get

                                <span id="hp_bonus2" class="font-weight-bold text-danger">

                                </span>
                                added to your bonus box.

                           <hr>

                        </span>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END HELP MODAL -->

<!-- BONUS  MODAL -->

<div class="modal fade " id="bonus-modal" tabindex="-1" role="dialog"
     aria-labelledby="ticket-modal-title"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered " role="document" id="ticket-modal-dialog">
        <div class="modal-content modal-content-full">
            <div class="modal-header bbbonus1">
                <h5 class="modal-title text-center" id="login-modal-title">
                    Lvl
                </h5>
                <button type="button" class="close modal-button" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body p-3 bbbonus1">
                <h1 class="text-center text-warning text-capitalize font-weight-bold"> Your level is
                    <spam
                        class="lvlspam"></spam>
                </h1>

                <div class="text-center">

                    Levels unlock bonus features!
                    <br>
                    Progress is updated every few minutes
                     <br>
                    <div class="pt-5">
                        <h3>* Current level features *</h3>
                    </div>

                    <div class="pt-5 msgbonus">

                    </div>


                </div>
            </div>
        </div>
    </div>
</div>
<!-- END BONUS MODAL -->

<!-- BONUS2  MODAL -->



<!-- END BONUS2 MODAL -->

<!-- BONUS2 THANK YOU  MODAL -->

<div class="modal fade " id="bonus3-modal" tabindex="-1" role="dialog"
     aria-labelledby="ticket-modal-title"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered " role="document" id="ticket-modal-dialog">
        <div class="modal-content modal-content-full">
            <div class="modal-header bbbonus1">
                <h5 class="modal-title text-center" id="login-modal-title">
                    Congratulations!
                </h5>
                <button type="button" class="close modal-button" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body p-3">
                <div class="text-center">
                    <h1 class="modal-title text-center text-warning" id="login-modal-title">
                        Bonus collect!
                    </h1>
            </div>
        </div>
    </div>
</div>

<!-- BONUS2 THANK YOU  MODAL -->

<!-- END HELPERS -->

<script>
    var BIGGER_WIN = "Bigger Win"
    var NUMBER_OF_WINS = "Number Of Wins"
    var LAST_WINNER = "Last Winner"
    var HELP_17 = "Bonus collected!"
    var SUCCESS = "Success!";

		var jackpotSettings = {
		    "jackpot-elem1": {
		        currentValue: {{$jpgs[0]->balance}},
		        isRed: false,
		        details: {
		            bigger_win: {
		                amount: 100,
		                date: "11-11-2019"
		            },
		            number_of_wins: 1,
		            last_winner: {
		                amount: 100,
		                date: "11-11-2019",
		                username: "test"
		            }
		        }
		    },
		    "jackpot-elem2": {
		        currentValue: {{$jpgs[1]->balance}},
		        isRed: true,
		        details: {
		            bigger_win: {
		                amount: 100,
		                date: "11-11-2019"
		            },
		            number_of_wins: 1,
		            last_winner: {
		                amount: 100,
		                date: "11-11-2019",
		                username: "test"
		            }
		        }
		    },
				@php
					
				@endphp
		    "jackpot-elem3": {
		        currentValue: {{$jpgs[2]->balance}},
		        isRed: false,
		        details: {
		            bigger_win: {
		                amount: 100,
		                date: "11-11-2019"
		            },
		            number_of_wins: 1,
		            last_winner: {
		                amount: 100,
		                date: "11-11-2019",
		                username: "test"
		            }
		        }
		    }
		};


</script>
<script src="/frontend/Tropicoblack/assets/vendors/jquery/3.4.1/jquery-3.4.1.min.js"></script>
<script src="/frontend/Tropicoblack/js/jquery-ui.js"></script>
<script src="/frontend/Tropicoblack/assets/vendors/popper/1.14.7/popper.min.js"></script>
<script src="/frontend/Tropicoblack/assets/vendors/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="/frontend/Tropicoblack/assets/vendors/swiper/4.5.0/js/swiper.min.js"></script>
<script src="/frontend/Tropicoblack/assets/vendors/odometer/0.4.6/js/odometer.min.js"></script>
<script src="/frontend/Tropicoblack/assets/vendors/bootstrap-select/1.13.9/js/bootstrap-select.min.js"></script>
<script src="/frontend/Tropicoblack/js/alertify.min.js?v=8324"></script>
<script src="/frontend/Tropicoblack/js/bonus.min.js?v=8324"></script>
<script src="/frontend/Tropicoblack/assets/js/games.js"></script>
<script src="/frontend/Tropicoblack/assets/js/tools.js"></script>
<script src="/frontend/Tropicoblack/assets/js/main.js"></script>
<script src="/frontend/Tropicoblack/assets/js/ui.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.7.9/jquery.lazy.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('[data-category="all"]').click();
});
@if (request()->category1 != "all")
window.location.href = "/categories/all";
@endif
</script>

</body>

</html>
