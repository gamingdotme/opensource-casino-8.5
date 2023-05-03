
pd = {
    '/jackpot_plasma/coins/index.html' : 7000,
    '/jackpot_plasma/present/index.html' : 7000
};

spd = ['/jackpot_plasma/coins/index.html','/jackpot_plasma/present/index.html'];

savedJackpots={};


function pad(n, width, z) {
    z = z || '0';
    n = n + '';
    return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
}

var odometers = [],
    odometer,
    hit_play_queue = [],
    jackpot_win_data = [],
    hit_jackpots_ids = {},
    jackpot_events_played = [],
    $main_animation,
    win_odometer, win_id_odometer,
    $win_odometers = $('.win'),
    $jackpot_odometers = $('.jackpot'),
    $video, VIDEO_WIDTH, VIDEO_HEIGHT;
var event_id=0;

function initOdometer(show_cents){

    //if show cents is on then add to <head> sectio color styles
    if(query['show_cents'] == 1){
      if(query['cents_color']){
        cents_color = query['cents_color'];
      } else {
        cents_color = '#ff0000';
      }
      $('head').append('<style type="text/css">.jackpot .odometer-digit:nth-last-child(-n+2), #win-amount .odometer-digit:nth-last-child(-n+2) {    color: '+ cents_color +';} .jackpot .odometer-formatting-mark.odometer-radix-mark, #win-amount .odometer-formatting-mark.odometer-radix-mark {    color: '+ cents_color +';}</style>');
    }

    

    if($.inArray(window.location.pathname,spd) != -1){
        var lead_zeroes = 5;
    } else {
        var lead_zeroes = 0;
    }

    //show cents, if 1 then display decomal 
    if(show_cents == 1){
        var format = "(d).DD";
    } else {
        var format = "d";
    }

    $('.jackpot .odometer').each(function () {

        if($.inArray(window.location.pathname,spd) != -1){
            lead_zeroes--;
        }
        odometer = new Odometer({
            el: $(this)[0],
            value: 0,
            format: format,
            minIntegerLen: lead_zeroes,
            theme: 'default',
        });
        odometers.push(odometer);
        odometer.render();
    });

    win_odometer = new Odometer({
        el: $('#win-odometer')[0],
        value: 0,
        format: format,
        minIntegerLen: 0,
        theme: 'default',
        
    });

    win_id_odometer = new Odometer({
        el: $('#win-id-odometer')[0],
        value: 0,
        theme: 'default',
        format: "d",
    });
}


var query = function () {
    var query_string = {};
    var query = window.location.search.substring(1);
    var vars = query.split("&");
    for (var i = 0; i < vars.length; i++) {
        var pair = vars[i].split("=");
        if (typeof query_string[pair[0]] === "undefined") {
            query_string[pair[0]] = decodeURIComponent(pair[1]);
        } else if (typeof query_string[pair[0]] === "string") {
            var arr = [query_string[pair[0]], decodeURIComponent(pair[1])];
            query_string[pair[0]] = arr;
        } else {
            query_string[pair[0]].push(decodeURIComponent(pair[1]));
        }
    }
    //query_string['show_cents']=true;
    return query_string;
}();




var group_id = query['group_id'],
    password = '', init = true, timeout;

//first init odometers
initOdometer(1);


var won_queue_handler = function () {
    if (hit_play_queue.length > 0) {
        var hit_to_play = hit_play_queue.shift(),
            $video_to_play = hit_jackpots_ids[hit_to_play],
            win_data = jackpot_win_data.shift();
        $('.hit-animation').each(function () {
            $(this)[0].muted = true;
        });
        $video_to_play.bind('ended', function () {
            switchAnimation(1);
            $video_to_play.hide();
        });

        setTimeout(function () {

            $('.hit-animation').each(function () {
                $(this)[0].pause();
                $(this)[0].currentTime = 0;
                $(this).hide();
            });

            $video_to_play[0].muted = false;
            $video_to_play[0].play();

            $video_to_play.show();
            switchAnimation(2);

            hideWinnerElem();
            setTimeout(function (){
                showWinnerElem($video_to_play);
                fillWinOdometers(win_data,$video_to_play);
            },calcDelayWinnerElem());

        }, 200);
    }
};

function calcDelayWinnerElem(){

    var page = window.location.pathname;

    if(pd[page]){
        return pd[page];
    } else {
        return 0;
    }

}

function hideWinnerElem(){
    $('#win-amount').hide();
    $('#win-amount').attr('class','win');
    $('#winner-id').hide();
}

