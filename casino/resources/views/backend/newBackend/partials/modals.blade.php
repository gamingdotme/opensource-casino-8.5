
    <div class="modal fade" id="inmodal" tabindex="-1" role="dialog" aria-labelledby="inModal" aria-hidden="true">
        <div class="modal-dialog" role="document" id="modal_in_loading">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-group row text-center">
                        <div class="alert  text-center container">
                            <h2> Loading....</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-dialog" role="document" id="modal_in_players_hidde">
            <form action="" id="inForm" method="post">
                <input type="hidden" name="_token" value="wKh21i6UEoxYW2BlOsVV7t9gYsJ2lDyJzhQF3oJM">
                <input type="hidden" name="credits_hidden" id="credits_hidden" value="0">
                <input type="hidden" name="playerhash" id="playerhash" value="0">
                <input type="hidden" name="dik_hidden" id="dik_hidden" value="0">
                <input type="hidden" name="email" id="email" value="0">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="col-12 text-center">
                            <input id="InName" disabled="" class="value text-gray-dark nameinput" value="">
                            <input id="InScore" disabled="" class="value text-gray-dark nameinput" value="">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">

                            <div class="alert alert-danger  virtual_games none_none text-center container" role="alert">
                                <h4 class="alert-heading">Warning</h4>
                                <p class="mb-0">This player has bets on VIRTUAL GAMES please try in few minutes</p>
                                <hr>
                            </div>


                            <div class="alert alert-danger  cancelbonusMessageIn none_none text-center container"
                                role="alert">
                                <h4 class="alert-heading">Warning</h4>
                                <p class="mb-0">Error this player got credits from bonus!</p>
                                <p class="mb-0">Please cash out first or play all credits.</p>
                                <hr>
                            </div>

                            <div class="alert alert-danger  none_none text-center container cancelButton"
                                id="cancelButton">
                                <h4 class="alert-heading">Warning</h4>
                                <p class="mb-0">Warning all BONUS / FREE SPINS uncollected wins will be LOST.</p>
                                <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelbonus"
                                    style="cursor: pointer;">Reset</button>
                            </div>

                            <div class="col-sm-12 text-center inamountDiv">
                                <button type="button" class="btn btn-primary btnIN" data-in="1.00">
                                    +1.00</button>
                                <button type="button" class="btn btn-primary btnIN" data-in="5.00">
                                    +5.00</button>
                                <button type="button" class="btn btn-primary btnIN" data-in="10.00">
                                    +10.00</button>
                                <button type="button"
                                    class="btn btn-primary d-none d-xl-table-cell d-lg-table-cell d-md-table-cell btnIN"
                                    data-in="20.00">
                                    +20.00</button>
                                <button type="button"
                                    class="btn btn-primary d-none d-xl-table-cell d-lg-table-cell d-md-table-cell btnIN"
                                    data-in="50.00">
                                    +50.00</button>
                                <div class="mb-2"></div>
                            </div>
                        </div>
                        <div class="form-group row" id="scoreInputInModal">
                            <div class="col-sm-12 indiv text-center">
                                <label for="Modalscore"></label>
                                <input class="in_Input scoreinput" value="0" name="score" id="Modalscore" type="tel">
                            </div>
                        </div>
                    </div>
                    <div id="happyHour" class="text-center">
                        <h4 id="happyHourA" class="none_none">Happy hour: <span class="text-success">Available</span>
                        </h4>
                        <h4 id="happyHourB" class="none_none">Happy hour: <span class="text-danger">Not available</span>
                        </h4>
                    </div>
                    <div class="modal-footer col-sm-12 text-left" id="footerInModal">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" id="incancel"
                            style="cursor: pointer;">Cancel</button>
                        <button type="button" id="creditsIn" class="btn btn-primary "
                            style="cursor: pointer;">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="outmodal" tabindex="-1" role="dialog" aria-labelledby="OutModal" aria-hidden="true">
        <div class="modal-dialog" role="document" id="modal_out_loading">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-group row text-center">
                        <div class="alert  text-center container">
                            <h2> Loading....</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-dialog" role="document" id="modal_out_players_hidde">
            <form action="https://netpos.gapi.lol/out" id="OutForm" method="post">
                <input type="hidden" id="playerhash_hidden_out" name="playerhash" value="0">
                <input type="hidden" id="credits_hidden_out" name="credits" value="0">
              
                <input type="hidden" id="_token" value="{{ csrf_token() }}">
                <input type="hidden" id="credits_hidden_out_helper" name="credits_hidden_out_helpere" value="">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="col-12 text-center">
                            <label for="OutName" class="none_none"></label>
                            <label for="OutScore" class="none_none"></label>
                            <input id="OutName" disabled="" class="value text-gray-dark nameinput" value="">
                            <input id="OutScore" disabled="" class="value text-gray-dark nameinput" value="0">
                            <div id="outscoreDiv" style="position:absolute; left:0; right:0; top:0; bottom:0; cursor:
                            pointer;"></div>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">

                            <div class="alert alert-danger  none_none text-center container cancelButtonOut"
                                id="cancelButtonOut">
                                <h4 class="alert-heading">Warning</h4>
                                <p class="mb-0  font-weight-bold"><span>Game: </span> <span
                                        class="freespinclass"></span> </p>
                                <p class="mb-0">Warning all BONUS / FREE SPINS uncollected wins will be LOST.</p>
                                <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelbonusOut"
                                    style="cursor: pointer;">Reset</button>
                            </div>
                            <div class="alert alert-danger  cancelbonusMessageOut none_none text-center container"
                                role="alert">
                                <h4 class="alert-heading">Warning</h4>
                                <p class="mb-0">Error this player got credits from bonus!</p>
                                <p class="mb-0">Please cash out first or play all credits.</p>
                                <hr>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-danger" id="outallBonus"
                                    style="cursor: pointer;">Out all</button>
                            </div>

                            <div class="col-sm-12 text-center inAmount">
                                <button type="button" class="btn btn-primary btnOUT" data-in="1.00">
                                    -1.00</button>
                                <button type="button" class="btn btn-primary btnOUT" data-in="5.00">
                                    -5.00</button>
                                <button type="button" class="btn btn-primary btnOUT" data-in="10.00">
                                    -10.00</button>
                                <button type="button"
                                    class="btn btn-primary d-none d-xl-table-cell d-lg-table-cell d-md-table-cell btnOUT"
                                    data-in="20.00">
                                    -20.00</button>
                                <button type="button"
                                    class="btn btn-primary d-none d-xl-table-cell d-lg-table-cell d-md-table-cell btnOUT"
                                    data-in="50.00">
                                    -50.00</button>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12 indiv text-center">
                                <div class="form-group row">
                                    <div class="col-sm-12 indiv text-center outinputss">
                                        <label for="out_Input"></label>
                                        <input class="in_Input scoreinput"
                                            onblur="if (this.value == '') {this.value = 0;}"
                                            onfocus="if (this.value == 0) {this.value = '';}" value="0" name="score"
                                            id="out_Input" type="tel">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <div class="text-right col-md-12">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal"
                                            id="outcancel">Cancel</button>
                                        <button type="button" class="btn btn-danger" id="outall">Out all</button>
                                        <button type="button" class="btn btn-primary" id="creditsOut">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <style>
        .pstyle {
            text-align: left;
            font-size: 15px;
            margin-top: 0px;
        }

        .pstyle3 {
            margin-top: -15px;
        }

    </style>
    <div class="modal fade" id="PrintTicket" tabindex="-1" role="dialog" aria-labelledby="v" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5> Voucher. This option allow generate tickets from cashier page.</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body" id="printDiv">
                    <style>
                        @page {
                            size: 88mm 0mm;
                            margin: 6mm;
                            padding: 0;
                            border: none;
                            border-collapse: collapse;
                        }

                        @media print {

                            .pstyle2 {
                                margin-top: 60px;
                            }

                            .pstyle3 {
                                margin-top: -15px;
                            }

                            .pstyle4 {
                                margin-top: 15px;
                            }

                            .pstyle5 {
                                padding-top: 50px;
                            }

                            #section-to-print,
                            #section-to-print * {
                                visibility: visible;
                                font-size: 22px;
                                padding-top: 0px;
                            }

                            #section-to-print {
                                position: absolute;
                                left: 0;
                                top: 0;
                            }

                            .printbotom222 {
                                padding-bottom: 25mm;
                            }

                            .printbotom {
                                display: none;
                            }

                            .center_div {
                                display: none;
                            }
                        }

                        .button2 {
                            display: inline;
                            *display: inline;
                            zoom: 1;
                            padding: 6px 20px;
                            margin: 0;
                            cursor: pointer;
                            border: 1px solid #bbb;
                            overflow: visible;
                            font: bold 13px arial, helvetica, sans-serif;
                            text-decoration: none;
                            white-space: nowrap;
                            color: #555;

                            background-color: #ddd;
                            background-image: -webkit-gradient(linear, left top, left bottom, from(rgba(255, 255, 255, 1)), to(rgba(255, 255, 255, 0)));
                            background-image: -webkit-linear-gradient(top, rgba(255, 255, 255, 1), rgba(255, 255, 255, 0));
                            background-image: -moz-linear-gradient(top, rgba(255, 255, 255, 1), rgba(255, 255, 255, 0));
                            background-image: -ms-linear-gradient(top, rgba(255, 255, 255, 1), rgba(255, 255, 255, 0));
                            background-image: -o-linear-gradient(top, rgba(255, 255, 255, 1), rgba(255, 255, 255, 0));
                            background-image: linear-gradient(top, rgba(255, 255, 255, 1), rgba(255, 255, 255, 0));

                            -webkit-transition: background-color .2s ease-out;
                            -moz-transition: background-color .2s ease-out;
                            -ms-transition: background-color .2s ease-out;
                            -o-transition: background-color .2s ease-out;
                            transition: background-color .2s ease-out;
                            background-clip: padding-box;
                            /* Fix bleeding */
                            -moz-border-radius: 3px;
                            -webkit-border-radius: 3px;
                            border-radius: 3px;
                            -moz-box-shadow: 0 1px 0 rgba(0, 0, 0, .3), 0 2px 2px -1px rgba(0, 0, 0, .5), 0 1px 0 rgba(255, 255, 255, .3) inset;
                            -webkit-box-shadow: 0 1px 0 rgba(0, 0, 0, .3), 0 2px 2px -1px rgba(0, 0, 0, .5), 0 1px 0 rgba(255, 255, 255, .3) inset;
                            box-shadow: 0 1px 0 rgba(0, 0, 0, .3), 0 2px 2px -1px rgba(0, 0, 0, .5), 0 1px 0 rgba(255, 255, 255, .3) inset;
                            text-shadow: 0 1px 0 rgba(255, 255, 255, .9);

                            -webkit-touch-callout: none;
                            -webkit-user-select: none;
                            -khtml-user-select: none;
                            -moz-user-select: none;
                            -ms-user-select: none;
                            user-select: none;
                            height: 50px;
                            width: 100px;
                            text-align: center;
                            padding: 25px;
                            font-size: 40px;
                        }

                        .none_none {
                            display: none;
                        }

                        .center_div {
                            margin: auto;
                            width: 50%;
                            padding: 10px;
                        }

                    </style>
                    <strong class="section-to-print" id="section-to-print">
                        <p class="pstyle pstyle2">
                        </p>
                        <p class="pstyle">
                            **************************************
                        </p>
                        <p class="pstyle text-uppercase pstyle3">
                            DEPOSIT TICKET
                        </p>
                        <p class="pstyle pstyle3">
                            **************************************
                        </p>
                        <p class="pstyle pstyle3">

                        </p>
                        <p class="pstyle pstyle3">
                            Username: <span id="pusername"></span>
                        </p>
                        <p class="pstyle pstyle3">
                            Password: <span id="pupassword"></span>
                        </p>
                        <p class="pstyle pstyle3">
                            Credits: <span id="pscore"></span>
                        </p>
                        <p class="pstyle pstyle3">
                            Date: <span id="pDate"></span>
                        </p>
                        <p class="pstyle pstyle3">
                            Time: <span id="pTime"></span>
                        </p>
                        <p class="pstyle">
                            ____________________
                        </p>
                        <p class="pstyle">
                            Signature
                        </p>
                        <p class="pstyle">
                            **************************************
                        </p>
                        <p class="pstylev printbotom222">
                            DEPOSIT TICKET
                        </p>
                        <p class="pstyle">
                            **************************************
                        </p>
                        <p class="pstyle pstyle4">
                        </p>
                        <p class="pstyle pstyle5 d-none">
                            -
                        </p>
                        <p class="pstyle pstyle5 d-none">
                            -
                        </p>
                        <div class="center_div">
                            <a href="" class="printbotom none_none button2"
                                onclick="window.opener.closePrinterDiv(); window.parent.closePrinterDiv()"
                                data-original-title="" title="">Close</a>
                        </div>
                    </strong>
                </div><strong class="section-to-print" id="section-to-print">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close
                        </button>
                        <button type="submit" class="btn btn-primary" onclick="PrintElem444('printDiv');">
                            Print
                        </button>
                    </div>
                </strong>
            </div><strong class="section-to-print" id="section-to-print">
            </strong>
        </div><strong class="section-to-print" id="section-to-print">
        </strong>
    </div><strong class="section-to-print" id="section-to-print">
        <input type="hidden" id="slave_userhash" name="slave_userhash" value="0">

        <div class="modal fade" id="cashier_profile_modal" tabindex="-1" role="dialog" aria-labelledby=""
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalusername">Username:
                            <span class="text-danger" id="modal_profile_username"></span>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-group row">
                                <label for="inputPassword" class="col-sm-4 col-form-label">Password</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="profile_password"
                                        onkeyup="checkpassword()" value="0">
                                    <small id="passwordHelper" style="display: none;"
                                        class="form-text text-danger">Password should be at least 6 character</small>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="enabled_slave" id="enabled_slave"
                                        value="1">
                                    <label class="form-check-label" for="enabled_slave">Enable</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="enabled_slave" value="0"
                                        id="disabled_slave">
                                    <label class="form-check-label" for="disabled_slave">Disable</label>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <div class="text-right col-md-12">
                            <button type="button"
                                class="btn btn-secondary d-none d-xl-table-cell d-lg-table-cell d-md-table-cell"
                                data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="save_profile_cashier">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <style>
            .bigtext {
                font-size: 30px;
            }

        </style>
        <input type="hidden" id="ticket_userhash" name="ticket_userhash" value="0">
        <input type="hidden" id="ticket_id" name="ticket_id" value="0">

        <div class="modal fade" id="ticketModalv2" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalusername">Terminal:
                            <span class="text-danger" id="ticket_out_username"></span>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="mx-auto  p-3 bg-dark">
                                <div class="card text-white bg-primary mb-3 justify-content-center">
                                    <div class="card-header text-danger text-center">
                                        <h3>#ID - <span id="ticket_id_modal" class="text-dangert"></span></h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="card-header text-danger text-center">
                                            <h3 class="text-uppercase">Credits - <span id="ticket_credits"
                                                    class="text-dangert"></span></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <div class="text-right col-md-12">
                            <button type="button"
                                class="btn btn-secondary d-none d-xl-table-cell d-lg-table-cell d-md-table-cell"
                                data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="ticket_payv2">Pay</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" id="profile_userhash" name="profile_userhash" value="0">

        <div class="modal fade" id="profilemodal" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalusername">Name:
                            <span class="text-danger" id="modal_profile_username"></span>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-group row">
                                <label for="staticEmail" class="col-sm-4 col-form-label">Username</label>
                                <div class="col-sm-8">
                                    <input type="text" disabled="" class="form-control" id="profile_username" value="0">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="staticEmail" class="col-sm-4 col-form-label">Name</label>
                                <div class="col-sm-8">
                                    <input type="text" disabled="" class="form-control" id="profile_name" value="0">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputPassword" class="col-sm-4 col-form-label">Password</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" onkeyup="checkpasswordPlayer()"
                                        id="profile_password_player" value="0">
                                    <small id="passwordHelper" style="display: none;"
                                        class="form-text text-danger">Password should be at least 6 character</small>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <div class="col-md-6 btn-group">
                            <button type="button" id="deleteuser" data-userhash="0"
                                class="btn btn-danger">Delete</button>
                            <a href="" target="_blank" class="history btn btn-warning" data-original-title=""
                                title="">History</a>
                        </div>
                        <div class="col-md-6 text-right">
                            <div class="btn-group">
                                <button type="button"
                                    class="btn btn-secondary d-none d-xl-table-cell d-lg-table-cell d-md-table-cell"
                                    data-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary " id="save_profile">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <label for="userhash" class="none_none"></label>
        <input id="userhash" value="70e1f20834b5ef27df872609eb44598b" class="none_none userhash">
        <label for="languege" class="none_none"></label>
        <input id="languege" value="en" class="none_none">
        <label for="disable" class="none_none"></label>
        <input id="disable" value="Disable" class="none_none">
        <label for="enable" class="none_none"></label>
        <input id="enable" value="Enable" class="none_none">
        <label for="success" class="none_none"></label>
        <input id="success" value="Success" class="none_none">
        <label for="error" class="none_none"></label>
        <input id="error" value="Error" class="none_none">
        <label for="currency" class="none_none"></label>
        <input id="currency" value="TND" class="none_none">
        <label for="credits" class="none_none"></label>
        <input id="credits" value="Credits" class="none_none">
        <label for="your_account_has" class="none_none"></label>
        <input id="your_account_has" value="Your account has been logged into from another computer" class="none_none">
        <label for="all_cashiers" class="none_none"></label>
        <input id="all_cashiers" value="All cashiers" class="none_none">
        <label for="afterlogout" class="none_none"></label>
        <input id="afterlogout" value="" class="none_none">
        <label for="riderect_cashier_on_alarm" class="none_none"></label>
        <input id="riderect_cashier_on_alarm" value="1" class="none_none">
        <label for="allow_in_out" class="none_none"></label>
        <input id="allow_in_out" value="1" class="none_none">
        <label for="print_receipt" class="none_none"></label>
        <input id="print_receipt" value="" class="none_none">
        <label for="cashier_master" class="none_none"></label>
        <input id="cashier_master" value="1" class="none_none">
        <label for="allow_view_bonus" class="none_none"></label>
        <input id="allow_view_bonus" value="0" class="none_none">
        <label for="voucher" class="none_none"></label>
        <input id="voucher" value="" class="none_none">
        <label for="allow_disableuser" class="none_none"></label>
        <input id="allow_disableuser" value="1" class="none_none">
        <label for="ticketv2" class="none_none"></label>
        <input id="ticketv2" value="0" class="none_none">
        <label for="trans_reset" class="none_none"></label>
        <input id="trans_reset" value="Reset" class="none_none">
        <label for="ARE_YOU_SURE" class="none_none"></label>
        <input id="ARE_YOU_SURE" value="Are you sure?" class="none_none">
        <label for="CANCEL" class="none_none"></label>
        <input id="CANCEL" value="Cancel" class="none_none">
        <label for="HISTORY_URL" class="none_none"></label>
        <input id="HISTORY_URL" value="https://api.gapi.lol/show/history" class="none_none">
        <label for="APP_DEBUG" class="none_none"></label>
        <input id="APP_DEBUG" value="" class="none_none">
        <label for="BONUS" class="none_none"></label>
        <input id="BONUS" value="Bonus" class="none_none">

        <label for="CREDITS" class="none_none"></label>
        <input id="CREDITS" value="Credits" class="none_none">
        <label for="THISCANNOTBE" class="none_none"></label>
        <input id="THISCANNOTBE" value="This cannot be undone" class="none_none">
        <label for="TICKET_WRONG_PIN" class="none_none"></label>
        <input id="TICKET_WRONG_PIN" value="Wrong pin. Please check PIN" class="none_none">
        <label for="TODAY" class="none_none"></label>
        <input id="TODAY" value="Today" class="none_none">
        <label for="YESTERDAY" class="none_none"></label>
        <input id="YESTERDAY" value="Yesterday" class="none_none">
        <label for="LASTSEVENDAYS" class="none_none"></label>
        <input id="LASTSEVENDAYS" value="Last 7 days" class="none_none">
        <label for="LAST30DAYS" class="none_none"></label>
        <input id="LAST30DAYS" value="Last 30 days" class="none_none">
        <label for="THISMONTH" class="none_none"></label>
        <input id="THISMONTH" value="This Month" class="none_none">
        <label for="LASTMONTH" class="none_none"></label>
        <input id="LASTMONTH" value="Last Month" class="none_none">
        <label for="CUSTOMRANGE" class="none_none"></label>
        <input id="CUSTOMRANGE" value="Custom range" class="none_none">
        <label for="CREDITS_MUST_NUMERIC" class="none_none"></label>
        <input id="CREDITS_MUST_NUMERIC" value="Credits must be numeric" class="none_none">
        <label for="LOADING" class="none_none"></label>
        <input id="LOADING" value="Loading...." class="none_none">
        <label for="WARNINGWINSLOSE" class="none_none"></label>
        <input id="WARNINGWINSLOSE" value="Warning all BONUS / FREE SPINS  uncollected wins will be LOST."
            class="none_none">
        <label for="WARNING" class="none_none"></label>
        <input id="WARNING" value="Warning" class="none_none">
        <label for="ERROR_BONUS_X_3333" class="none_none"></label>
        <input id="ERROR_BONUS_X_3333" value="Error this player got credits from bonus!" class="none_none">
        <label for="WAGER" class="none_none"></label>
        <input id="WAGER" value="0" class="none_none">
        <label for="ERROR_BLACKJACK" class="none_none"></label>
        <input id="ERROR_BLACKJACK" value="" class="none_none">
        <label for="SHOW_EMAIL_NAME" class="none_none"></label>
        <input id="SHOW_EMAIL_NAME" value="" class="none_none">
        <script src="/back/netpos/bower_components/jquery/dist/jquery.min.js"></script>
        <script src="/back/netpos/bower_components/moment/moment.js"></script>
        <script src="/back/netpos/bower_components/chart.js/dist/Chart.min.js"></script>
        <script src="/back/netpos/bower_components/select2/dist/js/select2.full.min.js"></script>
        <script src="/back/netpos/bower_components/ckeditor/ckeditor.js"></script>
        <script src="/back/netpos/bower_components/bootstrap-validator/dist/validator.min.js">
        </script>
        <script src="/back/netpos/bower_components/bootstrap-daterangepicker/daterangepicker.js">
        </script>
        <script src="/back/netpos/bower_components/dropzone/dist/dropzone.js"></script>
        <script src="/back/netpos/bower_components/editable-table/mindmup-editabletable.js">
        </script>
        <script src="/back/netpos/bower_components/datatables.net/js/jquery.dataTables.min.js">
        </script>
        <script src="/back/netpos/bower_components/fullcalendar/dist/fullcalendar.min.js"></script>
        <script
                src="/back/netpos/bower_components/perfect-scrollbar/js/perfect-scrollbar.jquery.min.js">
        </script>
        <script src="/back/netpos/bower_components/bootstrap/js/dist/util.js"></script>
        <script src="/back/netpos/bower_components/bootstrap/js/dist/tab.js"></script>
        <script src="/back/netpos/inc/dataTables.buttons.min.js"></script>
        <script src="/back/netpos/inc/dataTables.bootstrap4.min.js"></script>
        <script src="/back/netpos/inc/buttons.print.min.js"></script>
        <script src="/back/netpos/inc/alertify.min.js"></script>
        <script src="/back/netpos/inc/maina570.js"></script>
        <script src="/back/netpos/inc/tether.min.js"></script>
        <script src="/back/netpos/inc/bootstrap.min.js"></script>
        <script src="/back/netpos/inc/sweetalert2.all.js"></script>
        <script src="/back/netpos/inc/bootstrap-select.min.js"></script>
        <script src="/back/netpos/inc/bootstrap-toggle.min.js"></script>
        <script src="/back/netpos/settings.min.js?v=2.1"></script>
        <script src="/back/netpos/moment.min.js"></script>
        <script src="/back/netpos/pisoglentis.min.js?v=2.1"></script>
        <script src="/back/netpos/creditsout.min.js?v=2.1"></script>
        <script src="/back/netpos/creditin.min.js?v=2.1"></script>
        <script src="/back/netpos/helper.js?v=2.1"></script>
        <script>
            // PRINT

            function PrintElem(elem) {
                var ua = navigator.userAgent.toLowerCase();
                var isAndroid = ua.indexOf("android") > -1; //&& ua.indexOf("mobile");


                var dt = new Date();
                var time = dt.getHours() + ":" + dt.getMinutes() + ":" + dt.getSeconds();
                var d = new Date();
                var strDate = d.getFullYear() + "/" + (d.getMonth() + 1) + "/" + d.getDate();
                var strDate = d.getFullYear() + "/" + (d.getMonth() + 1) + "/" + d.getDate();

                $('#pDate').html(strDate);
                $('#pTime').html(formatAMPM(new Date));


                var mywindow = window.open('', 'PRINT', 'height=400,width=600');

                mywindow.document.write('<html><head><title> Voucher </title>');
                mywindow.document.write('</head><body >');
                mywindow.document.write('<h1>Voucher</h1>');
                mywindow.document.write(document.getElementById(elem).innerHTML);
                mywindow.document.write('</body></html>');

                mywindow.document.close(); // necessary for IE >= 10
                mywindow.focus(); // necessary for IE >= 10*/


                if (isAndroid) {
                    // https://developers.google.com/cloud-print/docs/gadget
                    var gadget = new cloudprint.Gadget();
                    gadget.setPrintDocument("url", $('title').html(), window.location.href, "utf-8");
                    gadget.openPrintDialog();
                } else {
                    mywindow.print();
                }



                setTimeout(function() {
                    mywindow.close();
                    return true;

                }, 5000);
            }

         
        </script>
        <script>
            function PrintElem444(elem) {
                var mywindow222 = window.open('', 'PRINT', 'height=400,width=600');


                mywindow222.document.write('<html><head><title>  </title>');
                mywindow222.document.write('</head> <body');
                mywindow222.document.write('<h1></h1>');
                mywindow222.document.write(document.getElementById(elem).innerHTML);
                mywindow222.document.write('</body></html>');

                mywindow222.document.close(); // necessary for IE >= 10
                mywindow222.focus(); // necessary for IE >= 10*/

                mywindow222.print();


                setTimeout(function() {
                    mywindow222.close();
                    return true;

                }, 5000);

                $('#PrintTicket').modal('toggle');
                $('.printbotom').addClass('none_none');
            }


            function closePrinterDiv() {

                if (mywindow222 !== undefined) {
                    mywindow222.close();
                    $('#PrintTicket').modal('toggle');
                    $('.printbotom').addClass('none_none');

                }
            }
        </script>
        <script>
            logs_footer()
        </script>


    </strong>