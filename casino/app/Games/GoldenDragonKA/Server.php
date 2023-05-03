<?php 
namespace VanguardLTE\Games\GoldenDragonKA
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
                        $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance());
                        $result_tmp = [];
                        $aid = '';
                        $aid = (string)$postData['action'];
                        switch( $aid ) 
                        {
                            case 'connector.accountHandler.twLogin':
                                $gameBets = $slotSettings->Bet;
                                $denoms = [];
                                $denoms[] = '' . ($slotSettings->CurrentDenom * 100) . '';
                                foreach( $slotSettings->Denominations as $b ) 
                                {
                                    $denoms[] = '' . ($b * 100) . '';
                                }
                                $result_tmp[0] = '{"code":200,"responseView":[4,0,0,0,4,1],"answerType":"","data":{"nickName":"' . $slotSettings->username . '","gender":1,"avatarUrl":"","playerId":"d607e29f-99cc-48bc-a37d-5590b80fa0f6","twSSOId":"accessKey|' . $slotSettings->slotCurrency . '|766764546","parentId":"","state":0,"role":0,"locale":"en","creditAmount":' . $balanceInCents . ',"creditCode":"' . $slotSettings->slotCurrency . '","rmpCannonCost":[1,2,3,5,8,10,10,20,30,50,80,100,100,200,300,500,800,1000],"denom":0.01,"currencySymbol":"' . $slotSettings->slotCurrency . '","currencyFractionDigits":2,"currencySymbolInBack":false,"thousandGroupingSepartor":",","decimalSeparator":".","transactionBufferSize":5,"transactionBufferMilliseconds":1000,"rmpCredit":' . $balanceInCents . ',"roomLevel":0,"cannonlevel":0,"token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1aWQiOiJlM2UyODkyYS1kZjQ4LTRkNTMtODI1Zi0zNDA3ZDg4N2ZiZjkiLCJpYXQiOjE1ODQyODM3NTcsImV4cCI6MTU4NDM3MDE1N30.zoeZ7VmLyP1GESKmfPx89a_JRmqGoTiaaDNOQZylJwlyAHrszs-DxjdyrvXByi1Iyg0ELrciQLUX53iHPkETY11YNjkwKapQgCZVKK3OiTjK_qtVUBVYy8N420LSHaYK7D_L4z-GTD_XSnZFWfI0xlmT5QgshUSNvnkybFpGIgk","recommendedGames":[],"openRecommendedGamesInNewWindow":false,"ip":"::ffff:127.0.0.1:52678","realip":"","gameServerId":"player-server-3","gameId":"10007","tableId":""}}';
                                break;
                            case 'playerControl.tableHandler.searchTableAndJoin':
                                $slotSettings->SetGameData('GoldenDragonKABullets', []);
                                $slotSettings->SetGameData('GoldenDragonKAFishes', []);
                                $slotSettings->SetGameData('GoldenDragonKAWaveTime', time());
                                $slotSettings->SetGameData('GoldenDragonKACurScene', 0);
                                $slotSettings->SetGameData('GoldenDragonKAIsGroupFish', 0);
                                $slotSettings->SetGameData('GoldenDragonKAGamePause', time());
                                if( $postData['query']['level'] == 0 ) 
                                {
                                    $slotSettings->SetGameData('GoldenDragonKABet', 0.01);
                                    $slotSettings->SetGameData('GoldenDragonKABetCnt', 0);
                                    $slotSettings->SetGameData('GoldenDragonKABetArr', [
                                        0.01, 
                                        0.02, 
                                        0.03, 
                                        0.05, 
                                        0.08, 
                                        0.1
                                    ]);
                                }
                                else if( $postData['query']['level'] == 1 ) 
                                {
                                    $slotSettings->SetGameData('GoldenDragonKABet', 0.1);
                                    $slotSettings->SetGameData('GoldenDragonKABetCnt', 0);
                                    $slotSettings->SetGameData('GoldenDragonKABetArr', [
                                        0.1, 
                                        0.2, 
                                        0.3, 
                                        0.5, 
                                        0.8, 
                                        1
                                    ]);
                                }
                                else if( $postData['query']['level'] == 2 ) 
                                {
                                    $slotSettings->SetGameData('GoldenDragonKABet', 1);
                                    $slotSettings->SetGameData('GoldenDragonKABetCnt', 0);
                                    $slotSettings->SetGameData('GoldenDragonKABetArr', [
                                        1, 
                                        2, 
                                        3, 
                                        5, 
                                        8, 
                                        10
                                    ]);
                                }
                                $Bet = $slotSettings->GetGameData('GoldenDragonKABet');
                                $fBets = $slotSettings->GetGameData('GoldenDragonKABetArr');
                                foreach( $fBets as &$fb ) 
                                {
                                    $fb = $fb * 100;
                                }
                                $slotSettings->SetGameData('GoldenDragonKABetLevel', $postData['query']['level']);
                                $result_tmp[0] = '{"answerType":"game.start","Balance":' . $balanceInCents . ',"curBet":' . $slotSettings->GetGameData('GoldenDragonKABet') . ',"responseView":[4,0,0,0,6,' . strlen('game.start') . '],"msg":{"area":{"id":"7909d963c43c6df71d7f7ee17bfc4b5133809d80342e2f645d01e16119eec1ad-connector-server-3","scene":0,"state":"started","pauseTime":0,"stage":"normal"},"areaPlayers":[{"id":"e65975e402d48d76e08ffee182054dff22fab8729c0013eab47367fa56ded7c8","areaId":"7909d963c43c6df71d7f7ee17bfc4b5133809d80342e2f645d01e16119eec1ad-connector-server-3","playerId":"d607e29f-99cc-48bc-a37d-5590b80fa0f6","cannonLevel":0,"cannonCost":' . ($Bet * 100) . ',"cannonMaxLen":18,"skin":1,"lockTargetId":0,"chairId":0}],"table":{"_id":"7909d963c43c6df71d7f7ee17bfc4b5133809d80342e2f645d01e16119eec1ad-connector-server-3","level":0,"maxChairs":1,"chairIds":["d607e29f-99cc-48bc-a37d-5590b80fa0f6","","",""],"recycle":true,"secret":"","gameId":"10007","serverId":"player-server-3","hostId":"","name":"Auto"},"players":[{"nickName":"' . $slotSettings->username . '","gender":1,"avatarUrl":"","gameServerId":"player-server-3","connectorId":"connector-server-3","teamId":"","gameId":"10007","tableId":"2ba04cc50c285a21b223e1043993456918e041415cac8ce29e72d3dc948474be-connector-server-3","gameState":"playing","id":"d607e29f-99cc-48bc-a37d-5590b80fa0f6","areaId":"7909d963c43c6df71d7f7ee17bfc4b5133809d80342e2f645d01e16119eec1ad-connector-server-3","gold":' . $balanceInCents . '}],"playerId":"d607e29f-99cc-48bc-a37d-5590b80fa0f6"}}';
                                $result_tmp[1] = '{"answerType":"","responseView":[4,0,0,0,4,2],"code":200,"data":{"table":{"_id":"7909d963c43c6df71d7f7ee17bfc4b5133809d80342e2f645d01e16119eec1ad-connector-server-3","name":"Auto","hostId":"","serverId":"player-server-3","recycle":true,"playerIds":["d607e29f-99cc-48bc-a37d-5590b80fa0f6"],"chairIds":["d607e29f-99cc-48bc-a37d-5590b80fa0f6","","",""],"level":0},"players":[{"nickName":"' . $slotSettings->username . '","gender":1,"avatarUrl":"","gameServerId":"player-server-3","connectorId":"connector-server-3","teamId":"","gameId":"10007","tableId":"","gameState":"free","id":"d607e29f-99cc-48bc-a37d-5590b80fa0f6","areaId":"","gold":' . $balanceInCents . '}],"ratio":1,"rmpRatioCredit":' . $balanceInCents . ',"denom":0.01,"roomLevel":' . $postData['query']['level'] . ',"rmpCannonCost":[1,2,3,5,8,10,10,20,30,50,80,100,100,200,300,500,800,1000]}}';
                                $fishesArr = [];
                                $fishes = $slotSettings->GetGameData('GoldenDragonKAFishes');
                                if( !is_array($fishes) ) 
                                {
                                    $fishes = [];
                                }
                                $rfish = rand(12, 35);
                                for( $i = 0; $i < $rfish; $i++ ) 
                                {
                                    $sid = rand(1, mt_getrandmax());
                                    $fishView = rand(1, 22);
                                    $state = 'solo';
                                    $stateArr = [
                                        'solo', 
                                        'solo', 
                                        'solo', 
                                        'solo', 
                                        'solo', 
                                        'solo', 
                                        'solo', 
                                        'solo', 
                                        'solo', 
                                        'solo', 
                                        'bomb', 
                                        'bomb', 
                                        'bomb', 
                                        'flock', 
                                        'flock', 
                                        'flock'
                                    ];
                                    shuffle($stateArr);
                                    if( $fishView > 18 ) 
                                    {
                                        $state = $stateArr[0];
                                    }
                                    if( $fishView < 10 ) 
                                    {
                                        $fishView = '0' . $fishView;
                                    }
                                    $fishes[$sid] = [
                                        'fishView' => 'Fish_' . $fishView, 
                                        'sid' => $sid, 
                                        'pay' => $slotSettings->Paytable['Fish_' . $fishView], 
                                        'tl' => time(), 
                                        'state' => $state
                                    ];
                                    $fishesArr[] = '{"areaId":"7909d963c43c6df71d7f7ee17bfc4b5133809d80342e2f645d01e16119eec1ad-connector-server-3","id":' . $sid . ',"type":"Fish_' . $fishView . '","amount":1,"born":1584296702070,"alive":' . rand(5, 15) . ',"state":"solo","path":"bezier_id_' . rand(1, 22) . '","index":' . $i . ',"score":1,"teamid":"none","_id":"7909d963c43c6df71d7f7ee17bfc4b5133809d80342e2f645d01e16119eec1ad-connector-server-31","expired":1584296782070}';
                                }
                                $slotSettings->SetGameData('GoldenDragonKAFishes', $fishes);
                                $result_tmp[2] = '{"answerType":"game.onSpawnFishes","responseView":[4,0,0,0,6,' . strlen('game.onSpawnFishes') . '],"msg":{"fishes":[' . implode(',', $fishesArr) . ']}}';
                                break;
                            case 'playerControl.tableHandler.leaveTable':
                                $result_tmp[0] = '{"answerType":"game.quit","responseView":[4,0,0,0,6,' . strlen('game.quit') . '],"msg":{"area":{"_id":"7909d963c43c6df71d7f7ee17bfc4b5133809d80342e2f645d01e16119eec1ad-connector-server-3","state":"started","scene":0,"stage":"group"},"areaPlayers":[],"bullets":[],"players":[{"nickName":"' . $slotSettings->username . '","gender":1,"avatarUrl":"","gameServerId":"player-server-3","connectorId":"connector-server-3","teamId":"","gameId":"10007","tableId":"2ba04cc50c285a21b223e1043993456918e041415cac8ce29e72d3dc948474be-connector-server-3","gameState":"free","id":"d607e29f-99cc-48bc-a37d-5590b80fa0f6","areaId":"","gold":0}]}}';
                                $result_tmp[0] = '{"answerType":"game.quit","responseView":[4,0,0,0,6,' . strlen('game.quit') . '],"msg":{"area":{"_id":"ae833cb4d796075ae3e5ab9749433381568f8fb2d101d823a56c3fa020bcb39f-connector-server-1","state":"started","scene":0,"stage":"group"},"areaPlayers":[],"bullets":[],"players":[{"nickName":"166764546","gender":1,"avatarUrl":"","gameServerId":"player-server-1","connectorId":"connector-server-1","teamId":"","gameId":"10007","tableId":"ae833cb4d796075ae3e5ab9749433381568f8fb2d101d823a56c3fa020bcb39f-connector-server-1","gameState":"free","id":"67244025-1483-4291-9dd4-43878eef07ee","areaId":"","gold":0}]}}';
                                break;
                            case 'connector.accountHandler.onPingBalance':
                                $bullets = '';
                                $Bet = $slotSettings->GetGameData('GoldenDragonKABet');
                                $BetCnt = $slotSettings->GetGameData('GoldenDragonKABetCnt');
                                $BetArr = $slotSettings->GetGameData('GoldenDragonKABetArr');
                                $BetLevel = $slotSettings->GetGameData('GoldenDragonKABetLevel');
                                $answerType = 'game.onSpawnFishes';
                                $result_tmp[0] = '{"answerType":"game.fire","responseView":[4,0,0,0,6,' . strlen('game.fire') . '],"msg":{"player":{"nickName":"' . $slotSettings->username . '","gender":1,"avatarUrl":"","gameServerId":"player-server-3","connectorId":"connector-server-3","teamId":"","gameId":"10007","tableId":"2ba04cc50c285a21b223e1043993456918e041415cac8ce29e72d3dc948474be-connector-server-3","gameState":"playing","id":"9dab0ea6-0cb0-4b8c-951a-e10077945f2b","areaId":"7909d963c43c6df71d7f7ee17bfc4b5133809d80342e2f645d01e16119eec1ad-connector-server-3","gold":100,"delta":' . ($Bet * 100) . ',"gain":0,"cost":' . ($Bet * 100) . ',"ratio":1,"rmpRatioCredit":' . $balanceInCents . ',"denom":0.01},"areaPlayer":{"id":"13879583b6558342be4b011cf849ee29989667afd2eb4c993a7f06371e78a2d4","areaId":"7909d963c43c6df71d7f7ee17bfc4b5133809d80342e2f645d01e16119eec1ad-connector-server-3","playerId":"d607e29f-99cc-48bc-a37d-5590b80fa0f6","cannonLevel":' . $BetCnt . ',"cannonCost":' . ($Bet * 100) . ',"cannonMaxLen":18,"skin":1,"lockTargetId":0,"chairId":2}' . $bullets . '}}';
                                $result_tmp[1] = '{"answerType":"' . $answerType . '","responseView":[4,0,0,0,6,' . strlen($answerType) . '],"msg":{"fishes":[]}}';
                                break;
                            case 'connector.accountHandler.onPingBalance_2':
                                $bullets = '';
                                $Bet = $slotSettings->GetGameData('GoldenDragonKABet');
                                $BetCnt = $slotSettings->GetGameData('GoldenDragonKABetCnt');
                                $BetArr = $slotSettings->GetGameData('GoldenDragonKABetArr');
                                $BetLevel = $slotSettings->GetGameData('GoldenDragonKABetLevel');
                                if( time() < $slotSettings->GetGameData('GoldenDragonKAGamePause') ) 
                                {
                                    $result_tmp[0] = '{"answerType":"game.fire","responseView":[4,0,0,0,6,' . strlen('game.fire') . '],"msg":{"player":{"nickName":"' . $slotSettings->username . '","gender":1,"avatarUrl":"","gameServerId":"player-server-3","connectorId":"connector-server-3","teamId":"","gameId":"10007","tableId":"2ba04cc50c285a21b223e1043993456918e041415cac8ce29e72d3dc948474be-connector-server-3","gameState":"playing","id":"9dab0ea6-0cb0-4b8c-951a-e10077945f2b","areaId":"7909d963c43c6df71d7f7ee17bfc4b5133809d80342e2f645d01e16119eec1ad-connector-server-3","gold":100,"delta":' . ($Bet * 100) . ',"gain":0,"cost":' . ($Bet * 100) . ',"ratio":1,"rmpRatioCredit":' . $balanceInCents . ',"denom":0.01},"areaPlayer":{"id":"13879583b6558342be4b011cf849ee29989667afd2eb4c993a7f06371e78a2d4","areaId":"7909d963c43c6df71d7f7ee17bfc4b5133809d80342e2f645d01e16119eec1ad-connector-server-3","playerId":"d607e29f-99cc-48bc-a37d-5590b80fa0f6","cannonLevel":' . $BetCnt . ',"cannonCost":' . ($Bet * 100) . ',"cannonMaxLen":18,"skin":1,"lockTargetId":0,"chairId":2}' . $bullets . '}}';
                                    break;
                                }
                                else
                                {
                                    $slotSettings->SetGameData('GoldenDragonKAGamePause', time());
                                }
                                if( time() - $slotSettings->GetGameData('GoldenDragonKAWaveTime') >= 300 ) 
                                {
                                    $slotSettings->SetGameData('GoldenDragonKAFishes', []);
                                    $curScene = $slotSettings->GetGameData('GoldenDragonKACurScene');
                                    $curScene++;
                                    if( $curScene > 3 ) 
                                    {
                                        $curScene = 0;
                                    }
                                    $result_tmp[0] = '{"answerType":"game.changeScene","responseView":[4,0,0,0,6,' . strlen('game.changeScene') . '],"msg":{"scene":' . $curScene . '}}';
                                    $slotSettings->SetGameData('GoldenDragonKAWaveTime', time());
                                    $slotSettings->SetGameData('GoldenDragonKACurScene', $curScene);
                                    $slotSettings->SetGameData('GoldenDragonKAIsGroupFish', 1);
                                }
                                else
                                {
                                    $result_tmp[0] = '{"answerType":"game.fire","responseView":[4,0,0,0,6,' . strlen('game.fire') . '],"msg":{"player":{"nickName":"' . $slotSettings->username . '","gender":1,"avatarUrl":"","gameServerId":"player-server-3","connectorId":"connector-server-3","teamId":"","gameId":"10007","tableId":"2ba04cc50c285a21b223e1043993456918e041415cac8ce29e72d3dc948474be-connector-server-3","gameState":"playing","id":"9dab0ea6-0cb0-4b8c-951a-e10077945f2b","areaId":"7909d963c43c6df71d7f7ee17bfc4b5133809d80342e2f645d01e16119eec1ad-connector-server-3","gold":100,"delta":' . ($Bet * 100) . ',"gain":0,"cost":' . ($Bet * 100) . ',"ratio":1,"rmpRatioCredit":' . $balanceInCents . ',"denom":0.01},"areaPlayer":{"id":"13879583b6558342be4b011cf849ee29989667afd2eb4c993a7f06371e78a2d4","areaId":"7909d963c43c6df71d7f7ee17bfc4b5133809d80342e2f645d01e16119eec1ad-connector-server-3","playerId":"d607e29f-99cc-48bc-a37d-5590b80fa0f6","cannonLevel":' . $BetCnt . ',"cannonCost":' . ($Bet * 100) . ',"cannonMaxLen":18,"skin":1,"lockTargetId":0,"chairId":2}' . $bullets . '}}';
                                    $fishesArr = [];
                                    $fishes = $slotSettings->GetGameData('GoldenDragonKAFishes');
                                    if( !is_array($fishes) ) 
                                    {
                                        $fishes = [];
                                    }
                                    if( $slotSettings->GetGameData('GoldenDragonKAIsGroupFish') == 0 ) 
                                    {
                                        $answerType = 'game.onSpawnFishes';
                                        $gr = '';
                                        $rfish = rand(10, 30);
                                        for( $i = 0; $i < $rfish; $i++ ) 
                                        {
                                            $sid = rand(1, mt_getrandmax());
                                            $fishViewArr = [
                                                1, 
                                                2, 
                                                3, 
                                                4, 
                                                5, 
                                                6, 
                                                7, 
                                                8, 
                                                9, 
                                                10, 
                                                11, 
                                                12, 
                                                13, 
                                                14, 
                                                15, 
                                                16, 
                                                17, 
                                                18, 
                                                19, 
                                                20, 
                                                21, 
                                                22, 
                                                13, 
                                                14, 
                                                15, 
                                                16, 
                                                17, 
                                                18, 
                                                19, 
                                                20, 
                                                21, 
                                                22, 
                                                13, 
                                                14, 
                                                15, 
                                                16, 
                                                17, 
                                                18, 
                                                19, 
                                                20, 
                                                21, 
                                                22
                                            ];
                                            shuffle($fishViewArr);
                                            $fishView = $fishViewArr[0];
                                            $state = 'solo';
                                            $stateArr = [
                                                'solo', 
                                                'solo', 
                                                'solo', 
                                                'solo', 
                                                'solo', 
                                                'solo', 
                                                'solo', 
                                                'solo', 
                                                'solo', 
                                                'solo', 
                                                'bomb', 
                                                'bomb', 
                                                'bomb', 
                                                'bomb', 
                                                'flock', 
                                                'flock', 
                                                'flock', 
                                                'flock'
                                            ];
                                            shuffle($stateArr);
                                            if( $fishView > 15 ) 
                                            {
                                                $state = $stateArr[0];
                                            }
                                            if( $fishView < 10 ) 
                                            {
                                                $fishView = '0' . $fishView;
                                            }
                                            $fishes[$sid] = [
                                                'fishView' => 'Fish_' . $fishView, 
                                                'sid' => $sid, 
                                                'pay' => $slotSettings->Paytable['Fish_' . $fishView], 
                                                'tl' => time(), 
                                                'state' => $state
                                            ];
                                            $fishesArr[] = '{"areaId":"7909d963c43c6df71d7f7ee17bfc4b5133809d80342e2f645d01e16119eec1ad-connector-server-3","id":' . $sid . ',"type":"Fish_' . $fishView . '","amount":1,"born":1584296702070,"alive":' . rand(5, 10) . ',"state":"' . $state . '","path":"bezier_id_' . rand(1, 22) . '","index":0,"score":1,"teamid":"none","_id":"7909d963c43c6df71d7f7ee17bfc4b5133809d80342e2f645d01e16119eec1ad-connector-server-31","expired":1584296782070}';
                                        }
                                    }
                                    else
                                    {
                                        $slotSettings->SetGameData('GoldenDragonKAIsGroupFish', 0);
                                        $gr = '"group":{"state":"group","group":"group_id_rtol","path":[],"seed":1584453624058,"alive":15},';
                                        $answerType = 'game.onSpawnGroup';
                                        $rfish = 80;
                                        $fishViewArr = [
                                            17, 
                                            18, 
                                            19, 
                                            20, 
                                            21, 
                                            22, 
                                            13, 
                                            14, 
                                            15, 
                                            16, 
                                            17, 
                                            18, 
                                            19, 
                                            20, 
                                            21, 
                                            22, 
                                            13, 
                                            14, 
                                            15, 
                                            16, 
                                            17, 
                                            18, 
                                            19, 
                                            20, 
                                            21, 
                                            22
                                        ];
                                        shuffle($fishViewArr);
                                        for( $i = 0; $i < $rfish; $i++ ) 
                                        {
                                            $sid = rand(1, mt_getrandmax());
                                            $fishView = $fishViewArr[0];
                                            if( $fishView < 10 ) 
                                            {
                                                $fishView = '0' . $fishView;
                                            }
                                            $fishes[$sid] = [
                                                'fishView' => 'Fish_' . $fishView, 
                                                'sid' => $sid, 
                                                'pay' => $slotSettings->Paytable['Fish_' . $fishView], 
                                                'tl' => time(), 
                                                'state' => 'group'
                                            ];
                                            $fishesArr[] = '{"areaId":"7909d963c43c6df71d7f7ee17bfc4b5133809d80342e2f645d01e16119eec1ad-connector-server-3","id":' . $sid . ',"type":"Fish_' . $fishView . '","amount":1,"born":1584296702070,"alive":15,"state":"group","path":"bezier_group_B1","index":' . $i . ',"score":1,"teamid":"none","_id":"7909d963c43c6df71d7f7ee17bfc4b5133809d80342e2f645d01e16119eec1ad-connector-server-31","expired":1584296782070}';
                                        }
                                        $rfish = 54;
                                        $fishViewArr = [
                                            17, 
                                            18, 
                                            19, 
                                            20, 
                                            21, 
                                            22, 
                                            13, 
                                            14, 
                                            15, 
                                            16, 
                                            17, 
                                            18, 
                                            19, 
                                            20, 
                                            21, 
                                            22, 
                                            13, 
                                            14, 
                                            15, 
                                            16, 
                                            17, 
                                            18, 
                                            19, 
                                            20, 
                                            21, 
                                            22
                                        ];
                                        shuffle($fishViewArr);
                                        for( $i = 0; $i < $rfish; $i++ ) 
                                        {
                                            $sid = rand(1, mt_getrandmax());
                                            $fishView = $fishViewArr[0];
                                            if( $fishView < 10 ) 
                                            {
                                                $fishView = '0' . $fishView;
                                            }
                                            $fishes[$sid] = [
                                                'fishView' => 'Fish_' . $fishView, 
                                                'sid' => $sid, 
                                                'pay' => $slotSettings->Paytable['Fish_' . $fishView], 
                                                'tl' => time()
                                            ];
                                            $fishesArr[] = '{"areaId":"7909d963c43c6df71d7f7ee17bfc4b5133809d80342e2f645d01e16119eec1ad-connector-server-3","id":' . $sid . ',"type":"Fish_' . $fishView . '","amount":1,"born":1584296702070,"alive":15,"state":"group","path":"bezier_group_B2","index":' . $i . ',"score":1,"teamid":"none","_id":"7909d963c43c6df71d7f7ee17bfc4b5133809d80342e2f645d01e16119eec1ad-connector-server-31","expired":1584296782070}';
                                        }
                                    }
                                    $fishesArrNew = [];
                                    foreach( $fishes as $k => $f ) 
                                    {
                                        if( time() - $fishes[$f['sid']]['tl'] < 20 ) 
                                        {
                                            $fishesArrNew[$f['sid']] = $fishes[$f['sid']];
                                        }
                                    }
                                    $result_tmp[1] = '{"answerType":"' . $answerType . '","responseView":[4,0,0,0,6,' . strlen($answerType) . '],"msg":{' . $gr . '"fishes":[' . implode(',', $fishesArr) . ']}}';
                                    $slotSettings->SetGameData('GoldenDragonKAFishes', $fishesArrNew);
                                }
                                break;
                            case 'playerControl.areaHandler.onCollider':
                                $Bet = $slotSettings->GetGameData('GoldenDragonKABet');
                                $BetCnt = $slotSettings->GetGameData('GoldenDragonKABetCnt');
                                $BetArr = $slotSettings->GetGameData('GoldenDragonKABetArr');
                                $BetLevel = $slotSettings->GetGameData('GoldenDragonKABetLevel');
                                $fid = $postData['query'][0]['fid'];
                                $bid = $postData['query'][0]['bid'];
                                $bank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                                $fishes = $slotSettings->GetGameData('GoldenDragonKAFishes');
                                $bulletsArr = $slotSettings->GetGameData('GoldenDragonKABullets');
                                $allbet = $slotSettings->GetGameData('GoldenDragonKABet');
                                $die = 'false';
                                $results = '';
                                $totalWin = 0;
                                $pause = '';
                                if( $BetLevel == 0 ) 
                                {
                                    $winRatio = 100;
                                }
                                else if( $BetLevel == 1 ) 
                                {
                                    $winRatio = 10;
                                }
                                else
                                {
                                    $winRatio = 1;
                                }
                                if( $allbet <= $slotSettings->GetBalance() ) 
                                {
                                    $bankSum = $allbet / 100 * $slotSettings->GetPercent();
                                    $slotSettings->UpdateJackpots($allbet);
                                    $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, 'bet');
                                    $slotSettings->SetBalance(-1 * $allbet, 'bet');
                                }
                                else
                                {
                                    $result_tmp[0] = '{"answerType":"game.colliderResult","Win":' . ($totalWin * 100) . ',"Balance":' . sprintf('%01.2f', $slotSettings->GetBalance()) . ',"curBet":' . $slotSettings->GetGameData('GoldenDragonKABet') . ',"responseView":[4,0,0,0,6,' . strlen('game.colliderResult') . '],"msg":{"player":{"id":"d607e29f-99cc-48bc-a37d-5590b80fa0f6","gold":' . ($totalWin * 100) . ',"delta":0,"gain":0,"cost":' . ($Bet * 100) . ',"rmpRatioCredit":' . $balanceInCents . ',"ratio":1},"result":[]}}';
                                    echo $result_tmp[0];
                                }
                                if( isset($fishes[$fid]) ) 
                                {
                                    if( !isset($fishes[$fid]['state']) ) 
                                    {
                                        $fishes[$fid]['state'] = 'solo';
                                    }
                                    if( $fishes[$fid]['state'] == 'bomb' ) 
                                    {
                                        $fidsAll = $fishes;
                                        $fidsArr = [];
                                        $winsArr = [];
                                        $fidsCnt = rand(2, 5);
                                        $totalWin = 0;
                                        shuffle($fidsAll);
                                        $fidsArr[] = '"' . $fid . '"';
                                        $winsArr[] = $fishes[$fid]['pay'] * $Bet * $winRatio;
                                        $totalWin += ($fishes[$fid]['pay'] * $Bet);
                                        foreach( $fidsAll as $k => $v ) 
                                        {
                                            if( time() - $v['tl'] < 10 ) 
                                            {
                                                $fidsArr[] = '"' . $v['sid'] . '"';
                                                $winsArr[] = $v['pay'] * $Bet * $winRatio;
                                                $totalWin += ($v['pay'] * $Bet);
                                                $fidsCnt--;
                                                if( $fidsCnt <= 0 ) 
                                                {
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                    else
                                    {
                                        $totalWin = $fishes[$fid]['pay'] * $Bet;
                                    }
                                    if( $totalWin <= $bank && rand(1, 5) == 1 && $totalWin > 0 && $fishes[$fid]['state'] == 'bomb' ) 
                                    {
                                        $income = 0;
                                        $ptime = '';
                                        $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                                        $slotSettings->SetBalance($totalWin);
                                        $results = '{"bid":' . $bid . ',"fids":[' . implode(',', $fidsArr) . '],"ftypes":["' . $fishes[$fid]['fishView'] . '|bomb"],"success":true,"die":true,"score":' . ($totalWin * $winRatio) . ',"income":' . ($totalWin * $winRatio) . ',"chairId":0,"typeBombs":[' . implode(',', $fidsArr) . '],"pause":[' . $pause . '],"diefids":[' . implode(',', $fidsArr) . '],"winscore":[' . implode(',', $winsArr) . '],"cannonlevel":0' . $ptime . '}';
                                        unset($fishes[$fid]);
                                    }
                                    else if( $totalWin <= $bank && rand(1, 5) == 1 && $totalWin > 0 && $fishes[$fid]['state'] != 'bomb' ) 
                                    {
                                        $income = 0;
                                        $ptime = '';
                                        if( $fishes[$fid]['fishView'] == 'Fish_01' ) 
                                        {
                                            $pause = '"' . $fid . '"';
                                            $income = $totalWin * $winRatio;
                                            $slotSettings->SetGameData('GoldenDragonKAGamePause', time() + 10);
                                            $ptime = ',"pauseTime":10000';
                                        }
                                        $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                                        $slotSettings->SetBalance($totalWin);
                                        $results = '{"bid":' . $bid . ',"fids":["' . $fid . '"],"ftypes":["' . $fishes[$fid]['fishView'] . '|' . $fishes[$fid]['state'] . '"],"success":true,"die":true,"score":' . ($totalWin * $winRatio) . ',"income":' . ($totalWin * $winRatio) . ',"chairId":0,"typeBombs":[],"pause":[' . $pause . '],"diefids":[' . $fid . '],"winscore":[' . ($totalWin * $winRatio) . '],"cannonlevel":0' . $ptime . '}';
                                        unset($fishes[$fid]);
                                    }
                                    else
                                    {
                                        $totalWin = 0;
                                        $results = '{"bid":' . $bid . ',"fids":["' . $fid . '"],"ftypes":["Fish_15|flock"],"success":true,"die":false,"score":0,"income":0,"chairId":0,"typeBombs":[],"pause":[],"diefids":[],"winscore":[],"cannonlevel":0}';
                                    }
                                }
                                $slotSettings->SetGameData('GoldenDragonKAFishes', $fishes);
                                $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                                if( $balanceInCents < 0.01 ) 
                                {
                                    $result_tmp[0] = '{"answerType":"game.colliderResult","Win":' . ($totalWin * 100) . ',"Balance":0.0,"curBet":' . $slotSettings->GetGameData('GoldenDragonKABet') . ',"responseView":[4,0,0,0,6,' . strlen('game.colliderResult') . '],"msg":{"player":{"id":"d607e29f-99cc-48bc-a37d-5590b80fa0f6","gold":' . ($totalWin * 100) . ',"delta":0,"gain":0,"cost":' . ($Bet * 100) . ',"rmpRatioCredit":0.1,"ratio":1},"result":[' . $results . ']}}';
                                }
                                else
                                {
                                    $result_tmp[0] = '{"answerType":"game.colliderResult","Win":' . ($totalWin * 100) . ',"Balance":' . sprintf('%01.2f', $slotSettings->GetBalance()) . ',"curBet":' . $slotSettings->GetGameData('GoldenDragonKABet') . ',"responseView":[4,0,0,0,6,' . strlen('game.colliderResult') . '],"msg":{"player":{"id":"d607e29f-99cc-48bc-a37d-5590b80fa0f6","gold":' . ($totalWin * 100) . ',"delta":1,"gain":1,"cost":' . ($Bet * 100) . ',"rmpRatioCredit":' . $balanceInCents . ',"ratio":1},"result":[' . $results . ']}}';
                                }
                                $response = '{"responseEvent":"spin","responseType":"bet","serverResponse":{"slotBet":0,"totalFreeGames":0,"currentFreeGames":0,"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":0,"totalWin":' . $totalWin . ',"winLines":[],"Jackpots":[],"reelsSymbols":[]}}';
                                $slotSettings->SaveLogReport($response, $allbet, 1, $totalWin, 'bet');
                                break;
                            case 'fishHunter.areaHandler.onUpdateCannon':
                                $Bet = $slotSettings->GetGameData('GoldenDragonKABet');
                                $BetCnt = $slotSettings->GetGameData('GoldenDragonKABetCnt');
                                $BetArr = $slotSettings->GetGameData('GoldenDragonKABetArr');
                                $BetLevel = $slotSettings->GetGameData('GoldenDragonKABetLevel');
                                $cnLevel = $slotSettings->GetGameData('GoldenDragonKABetLevel') * 6;
                                if( $postData['query']['upgrade'] ) 
                                {
                                    $BetCnt++;
                                }
                                else
                                {
                                    $BetCnt--;
                                }
                                if( count($BetArr) <= $BetCnt ) 
                                {
                                    $BetCnt = count($BetArr) - 1;
                                }
                                if( $BetCnt <= 0 ) 
                                {
                                    $BetCnt = 0;
                                }
                                $Bet = $BetArr[$BetCnt];
                                $slotSettings->SetGameData('GoldenDragonKABet', $Bet);
                                $slotSettings->SetGameData('GoldenDragonKABetCnt', $BetCnt);
                                $slotSettings->SetGameData('GoldenDragonKABetArr', $BetArr);
                                $slotSettings->SetGameData('GoldenDragonKABetLevel', $BetLevel);
                                $result_tmp[0] = '{"answerType":"game.updateCannon","Balance":' . sprintf('%01.2f', $slotSettings->GetBalance()) . ',"curBet":' . $slotSettings->GetGameData('GoldenDragonKABet') . ',"responseView":[4,0,0,0,6,' . strlen('game.updateCannon') . '],"msg":{"areaPlayer":{"id":"2b7e1e20bfc42e388fe81831c2a2d80a6657370638b7170b5031110e10e73379","areaId":"e339f81cfa6b0d3ed09053d2d6f186416e90b358c2b26a84a81fa9e90d94004a-connector-server-3","playerId":"d607e29f-99cc-48bc-a37d-5590b80fa0f6","cannonLevel":' . $BetCnt . ',"cannonCost":' . ($Bet * 100) . ',"cannonMaxLen":18,"skin":1,"lockTargetId":0,"chairId":0}}}';
                                break;
                            case 'connector.accountHandler.gameRecall':
                                $Bet = $slotSettings->GetGameData('GoldenDragonKABet');
                                $BetCnt = $slotSettings->GetGameData('GoldenDragonKABetCnt');
                                $BetArr = $slotSettings->GetGameData('GoldenDragonKABetArr');
                                $BetLevel = $slotSettings->GetGameData('GoldenDragonKABetLevel');
                                if( $postData['query']['upgrade'] ) 
                                {
                                    $BetCnt++;
                                }
                                else
                                {
                                    $BetCnt--;
                                }
                                if( count($BetArr) <= $BetCnt ) 
                                {
                                    $BetCnt = count($BetArr) - 1;
                                }
                                if( $BetCnt <= 0 ) 
                                {
                                    $BetCnt = 0;
                                }
                                $Bet = $BetArr[$BetCnt];
                                $result_tmp[0] = '{"answerType":"game.updateCannon","responseView":[4,0,0,0,6,' . strlen('game.updateCannon') . '],"msg":{"areaPlayer":{"id":"2b7e1e20bfc42e388fe81831c2a2d80a6657370638b7170b5031110e10e73379","areaId":"e339f81cfa6b0d3ed09053d2d6f186416e90b358c2b26a84a81fa9e90d94004a-connector-server-3","playerId":"d607e29f-99cc-48bc-a37d-5590b80fa0f6","cannonLevel":' . $BetCnt . ',"cannonCost":' . ($Bet * 100) . ',"cannonMaxLen":18,"skin":1,"lockTargetId":0,"chairId":0}}}';
                                break;
                            case 'areaFishControl.fishHandler.fetchFishInfo':
                                $Bet = $slotSettings->GetGameData('GoldenDragonKABet');
                                $BetCnt = $slotSettings->GetGameData('GoldenDragonKABetCnt');
                                $BetArr = $slotSettings->GetGameData('GoldenDragonKABetArr');
                                $BetLevel = $slotSettings->GetGameData('GoldenDragonKABetLevel');
                                $result_tmp[0] = '{"code":200,"data":{"scores":{"Fish_22":2,"Fish_21":3,"Fish_20":4,"Fish_19":5,"Fish_18":6,"Fish_17":7,"Fish_16":8,"Fish_15":9,"Fish_14":10,"Fish_13":12,"Fish_12":15,"Fish_11":18,"Fish_10":20,"Fish_09":25,"Fish_08":30,"Fish_07":40,"Fish_06":80,"Fish_05":100,"Fish_04":150,"Fish_03":200,"Fish_02":200,"Fish_01":20},"cannonCost":' . ($Bet * 100) . '}}';
                                break;
                            case 'fishHunter.areaHandler.onFire':
                                $bulletsArr = $slotSettings->GetGameData('GoldenDragonKABullets');
                                $bulletBet = $slotSettings->GetGameData('GoldenDragonKABet');
                                $allbet = $bulletBet;
                                $Bet = $slotSettings->GetGameData('GoldenDragonKABet');
                                $BetCnt = $slotSettings->GetGameData('GoldenDragonKABetCnt');
                                $BetArr = $slotSettings->GetGameData('GoldenDragonKABetArr');
                                $BetLevel = $slotSettings->GetGameData('GoldenDragonKABetLevel');
                                if( $slotSettings->GetBalance() < $allbet ) 
                                {
                                    $bullets = '';
                                    $result_tmp[0] = '{"answerType":"game.fire","responseView":[4,0,0,0,6,' . strlen('game.fire') . '],"msg":{"player":{"nickName":"' . $slotSettings->username . '","gender":1,"avatarUrl":"","gameServerId":"player-server-3","connectorId":"connector-server-3","teamId":"","gameId":"10007","tableId":"2ba04cc50c285a21b223e1043993456918e041415cac8ce29e72d3dc948474be-connector-server-3","gameState":"playing","id":"9dab0ea6-0cb0-4b8c-951a-e10077945f2b","areaId":"7909d963c43c6df71d7f7ee17bfc4b5133809d80342e2f645d01e16119eec1ad-connector-server-3","gold":100,"delta":' . $Bet . ',"gain":0,"cost":0,"ratio":1,"rmpRatioCredit":1,"denom":0.01},"areaPlayer":{"id":"13879583b6558342be4b011cf849ee29989667afd2eb4c993a7f06371e78a2d4","areaId":"7909d963c43c6df71d7f7ee17bfc4b5133809d80342e2f645d01e16119eec1ad-connector-server-3","playerId":"d607e29f-99cc-48bc-a37d-5590b80fa0f6","cannonLevel":' . $BetCnt . ',"cannonCost":' . ($Bet * 100) . ',"cannonMaxLen":18,"skin":1,"lockTargetId":0,"chairId":1}' . $bullets . '}}';
                                }
                                else
                                {
                                    $slotSettings->SetBalance(-1 * $allbet, 'bet');
                                    $bankSum = $allbet / 100 * $slotSettings->GetPercent();
                                    $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, 'bet');
                                    $slotSettings->UpdateJackpots($allbet);
                                    if( !is_array($bulletsArr) ) 
                                    {
                                        $bulletsArr = [];
                                    }
                                    $bulletId = rand(10000000000000, mt_getrandmax());
                                    $bullets = ',"bullet":{"transactionId":"4623343b-db8d-442c-a032-7523aac41417","createTime":1584376468710,"areaId":"7909d963c43c6df71d7f7ee17bfc4b5133809d80342e2f645d01e16119eec1ad-connector-server-3","playerId":"d607e29f-99cc-48bc-a37d-5590b80fa0f6","bulletId":' . $bulletId . ',"angle":' . $postData['query']['angle'] . ',"cost":' . $bulletBet . ',"lockTargetId":0,"chairId":0,"cannonlevel":' . $BetCnt . ',"cannonskin":1,"_id":"9dab0ea6-0cb0-4b8c-951a-e10077945f2b3584376468710","level":1}';
                                    $bulletsArr[$bulletId] = ['bulletId' => $bulletId];
                                    $result_tmp[0] = '{"answerType":"game.fire","responseView":[4,0,0,0,6,' . strlen('game.fire') . '],"msg":{"player":{"nickName":"' . $slotSettings->username . '","gender":1,"avatarUrl":"","gameServerId":"player-server-3","connectorId":"connector-server-3","teamId":"","gameId":"10007","tableId":"2ba04cc50c285a21b223e1043993456918e041415cac8ce29e72d3dc948474be-connector-server-3","gameState":"playing","id":"9dab0ea6-0cb0-4b8c-951a-e10077945f2b","areaId":"7909d963c43c6df71d7f7ee17bfc4b5133809d80342e2f645d01e16119eec1ad-connector-server-3","gold":100,"delta":' . $bulletBet . ',"gain":0,"cost":0,"ratio":1,"rmpRatioCredit":100,"denom":0.01},"areaPlayer":{"id":"13879583b6558342be4b011cf849ee29989667afd2eb4c993a7f06371e78a2d4","areaId":"7909d963c43c6df71d7f7ee17bfc4b5133809d80342e2f645d01e16119eec1ad-connector-server-3","playerId":"d607e29f-99cc-48bc-a37d-5590b80fa0f6","cannonLevel":' . $BetCnt . ',"cannonCost":' . ($Bet * 100) . ',"cannonMaxLen":18,"skin":1,"lockTargetId":0,"chairId":2}' . $bullets . '}}';
                                    $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                                    $result_tmp[1] = '{"answerType":"game.colliderResult","responseView":[4,0,0,0,6,' . strlen('game.colliderResult') . '],"msg":{"player":{"id":"d607e29f-99cc-48bc-a37d-5590b80fa0f6","gold":0,"delta":0,"gain":0,"cost":' . ($Bet * 100) . ',"rmpRatioCredit":' . $balanceInCents . ',"ratio":1},"result":[]}}';
                                    $slotSettings->SetGameData('GoldenDragonKABullets', $bulletsArr);
                                    $response = '{"responseEvent":"spin","responseType":"bet","serverResponse":{"slotBet":' . $allbet . ',"totalFreeGames":0,"currentFreeGames":0,"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":0,"totalWin":0,"winLines":[],"Jackpots":[],"reelsSymbols":[]}}';
                                    $slotSettings->SaveLogReport($response, $allbet, 1, 0, 'bet');
                                }
                                break;
                        }
                        $response = implode('---', $result_tmp);
                        $slotSettings->SaveGameData();
                        echo ':::' . $response;
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