function showWinnerElem(video_to_play){

    var vp = $(video_to_play).attr('src');
    $('#win-amount').addClass(vp.replace('.','-'));
    $('#win-amount').show();
    $('#winner-id').show();
}


// group (1) - Main animation elements ; (2) - Hit animation elements
function switchAnimation(group) {
    [$main_animation, $jackpot_odometers, $win_odometers].forEach(function (item) {
        item.each(function () {
            var data_group = $(this).attr('data-group');
            $(this).toggle(data_group == group);
        });
    });
    stretchVideo();
}

function fillWinOdometers(win_data,video_to_play) {
    var vp = $(video_to_play).attr('src');

    if($.inArray(window.location.pathname,spd) != -1){

         switch (vp) {
            case 'big.ogv':
                renderJackpotAmount(0,win_data['credit'],win_data.winner);
                break;
            case 'medium.ogv':
                renderJackpotAmount(1,win_data['credit'],win_data.winner);
                break;
            case 'small.ogv':
                renderJackpotAmount(2,win_data['credit'],win_data.winner);
                break;
            default:
                win_odometer.options.minIntegerLen = 0;
                win_odometer.update(parseFloat(win_data['credit']));
                break;
        }

        resizeFonts();
        styleUpJackpots();

    } else {
        win_odometer.update(parseFloat(win_data['credit']));
        win_id_odometer.update(win_data.winner);
    }


    
}

