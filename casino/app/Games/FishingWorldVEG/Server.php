<?php 
namespace VanguardLTE\Games\FishingWorldVEG
{
    set_time_limit(5);
    class Server
    {
        public function get($request, $game)
        {
            function get_($request, $game)
            {
                \DB::transaction(function() use ($request, $game)
                {
                    try
                    {
                        $userId = \Auth::id();
                        if( $userId == null ) 
                        {
                            $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid login"}';
                            exit( $response );
                        }
                        $slotSettings = new SlotSettings($game, $userId);
                        if( !$slotSettings->is_active() ) 
                        {
                            $response = '{"responseEvent":"error","responseType":"","serverResponse":"Game is disabled"}';
                            exit( $response );
                        }
                        $postData = json_decode(trim(file_get_contents('php://input')), true);
                        if( isset($postData['command']) && $postData['command'] == 'CheckAuth' ) 
                        {
                            $response = '{"responseEvent":"CheckAuth","startTimeSystem":' . (time() * 1000) . ',"userId":' . $userId . ',"shop_id":' . $slotSettings->shop_id . ',"username":"' . $slotSettings->username . '"}';
                            exit( $response );
                        }
                        $result_tmp = [];
                        $balanceInCents = round(sprintf('%01.2f', $slotSettings->GetBalance()) * 100);
                        if( isset($_GET['command']) ) 
                        {
                            $aid = $_GET['command'];
                        }
                        else
                        {
                            $aid = $postData['gameData']['router'];
                        }
                        $curTime = floor(microtime(true) * 1000);
                        switch( $aid ) 
                        {
                            case 'sync':
                                $result_tmp[0] = '{"code":200,"data":{"player":{"id":30731,"uuid":"17a5e5d9-e394-f600-d11c-d5d92c546bd7","account":"BJ0021956936","nickname":"' . $slotSettings->username . '","language":"en_US","walletType":0,"balance":0,"exchange":0,"agent":"guest","trial":1,"coins":0,"currency":"USD","items":{"novice":{"1000":0,"1001":0,"1002":0,"1003":0},"elite":{"1000":0,"1001":0,"1002":0,"1003":0},"master":{"1000":0,"1001":0,"1002":0,"1003":0},"legend":{"1000":0,"1001":0,"1002":0,"1003":0},"emperor":{"1000":0,"1001":0,"1002":0,"1003":0},"god":{"1000":0,"1001":0,"1002":0,"1003":0}},"happyTimeArray":{"novice":{"_events":null,"roomType":"novice","count":0,"endTime":0,"rewardRateIndex":0,"isCanOpenHappyTime":false,"shellLevel":0},"elite":{"_events":null,"roomType":"elite","count":0,"endTime":0,"rewardRateIndex":0,"isCanOpenHappyTime":false,"shellLevel":0},"master":{"_events":null,"roomType":"master","count":0,"endTime":0,"rewardRateIndex":0,"isCanOpenHappyTime":false,"shellLevel":0},"legend":{"_events":null,"roomType":"legend","count":0,"endTime":0,"rewardRateIndex":0,"isCanOpenHappyTime":false,"shellLevel":0},"emperor":{"_events":null,"roomType":"emperor","count":0,"endTime":0,"rewardRateIndex":0,"isCanOpenHappyTime":false,"shellLevel":0},"god":{"_events":null,"roomType":"god","count":0,"endTime":0,"rewardRateIndex":0,"isCanOpenHappyTime":false,"shellLevel":0}},"agentType":"guest"},"token":"eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6Mzg2ODgyLCJjb3VudHJ5IjpudWxsLCJ1dWlkIjoiODBhN2RlNjEtMzNjZS0xZDNkLTQyMWUtNjM4YTA1YzI4NmZkIiwiYWNjb3VudCI6IkJKMDAyMTk1NjkzNiIsImFnZW50Ijoic2dnIiwiYWdlbnRUeXBlIjoibmV3Z2ciLCJuaWNrbmFtZSI6IjE5NTY5MzYiLCJnYW1lSWQiOiIxMTAiLCJzZXNzaW9uSWQiOiJuY2pHQXpuRlQ2aTB0alZaaFJsVWxPbmZjQ01zNytOSS9yZW1RbzM0d2x4MTZEaDhVWjlWSlRDRUtUMkFCeGduIiwiY3VycmVuY3kiOiJVU0QiLCJ0cmlhbCI6MCwiaXAiOiIxNzguMTg0LjI4LjIxNCwgNDcuNTYuMTEyLjc2Iiwib3JpZ2luIjoiMCIsImlhdCI6MTU5NjM3ODM2MywiZXhwIjoxNTk2Mzk5OTYzLCJhdWQiOiJVc2VyIiwiaXNzIjoiRmlzaGluZyBJbmMuIiwic3ViIjoiQWNjZXNzVG9rZW4iLCJqdGkiOiIyMzg1ODA3MC02YmU5LTRlOTctOWYyYi1kMmVjMWY4MDkyZjEifQ.ypFb4XVzRiaL1JkwDG_kRW4K467mbeap6n106Nf8tsA","millisecond":' . $curTime . ',"currency":{"ratio":1,"unit":1,"currency":"USD","name":"人民币"}},"timestamp":"' . $curTime . '"}';
                                break;
                            case 'get-bulletins':
                                $result_tmp[0] = '{"code":200,"data":[],"timestamp":"' . $curTime . '"}';
                                break;
                            case 'balance':
                                $result_tmp[0] = '{"code":200,"data":{"code":0,"money":' . $balanceInCents . ',"balance":' . $balanceInCents . ',"activity":{"10001":false,"10002":false,"10003":false,"10004":false,"10005":false,"10006":false}},"timestamp":"' . $curTime . '"}';
                                break;
                            case 'lobby':
                                $result_tmp[0] = '{"code":200,"data":{},"timestamp":"' . $curTime . '"}';
                                break;
                            case 'room':
                                $result_tmp[0] = '{"code":200,"data":{},"timestamp":"' . $curTime . '"}';
                                break;
                            case 'get-notice':
                                $result_tmp[0] = '';
                                break;
                            case 'get-bill-list':
                                $result_tmp[0] = '{"code":200,"data":{"data":{"upRecords":[{"subData":{"sbgameid":"a7de1602-de77-4e3e-9577-cf95c3ff072b","remainingamount":0},"id":15359765,"billno":"ABCDEFGsgg202008022054050664057363","sectionId":"202008022054008860560808","uuid":"80a7de61-33ce-1d3d-421e-638a05c286fd","account":"BJ0021956936","nickname":"' . $slotSettings->username . '","agent":"sgg","agentType":"newgg","sid":"ncjGAznFT6i0tjVZhRlUlOnfcCMs7+NI/remQo34wlx16Dh8UZ9VJTCEKT2ABxgn","credit":138,"turnover":0,"winloss":0,"ip":"","origin":0,"trial":0,"closeFlag":1,"balance":690,"currency":"USD","country":"zh-CN","retry":0,"shellCount":0,"chair":2,"roomId":"202008021316259990089155","warning":0,"state":2,"type":0,"createdAt":"2020-08-02T12:54:05.000Z","updatedAt":"2020-08-02T12:54:05.000Z","warningAt":null,"reviewAt":null},{"subData":{"sbgameid":"0b86d633-f884-40b1-89fb-f966e3f9d664","remainingamount":0},"id":15359899,"billno":"ABCDEFGsgg202008022059510026340786","sectionId":"202008022059460410234950","uuid":"80a7de61-33ce-1d3d-421e-638a05c286fd","account":"BJ0021956936","nickname":"' . $slotSettings->username . '","agent":"sgg","agentType":"newgg","sid":"ncjGAznFT6i0tjVZhRlUlOnfcCMs7+NI/remQo34wlx16Dh8UZ9VJTCEKT2ABxgn","credit":134,"turnover":0,"winloss":0,"ip":"","origin":0,"trial":0,"closeFlag":1,"balance":670,"currency":"USD","country":"zh-CN","retry":0,"shellCount":0,"chair":0,"roomId":"202004251202448250256047","warning":0,"state":2,"type":0,"createdAt":"2020-08-02T12:59:52.000Z","updatedAt":"2020-08-02T12:59:52.000Z","warningAt":null,"reviewAt":null},{"subData":{"sbgameid":"124bb2e0-6309-49e5-8422-388d375028e9","remainingamount":0},"id":15360633,"billno":"ABCDEFGsgg202008022128010088390485","sectionId":"202008022127271450026027","uuid":"80a7de61-33ce-1d3d-421e-638a05c286fd","account":"BJ0021956936","nickname":"' . $slotSettings->username . '","agent":"sgg","agentType":"newgg","sid":"ncjGAznFT6i0tjVZhRlUlOnfcCMs7+NI/remQo34wlx16Dh8UZ9VJTCEKT2ABxgn","credit":134,"turnover":0,"winloss":0,"ip":"","origin":0,"trial":0,"closeFlag":1,"balance":670,"currency":"USD","country":"zh-CN","retry":0,"shellCount":0,"chair":1,"roomId":"202006300425128890715908","warning":0,"state":2,"type":0,"createdAt":"2020-08-02T13:28:02.000Z","updatedAt":"2020-08-02T13:28:02.000Z","warningAt":null,"reviewAt":null}],"downRecords":[{"subData":{"sbgameid":"a7de1602-de77-4e3e-9577-cf95c3ff072b","remainingamount":0,"leave":1},"id":15359846,"billno":"ABCDEFGsgg202008022057160755507420","sectionId":"202008022054008860560808","uuid":"80a7de61-33ce-1d3d-421e-638a05c286fd","account":"BJ0021956936","nickname":"' . $slotSettings->username . '","agent":"sgg","agentType":"newgg","sid":"ncjGAznFT6i0tjVZhRlUlOnfcCMs7+NI/remQo34wlx16Dh8UZ9VJTCEKT2ABxgn","credit":134,"turnover":70,"winloss":-10,"ip":"","origin":0,"trial":0,"closeFlag":1,"balance":670,"currency":"USD","country":"zh-CN","retry":0,"shellCount":1,"chair":2,"roomId":"202008021316259990089155","warning":0,"state":2,"type":1,"createdAt":"2020-08-02T12:57:16.000Z","updatedAt":"2020-08-02T12:57:18.000Z","warningAt":null,"reviewAt":null},{"subData":{"sbgameid":"0b86d633-f884-40b1-89fb-f966e3f9d664","remainingamount":0,"leave":1},"id":15360027,"billno":"ABCDEFGsgg202008022104580991049858","sectionId":"202008022059460410234950","uuid":"80a7de61-33ce-1d3d-421e-638a05c286fd","account":"BJ0021956936","nickname":"' . $slotSettings->username . '","agent":"sgg","agentType":"newgg","sid":"ncjGAznFT6i0tjVZhRlUlOnfcCMs7+NI/remQo34wlx16Dh8UZ9VJTCEKT2ABxgn","credit":134,"turnover":0,"winloss":0,"ip":"","origin":0,"trial":0,"closeFlag":1,"balance":670,"currency":"USD","country":"zh-CN","retry":0,"shellCount":1,"chair":0,"roomId":"202004251202448250256047","warning":0,"state":2,"type":1,"createdAt":"2020-08-02T13:04:58.000Z","updatedAt":"2020-08-02T13:05:00.000Z","warningAt":null,"reviewAt":null},{"subData":{"sbgameid":"124bb2e0-6309-49e5-8422-388d375028e9","remainingamount":0,"leave":1},"id":15360651,"billno":"ABCDEFGsgg202008022128520373574364","sectionId":"202008022127271450026027","uuid":"80a7de61-33ce-1d3d-421e-638a05c286fd","account":"BJ0021956936","nickname":"' . $slotSettings->username . '","agent":"sgg","agentType":"newgg","sid":"ncjGAznFT6i0tjVZhRlUlOnfcCMs7+NI/remQo34wlx16Dh8UZ9VJTCEKT2ABxgn","credit":134,"turnover":0,"winloss":0,"ip":"","origin":0,"trial":0,"closeFlag":1,"balance":670,"currency":"USD","country":"zh-CN","retry":0,"shellCount":1,"chair":1,"roomId":"202006300425128890715908","warning":0,"state":2,"type":1,"createdAt":"2020-08-02T13:28:52.000Z","updatedAt":"2020-08-02T13:28:53.000Z","warningAt":null,"reviewAt":null}]}},"timestamp":"' . $curTime . '"}';
                                break;
                            case 'room.handshake':
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentBet', 1);
                                $slotSettings->SetGameData($slotSettings->slotId . 'batteryLevel', 0);
                                $result_tmp[0] = '{"id":' . $postData['gameData']['id'] . ',"router":"room.handshake","code":200,"data":{"player":{"id":386882,"account":"BJ0021956936","agent":"sgg","website":"","agentType":"newgg","trial":0,"uuid":"80a7de61-33ce-1d3d-421e-638a05c286fd","nickname":"' . $slotSettings->username . '","balance":0,"language":"en_US","currency":"USD","country":"zh-CN","roomId":null,"roomUUID":"202008022158165150493602","battery":0,"batteryLevel":0,"lockFishUUID":null,"isFree":false,"freeStartTime":0,"isRage":false,"rageStartTime":0,"isDouble":false,"doubleStartTime":0,"allConsume":410,"allAward":420,"asset":10,"happyTimeArray":{"novice":{"roomType":"novice","count":0,"endTime":0,"rewardRateIndex":0,"isCanOpenHappyTime":false,"shellLevel":0},"elite":{"roomType":"elite","count":0,"endTime":0,"rewardRateIndex":0,"isCanOpenHappyTime":false,"shellLevel":0},"master":{"roomType":"master","count":0,"endTime":0,"rewardRateIndex":0,"isCanOpenHappyTime":false,"shellLevel":0},"legend":{"roomType":"legend","count":0,"endTime":0,"rewardRateIndex":0,"isCanOpenHappyTime":false,"shellLevel":0},"emperor":{"roomType":"emperor","count":0,"endTime":0,"rewardRateIndex":0,"isCanOpenHappyTime":false,"shellLevel":0},"god":{"roomType":"god","count":0,"endTime":0,"rewardRateIndex":0,"isCanOpenHappyTime":false,"shellLevel":0}},"machineCannonList":{"novice":{"roomType":"novice","count":0,"batteryLevel":0},"elite":{"roomType":"elite","count":0,"batteryLevel":0},"master":{"roomType":"master","count":0,"batteryLevel":0},"legend":{"roomType":"legend","count":0,"batteryLevel":0},"emperor":{"roomType":"emperor","count":0,"batteryLevel":0},"god":{"roomType":"god","count":0,"batteryLevel":0}},"isOpenBlackHole":false,"blackHoleFishPos":null,"isOpenWealthGod":false,"wealthGodInfo":{},"roomOwner":false,"items":{"novice":{"1000":0,"1001":0,"1002":0,"1003":0},"elite":{"1000":0,"1001":0,"1002":0,"1003":0},"master":{"1000":0,"1001":0,"1002":0,"1003":0},"legend":{"1000":0,"1001":0,"1002":0,"1003":0},"emperor":{"1000":0,"1001":0,"1002":0,"1003":0},"god":{"1000":0,"1001":0,"1002":0,"1003":0}},"lastSendShellTime":0,"oldBalance":0,"lastIp":"","origin":0,"sessionId":"ncjGAznFT6i0tjVZhRlUlOnfcCMs7+NI/remQo34wlx16Dh8UZ9VJTCEKT2ABxgn","sectionId":"202008030746105650086029","lastSummaryLogTime":0,"tempRoomType":"","lastFireTime":' . $curTime . ',"roomConsume":{"novice":410},"walletType":1,"shellIndex":1,"sid":"202008030746105650086029100001"},"heartbeat":15}}';
                                break;
                            case 'room.changeBatteryLevel':
                                $CurrentBet = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentBet');
                                $batteryLevel = $slotSettings->GetGameData($slotSettings->slotId . 'batteryLevel');
                                $CurrentRoom = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentRoom');
                                $batteryLevel = $postData['gameData']['data']['level'];
                                if( $CurrentRoom == 'novice' ) 
                                {
                                    $bets = [
                                        10, 
                                        20, 
                                        50, 
                                        100
                                    ];
                                }
                                else if( $CurrentRoom == 'elite' ) 
                                {
                                    $bets = [
                                        100, 
                                        200, 
                                        500, 
                                        1000
                                    ];
                                }
                                else if( $CurrentRoom == 'master' ) 
                                {
                                    $bets = [
                                        200, 
                                        400, 
                                        1000, 
                                        2000
                                    ];
                                }
                                else if( $CurrentRoom == 'legend' ) 
                                {
                                    $bets = [
                                        1000, 
                                        2000, 
                                        5000, 
                                        10000
                                    ];
                                }
                                else if( $CurrentRoom == 'emperor' ) 
                                {
                                    $bets = [
                                        5000, 
                                        10000, 
                                        25000, 
                                        50000
                                    ];
                                }
                                else if( $CurrentRoom == 'god' ) 
                                {
                                    $bets = [
                                        10000, 
                                        20000, 
                                        50000, 
                                        100000
                                    ];
                                }
                                $CurrentBet = $bets[$batteryLevel];
                                $result_tmp[0] = '{"id":' . $postData['gameData']['id'] . ',"router":"room.changeBatteryLevel","code":200,"data":{"code":"player_update","data":{"uuid":"80a7de61-33ce-1d3d-421e-638a05c286fd","battery":5,"batteryLevel":' . $batteryLevel . ',"roomId":"202008022158165150493602"},"playerUUIDArray":["7df26088-8fc2-2760-c9b7-0244e4182fdb","38f76004-55c2-779b-7ca2-4389246cfeaa","dac7171d-c0ea-3bc8-fc13-0fdec1ace5bc"],"player":{"uuid":"80a7de61-33ce-1d3d-421e-638a05c286fd","battery":5,"batteryLevel":' . $batteryLevel . ',"roomId":"202008022158165150493602"}}}';
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentBet', $CurrentBet);
                                $slotSettings->SetGameData($slotSettings->slotId . 'batteryLevel', $batteryLevel);
                                break;
                            case 'room.checkEnterGame':
                                $result_tmp[0] = '{"id":' . $postData['gameData']['id'] . ',"router":"room.checkEnterGame","code":200,"data":{}}';
                                break;
                            case 'room.transferBalance':
                                $result_tmp[0] = '{"id":' . $postData['gameData']['id'] . ',"router":"room.transferBalance","code":200,"data":{"balance":' . $balanceInCents . '}}';
                                $result_tmp[1] = '{"code":200,"data":{"code":"player_update","data":{"uuid":"80a7de61-33ce-1d3d-421e-638a05c286fd","battery":5,"balance":' . $balanceInCents . ',"roomId":"202008022158165150493602"}}}';
                                break;
                            case 'room.quitRoom':
                                $result_tmp[0] = '{"id":' . $postData['gameData']['id'] . ',"router":"room.quitRoom","code":200,"data":{"player":{"happyTimeArray":{"novice":{"_events":null,"roomType":"novice","count":0,"endTime":0,"rewardRateIndex":0,"isCanOpenHappyTime":false,"shellLevel":0},"elite":{"_events":null,"roomType":"elite","count":0,"endTime":0,"rewardRateIndex":0,"isCanOpenHappyTime":false,"shellLevel":0},"master":{"_events":null,"roomType":"master","count":0,"endTime":0,"rewardRateIndex":0,"isCanOpenHappyTime":false,"shellLevel":0},"legend":{"_events":null,"roomType":"legend","count":0,"endTime":0,"rewardRateIndex":0,"isCanOpenHappyTime":false,"shellLevel":0},"emperor":{"_events":null,"roomType":"emperor","count":0,"endTime":0,"rewardRateIndex":0,"isCanOpenHappyTime":false,"shellLevel":0},"god":{"_events":null,"roomType":"god","count":0,"endTime":0,"rewardRateIndex":0,"isCanOpenHappyTime":false,"shellLevel":0}}}}}';
                                break;
                            case 'room.requestEnterRoom':
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentRoom', $postData['gameData']['data']['roomType']);
                                $CurrentBet = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentBet');
                                $batteryLevel = $slotSettings->GetGameData($slotSettings->slotId . 'batteryLevel');
                                $CurrentRoom = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentRoom');
                                if( $CurrentRoom == 'novice' ) 
                                {
                                    $bets = [
                                        10, 
                                        20, 
                                        50, 
                                        100
                                    ];
                                }
                                else if( $CurrentRoom == 'elite' ) 
                                {
                                    $bets = [
                                        100, 
                                        200, 
                                        500, 
                                        1000
                                    ];
                                }
                                else if( $CurrentRoom == 'master' ) 
                                {
                                    $bets = [
                                        200, 
                                        400, 
                                        1000, 
                                        2000
                                    ];
                                }
                                else if( $CurrentRoom == 'legend' ) 
                                {
                                    $bets = [
                                        1000, 
                                        2000, 
                                        5000, 
                                        10000
                                    ];
                                }
                                else if( $CurrentRoom == 'emperor' ) 
                                {
                                    $bets = [
                                        5000, 
                                        10000, 
                                        25000, 
                                        50000
                                    ];
                                }
                                else if( $CurrentRoom == 'god' ) 
                                {
                                    $bets = [
                                        10000, 
                                        20000, 
                                        50000, 
                                        100000
                                    ];
                                }
                                $CurrentBet = $bets[$batteryLevel];
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentBet', $CurrentBet);
                                $slotSettings->SetGameData($slotSettings->slotId . 'batteryLevel', $batteryLevel);
                                $result_tmp[] = '{"code":200,"data":{"code":"player_enter_room","data":[{"userId":386882,"uuid":"80a7de61-33ce-1d3d-421e-638a05c286fd","nickname":"' . $slotSettings->username . '","balance":0,"battery":4,"batteryLevel":0,"machineCannonInfo":{"_events":null,"roomType":"novice","count":0,"batteryLevel":0},"isOpenWealthGod":false}]}}';
                                $result_tmp[] = "{\r\n  \"id\": " . $postData['gameData']['id'] . ",\r\n  \"router\": \"room.requestEnterRoom\",\r\n  \"code\": 200,\r\n  \"data\": {\r\n    \"bgIndex\": 4,\r\n    \"playMusicList\": {\r\n      \"0\": 7\r\n    },\r\n    \"bossEndTime\": 0,\r\n    \"bossType\": 0,\r\n    \"roomUUID\": \"202008022158165150493602\",\r\n    \"roomType\": \"novice\",\r\n    \"roundType\": 1,\r\n    \"roundEndTime\": " . ($curTime + 20000) . ",\r\n    \"iceItemEndTime\": 1596430357,\r\n    \"batteryIndex\": 4,\r\n    \"fishArray\": []\r\n  }\r\n}";
                                break;
                            case 'room.queryBalance':
                                $result_tmp[0] = '{"id":' . $postData['gameData']['id'] . ',"router":"room.queryBalance","code":200,"data":{"balance":' . $balanceInCents . '}}';
                                break;
                            case 'room.heartBeat':
                                $result_tmp[0] = '{"id":' . $postData['gameData']['id'] . ',"router":"room.heartBeat","code":200,"data":{}}';
                                break;
                            case 'room.hit':
                                $CurrentBet = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentBet');
                                $allbet = $CurrentBet / 100;
                                if( $allbet <= $slotSettings->GetBalance() ) 
                                {
                                    $bankSum = $allbet / 100 * $slotSettings->GetPercent();
                                    $slotSettings->UpdateJackpots($allbet);
                                    $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, 'bet');
                                    $slotSettings->SetBalance(-1 * $allbet, 'bet');
                                }
                                else
                                {
                                    $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid balance"}';
                                    exit( $response );
                                }
                                $bulletid = $postData['gameData']['data']['shellUUID'];
                                $fishids = $postData['gameData']['data']['fishUUIDs'];
                                $fishpids = [];
                                foreach( $fishids as $fid ) 
                                {
                                    $t_fid = explode('-', $fid);
                                    $fishpids[] = $t_fid[1];
                                }
                                $CurrentBet = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentBet');
                                $allbet = $CurrentBet / 100;
                                $totalWin = 0;
                                $totalWinsArr = [];
                                $totalWinsArr2 = [];
                                $bank = $slotSettings->GetBank('');
                                $isBombId = 0;
                                $isBomb = 0;
                                $isBombWin = 0;
                                for( $i = 0; $i < count($fishpids); $i++ ) 
                                {
                                    $fishType = $fishpids[$i];
                                    if( $fishType == 21 ) 
                                    {
                                        $isBombId = $fishids[$i];
                                        $isBomb = 1;
                                        $isBombWin = rand(1, $slotSettings->FishDamage['Fish_' . $fishType][0]);
                                    }
                                }
                                $i = 0;
                                while( $i < count($fishpids) ) 
                                {
                                    $fishType = $fishpids[$i];
                                    if( count($slotSettings->Paytable['Fish_' . $fishType]) > 1 ) 
                                    {
                                        $payRate = rand($slotSettings->Paytable['Fish_' . $fishType][0], $slotSettings->Paytable['Fish_' . $fishType][1]);
                                    }
                                    else
                                    {
                                        $payRate = $slotSettings->Paytable['Fish_' . $fishType][0];
                                    }
                                    $isWin = rand(1, $slotSettings->FishDamage['Fish_' . $fishType][0]);
                                    if( $isBomb && $isBombWin == 1 ) 
                                    {
                                        if( $isBombId == $fishids[$i] ) 
                                        {
                                        }
                                        else
                                        {
                                            $isWin = 1;
                                        }
                                        $i++;
                                    }
                                    if( $isWin == 1 && $totalWin + ($payRate * $allbet) <= $bank ) 
                                    {
                                        $totalWin += ($payRate * $allbet);
                                        $totalWinsArr[] = '"' . $fishids[$i] . '":{"reward":' . round($payRate * $allbet * 100) . '}';
                                        $totalWinsArr2[] = '"' . $fishids[$i] . '"';
                                    }
                                }
                                if( $totalWin > 0 ) 
                                {
                                    $slotSettings->SetBank('', -1 * $totalWin);
                                    $slotSettings->SetBalance($totalWin);
                                }
                                $balanceInCents = round(sprintf('%01.2f', $slotSettings->GetBalance()) * 100);
                                $result_tmp[] = '{"id":' . $postData['gameData']['id'] . ',"router":"room.hit","code":200,"data":{"deadFishUUIDArray":[' . implode(',', $totalWinsArr2) . '],"deadFishRewardArray":{' . implode(',', $totalWinsArr) . '},"balance":' . $balanceInCents . '}}';
                                $response = '{"responseEvent":"spin","responseType":"bet","serverResponse":{"slotBet":0,"totalFreeGames":0,"currentFreeGames":0,"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":0,"totalWin":' . $totalWin . ',"winLines":[],"Jackpots":[],"reelsSymbols":[]}}';
                                $slotSettings->SaveLogReport($response, $allbet, 1, $totalWin, 'bet');
                                break;
                        }
                        $slotSettings->SaveGameData();
                        echo implode('------', $result_tmp);
                    }
                    catch( \Exception $e ) 
                    {
                        if( isset($slotSettings) ) 
                        {
                            $slotSettings->InternalErrorSilent($e);
                        }
                        else
                        {
                            $strLog = '';
                            $strLog .= "\n";
                            $strLog .= ('{"responseEvent":"error","responseType":"' . $e . '","serverResponse":"InternalError","request":' . json_encode($_REQUEST) . ',"requestRaw":' . file_get_contents('php://input') . '}');
                            $strLog .= "\n";
                            $strLog .= ' ############################################### ';
                            $strLog .= "\n";
                            $slg = '';
                            if( file_exists(storage_path('logs/') . 'GameInternal.log') ) 
                            {
                                $slg = file_get_contents(storage_path('logs/') . 'GameInternal.log');
                            }
                            file_put_contents(storage_path('logs/') . 'GameInternal.log', $slg . $strLog);
                        }
                    }
                }, 5);
            }
            get_($request, $game);
        }
    }

}
