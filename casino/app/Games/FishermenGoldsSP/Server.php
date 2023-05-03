<?php 
namespace VanguardLTE\Games\FishermenGoldsSP
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
                            $aidsTmp = explode('&', $postData['gameData']);
                            $aids = [];
                            foreach( $aidsTmp as $pr ) 
                            {
                                $pr_t = explode('=', $pr);
                                $aids[$pr_t[0]] = $pr_t[1];
                            }
                            $aid = $aids['action'];
                            var_dump($aids);
                        }
                        $curTime = floor(microtime(true) * 1000);
                        switch( $aid ) 
                        {
                            case 'request_host':
                                $result_tmp[0] = '{"host":[""],"timezone":8,"show_currency":false,"code":200,"msg":"Success"}';
                                echo $result_tmp[0];
                                break;
                            case 'player_info':
                                $result_tmp[0] = '{"code":200,"msg":"Success"}';
                                echo $result_tmp[0];
                                break;
                            case 'getinfo':
                                $result_tmp[0] = '{"range":[{"point":"1000","price":10},{"point":"5000","price":50},{"point":"10000","price":100},{"point":"50000","price":500},{"point":"100000","price":1000},{"point":"500000","price":5000}],"currency":"CNY","rate":"100","code":200,"msg":"Success"}';
                                echo $result_tmp[0];
                                break;
                            case 'lobby':
                                $result_tmp[0] = '{"code":200,"data":{},"timestamp":"1596378364036"}';
                                echo $result_tmp[0];
                                break;
                            case 'account_register':
                                $result_tmp[0] = "{\r\n   \"account_id\" : \"" . $slotSettings->username . "\",\r\n   \"action\" : \"60\",\r\n   \"balance\" : \"" . $balanceInCents . "\",\r\n   \"code\" : \"200\",\r\n   \"denom_range\" : [ 10, 50, 50, 500, 100, 1000, 0, 0, 0, 0, 0, 0 ],\r\n   \"denomination\" : \"100\",\r\n   \"free_bullet\" : \"0\",\r\n   \"multiplier\" : \"1\",\r\n   \"multiplier_list\" : [ 1, 2, 3, 5, 10 ],\r\n   \"result\" : \"1\",\r\n   \"session_id\" : \"ce94c6b0-d571-11ea-8211-dab88abf32ff\",\r\n   \"status\" : \"0\",\r\n   \"user_id\" : \"10166\"\r\n}";
                                break;
                            case 'account_fund_enquiry':
                                $result_tmp[0] = "{\r\n   \"action\" : \"88\",\r\n   \"result\" : \"1\",\r\n   \"wallet_balance\" : \"0.000000\"\r\n}\r\n";
                                break;
                            case 'account_set_denomination':
                                if( $aids['value'] > 0 ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentBet', $aids['value']);
                                }
                                $CurrentBet = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentBet');
                                $CurrentMpl = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentMpl');
                                $result_tmp[0] = "{\r\n   \"action\" : \"79\",\r\n   \"player\" : \"2\",\r\n   \"result\" : \"1\",\r\n   \"value\" : \"" . $CurrentBet . "\"\r\n}";
                                break;
                            case 'account_leave_lobby':
                                $result_tmp[0] = "{\r\n   \"action\" : \"72\",\r\n   \"player_id\" : \"0\"\r\n}";
                                $result_tmp[1] = "{\r\n   \"action\" : \"82\",\r\n   \"result\" : \"1\"\r\n}";
                                break;
                            case 'account_set_multiplier':
                                if( $aids['value'] > 0 ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentMpl', $aids['value']);
                                }
                                $CurrentBet = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentBet');
                                $CurrentMpl = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentMpl');
                                $result_tmp[0] = "{\r\n   \"action\" : \"80\",\r\n   \"player\" : \"2\",\r\n   \"result\" : \"1\",\r\n   \"value\" : \"" . $CurrentMpl . "\"\r\n}\r\n";
                                break;
                            case 'account_join_lobby':
                                if( $aids['scene_lv'] == 0 ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentBet', 10);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentMpl', 1);
                                    $denomina_list = [
                                        10, 
                                        20, 
                                        30, 
                                        40, 
                                        50
                                    ];
                                }
                                if( $aids['scene_lv'] == 1 ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentBet', 10);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentMpl', 1);
                                    $denomina_list = [
                                        100, 
                                        200, 
                                        300, 
                                        400, 
                                        500
                                    ];
                                }
                                if( $aids['scene_lv'] == 2 ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentBet', 10);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentMpl', 1);
                                    $denomina_list = [
                                        1000, 
                                        2000, 
                                        3000, 
                                        4000, 
                                        5000
                                    ];
                                }
                                $CurrentBet = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentBet');
                                $CurrentMpl = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentMpl');
                                $result_tmp[] = "{\r\n   \"account_id\" : \"" . $slotSettings->username . "\",\r\n   \"action\" : \"71\",\r\n   \"balance\" : \"" . $balanceInCents . "\",\r\n   \"denomination\" : \"" . $CurrentBet . "\",\r\n   \"free_bullet\" : \"0\",\r\n   \"multiplier\" : \"" . $CurrentMpl . "\",\r\n   \"player_id\" : \"2\"\r\n}\r\n";
                                $result_tmp[] = "{\r\n   \"account_id\" : \"" . $slotSettings->username . "\",\r\n   \"action\" : \"67\",\r\n   \"background\" : \"1\",\r\n   \"denomina_list\" : [ " . implode(',', $denomina_list) . " ],\r\n   \"denomination\" : \"" . $CurrentBet . "\",\r\n   \"free_bullet\" : \"0\",\r\n   \"multiplier\" : \"" . $CurrentMpl . "\",\r\n   \"multiplier_list\" : [ 1, 2, 3, 5, 10 ],\r\n   \"next_denomination\" : \"1\",\r\n   \"result\" : \"1\",\r\n   \"scene_id\" : \"0\",\r\n   \"scene_level\" : \"0\",\r\n   \"seat_id\" : \"2\"\r\n}\r\n";
                                break;
                            case 'account_get_scene_data':
                                $CurrentBet = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentBet');
                                $CurrentMpl = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentMpl');
                                if( !isset($aids['page']) ) 
                                {
                                    if( $aids['mode'] == 3 ) 
                                    {
                                        $result_tmp[] = '{"Object":[{"coord":{"x":"1.357740","y":"-0.140187"},"curpos":"-0.150000","inst_id":"21359763","species":"11","state":"1","timestamp":"1596466407652","transform":["-0.707107","-0.707107","1.780982","0.707107","-0.707107","0.225014"]},{"coord":{"x":"0.839826","y":"0.419937"},"curpos":"1.323400","inst_id":"21359531","species":"3","state":"1","timestamp":"1596466398496","transform":["0.422618","-0.906308","1.176382","0.906308","0.422618","-0.556916"]},{"coord":{"x":"-0.163532","y":"0.278580"},"curpos":"4.644550","inst_id":"21359076","species":"28","state":"1","timestamp":"1596466376355","transform":["-0.974370","-0.224951","1.867471","0.224951","-0.974370","0.697229"]},{"coord":{"x":"1.230047","y":"-0.208233"},"curpos":"0.854700","inst_id":"21359289","species":"11","state":"1","timestamp":"1596466387558","transform":["0.766044","0.642788","-0.363433","-0.642788","0.766044","0.688345"]},{"coord":{"x":"1.547653","y":"0.111514"},"curpos":"7.900000","inst_id":"21358857","species":"18","state":"1","timestamp":"1596466368152","transform":["0.990268","0.139173","-0.060936","-0.139173","0.990268","0.018575"]},{"coord":{"x":"1.276400","y":"1.209676"},"curpos":"0.123400","inst_id":"21359742","species":"9","state":"1","timestamp":"1596466406496","transform":["-0.601815","0.798636","0.964518","-0.798636","-0.601815","1.510806"]},{"coord":{"x":"0.989629","y":"0.542672"},"curpos":"1.623400","inst_id":"21359467","species":"3","state":"1","timestamp":"1596466396496","transform":["0.422618","-0.906308","1.126382","0.906308","0.422618","-0.536916"]},{"coord":{"x":"1.990528","y":"0.293619"},"curpos":"0.998120","inst_id":"21359133","species":"13","state":"1","timestamp":"1596466378699","transform":["0.996195","0.087156","-0.040195","-0.087156","0.996195","-0.030626"]},{"coord":{"x":"0.666210","y":"0.174131"},"curpos":"0.812500","inst_id":"21359629","species":"3","state":"1","timestamp":"1596466401902","transform":["0.422618","-0.906308","1.176382","0.906308","0.422618","-0.586916"]},{"coord":{"x":"2.296151","y":"1.647262"},"curpos":"0.023720","inst_id":"21359661","species":"13","state":"1","timestamp":"1596466403059","transform":["-0.707107","0.707107","1.423875","-0.707107","-0.707107","1.482093"]},{"coord":{"x":"0.988016","y":"0.606817"},"curpos":"1.607050","inst_id":"21359470","species":"3","state":"1","timestamp":"1596466396605","transform":["0.422618","-0.906308","1.136382","0.906308","0.422618","-0.466916"]},{"coord":{"x":"0.921596","y":"0.488691"},"curpos":"1.555450","inst_id":"21359485","species":"3","state":"1","timestamp":"1596466396949","transform":["0.422618","-0.906308","1.106382","0.906308","0.422618","-0.566916"]},{"coord":{"x":"0.556066","y":"0.263307"},"curpos":"0.723400","inst_id":"21359645","species":"3","state":"1","timestamp":"1596466402496","transform":["0.422618","-0.906308","1.066382","0.906308","0.422618","-0.446916"]},{"coord":{"x":"1.061331","y":"0.695389"},"curpos":"1.810900","inst_id":"21359432","species":"3","state":"1","timestamp":"1596466395246","transform":["0.422618","-0.906308","1.066382","0.906308","0.422618","-0.456916"]},{"coord":{"x":"0.306675","y":"0.728114"},"curpos":"4.403200","inst_id":"21359382","species":"27","state":"1","timestamp":"1596466393308","transform":["-0.615661","-0.788011","1.580149","0.788011","-0.615661","0.107377"]},{"coord":{"x":"1.260676","y":"1.058401"},"curpos":"0.632050","inst_id":"21359663","species":"16","state":"1","timestamp":"1596466403105","transform":["-0.754710","0.656059","1.151713","-0.656059","-0.754710","1.460518"]},{"coord":{"x":"2.021277","y":"1.373841"},"curpos":"0.301450","inst_id":"21359715","species":"5","state":"1","timestamp":"1596466405309","transform":["-0.642788","0.766044","1.307234","-0.766044","-0.642788","1.502322"]},{"coord":{"x":"0.888022","y":"0.611814"},"curpos":"1.592950","inst_id":"21359472","species":"3","state":"1","timestamp":"1596466396699","transform":["0.422618","-0.906308","1.046382","0.906308","0.422618","-0.456916"]},{"coord":{"x":"0.539010","y":"0.766376"},"curpos":"6.011000","inst_id":"21359237","species":"4","state":"1","timestamp":"1596466383808","transform":["-0.951057","-0.309017","1.888781","0.309017","-0.951057","0.680846"]},{"coord":{"x":"0.515124","y":"0.237312"},"curpos":"0.782050","inst_id":"21359631","species":"3","state":"1","timestamp":"1596466402105","transform":["0.422618","-0.906308","1.026382","0.906308","0.422618","-0.506916"]},{"coord":{"x":"1.006357","y":"0.566873"},"curpos":"1.689100","inst_id":"21359450","species":"3","state":"1","timestamp":"1596466396058","transform":["0.422618","-0.906308","1.096382","0.906308","0.422618","-0.536916"]},{"coord":{"x":"-0.196146","y":"0.889745"},"curpos":"15.564101","inst_id":"21358646","species":"7","state":"1","timestamp":"1596466356105","transform":["0.798636","-0.601815","0.409898","0.601815","0.798636","-0.434265"]},{"coord":{"x":"0.403560","y":"0.343068"},"curpos":"0.010150","inst_id":"21359695","species":"11","state":"1","timestamp":"1596466404449","transform":["0.874620","-0.484810","0.353854","0.484810","0.874620","-0.288252"]},{"coord":{"x":"-0.001041","y":"0.503884"},"curpos":"2.209400","inst_id":"21359248","species":"17","state":"1","timestamp":"1596466384558","transform":["-0.939693","-0.342020","1.895182","0.342020","-0.939693","0.545828"]},{"coord":{"x":"0.156767","y":"0.610755"},"curpos":"0.868600","inst_id":"21359674","species":"18","state":"1","timestamp":"1596466403309","transform":["0.987688","-0.156434","0.089161","0.156434","0.987688","-0.072897"]},{"coord":{"x":"0.830085","y":"0.568012"},"curpos":"1.435900","inst_id":"21359509","species":"3","state":"1","timestamp":"1596466397746","transform":["0.422618","-0.906308","1.096382","0.906308","0.422618","-0.446916"]},{"coord":{"x":"0.882091","y":"0.596108"},"curpos":"5.881200","inst_id":"21359119","species":"12","state":"1","timestamp":"1596466378246","transform":["-0.731354","-0.681998","1.809980","0.681998","-0.731354","0.259456"]},{"coord":{"x":"0.459259","y":"1.098204"},"curpos":"0.625000","inst_id":"21359666","species":"9","state":"1","timestamp":"1596466403152","transform":["0.190809","0.981627","-0.021533","-0.981627","0.190809","1.277153"]},{"coord":{"x":"0.571567","y":"-0.251986"},"curpos":"0.130450","inst_id":"21359741","species":"29","state":"1","timestamp":"1596466406449","transform":["0.469472","-0.882948","0.843055","0.882948","0.469472","-0.519578"]},{"coord":{"x":"0.612582","y":"0.318291"},"curpos":"1.051600","inst_id":"21359579","species":"3","state":"1","timestamp":"1596466400308","transform":["0.422618","-0.906308","1.076382","0.906308","0.422618","-0.556916"]},{"coord":{"x":"0.304044","y":"0.149290"},"curpos":"3.946150","inst_id":"21359185","species":"5","state":"1","timestamp":"1596466381011","transform":["-0.992546","0.121869","1.710218","-0.121869","-0.992546","1.004601"]},{"coord":{"x":"0.350058","y":"1.703832"},"curpos":"-0.186050","inst_id":"21359788","species":"5","state":"1","timestamp":"1596466408559","transform":["0.544639","0.838671","0.075430","-0.838671","0.544639","0.973166"]},{"coord":{"x":"0.816363","y":"-0.126499"},"curpos":"0.369550","inst_id":"21359704","species":"14","state":"1","timestamp":"1596466404855","transform":["0.544639","-0.838671","1.054101","0.838671","0.544639","-0.517805"]},{"coord":{"x":"2.001748","y":"0.388193"},"curpos":"4.733650","inst_id":"21359063","species":"5","state":"1","timestamp":"1596466375761","transform":["0.913545","0.406737","-0.126520","-0.406737","0.913545","0.354771"]},{"coord":{"x":"1.971229","y":"0.972620"},"curpos":"-0.090600","inst_id":"21359777","species":"30","state":"1","timestamp":"1596466408105","transform":["-0.996195","-0.087156","1.817973","0.087156","-0.996195","0.990626"]}],"action":"69","background":"4","machine_id":"20004","player":[{"account_id":"' . $slotSettings->username . '","balance":"' . $balanceInCents . '","denomination":"' . $CurrentBet . '","free_bullet":"0","multiplier":"1","seat_id":2}],"stage_id":"673226195","status":"2","timestamp":"' . $curTime . '"}';
                                        $result_tmp[] = '{"action":"74","inst_id":"21359289"}';
                                    }
                                }
                                else if( $aids['mode'] == 2 && $aids['page'] == 0 ) 
                                {
                                    $result_tmp[] = '{"action":"69","background":"6","machine_id":"20005","player":[{"account_id":"' . $slotSettings->username . '","balance":"199992","denomination":"' . $CurrentBet . '","free_bullet":"0","multiplier":"1","seat_id":2}],"species":[{"disp_offset":{"x":"0.000000","y":"0.000000"},"path":{"type":"locus","x":{"cosine":"0.700000","sine":"0.200000","t0":"-0.200000","t1":"0.600000","t2":"-0.100000","tc":"0.350000","ts":"0.630000"},"y":{"cosine":"0.400000","sine":"0.200000","t0":"-0.100000","t1":"0.050000","t2":"0.050000","tc":"0.600000","ts":"0.150000"}},"proxy":{"height":"0.020000","type":"box","width":"0.060000"},"size":"0.050000","species":"1","speed":"0.150000","type_id":"1"},{"disp_offset":{"x":"0.000000","y":"0.000000"},"path":{"type":"locus","x":{"cosine":"0.220000","sine":"0.060000","t0":"-0.500000","t1":"0.260000","t2":"0.050000","tc":"0.250000","ts":"0.100000"},"y":{"cosine":"0.070000","sine":"0.750000","t0":"0.000000","t1":"0.110000","t2":"0.020000","tc":"-0.100000","ts":"0.180000"}},"proxy":{"height":"0.025000","type":"box","width":"0.070000"},"size":"0.050000","species":"2","speed":"0.200000","type_id":"1"},{"disp_offset":{"x":"0.000000","y":"0.000000"},"path":{"type":"locus","x":{"cosine":"0.610000","sine":"0.240000","t0":"-0.100000","t1":"0.360000","t2":"0.050000","tc":"0.100000","ts":"0.430000"},"y":{"cosine":"0.170000","sine":"0.610000","t0":"0.200000","t1":"0.110000","t2":"0.050000","tc":"0.090000","ts":"0.380000"}},"proxy":{"height":"0.030000","type":"box","width":"0.070000"},"size":"0.050000","species":"3","speed":"0.150000","type_id":"1"},{"disp_offset":{"x":"0.000000","y":"0.000000"},"path":{"type":"locus","x":{"cosine":"0.000000","sine":"0.200000","t0":"0.120000","t1":"0.000000","t2":"0.040000","tc":"0.000000","ts":"0.430000"},"y":{"cosine":"0.400000","sine":"-0.200000","t0":"0.200000","t1":"0.000000","t2":"0.010000","tc":"0.100000","ts":"-0.430000"}},"proxy":{"height":"0.030000","type":"box","width":"0.090000"},"size":"0.050000","species":"4","speed":"0.250000","type_id":"1"},{"disp_offset":{"x":"0.000000","y":"0.000000"},"path":{"type":"locus","x":{"cosine":"0.190000","sine":"0.110000","t0":"0.000000","t1":"0.150000","t2":"0.030000","tc":"-0.430000","ts":"0.140000"},"y":{"cosine":"0.210000","sine":"0.160000","t0":"0.300000","t1":"-0.060000","t2":"0.050000","tc":"0.330000","ts":"0.130000"}},"proxy":{"height":"0.040000","type":"box","width":"0.100000"},"size":"0.050000","species":"5","speed":"0.150000","type_id":"1"},{"disp_offset":{"x":"0.000000","y":"0.000000"},"path":{"type":"locus","x":{"cosine":"0.000000","sine":"0.000000","t0":"0.000000","t1":"0.150000","t2":"0.000000","tc":"0.000000","ts":"0.000000"},"y":{"cosine":"0.000000","sine":"0.130000","t0":"0.000000","t1":"0.070000","t2":"0.000000","tc":"0.000000","ts":"0.110000"}},"proxy":{"height":"0.040000","type":"box","width":"0.100000"},"size":"0.050000","species":"6","speed":"0.500000","type_id":"1"},{"disp_offset":{"x":"0.000000","y":"0.000000"},"path":{"type":"locus","x":{"cosine":"0.140000","sine":"0.000000","t0":"1.700000","t1":"-0.100000","t2":"0.000000","tc":"0.200000","ts":"0.000000"},"y":{"cosine":"0.000000","sine":"0.250000","t0":"0.000000","t1":"0.100000","t2":"0.000000","tc":"0.000000","ts":"0.400000"}},"proxy":{"height":"0.040000","type":"box","width":"0.100000"},"size":"0.050000","species":"7","speed":"0.300000","type_id":"1"},{"disp_offset":{"x":"0.010000","y":"0.000000"},"path":{"type":"locus","x":{"cosine":"0.100000","sine":"0.260000","t0":"-0.200000","t1":"0.000000","t2":"0.000000","tc":"0.300000","ts":"0.210000"},"y":{"cosine":"0.000000","sine":"0.000000","t0":"-0.200000","t1":"0.090000","t2":"0.020000","tc":"0.000000","ts":"0.000000"}},"proxy":{"radius":"0.040000","type":"single"},"size":"0.050000","species":"8","speed":"0.200000","type_id":"1"},{"disp_offset":{"x":"0.000000","y":"0.000000"},"path":{"type":"locus","x":{"cosine":"0.000000","sine":"0.050000","t0":"0.000000","t1":"0.420000","t2":"0.000000","tc":"0.000000","ts":"0.050000"},"y":{"cosine":"0.000000","sine":"0.000000","t0":"0.430000","t1":"0.000000","t2":"0.020000","tc":"0.000000","ts":"0.000000"}},"proxy":{"radius":"0.050000","type":"single"},"size":"0.050000","species":"9","speed":"0.150000","type_id":"1"},{"disp_offset":{"x":"0.000000","y":"0.000000"},"path":{"type":"locus","x":{"cosine":"0.000000","sine":"0.780000","t0":"0.000000","t1":"0.500000","t2":"0.000000","tc":"0.000000","ts":"0.110000"},"y":{"cosine":"0.000000","sine":"0.200000","t0":"0.380000","t1":"0.000000","t2":"0.000000","tc":"0.000000","ts":"0.200000"}},"proxy":{"radius":"0.040000","type":"single"},"size":"0.050000","species":"10","speed":"0.150000","type_id":"1"},{"disp_offset":{"x":"0.000000","y":"0.000000"},"path":{"type":"locus","x":{"cosine":"0.100000","sine":"0.370000","t0":"-0.500000","t1":"0.810000","t2":"0.000000","tc":"0.830000","ts":"0.960000"},"y":{"cosine":"0.290000","sine":"0.000000","t0":"0.400000","t1":"-0.190000","t2":"0.010000","tc":"0.130000","ts":"0.000000"}},"proxy":{"radius":"0.060000","type":"single"},"size":"0.050000","species":"11","speed":"0.050000","type_id":"1"},{"disp_offset":{"x":"0.000000","y":"0.000000"},"path":{"type":"locus","x":{"cosine":"0.050000","sine":"0.000000","t0":"-0.250000","t1":"0.000000","t2":"0.030000","tc":"0.200000","ts":"0.000000"},"y":{"cosine":"0.000000","sine":"0.200000","t0":"-0.200000","t1":"0.000000","t2":"0.020000","tc":"0.000000","ts":"0.200000"}},"proxy":{"height":"0.050000","type":"box","width":"0.350000"},"size":"0.050000","species":"12","speed":"0.200000","type_id":"1"},{"disp_offset":{"x":"0.025000","y":"0.000000"},"path":{"end":{"x":"2.000000","y":"0.500000"},"start":{"x":"-0.800000","y":"0.500000"},"type":"linear"},"proxy":{"height":"0.100000","type":"box","width":"0.100000"},"size":"0.050000","species":"13","speed":"0.040000","type_id":"1"},{"disp_offset":{"x":"0.000000","y":"0.000000"},"path":{"type":"locus","x":{"cosine":"0.000000","sine":"0.060000","t0":"0.000000","t1":"0.500000","t2":"0.000000","tc":"0.000000","ts":"0.200000"},"y":{"cosine":"0.140000","sine":"0.000000","t0":"0.600000","t1":"0.000000","t2":"0.000000","tc":"-0.190000","ts":"0.000000"}},"proxy":{"height":"0.070000","type":"box","width":"0.250000"},"size":"0.050000","species":"14","speed":"0.150000","type_id":"1"},{"disp_offset":{"x":"0.000000","y":"0.000000"},"path":{"type":"locus","x":{"cosine":"0.000000","sine":"0.120000","t0":"-0.200000","t1":"0.000000","t2":"0.040000","tc":"0.000000","ts":"0.130000"},"y":{"cosine":"0.130000","sine":"0.000000","t0":"0.500000","t1":"0.000000","t2":"0.000000","tc":"-0.140000","ts":"0.000000"}},"proxy":{"height":"0.050000","type":"box","width":"0.200000"},"size":"0.050000","species":"15","speed":"0.250000","type_id":"1"},{"disp_offset":{"x":"0.030000","y":"0.000000"},"path":{"type":"locus","x":{"cosine":"0.300000","sine":"0.000000","t0":"-0.300000","t1":"0.500000","t2":"0.000000","tc":"0.200000","ts":"0.000000"},"y":{"cosine":"0.000000","sine":"0.230000","t0":"0.300000","t1":"0.000000","t2":"0.000000","tc":"0.000000","ts":"0.170000"}},"proxy":{"height":"0.100000","type":"box","width":"0.100000"},"size":"0.050000","species":"16","speed":"0.150000","type_id":"1"},{"disp_offset":{"x":"0.000000","y":"0.000000"},"path":{"type":"locus","x":{"cosine":"0.000000","sine":"0.000000","t0":"0.000000","t1":"0.800000","t2":"0.000000","tc":"0.000000","ts":"0.000000"},"y":{"cosine":"0.300000","sine":"0.000000","t0":"0.000000","t1":"0.400000","t2":"0.000000","tc":"0.400000","ts":"0.000000"}},"proxy":{"height":"0.060000","type":"box","width":"0.350000"},"size":"0.050000","species":"17","speed":"0.100000","type_id":"1"},{"disp_offset":{"x":"0.050000","y":"0.000000"},"path":{"type":"locus","x":{"cosine":"0.000000","sine":"0.000000","t0":"0.000000","t1":"0.200000","t2":"0.000000","tc":"0.000000","ts":"0.000000"},"y":{"cosine":"0.100000","sine":"0.000000","t0":"0.000000","t1":"0.100000","t2":"0.000000","tc":"0.600000","ts":"0.000000"}},"proxy":{"height":"0.080000","type":"box","width":"0.250000"},"size":"0.050000","species":"18","speed":"0.200000","type_id":"1"},{"disp_offset":{"x":"0.000000","y":"0.000000"},"path":{"end":{"x":"2.000000","y":"0.700000"},"start":{"x":"-0.700000","y":"0.200000"},"type":"linear"},"proxy":{"height":"0.100000","type":"box","width":"0.400000"},"size":"0.050000","species":"19","speed":"0.024000","type_id":"1"},{"disp_offset":{"x":"0.030000","y":"0.000000"},"path":{"end":{"x":"2.700000","y":"0.500000"},"start":{"x":"-1.000000","y":"0.500000"},"type":"linear"},"proxy":{"height":"0.120000","type":"box","width":"0.400000"},"size":"0.050000","species":"20","speed":"0.006000","type_id":"1"},{"disp_offset":{"x":"0.000000","y":"0.000000"},"path":{"type":"locus","x":{"cosine":"0.780000","sine":"0.320000","t0":"0.000000","t1":"0.150000","t2":"0.030000","tc":"-0.430000","ts":"0.140000"},"y":{"cosine":"0.480000","sine":"0.160000","t0":"0.300000","t1":"-0.060000","t2":"0.050000","tc":"0.330000","ts":"0.130000"}},"proxy":{"height":"0.020000","type":"box","width":"0.060000"},"size":"0.050000","species":"21","speed":"0.150000","type_id":"1"},{"disp_offset":{"x":"0.000000","y":"0.000000"},"path":{"type":"locus","x":{"cosine":"0.280000","sine":"0.090000","t0":"-0.300000","t1":"0.320000","t2":"0.030000","tc":"0.130000","ts":"0.140000"},"y":{"cosine":"0.520000","sine":"0.160000","t0":"0.190000","t1":"0.060000","t2":"0.050000","tc":"0.230000","ts":"0.130000"}},"proxy":{"height":"0.020000","type":"box","width":"0.060000"},"size":"0.050000","species":"22","speed":"0.150000","type_id":"1"},{"disp_offset":{"x":"0.000000","y":"0.000000"},"path":{"type":"locus","x":{"cosine":"0.120000","sine":"0.060000","t0":"-0.500000","t1":"0.210000","t2":"0.050000","tc":"0.250000","ts":"0.100000"},"y":{"cosine":"0.070000","sine":"0.360000","t0":"0.000000","t1":"0.010000","t2":"0.020000","tc":"0.100000","ts":"0.180000"}},"proxy":{"height":"0.025000","type":"box","width":"0.070000"},"size":"0.050000","species":"23","speed":"0.200000","type_id":"1"},{"disp_offset":{"x":"0.000000","y":"0.000000"},"path":{"type":"locus","x":{"cosine":"0.520000","sine":"0.960000","t0":"-0.200000","t1":"0.710000","t2":"0.050000","tc":"0.250000","ts":"0.100000"},"y":{"cosine":"0.910000","sine":"0.360000","t0":"0.000000","t1":"0.110000","t2":"0.020000","tc":"0.100000","ts":"0.280000"}},"proxy":{"height":"0.025000","type":"box","width":"0.070000"},"size":"0.050000","species":"24","speed":"0.100000","type_id":"1"},{"disp_offset":{"x":"0.000000","y":"0.000000"},"path":{"type":"locus","x":{"cosine":"0.780000","sine":"0.230000","t0":"-0.050000","t1":"0.360000","t2":"0.050000","tc":"0.650000","ts":"0.100000"},"y":{"cosine":"0.170000","sine":"0.390000","t0":"-0.200000","t1":"0.140000","t2":"0.050000","tc":"0.270000","ts":"0.530000"}},"proxy":{"height":"0.030000","type":"box","width":"0.070000"},"size":"0.050000","species":"25","speed":"0.050000","type_id":"1"},{"disp_offset":{"x":"0.000000","y":"0.000000"},"path":{"type":"locus","x":{"cosine":"0.050000","sine":"0.130000","t0":"-0.050000","t1":"0.130000","t2":"0.010000","tc":"0.010000","ts":"0.390000"},"y":{"cosine":"0.190000","sine":"0.230000","t0":"0.100000","t1":"0.080000","t2":"0.020000","tc":"0.100000","ts":"0.230000"}},"proxy":{"height":"0.030000","type":"box","width":"0.070000"},"size":"0.050000","species":"26","speed":"0.200000","type_id":"1"},{"disp_offset":{"x":"0.000000","y":"0.000000"},"path":{"type":"locus","x":{"cosine":"0.000000","sine":"0.200000","t0":"0.120000","t1":"0.050000","t2":"0.040000","tc":"0.000000","ts":"0.430000"},"y":{"cosine":"0.260000","sine":"-0.600000","t0":"0.400000","t1":"0.000000","t2":"0.010000","tc":"0.100000","ts":"-0.130000"}},"proxy":{"height":"0.030000","type":"box","width":"0.090000"},"size":"0.050000","species":"27","speed":"0.300000","type_id":"1"},{"disp_offset":{"x":"0.000000","y":"0.000000"},"path":{"type":"locus","x":{"cosine":"0.190000","sine":"0.110000","t0":"0.000000","t1":"0.150000","t2":"0.030000","tc":"-0.430000","ts":"0.140000"},"y":{"cosine":"0.210000","sine":"0.160000","t0":"0.300000","t1":"-0.060000","t2":"0.050000","tc":"0.330000","ts":"0.130000"}},"proxy":{"height":"0.040000","type":"box","width":"0.100000"},"size":"0.050000","species":"28","speed":"0.150000","type_id":"1"},{"disp_offset":{"x":"0.000000","y":"0.000000"},"path":{"type":"locus","x":{"cosine":"0.670000","sine":"0.170000","t0":"0.000000","t1":"0.350000","t2":"0.010000","tc":"0.030000","ts":"0.490000"},"y":{"cosine":"0.120000","sine":"0.370000","t0":"0.000000","t1":"0.070000","t2":"0.000000","tc":"0.340000","ts":"0.110000"}},"proxy":{"height":"0.040000","type":"box","width":"0.100000"},"size":"0.050000","species":"29","speed":"0.150000","type_id":"1"},{"disp_offset":{"x":"0.000000","y":"0.000000"},"path":{"type":"locus","x":{"cosine":"0.230000","sine":"0.090000","t0":"0.000000","t1":"0.100000","t2":"0.020000","tc":"-0.130000","ts":"0.610000"},"y":{"cosine":"0.690000","sine":"0.150000","t0":"0.000000","t1":"0.100000","t2":"0.000000","tc":"0.020000","ts":"0.140000"}},"proxy":{"height":"0.040000","type":"box","width":"0.100000"},"size":"0.050000","species":"30","speed":"0.200000","type_id":"1"}],"species_count":"43","species_page":"0","stage_id":"673225579","status":"2","timestamp":"' . $curTime . '"}';
                                }
                                else if( $aids['mode'] == 2 && $aids['page'] == 1 ) 
                                {
                                    $result_tmp[] = '{"action":"69","background":"6","machine_id":"20005","player":[{"account_id":"' . $slotSettings->username . '","balance":"199992","denomination":"1","free_bullet":"11","multiplier":"1","seat_id":3}],"species":[{"disp_offset":{"x":"0.000000","y":"0.000000"},"path":{"type":"locus","x":{"cosine":"0.000000","sine":"0.000000","t0":"0.000000","t1":"0.500000","t2":"0.000000","tc":"0.000000","ts":"0.000000"},"y":{"cosine":"1.000000","sine":"0.000000","t0":"0.500000","t1":"0.000000","t2":"0.000000","tc":"0.050000","ts":"0.000000"}},"proxy":{"height":"0.020000","type":"box","width":"0.060000"},"size":"0.050000","species":"31","speed":"0.300000","type_id":"1"},{"disp_offset":{"x":"0.000000","y":"0.000000"},"path":{"type":"locus","x":{"cosine":"0.000000","sine":"0.000000","t0":"0.000000","t1":"0.600000","t2":"0.000000","tc":"0.000000","ts":"0.000000"},"y":{"cosine":"0.100000","sine":"0.000000","t0":"0.750000","t1":"0.000000","t2":"0.000000","tc":"0.000000","ts":"0.000000"}},"proxy":{"height":"0.025000","type":"box","width":"0.070000"},"size":"0.050000","species":"32","speed":"0.300000","type_id":"1"},{"disp_offset":{"x":"0.050000","y":"0.000000"},"path":{"type":"locus","x":{"cosine":"0.000000","sine":"0.000000","t0":"0.000000","t1":"0.500000","t2":"0.000000","tc":"0.000000","ts":"0.000000"},"y":{"cosine":"0.000000","sine":"0.000000","t0":"0.500000","t1":"0.000000","t2":"0.000000","tc":"0.000000","ts":"0.000000"}},"proxy":{"height":"0.080000","type":"box","width":"0.250000"},"size":"0.050000","species":"33","speed":"0.250000","type_id":"1"},{"disp_offset":{"x":"0.030000","y":"0.000000"},"path":{"type":"locus","x":{"cosine":"0.300000","sine":"0.000000","t0":"-0.300000","t1":"0.500000","t2":"0.000000","tc":"0.200000","ts":"0.000000"},"y":{"cosine":"0.000000","sine":"0.230000","t0":"0.300000","t1":"0.000000","t2":"0.000000","tc":"0.000000","ts":"0.170000"}},"proxy":{"height":"0.060000","type":"box","width":"0.400000"},"size":"0.050000","species":"34","speed":"0.150000","type_id":"1"},{"disp_offset":{"x":"0.000000","y":"0.000000"},"path":{"type":"locus","x":{"cosine":"0.000000","sine":"0.000000","t0":"0.000000","t1":"0.800000","t2":"0.000000","tc":"0.000000","ts":"0.000000"},"y":{"cosine":"0.300000","sine":"0.000000","t0":"0.000000","t1":"0.400000","t2":"0.000000","tc":"0.400000","ts":"0.000000"}},"proxy":{"height":"0.060000","type":"box","width":"0.350000"},"size":"0.050000","species":"35","speed":"0.100000","type_id":"1"},{"disp_offset":{"x":"0.050000","y":"0.000000"},"path":{"type":"locus","x":{"cosine":"0.000000","sine":"0.000000","t0":"0.000000","t1":"0.200000","t2":"0.000000","tc":"0.000000","ts":"0.000000"},"y":{"cosine":"0.100000","sine":"0.000000","t0":"0.000000","t1":"0.100000","t2":"0.000000","tc":"0.600000","ts":"0.000000"}},"proxy":{"height":"0.060000","type":"box","width":"0.400000"},"size":"0.050000","species":"36","speed":"0.200000","type_id":"1"},{"disp_offset":{"x":"0.000000","y":"0.000000"},"path":{"end":{"x":"2.000000","y":"0.700000"},"start":{"x":"-0.700000","y":"0.200000"},"type":"linear"},"proxy":{"height":"0.100000","type":"box","width":"0.400000"},"size":"0.050000","species":"37","speed":"0.024000","type_id":"1"},{"disp_offset":{"x":"0.030000","y":"0.000000"},"path":{"end":{"x":"2.700000","y":"0.500000"},"start":{"x":"-1.000000","y":"0.500000"},"type":"linear"},"proxy":{"height":"0.120000","type":"box","width":"0.500000"},"size":"0.050000","species":"38","speed":"0.006000","type_id":"1"},{"disp_offset":{"x":"0.100000","y":"0.000000"},"path":{"type":"locus","x":{"cosine":"0.300000","sine":"0.000000","t0":"-0.300000","t1":"0.500000","t2":"0.000000","tc":"0.200000","ts":"0.000000"},"y":{"cosine":"0.000000","sine":"0.230000","t0":"0.300000","t1":"0.000000","t2":"0.000000","tc":"0.000000","ts":"0.170000"}},"proxy":{"height":"0.100000","type":"box","width":"0.100000"},"size":"0.050000","species":"39","speed":"0.150000","type_id":"1"},{"disp_offset":{"x":"0.000000","y":"0.000000"},"path":{"type":"locus","x":{"cosine":"0.000000","sine":"0.000000","t0":"0.000000","t1":"0.800000","t2":"0.000000","tc":"0.000000","ts":"0.000000"},"y":{"cosine":"0.300000","sine":"0.000000","t0":"0.000000","t1":"0.400000","t2":"0.000000","tc":"0.400000","ts":"0.000000"}},"proxy":{"height":"0.120000","type":"box","width":"0.300000"},"size":"0.050000","species":"40","speed":"0.100000","type_id":"1"},{"disp_offset":{"x":"0.000000","y":"0.000000"},"path":{"type":"locus","x":{"cosine":"0.000000","sine":"0.000000","t0":"0.000000","t1":"0.200000","t2":"0.000000","tc":"0.000000","ts":"0.000000"},"y":{"cosine":"0.100000","sine":"0.000000","t0":"0.000000","t1":"0.100000","t2":"0.000000","tc":"0.600000","ts":"0.000000"}},"proxy":{"height":"0.120000","type":"box","width":"0.550000"},"size":"0.050000","species":"41","speed":"0.200000","type_id":"1"},{"disp_offset":{"x":"0.000000","y":"0.000000"},"path":{"end":{"x":"2.000000","y":"0.700000"},"start":{"x":"-0.700000","y":"0.200000"},"type":"linear"},"proxy":{"height":"0.100000","type":"box","width":"0.400000"},"size":"0.050000","species":"42","speed":"0.024000","type_id":"1"},{"disp_offset":{"x":"0.000000","y":"0.000000"},"path":{"end":{"x":"2.700000","y":"0.500000"},"start":{"x":"-1.000000","y":"0.500000"},"type":"linear"},"proxy":{"height":"0.120000","type":"box","width":"0.700000"},"size":"0.050000","species":"43","speed":"0.006000","type_id":"1"}],"species_count":"43","species_page":"1","stage_id":"673225579","status":"2","timestamp":"' . $curTime . '"}';
                                }
                                break;
                            case 'account_attack':
                                $CurrentBet = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentBet');
                                $CurrentMpl = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentMpl');
                                if( !isset($aids['target_id']) ) 
                                {
                                    $aids['target_id'] = 0;
                                }
                                $result_tmp[] = "{\r\n   \"action\" : \"70\",\r\n   \"bullet\" : {\r\n      \"balance\" : \"199700\",\r\n      \"coord\" : {\r\n         \"x\" : \"1.483134\",\r\n         \"y\" : \"0.921284\"\r\n      },\r\n      \"cost\" : \"" . ($CurrentBet * $CurrentMpl) . "\",\r\n      \"denomination\" : \"" . $CurrentBet . "\",\r\n      \"free_bullet\" : \"0\",\r\n      \"head_to\" : \"" . $aids['heading'] . "\",\r\n      \"id\" : \"" . rand(1, 900000) . "\",\r\n      \"multiplier\" : \"" . $CurrentMpl . "\",\r\n      \"player_id\" : \"2\",\r\n      \"rebound\" : {\r\n         \"x\" : \"0.000000\",\r\n         \"y\" : \"0.0\"\r\n      },\r\n      \"speed\" : \"1.000000\",\r\n      \"target_id\" : \"" . $aids['target_id'] . "\",\r\n      \"timestamp\" : \"" . $curTime . "\"\r\n   },\r\n   \"result\" : \"1\"\r\n}\r\n";
                                break;
                            case 'hit':
                                $CurrentBet = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentBet');
                                $CurrentMpl = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentMpl');
                                $allbet = ($CurrentBet * $CurrentMpl) / 100;
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
                                $bulletid = $aids['bid'];
                                $fishids = [$aids['inst_id']];
                                $fishpids = [$aids['fishType']];
                                $CurrentBet = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentBet');
                                $allbet = $CurrentBet / 100;
                                $totalWin = 0;
                                $totalWinsArr = [];
                                $bank = $slotSettings->GetBank('');
                                $isBombId = 0;
                                $isBomb = 0;
                                $isBombWin = 0;
                                $payRate = 0;
                                for( $i = 0; $i < count($fishpids); $i++ ) 
                                {
                                    $fishType = $fishpids[$i];
                                    if( $fishType == 21 ) 
                                    {
                                        $isBombId = $fishids[$i];
                                        $isBomb = 1;
                                        $isBombWin = rand(1, $slotSettings->FishDamage['Fish_' . $fishType][0]);
                                        $totalWinsArr[] = '{"uid":' . $isBombId . ',"score":0,"rate":0,"ext":0}';
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
                                        $totalWinsArr[] = '{"uid":' . $fishids[$i] . ',"score":' . round($payRate * $allbet * 100) . ',"rate":' . $payRate . ',"ext":0}';
                                    }
                                }
                                if( $totalWin > 0 ) 
                                {
                                    $slotSettings->SetBank('', -1 * $totalWin);
                                    $slotSettings->SetBalance($totalWin);
                                    $balanceInCents = round(sprintf('%01.2f', $slotSettings->GetBalance()) * 100);
                                    $result_tmp[] = "{\r\n   \"action\" : \"75\",\r\n   \"balance\" : \"" . $balanceInCents . "\",\r\n   \"bullet_id\" : \"" . $aids['bid'] . "\",\r\n   \"hitpoint\" : {\r\n      \"x\" : \"" . $aids['cX'] . "\",\r\n      \"y\" : \"" . $aids['cY'] . "\"\r\n   },\r\n   \"inst_id\" : \"" . $aids['inst_id'] . "\",\r\n   \"online\" : \"1\",\r\n   \"point_award\" : \"" . round($totalWin * 100) . "\",\r\n   \"point_multiply\" : \"" . $payRate . "\",\r\n   \"seat_id\" : \"2\",\r\n   \"timestamp\" : \"" . $curTime . "\"\r\n}\r\n";
                                }
                                $response = '{"responseEvent":"spin","responseType":"bet","serverResponse":{"slotBet":0,"totalFreeGames":0,"currentFreeGames":0,"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":0,"totalWin":' . $totalWin . ',"winLines":[],"Jackpots":[],"reelsSymbols":[]}}';
                                $slotSettings->SaveLogReport($response, $allbet, 1, $totalWin, 'bet');
                                break;
                            case 'quickenterroom':
                                if( $postData['message']['roomtype'] == 0 ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentBet', 1);
                                    $min = 1;
                                    $max = 200;
                                }
                                else if( $postData['message']['roomtype'] == 1 ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentBet', 10);
                                    $min = 10;
                                    $max = 2000;
                                }
                                else if( $postData['message']['roomtype'] == 2 ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentBet', 50);
                                    $min = 50;
                                    $max = 20000;
                                }
                                $fishes = [];
                                $cfishes = rand(10, 20);
                                for( $i = 0; $i < $cfishes; $i++ ) 
                                {
                                    $fishes[] = '{"id":' . rand(1, 9999999) . ',"classid":5,"fishid":' . rand(1, 12) . ',"born_time":' . $curTime . ',"routeid":' . rand(1, 110) . ',"dead_time":' . ($curTime + 60000) . ',"offsettype":0,"offsetx":0,"offsety":0,"offsetr":0,"rate":2,"ext":0}';
                                }
                                $CurrentBet = $slotSettings->GetGameData($slotSettings->slotId . 'CurrentBet');
                                $balanceInCents = round(sprintf('%01.2f', $slotSettings->GetBalance()) * 100);
                                $result_tmp[0] = '{"message":{"result":1,"roompos":3,"scenestate":5,"sceneid":1,"scene_etime":' . $curTime . ',"scene_btime":' . ($curTime + 80000) . ',"players":[{"uid":7401,"ws":0,"pos":5,"gunid":1,"gunnum":0,"reward_rate":' . $CurrentBet . ',"score":' . $balanceInCents . ',"isvistor":false}],"sprites":[],"bullets":[],"min":' . $min . ',"max":' . $max . ',"coinrate":1000,"bombs":[]},"succ":true,"errinfo":"ok","type":"enterroom"}';
                                break;
                        }
                        $slotSettings->SaveGameData();
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