function getUrlVars() {
    var vars = [], hash;
    var hashes = document.location.href.slice(document.location.href.indexOf('?') + 1).split('&');
    for (var i = 0; i < hashes.length; i++)
    {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
}

get=getUrlVars();

var login = function (callback) {
 
               
                show_cents=true;
                return callback(true);
        
   
};

function UpdateJack(data){


                var jackpots=JSON.parse(data);
              
                var communities=undefined;
                var won=[];
                if (Array.isArray(jackpots) && jackpots.length > 0) {
                    jackpots.sort(function(a,b) {
                        return jackpotsSort[a.name]-jackpotsSort[b.name];
                    });
                    jackpots.forEach(function (item, index) {

                    	if (item['type']!==undefined) {
                    		index=jackpotsSort[item['type']];
                    	} else {
                    		index=jackpotsSort[item['name']];
                    	}
                        if (item['dateTie']!==undefined) {
                        	if (savedJackpots[item['dateTie']]===undefined) {
                        		savedJackpots[item['dateTie']]=true;
                            	won.push({'jackpot_id':index,'event_id':event_id++,credit:item['jackpot'],winner:String(item['user'])});
                        	}
                            return;
                        }
                        if($.inArray(window.location.pathname,spd) != -1){
                            styleUpJackpots();
                        } else {
                          odometers[index].update(item['jackpot']);    
                          setTimeout(function () {
                            odometers[index].render();
                           }, 10000);  
                        }
                        resizeFonts(communities);

                        if (init) hit_jackpots_ids[index] = $('.hit-animation').eq(index);
                    });
                } 
                if (won.length > 0) {
                    won.forEach(function (item) {
                        if (jackpot_events_played.indexOf(item['event_id']) == -1) {
                            hit_play_queue.push(item['jackpot_id']);
                            jackpot_events_played.push(item['event_id']);
                            jackpot_win_data.push({credit: item['credit'], winner: item['winner']});
                        }
                    });
                }

                if (init) {
                    $('#password-wrapper').hide();
                    $('#jackpot-wrapper').show();
                    init = false;
                }
              
	
}


var updateJackpots = function (callback) {
    if (true) {
        $('#passwdBlock').not(':hidden').hide('fast');
        $.ajax({
            url: apiUrl,
            type: "GET",
            data: {'cmd':'jackpotShow', 'id': id},
            dataType: 'json',
            success: function (data) {
                var jackpots=data.content;
                var communities=undefined;
                var won=[];
                if (Array.isArray(jackpots) && jackpots.length > 0) {
                    jackpots.sort(function(a,b) {
                        return jackpotsSort[a.name]-jackpotsSort[b.name];
                    });
                    jackpots.forEach(function (item, index) {

                    	if (item['type']!==undefined) {
                    		index=jackpotsSort[item['type']];
                    	} else {
                    		index=jackpotsSort[item['name']];
                    	}
                        if (item['dateTie']!==undefined) {
                        	if (savedJackpots[item['dateTie']]===undefined) {
                        		savedJackpots[item['dateTie']]=true;
                            	won.push({'jackpot_id':index,'event_id':event_id++,credit:item['jackpot'],winner:String(item['user'])});
                        	}
                            return;
                        }
                        if($.inArray(window.location.pathname,spd) != -1){
                            styleUpJackpots();
                        } else {
                          odometers[index].update(item['jackpot']);    
                          setTimeout(function () {
                            odometers[index].render();
                           }, 10000);  
                        }
                        resizeFonts(communities);

                        if (init) hit_jackpots_ids[index] = $('.hit-animation').eq(index);
                    });
                } else {
                    alert('Incorrect server answer');
                    return false;
                }
                if (won.length > 0) {
                    won.forEach(function (item) {
                        if (jackpot_events_played.indexOf(item['event_id']) == -1) {
                            hit_play_queue.push(item['jackpot_id']);
                            jackpot_events_played.push(item['event_id']);
                            jackpot_win_data.push({credit: item['credit'], winner: item['winner']});
                        }
                    });
                }

                if (init) {
                    $('#password-wrapper').hide();
                    $('#jackpot-wrapper').show();
                    init = false;
                }
                timeout = setTimeout(updateJackpots, updateTime);
                if (callback != null) callback();
            },
            error: function () {
                timeout = setTimeout(updateJackpots, updateTime);
                if (callback != null) callback();
                //alert('Network error');
                //window.location.reload();

            }
        });
    } else {
        clearTimeout(timeout);
    }
};

function styleUpJackpots(){

    $('.jackpot').each(function(){
      flg_null = true;
      $(this).find('.odometer-value').each(function(){
        val = $(this).html();
        if(val != 0){
          flg_null = false;
        }
      });
      
      if(flg_null){
        $(this).hide();
      }
      
    });
}

function createAnimations() {
    var $wrapper = $('#video-wrapper'), $video;

    $wrapper.append($('<video/>').attr({src: main_video, loop: true, id: 'main-animation', 'data-group': 1}));
    
    hit_jackpots_videos.forEach(function (item) {
        $video = $('<video/>').attr({src: item, 'data-group': 2, class: 'hit-animation'});
        $wrapper.append($video);
    });
}

function showDuration(callback) {

    var totalDuration = 0;
    var totalBuffered = 0;
    var loaded = [];

    $('#progress-bar').show();
    $('video').each(function (index, item) {
        var video = $(this)[0],
            buffered;

        video.muted = true;
        video.play();
        totalDuration += video.duration;

        var interval = setInterval(function () {

            try {
                buffered = video.buffered.end(0);
            } catch (e) {

            }
            if (buffered != null) {
                loaded[index] = (buffered * 100 / video.duration).toFixed(1);
                if (buffered == video.duration) {
                    clearInterval(interval);
                }
            } else {
                loaded[index] = 0;
            }
        }, 100);

    });

    var interval = setInterval(function () {
        var percent = 0;

        loaded.forEach(function (item) {
            percent += parseFloat(item) / loaded.length;
        });

        $('#progress-bar').find('div').animate({width: percent + '%'}, 500);

        if (parseInt(percent) == 100) {
            clearInterval(interval);
            callback();
        }

    }, 1000);
}

function stretchVideo() {

    var widthRate = $(window).width() / VIDEO_WIDTH;
    var heightRate = $(window).height() / VIDEO_HEIGHT;

    $('video').each(function () {
        $(this).css({transform: 'matrix(' + widthRate + ', 0, 0, ' + heightRate + ', 0, 0)'});
        $(this).offset({left: 0, top: 0});
    });

    resizeFonts();
    styleUpJackpots();
}

function getCookie(name) {
    var matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));
    return matches ? decodeURIComponent(matches[1]) : '';
}

setInterval(won_queue_handler, 500);

$(function () {

    var lang = getCookie('langCountry').split('_');

    switch (lang[1]) {
        case '1':
            s_password = 'Пароль';
            s_proceed = 'Продолжить';
            break;

        case '2':
            s_password = 'Password';
            s_proceed = 'Proceed';
            break;
        case '4':
            s_password = 'Contrasena';
            s_proceed = 'Proceder';
            break;
        case '5':
            s_password = 'Password';
            s_proceed = 'Procedere';
            break;
        case '6':
            s_password = 'Senha';
            s_proceed = 'Proceder';
            break;
        case '7':
            s_password = 'Mot de passé';
            s_proceed = 'Procéder';
            break;
        default:
            s_password = 'Password';
            s_proceed = 'Proceed';
            break
    }

    $('#proceed-btn').html(s_proceed);
    $('#pwd').html(s_password);




});

