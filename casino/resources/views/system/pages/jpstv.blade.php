
<meta charset="UTF-8">
<title>JPS TV</title>
<link rel="stylesheet" href="/global/jpstv/css/odometer-theme-default.css">
<link rel="stylesheet" href="/global/jpstv/css/plazma_video_animations_style.css">
<script src="/global/jpstv/js/config.js"></script>
<script src="/global/jpstv/js/odometer.js"></script>
<script src="/global/jpstv/js/jquery.js"></script>
<style>

    #jackpot_01 {
        top: 17%;
        left: 0;
        font-size: 89px;
        padding-left: 20%;
    }

    #jackpot_02 {
        top: 40.5%;
        left: 0;
        font-size: 105px;
        width: 550px;
        padding-left: 20%;
    }

    #jackpot_03 {
        top: 61.8%;
        left: 0;
        font-size: 90px;
        padding-left: 20%;
    }

    #jackpot_04 {
        font-size: 82px;
        top: 80%;
        left: 0;
        padding-left: 20%;
    }

    #jackpot_05 {
        font-size: 82px;
        top: 70%;
        right: 0;
        padding-right: 25%;
    }

    #jackpot_06 {
        font-size: 78px;
        top: 86.5%;
        right: 0;
        padding-right: 25%;
    }

    #win-amount {
        top: 57.5%;
        left: 0;
        font-size: 140px;
    }

    #winner-id {
        top: 86.5%;
        left: 0;
        font-size: 50px;
        height: 10%;
    }
    #winner-id #win-id-odometer{
        top: 25%;
    }

    .community_sign_1,
    .community_sign_2,
    .community_sign_3 {
        width: 85% !important;
        height: 154px;
        display: block;
        position: absolute;
        z-index: 20;
        text-align: center;
        background: url("/global/jpstv/community_sign.png") top center no-repeat;
        margin-top: -30px;
    }
    .community_sign_2 {
        margin-top: -32px;
    }
    .community_sign_3 {
        margin-top: -34px;
    }
</style>
<div id="progress-bar">
    <div></div>
</div>
<div id="jackpot-wrapper">
    <div id="video-wrapper"></div>

    <div id="jackpot_01" class="jackpot" data-group="1">
        <div class="odometer"></div>
    </div>

    <div id="jackpot_02" class="jackpot" data-group="1">
        <div class="odometer"></div>
    </div>

    <div id="jackpot_03" class="jackpot" data-group="1">
        <div class="odometer"></div>
    </div>

    <div id="jackpot_04" class="jackpot" data-group="1">
        <div class="odometer"></div>
    </div>

    <div id="jackpot_05" class="jackpot" data-group="1">
        <div class="odometer"></div>
    </div>

    <div id="jackpot_06" class="jackpot" data-group="1">
        <div class="odometer"></div>
    </div>

    <div id="win-amount" class="win" data-group="2">
        <div id="win-odometer"></div>
    </div>

    <div id="winner-id" class="win" data-group="2">
        <div id="win-id-odometer"></div>
    </div>

</div>
<div id="password-wrapper" style="display: none">
    <div>
        <label>
            <span id='pwd'></span>
            <input type="password" id="password-input">
        </label>
    </div>
    <div>
        <button id="proceed-btn"></button>
    </div>
</div>

<script>
    var main_video = '/global/jpstv/main_animation.ogv';
    var hit_jackpots_videos = ['/global/jpstv/diamond_hit.ogv' , '/global/jpstv/platinum_hit.ogv', '/global/jpstv/gold_hit.ogv', '/global/jpstv/silver_hit.ogv', '/global/jpstv/bronze_hit.ogv', '/global/jpstv/iron_hit.ogv'];
    var id = {{ $id }};

    function resizeFonts(communities) {
        $('#jackpot_01').css({'font-size': ( $(window).height() * 0.12133 ) + 'px'});
        $('#jackpot_02').css({'font-size': ( $(window).height() * 0.10616) + 'px'});
        $('#jackpot_03').css({'font-size': ( $(window).height() * 0.091) + 'px'});
        $('#jackpot_04').css({'font-size': ( $(window).height() * 0.0829) + 'px'});
        $('#jackpot_05').css({'font-size': ( $(window).height() * 0.0829) + 'px'});
        $('#jackpot_06').css({'font-size': ( $(window).height() * 0.0788) + 'px'});

        $('#win-amount').css({'font-size': ( $(window).height() * 0.14155) + 'px'});
        $('#winner-id').css({'font-size': ( $(window).width() * 0.02604) + 'px'});

        setTimeout(function() {
            for (var i=1, w=0, h=0, d; i<=3; i++) {
                d = $('#jackpot_0' + i);
                w = d.find('.odometer').width();
                h = (d.find('.odometer-value img').length ? d.find('.odometer-value img').height() : d.find('.odometer-value').height()) + 15;
                if (communities != undefined) {
                    if ($.inArray(i, communities) >= 0 && !d.find('.community_sign_' + i).length) {
                        $('<div class="community_sign_' + i + '"></div>').prependTo(d).css({'width': w + 'px', 'height': h + 'px'});
                    }
                } else if (d.find('.community_sign_' + i).length) {
                    $('.community_sign_' + i).css({'width': w + 'px', 'height': h + 'px'});
                }
            }
        }, 100);
    }
</script>
<script src="/global/jpstv/js/plazma_video_animations_handler.js"></script>
<script>initAll();</script>