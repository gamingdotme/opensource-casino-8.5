<?php 
namespace VanguardLTE\Games\BuffaloPGD
{
    set_time_limit(5);
    class Server
    {
        public function get($request, $game)
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
                    $postData = json_decode(trim(file_get_contents('php://input')), true);
                    if( isset($postData['command']) && $postData['command'] == 'CheckAuth' ) 
                    {
                        $response = '{"responseEvent":"CheckAuth","startTimeSystem":' . (time() * 1000) . ',"userId":' . $userId . ',"shop_id":' . $slotSettings->shop_id . ',"username":"' . $slotSettings->username . '"}';
                        exit( $response );
                    }
                    $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                    $result_tmp = [];
                    $aid = '';
                    $baseEncode = false;
                    if( isset($_GET['command']) && $_GET['command'] == 'lobby' && !isset($_GET['command2']) ) 
                    {
                        exit( 'e345f6ff3f2805fd944d35be0bd4aa8e' );
                    }
                    if( isset($_GET['command']) ) 
                    {
                        $baseEncode = true;
                        $tmpReq0 = json_decode(base64_decode(file_get_contents('php://input')), true);
                        $tmpReq1 = json_decode(base64_decode($tmpReq0['ark_data']), true);
                        if( isset($tmpReq1['cmd_name']) ) 
                        {
                            $aid = $tmpReq1['cmd_name'];
                        }
                        $_GET['command'] == 'sys';
                        if( isset($_GET['command2']) && $_GET['command2'] == 'login' ) 
                        {
                            $_GET['command'] == 'sys';
                            $aid = 'login';
                        }
                        if( isset($_GET['command2']) && $_GET['command2'] == 'auth' ) 
                        {
                            $_GET['command'] == 'sys';
                            $aid = 'auth0';
                        }
                    }
                    if( isset($postData['cmd']) ) 
                    {
                        $aid = $postData['cmd'];
                    }
                    switch( $aid ) 
                    {
                        case 'getCommonEventInfo':
                            $responseStr = '{"cmd_sn":"' . (time() * 1000) . '","cmd_data":{"900004":{"data":{"Enable":false,"BeginTimeUTC":1595995200,"EndTimeUTC":1916366400,"NowTimeUTC":1607954022,"QuestList":[{"GameThemeID":"148001","QuestID":"MonsterFrenzyQuest_Type1","CustomType":4,"CycleSeconds":86400,"ExpireTimeUTC":1608008400,"ExtraInfo":null,"Award":{"AwardWinnings":0,"AwardEntries":0,"AwardItem":[{"ItemID":"MF100002","Amount":1,"ItemLevel":1}],"Type":4,"AwardQuest":null},"CustomInfo":{},"Priority":1,"WinAmountGate":0,"SpinTimesGate":0,"QuestTitle":"MonsterFrenzyQuestLv01_01","TitleType":200,"QuestLevel":1,"CustomGate":30,"SpinAmountGate":0},{"GameThemeID":"152001","QuestID":"FishKingKongQuest_Type1","CustomType":4,"CycleSeconds":86400,"ExpireTimeUTC":1608008400,"ExtraInfo":null,"Award":{"AwardWinnings":0,"AwardEntries":0,"AwardItem":[{"ItemID":"MF100002","Amount":1,"ItemLevel":1}],"Type":4,"AwardQuest":null},"CustomInfo":{},"Priority":1,"WinAmountGate":0,"SpinTimesGate":0,"QuestTitle":"FishKingKongQuestLv01_01","TitleType":200,"QuestLevel":1,"CustomGate":30,"SpinAmountGate":0},{"GameThemeID":"149001","QuestID":"FishBuffaloQuest_Type3","CustomType":5,"CycleSeconds":86400,"ExpireTimeUTC":1608008400,"ExtraInfo":null,"Award":{"AwardWinnings":0,"AwardEntries":0,"AwardItem":[{"ItemID":"MF100001","Amount":2,"ItemLevel":1}],"Type":4,"AwardQuest":null},"CustomInfo":{"FishName":"BUFFALO"},"Priority":3,"WinAmountGate":0,"SpinTimesGate":0,"QuestTitle":"FishBuffaloQuestLv01_03","TitleType":200,"QuestLevel":1,"CustomGate":1,"SpinAmountGate":0},{"GameThemeID":"152001","QuestID":"FishKingKongQuest_Type3","CustomType":5,"CycleSeconds":86400,"ExpireTimeUTC":1608008400,"ExtraInfo":null,"Award":{"AwardWinnings":0,"AwardEntries":0,"AwardItem":[{"ItemID":"MF100001","Amount":2,"ItemLevel":1}],"Type":4,"AwardQuest":null},"CustomInfo":{"FishName":"BUFFALO"},"Priority":3,"WinAmountGate":0,"SpinTimesGate":0,"QuestTitle":"FishKingKongQuestLv01_03","TitleType":200,"QuestLevel":1,"CustomGate":1,"SpinAmountGate":0},{"GameThemeID":"148001","QuestID":"MonsterFrenzyQuest_Type3","CustomType":5,"CycleSeconds":86400,"ExpireTimeUTC":1608008400,"ExtraInfo":null,"Award":{"AwardWinnings":0,"AwardEntries":0,"AwardItem":[{"ItemID":"MF100001","Amount":2,"ItemLevel":1}],"Type":4,"AwardQuest":null},"CustomInfo":{"FishName":"HOYEAH_FISH"},"Priority":3,"WinAmountGate":0,"SpinTimesGate":0,"QuestTitle":"MonsterFrenzyQuestLv01_03","TitleType":200,"QuestLevel":1,"CustomGate":1,"SpinAmountGate":0},{"GameThemeID":"126001","QuestID":"Daily_LuckyFortune_1214","CustomType":0,"CycleSeconds":86400,"ExpireTimeUTC":1608008400,"ExtraInfo":null,"Award":{"AwardWinnings":100,"AwardEntries":0,"AwardItem":null,"Type":0,"AwardQuest":null},"CustomInfo":{},"Priority":1,"WinAmountGate":0,"SpinTimesGate":0,"QuestTitle":"LuckyFortune","TitleType":0,"QuestLevel":1,"CustomGate":0,"SpinAmountGate":10000},{"GameThemeID":"152001","QuestID":"FishKingKongQuest_Type2","CustomType":6,"CycleSeconds":86400,"ExpireTimeUTC":1608008400,"ExtraInfo":null,"Award":{"AwardWinnings":0,"AwardEntries":0,"AwardItem":[{"ItemID":"MF100001","Amount":1,"ItemLevel":1}],"Type":4,"AwardQuest":null},"CustomInfo":{"ItemID":null,"ItemUseType":0},"Priority":2,"WinAmountGate":0,"SpinTimesGate":0,"QuestTitle":"FishKingKongQuestLv01_02","TitleType":200,"QuestLevel":1,"CustomGate":4,"SpinAmountGate":0},{"GameThemeID":"149001","QuestID":"FishBuffaloQuest_Type1","CustomType":4,"CycleSeconds":86400,"ExpireTimeUTC":1608008400,"ExtraInfo":null,"Award":{"AwardWinnings":0,"AwardEntries":0,"AwardItem":[{"ItemID":"MF100002","Amount":1,"ItemLevel":1}],"Type":4,"AwardQuest":null},"CustomInfo":{},"Priority":1,"WinAmountGate":0,"SpinTimesGate":0,"QuestTitle":"FishBuffaloQuestLv01_01","TitleType":200,"QuestLevel":1,"CustomGate":30,"SpinAmountGate":0},{"GameThemeID":"TreasureMap","QuestID":"RMap1208_Jp_NC","CustomType":3,"CycleSeconds":691199,"ExpireTimeUTC":1608094799,"ExtraInfo":{"ranking_list":[{"amount":157,"ranking_id":"95909225","ark_id":"10919645","time":1607489588},{"amount":113,"ranking_id":"32654940","ark_id":"10949566","time":1607489687},{"amount":93,"ranking_id":"20278358","ark_id":"10670692","time":1607487132},{"amount":83,"ranking_id":"50066522","ark_id":"11384731","time":1607489173},{"amount":76,"ranking_id":"97146986","ark_id":"11318103","time":1607486702},{"amount":72,"ranking_id":"94477054","ark_id":"10958000","time":1607487821},{"amount":68,"ranking_id":"34037524","ark_id":"10748982","time":1607489545},{"amount":67,"ranking_id":"61767761","ark_id":"10650960","time":1607486942},{"amount":64,"ranking_id":"89435880","ark_id":"10906619","time":1607485963},{"amount":63,"ranking_id":"60255029","ark_id":"10670916","time":1607487573},{"amount":55,"ranking_id":"80941044","ark_id":"10946091","time":1607482204},{"amount":54,"ranking_id":"27312968","ark_id":"10914353","time":1607486604},{"amount":53,"ranking_id":"92323511","ark_id":"10853283","time":1607489852},{"amount":51,"ranking_id":"22217247","ark_id":"11293250","time":1607489031},{"amount":49,"ranking_id":"37605521","ark_id":"10834511","time":1607477477}],"ranking_id":"86911241","people":389,"extra_jp_value":0,"exchange_rate":0.01,"jp_value":1830635,"quest_id":"RMap1208_Jp_NC","state_id":"NC","jp_begin_time":1607403600,"jp_end_time":1607490000},"Award":{"AwardWinnings":0,"AwardEntries":0,"AwardItem":null,"Type":3,"AwardQuest":null},"CustomInfo":{"RankAward":[{"Begin":1,"End":1,"Award":100000},{"Begin":2,"End":2,"Award":80000},{"Begin":3,"End":3,"Award":70000},{"Begin":4,"End":4,"Award":60000},{"Begin":5,"End":5,"Award":50000},{"Begin":6,"End":6,"Award":40000},{"Begin":7,"End":7,"Award":35000},{"Begin":8,"End":8,"Award":30000},{"Begin":9,"End":9,"Award":25000},{"Begin":10,"End":10,"Award":20000},{"Begin":11,"End":11,"Award":17500},{"Begin":12,"End":12,"Award":15000},{"Begin":13,"End":13,"Award":12500},{"Begin":14,"End":15,"Award":10000}],"InitValue":100000,"RankEnable":true,"DonateRate":99.6,"ExtraValue":0,"RankNum":15},"Priority":0,"WinAmountGate":0,"SpinTimesGate":0,"QuestTitle":"RMap1208_Jp_NC","TitleType":102,"QuestLevel":1,"CustomGate":9,"SpinAmountGate":0},{"GameThemeID":"149001","QuestID":"FishBuffaloQuest_Type2","CustomType":6,"CycleSeconds":86400,"ExpireTimeUTC":1608008400,"ExtraInfo":null,"Award":{"AwardWinnings":0,"AwardEntries":0,"AwardItem":[{"ItemID":"MF100001","Amount":1,"ItemLevel":1}],"Type":4,"AwardQuest":null},"CustomInfo":{"ItemID":null,"ItemUseType":0},"Priority":2,"WinAmountGate":0,"SpinTimesGate":0,"QuestTitle":"FishBuffaloQuestLv01_02","TitleType":200,"QuestLevel":1,"CustomGate":4,"SpinAmountGate":0},{"GameThemeID":"TreasureMap","QuestID":"RMap1210_Jp_NC","CustomType":3,"CycleSeconds":691199,"ExpireTimeUTC":1608267599,"ExtraInfo":{"ranking_list":[{"amount":164,"ranking_id":"32654940","ark_id":"10949566","time":1607661901},{"amount":146,"ranking_id":"86901511","ark_id":"11278320","time":1607657939},{"amount":102,"ranking_id":"99743674","ark_id":"10695743","time":1607662567},{"amount":97,"ranking_id":"47952899","ark_id":"10436268","time":1607662406},{"amount":92,"ranking_id":"34037524","ark_id":"10748982","time":1607662689},{"amount":91,"ranking_id":"50066522","ark_id":"11384731","time":1607658442},{"amount":90,"ranking_id":"44714559","ark_id":"10864997","time":1607662425},{"amount":82,"ranking_id":"66849444","ark_id":"11339081","time":1607647685},{"amount":77,"ranking_id":"75717829","ark_id":"10589524","time":1607655812},{"amount":70,"ranking_id":"20711401","ark_id":"10892207","time":1607641335},{"amount":63,"ranking_id":"40627958","ark_id":"10454593","time":1607661505},{"amount":63,"ranking_id":"62115882","ark_id":"10842523","time":1607661831},{"amount":60,"ranking_id":"65054942","ark_id":"10612407","time":1607659767},{"amount":58,"ranking_id":"56467679","ark_id":"10693975","time":1607661275},{"amount":57,"ranking_id":"25362889","ark_id":"11282469","time":1607659648}],"ranking_id":"86911241","people":443,"extra_jp_value":0,"exchange_rate":0.01,"jp_value":2082594,"quest_id":"RMap1210_Jp_NC","state_id":"NC","jp_begin_time":1607576400,"jp_end_time":1607662800},"Award":{"AwardWinnings":0,"AwardEntries":0,"AwardItem":null,"Type":3,"AwardQuest":null},"CustomInfo":{"RankAward":[{"Begin":1,"End":1,"Award":100000},{"Begin":2,"End":2,"Award":80000},{"Begin":3,"End":3,"Award":70000},{"Begin":4,"End":4,"Award":60000},{"Begin":5,"End":5,"Award":50000},{"Begin":6,"End":6,"Award":40000},{"Begin":7,"End":7,"Award":35000},{"Begin":8,"End":8,"Award":30000},{"Begin":9,"End":9,"Award":25000},{"Begin":10,"End":10,"Award":20000},{"Begin":11,"End":11,"Award":17500},{"Begin":12,"End":12,"Award":15000},{"Begin":13,"End":13,"Award":12500},{"Begin":14,"End":15,"Award":10000}],"InitValue":100000,"RankEnable":true,"DonateRate":99.6,"ExtraValue":0,"RankNum":15},"Priority":0,"WinAmountGate":0,"SpinTimesGate":0,"QuestTitle":"RMap1210_Jp_NC","TitleType":102,"QuestLevel":1,"CustomGate":9,"SpinAmountGate":0},{"GameThemeID":"TreasureMap","QuestID":"RMap1212_Jp_NC","CustomType":3,"CycleSeconds":691199,"ExpireTimeUTC":1608440399,"ExtraInfo":{"ranking_list":[{"amount":149,"ranking_id":"64602987","ark_id":"11223612","time":1607835509},{"amount":132,"ranking_id":"20278358","ark_id":"10670692","time":1607831829},{"amount":100,"ranking_id":"65709184","ark_id":"11337876","time":1607820114},{"amount":88,"ranking_id":"94768184","ark_id":"10596632","time":1607835461},{"amount":80,"ranking_id":"87435337","ark_id":"11149576","time":1607835002},{"amount":79,"ranking_id":"81463479","ark_id":"11295224","time":1607835503},{"amount":71,"ranking_id":"90921339","ark_id":"10423097","time":1607767995},{"amount":71,"ranking_id":"76476620","ark_id":"10863947","time":1607789925},{"amount":71,"ranking_id":"19524864","ark_id":"11424144","time":1607800279},{"amount":67,"ranking_id":"21026889","ark_id":"11143409","time":1607831810},{"amount":65,"ranking_id":"44714559","ark_id":"10864997","time":1607832978},{"amount":63,"ranking_id":"57554638","ark_id":"10539749","time":1607784064},{"amount":63,"ranking_id":"25362889","ark_id":"11282469","time":1607821264},{"amount":59,"ranking_id":"87030951","ark_id":"10692414","time":1607766648},{"amount":59,"ranking_id":"94900735","ark_id":"11040989","time":1607835194}],"ranking_id":"86911241","people":455,"extra_jp_value":0,"exchange_rate":0.01,"jp_value":2221760,"quest_id":"RMap1212_Jp_NC","state_id":"NC","jp_begin_time":1607749200,"jp_end_time":1607835600},"Award":{"AwardWinnings":0,"AwardEntries":0,"AwardItem":null,"Type":3,"AwardQuest":null},"CustomInfo":{"RankAward":[{"Begin":1,"End":1,"Award":100000},{"Begin":2,"End":2,"Award":80000},{"Begin":3,"End":3,"Award":70000},{"Begin":4,"End":4,"Award":60000},{"Begin":5,"End":5,"Award":50000},{"Begin":6,"End":6,"Award":40000},{"Begin":7,"End":7,"Award":35000},{"Begin":8,"End":8,"Award":30000},{"Begin":9,"End":9,"Award":25000},{"Begin":10,"End":10,"Award":20000},{"Begin":11,"End":11,"Award":17500},{"Begin":12,"End":12,"Award":15000},{"Begin":13,"End":13,"Award":12500},{"Begin":14,"End":15,"Award":10000}],"InitValue":100000,"RankEnable":true,"DonateRate":99.6,"ExtraValue":0,"RankNum":15},"Priority":0,"WinAmountGate":0,"SpinTimesGate":0,"QuestTitle":"RMap1212_Jp_NC","TitleType":102,"QuestLevel":1,"CustomGate":9,"SpinAmountGate":0},{"GameThemeID":"108001","QuestID":"Daily_Hot7_1214","CustomType":0,"CycleSeconds":86400,"ExpireTimeUTC":1608008400,"ExtraInfo":null,"Award":{"AwardWinnings":100,"AwardEntries":0,"AwardItem":null,"Type":0,"AwardQuest":null},"CustomInfo":{},"Priority":2,"WinAmountGate":0,"SpinTimesGate":0,"QuestTitle":"Hot7","TitleType":0,"QuestLevel":1,"CustomGate":0,"SpinAmountGate":10000},{"GameThemeID":"148001","QuestID":"MonsterFrenzyQuest_Type2","CustomType":6,"CycleSeconds":86400,"ExpireTimeUTC":1608008400,"ExtraInfo":null,"Award":{"AwardWinnings":0,"AwardEntries":0,"AwardItem":[{"ItemID":"MF100001","Amount":1,"ItemLevel":1}],"Type":4,"AwardQuest":null},"CustomInfo":{"ItemID":null,"ItemUseType":0},"Priority":2,"WinAmountGate":0,"SpinTimesGate":0,"QuestTitle":"MonsterFrenzyQuestLv01_02","TitleType":200,"QuestLevel":1,"CustomGate":4,"SpinAmountGate":0},{"GameThemeID":"104001","QuestID":"Daily_LuckyShamrock_1214","CustomType":0,"CycleSeconds":86400,"ExpireTimeUTC":1608008400,"ExtraInfo":null,"Award":{"AwardWinnings":100,"AwardEntries":0,"AwardItem":null,"Type":0,"AwardQuest":null},"CustomInfo":{},"Priority":3,"WinAmountGate":0,"SpinTimesGate":0,"QuestTitle":"LuckyShamrock","TitleType":0,"QuestLevel":1,"CustomGate":0,"SpinAmountGate":10000}],"UserQuestData":[{"Status":0,"GameThemeID":"148001","QuestID":"MonsterFrenzyQuest_Type1","ExpireTimeUTC":"2020-12-15T05:00:00","CustomAmount":0,"CollectTimeUTC":null,"SpinAmount":0,"QuestLevel":1,"SpinTimes":0,"WinAmount":0},{"Status":0,"GameThemeID":"152001","QuestID":"FishKingKongQuest_Type1","ExpireTimeUTC":"2020-12-15T05:00:00","CustomAmount":0,"CollectTimeUTC":null,"SpinAmount":0,"QuestLevel":1,"SpinTimes":0,"WinAmount":0},{"Status":0,"GameThemeID":"149001","QuestID":"FishBuffaloQuest_Type3","ExpireTimeUTC":"2020-12-15T05:00:00","CustomAmount":0,"CollectTimeUTC":null,"SpinAmount":100,"QuestLevel":1,"SpinTimes":20,"WinAmount":245},{"Status":0,"GameThemeID":"152001","QuestID":"FishKingKongQuest_Type3","ExpireTimeUTC":"2020-12-15T05:00:00","CustomAmount":0,"CollectTimeUTC":null,"SpinAmount":0,"QuestLevel":1,"SpinTimes":0,"WinAmount":0},{"Status":0,"GameThemeID":"148001","QuestID":"MonsterFrenzyQuest_Type3","ExpireTimeUTC":"2020-12-15T05:00:00","CustomAmount":0,"CollectTimeUTC":null,"SpinAmount":0,"QuestLevel":1,"SpinTimes":0,"WinAmount":0},{"Status":0,"GameThemeID":"126001","QuestID":"Daily_LuckyFortune_1214","ExpireTimeUTC":"2020-12-15T05:00:00","CustomAmount":0,"CollectTimeUTC":null,"SpinAmount":0,"QuestLevel":1,"SpinTimes":0,"WinAmount":0},{"Status":0,"GameThemeID":"152001","QuestID":"FishKingKongQuest_Type2","ExpireTimeUTC":"2020-12-15T05:00:00","CustomAmount":0,"CollectTimeUTC":null,"SpinAmount":0,"QuestLevel":1,"SpinTimes":0,"WinAmount":0},{"Status":0,"GameThemeID":"149001","QuestID":"FishBuffaloQuest_Type1","ExpireTimeUTC":"2020-12-15T05:00:00","CustomAmount":3,"CollectTimeUTC":null,"SpinAmount":100,"QuestLevel":1,"SpinTimes":20,"WinAmount":245},{"Status":0,"GameThemeID":"TreasureMap","QuestID":"RMap1208_Jp_NC","ExpireTimeUTC":"2020-12-16T04:59:59","CustomAmount":0,"CollectTimeUTC":null,"SpinAmount":0,"QuestLevel":1,"SpinTimes":0,"WinAmount":0},{"Status":0,"GameThemeID":"149001","QuestID":"FishBuffaloQuest_Type2","ExpireTimeUTC":"2020-12-15T05:00:00","CustomAmount":0,"CollectTimeUTC":null,"SpinAmount":100,"QuestLevel":1,"SpinTimes":20,"WinAmount":245},{"Status":0,"GameThemeID":"TreasureMap","QuestID":"RMap1210_Jp_NC","ExpireTimeUTC":"2020-12-18T04:59:59","CustomAmount":0,"CollectTimeUTC":null,"SpinAmount":0,"QuestLevel":1,"SpinTimes":0,"WinAmount":0},{"Status":0,"GameThemeID":"TreasureMap","QuestID":"RMap1212_Jp_NC","ExpireTimeUTC":"2020-12-20T04:59:59","CustomAmount":0,"CollectTimeUTC":null,"SpinAmount":0,"QuestLevel":1,"SpinTimes":0,"WinAmount":0},{"Status":0,"GameThemeID":"108001","QuestID":"Daily_Hot7_1214","ExpireTimeUTC":"2020-12-15T05:00:00","CustomAmount":0,"CollectTimeUTC":null,"SpinAmount":0,"QuestLevel":1,"SpinTimes":0,"WinAmount":0},{"Status":0,"GameThemeID":"148001","QuestID":"MonsterFrenzyQuest_Type2","ExpireTimeUTC":"2020-12-15T05:00:00","CustomAmount":0,"CollectTimeUTC":null,"SpinAmount":0,"QuestLevel":1,"SpinTimes":0,"WinAmount":0},{"Status":0,"GameThemeID":"104001","QuestID":"Daily_LuckyShamrock_1214","ExpireTimeUTC":"2020-12-15T05:00:00","CustomAmount":0,"CollectTimeUTC":null,"SpinAmount":0,"QuestLevel":1,"SpinTimes":0,"WinAmount":0}]},"result":0}}}';
                            $responseL = str_pad(strlen($responseStr), 4, '0', STR_PAD_LEFT);
                            $result_tmp[] = $responseStr;
                            break;
                        case 'KioskUrl':
                            $responseStr = '{"cmd_sn":"' . (time() * 1000) . '","cmd_data":{"status":0,"url":"f:6081","surl":"f:5081"}}';
                            $responseL = str_pad(strlen($responseStr), 4, '0', STR_PAD_LEFT);
                            $result_tmp[] = $responseStr;
                            break;
                        case 'getClientBannerAdv':
                            $responseStr = '{"cmd_sn":"' . (time() * 1000) . '","cmd_data":{"data":{"banner_version":"974.0","banner_info":[{"button_action":"GAME_ID=152001","button_text":"GO","priority":1,"mode":[0],"startTime":"2020-08-28T04:00:00","bundle_name":"2020_Banner_RagingFire","endTime":"2020-12-31T04:00:00"},{"button_action":"CUSTOM_ID=TreasureMap","button_text":"GO","priority":2,"mode":[0],"startTime":"2020-09-22T04:00:00","bundle_name":"2020_Banner_TreasureMap1","endTime":"2020-12-31T04:00:00"}]},"result":0}}';
                            $responseL = str_pad(strlen($responseStr), 4, '0', STR_PAD_LEFT);
                            $result_tmp[] = $responseStr;
                            break;
                        case 'getClientPopUpAdv':
                            $responseStr = '{"cmd_sn":"' . (time() * 1000) . '","cmd_data":{"data":{"pop_up_version":"1041.0","pop_up_info":[{"button_action":"CUSTOM_ID=getBonusKey","button_text":"GO","priority":0,"mode":[0],"startTime":"2020-08-28T04:00:00","bundle_name":"PhoneGetBonus_PopUp","endTime":"2020-12-31T12:00:00"},{"button_action":"CUSTOM_ID=TreasureMap","button_text":"GO","priority":1,"mode":[0],"startTime":"2020-09-22T04:00:00","bundle_name":"PopUp_TreasuMap","endTime":"2021-12-31T04:00:00"},{"button_action":"CUSTOM_ID=RankingBonus","button_text":"GO","priority":3,"mode":[0],"startTime":"2020-09-01T04:00:00","bundle_name":"PopUp_TreaMapRankBonus","endTime":"2021-12-31T04:00:00"}]},"result":0}}';
                            $responseL = str_pad(strlen($responseStr), 4, '0', STR_PAD_LEFT);
                            $result_tmp[] = $responseStr;
                            break;
                        case 'getUserCellphoneVerify':
                            $responseStr = '{"cmd_sn":"' . (time() * 1000) . '","cmd_data":{"data":{"error_msg":"Phone Number Registration Bonus has ended."},"result":-28}}';
                            $responseL = str_pad(strlen($responseStr), 4, '0', STR_PAD_LEFT);
                            $result_tmp[] = $responseStr;
                            break;
                        case 'getKioskGameSetting':
                            $responseStr = '{"cmd_sn":"' . (time() * 1000) . '","cmd_data":{"data":{"disable_game_type_list":["3"],"disable_game_list":["103001"]},"result":0}}';
                            $responseL = str_pad(strlen($responseStr), 4, '0', STR_PAD_LEFT);
                            $result_tmp[] = $responseStr;
                            break;
                        case 'getLobbyInfo':
                            $responseStr = '{"cmd_sn":"' . (time() * 1000) . '","cmd_data":{"data":{"score_box":true,"donate":false,"name":"Promotional Software","allow_state":"NC","mobile":true,"shutter_skill":false,"mode":0,"skill_game_rate":0.9,"skill_game_enable":true,"internet_time":false,"shutter":false,"machine_id":"1167"},"result":0}}';
                            $responseL = str_pad(strlen($responseStr), 4, '0', STR_PAD_LEFT);
                            $result_tmp[] = $responseStr;
                            break;
                        case 'getUserProperty':
                            $responseStr = '{"cmd_sn":"' . (time() * 1000) . '","cmd_data":{"data":{"last_name":"","purchase_revert":false,"winnings":275.0,"drivers_license":"","last_purchase_internet_after":0,"client_purchase_serial_id":3,"kiosk_id":"8442799","first_name":"eric123","last_purchase":"2020-11-28T01:14:02.997000","ark_id":"11357450","mobile_password_update_time":"2020-12-10T15:04:05.729000","create_local_time":"2020-11-27T20:13:55.009000","first_purchase_time":"2020-11-27T20:14:02.993000","last_purchase_amount":50,"jp_total_bet_temp":125,"editor":"system","mail":"","mobile_password_update_editor":"4650456","last_login_machineID":"1167","enable":true,"comps_flag":"2020-11-28T01:14:02.997000","last_purchase_comps":10,"phone":"","birthday":"2020-11-27","CompsIn":275.0,"entries":5175.0,"last_comps_type":2,"exchange_money":0.0,"gender":"1","last_enter_themeID":"149001","password":"81dc9bdb52d04dc20036dbd8313ed055","create_time":"2020-11-28T01:13:55.009000","pin_id":"4650456","internet_time":19200.0,"default_password":false},"result":0}}';
                            $responseL = str_pad(strlen($responseStr), 4, '0', STR_PAD_LEFT);
                            $result_tmp[] = $responseStr;
                            break;
                        case 'lobby':
                            $responseStr = '{"cmd_sn":"' . (time() * 1000) . '","cmd_data":null}';
                            $responseL = str_pad(strlen($responseStr), 4, '0', STR_PAD_LEFT);
                            $result_tmp[] = $responseStr;
                            break;
                        case 'connect':
                            $responseStr = '{"cmd_sn":"' . (time() * 1000) . '","cmd_data":{"status":0,"url":"35.182.81.157:6000","surl":"lobby002.goldendragoncity.com:5000"}}';
                            $responseL = str_pad(strlen($responseStr), 4, '0', STR_PAD_LEFT);
                            $result_tmp[] = $responseStr;
                            break;
                        case 'auth0':
                            $responseStr = '{"ark_id":"11414255","ark_token":"a7222f3fa795d96d23028782d81ccc3f"}';
                            $responseL = str_pad(strlen($responseStr), 4, '0', STR_PAD_LEFT);
                            $result_tmp[] = $responseStr;
                            break;
                        case 'login':
                            $responseStr = '{"auto_id":"11414255","invite_code":"NSAVRLHXYW"}';
                            $responseL = str_pad(strlen($responseStr), 4, '0', STR_PAD_LEFT);
                            $result_tmp[] = $responseStr;
                            break;
                        case 'verify_mobile':
                            $responseStr = '{"data":{"kiosk_id":"8442799","pin_id":"4650456","is_guest":false,"default_password":false,"machine_id":"1167","device_id":"11357450"},"result":0}';
                            $responseL = str_pad(strlen($responseStr), 4, '0', STR_PAD_LEFT);
                            $result_tmp[] = $responseStr;
                            break;
                        case 'auth':
                            $responseStr = '{"data":{"status":0},"sn":"' . (time() * 1000) . '","ret":"auth"}';
                            $responseL = str_pad(strlen($responseStr), 4, '0', STR_PAD_LEFT);
                            $result_tmp[] = $responseL . $responseStr;
                            break;
                        case 'pin':
                            $responseStr = '{"sys":"lobby","data":{"result":0},"sn":"' . (time() * 1000) . '","ret":"pin"}';
                            $responseL = str_pad(strlen($responseStr), 4, '0', STR_PAD_LEFT);
                            $result_tmp[] = $responseL . $responseStr;
                            $responseStr = '{"sys":"lobby","cmd":"UpdateInfo","data":{"game_maintain_list":[],"msg_info":{"msgContent":"Please be advised that the following behaviors are NOT allowed on GD. Any violation will lead to terminate the account immediately!","param":[],"platform":["POS"],"msgType":0,"mode":[0],"duration":10},"game_version":{"PC":"1.11912.0","FireBall":"1.11371.0","FireStorm":"1.11371.0","PC_H5":"1278.3"}}}';
                            $responseL = str_pad(strlen($responseStr), 4, '0', STR_PAD_LEFT);
                            $result_tmp[] = $responseL . $responseStr;
                            break;
                        case 'jp':
                            $responseStr = '{"sys":"jp","data":{"data":{"jp_rate":"0.000001","exchange_rate":0.01,"jp1":0,"jp0":0,"jp3":0,"jp2":0},"result":"0"},"sn":"' . (time() * 1000) . '","ret":"jp"}';
                            $responseL = str_pad(strlen($responseStr), 4, '0', STR_PAD_LEFT);
                            $result_tmp[] = $responseL . $responseStr;
                            break;
                        case 'alive':
                            $responseStr = '{"sys":"lobby","cmd":"UpdateInfo","data":{"game_maintain_list":[],"msg_info":{},"game_version":{"PC":"1.11912.0","FireBall":"1.11371.0","FireStorm":"1.11371.0","PC_H5":"1278.3"}}}';
                            $responseL = str_pad(strlen($responseStr), 4, '0', STR_PAD_LEFT);
                            $result_tmp[] = $responseL . $responseStr;
                            break;
                        case 'JOIN_GAME':
                            $responseStr = '{"cmd_sn":"' . (time() * 1000) . '","cmd_data":{"data":{"A14":0,"GS":{"GS1":true,"GS0":-1,"GS3":1,"GS2":0,"GS5":{},"GS4":30},"eventInfo":{"900004":{}},"A13":{"1":15,"0":3,"3":3,"2":10},"A12":0,"A10":{"11":[20],"10":[25],"13":[5],"12":[10],"15":[0],"14":[0],"17":[0],"16":[0],"19":[0],"18":[0],"1":[6095],"0":[10810],"3":[1000],"2":[3100],"5":[400],"4":[500],"7":[100],"6":[250],"9":[50],"20":[0],"8":[75]},"A1":30,"A0":1,"A3":16,"A2":30,"A5":10000000,"A4":1,"A7":[0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16],"A6":0,"A9":5,"A8":[30],"A11":{},"WB":[{"WB7":{"0":[2,15,5,13,16,12,7,13,9,14,4,18,20,21,10,15,11,8,10,12,20,1,21,17,1,16,7,3,19,6,19,17,16,20,4,21,18,10,11,6,18,15,19,20,14,14,7,19,11,17]},"WB0":0,"WB8":{"0":[20,18,13]}},{"WB7":{"0":[15,11,8,10,12,4,18,20,21,10,12,7,13,9,14,2,15,5,13,14,20,15,21,17,1,16,18,3,19,6,14,7,19,11,17,18,1,19,20,16,21,7,10,11,6,19,17,16,20,4]},"WB0":1,"WB8":{"0":[16,20,10]}},{"WB7":{"0":[14,19,7,11,17,18,1,19,20,1,12,7,13,9,14,2,15,5,13,16,4,18,10,20,21,15,11,10,8,6,16,18,3,19,6,20,15,21,17,12,19,16,17,4,20,21,7,10,11,6]},"WB0":2,"WB8":{"0":[6,21,20]}},{"WB7":{"0":[18,16,3,19,6,20,15,21,17,1,4,18,20,21,15,10,11,8,10,14,12,7,13,9,14,2,15,13,5,12,14,19,7,11,17,18,19,1,20,16,19,17,16,20,4,21,7,10,11,6]},"WB0":3,"WB8":{"0":[20,17,1]}},{"WB7":{"0":[19,17,16,4,20,21,7,10,11,6,12,7,13,9,14,15,2,5,13,16,14,7,11,19,17,18,1,19,20,14,18,4,20,21,10,15,11,8,10,12,16,18,3,19,6,20,21,15,1,17]},"WB0":4,"WB8":{"0":[12,19,11]}}]},"result":0,"playerInfo":{"P0":' . $balanceInCents . ',"P1":0.0}}}';
                            $result_tmp[0] = base64_encode($responseStr);
                            break;
                        case 'playerFlow':
                            $responseStr = '{"cmd_sn":"' . (time() * 1000) . '","cmd_data":null}';
                            $result_tmp[0] = base64_encode($responseStr);
                            break;
                    }
                    $response = implode('------', $result_tmp);
                    $slotSettings->SaveGameData();
                    if( $baseEncode ) 
                    {
                        echo base64_encode($response);
                    }
                    else
                    {
                        echo $response;
                    }
                }
                catch( \Exception $e ) 
                {
                    $slotSettings->InternalErrorSilent($e);
                }
            }, 5);
        }
    }

}