function initAll() {
        login(function (success) {
          

                $('#password-wrapper').hide();

                createAnimations();

                $video = $('video');

                $main_animation = $('#main-animation');
                showDuration(function () {
                    updateJackpots(function () {
                        $('#jackpot-wrapper').show();
                        $('#progress-bar').hide();

                        $video.each(function () {
                            VIDEO_WIDTH = $(this).width();
                            VIDEO_HEIGHT = $(this).height();
                        });

                        switchAnimation(1);

                       
                        stretchVideo();

                        $(window).resize(stretchVideo);

                    });
                });
           
        });
}


function array_pad(input, pad_size, pad_value) {
 

  var pad = [],
    newArray = [],
    newLength,
    diff = 0,
    i = 0;

  if (Object.prototype.toString.call(input) === '[object Array]' && !isNaN(pad_size)) {
    newLength = ((pad_size < 0) ? (pad_size * -1) : pad_size);
    diff = newLength - input.length;

    if (diff > 0) {
      for (i = 0; i < diff; i++) {
        newArray[i] = pad_value;
      }
      pad = ((pad_size < 0) ? newArray.concat(input) : input.concat(newArray));
    } else {
      pad = input;
    }
  }

  return pad;
}

function renderWinnerID(index,winner_id){

        winner_id = winner_id.toString();
        jp_rows = ['big','mid','sml'];
        jp_max_symb = [8,7,6];

        jp_row = jp_rows[index];

        amount_arr = winner_id.split("");

        cnt = 0;

        wrapper = $('#winner-id .odometer-inside');
        wrapper.html('');

        for (var i = 0; i < amount_arr.length ; i++) {

            val = amount_arr[i];

            dom = '<span class="odometer-digit"> <span class="odometer-digit-spacer"></span> <span class="odometer-digit-inner"><span class="odometer-ribbon"> <span class="odometer-ribbon-inner"> <span class="odometer-value">';

            dom += "<img src='../img/digits/Digits_" + jp_row + "/"+ val +".png'>";

            dom += '</span> </span> </span> </span> </span>';

            $(wrapper).append(dom);


        }


    }

    function renderJackpotAmount(index,amount,winner_id){

        amount = amount.toString();

        if(winner_id !== undefined){
            renderWinnerID(index,winner_id);
            wrapper = $('#win-amount .odometer-inside');
        } else {
            wrapper = $('#jackpot_0' + (index + 1) + ' .odometer-inside');    
        }

        // return;
        jp_rows = ['big','mid','sml'];
          
        jp_row = jp_rows[index];
        amount_arr = amount.split("");

        if(query['show_cents'] != 1){
            for (var i = 0; i < 3 ; i++) {
                amount_arr.pop();
            }

            jp_max_symb = [7,6,5];
        } else {
            jp_max_symb = [8,7,6];
        }

        if(window.location.pathname == "/jackpot_plasma/present/index.html"){
            jp_max_symb[2] += 1;
        }



        cnt = 0;
        
        wrapper.html('');

        flag = false; //float flag

        //if value < symbols at row
        if(amount_arr.length < jp_max_symb[index]){
            amount_arr = array_pad(amount_arr,-jp_max_symb[index],'#');
        }

        // debugger;               

        for (var i = 0; i < amount_arr.length ; i++) {


            val = amount_arr[cnt];
            if (val === undefined){
              return;
            }

            dom = '<span class="odometer-digit"> <span class="odometer-digit-spacer"></span> <span class="odometer-digit-inner"><span class="odometer-ribbon"> <span class="odometer-ribbon-inner"> <span class="odometer-value">';

            
            if (val == '#'){
                dom += '<span style="visibility:hidden">#</span>';
            }
            else if(val == '.'){

              flag = true;
              cnt++;
              val = amount_arr[cnt];

              dom += "<img class='_p' src='../img/digits/Digits_" + jp_row + "/" + val +"_p.png'>";
            } else if(flag === true) {
              dom += "<img class='_s' src='../img/digits/Digits_" + jp_row + "/" + val +"_s.png'>";
            } else {
              dom += "<img src='../img/digits/Digits_" + jp_row + "/" + val +".png'>";
            }

            dom += '</span> </span> </span> </span> </span>';

            $(wrapper).append(dom);
               
            cnt++;
        }

    }