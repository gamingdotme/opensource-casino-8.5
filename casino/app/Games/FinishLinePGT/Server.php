<?php 
namespace VanguardLTE\Games\FinishLinePGT
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
                    $postData = $postData['gameData'];
                    $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                    $result_tmp = [];
                    $aid = '';
                    if( isset($_GET['command']) && $_GET['command'] == 'login' ) 
                    {
                        return '{"code":1,"description":"Store Login Success."}';
                    }
                    $aid = $postData['CmdId'];
                    switch( $aid ) 
                    {
                        case '1035':
                            $result_tmp[] = '{"timeout":false,"deviceID":0,"Version":1,"msgLength":0,"Retries":0,"Sequence":9,"MessageID":1035,"ReplyID":0,"CmdId":1,"Status":0,"MsgTime":"2020-10-02T23:40:48+09:00","MgsSendTime":"2020-10-02T23:40:48+09:00"}';
                            break;
                        case '302':
                            $result_tmp[] = '{"timeout":false,"deviceID":0,"Version":1,"msgLength":0,"Retries":0,"Sequence":4,"MessageID":302,"ReplyID":0,"Success":true,"Points":0,"FirstName":"Eric","LastName":"Heath","Upgraded":false,"SessionID":1588,"CashableBalance":0,"NonCashableBalance":' . $balanceInCents . ',"TotalBalance":' . $balanceInCents . ',"SweepstakesBalance":' . $balanceInCents . ',"OfflineMode":true,"ErrorMessage":"","CmdId":303,"MsgTime":"2020-10-02T22:59:49+09:00","MgsSendTime":"2020-10-02T22:59:49+09:00"}';
                            break;
                        case '4':
                            $result_tmp[] = '{"enabledFlag":1,"debugFlag":0,"timeout":false,"deviceID":0,"Version":1,"msgLength":0,"Retries":0,"Sequence":0,"MessageID":4,"ReplyID":0,"RemainingProfitPercentage":0,"CmdId":5,"Status":0,"MsgTime":"2020-10-02T22:09:37+09:00","MgsSendTime":"2020-10-02T22:09:37+09:00"}';
                            break;
                        case '150':
                            $result_tmp[] = '{"gtID":1588,"gameIDNum":1,"gameID":[5],"billLimit":0,"IRSLimit":120000,"lockupLimit":10000,"gameSound_vol":0,"attractVol":0,"attractVisual":0,"logLevel":0,"gtBank":0,"helpTimeout":15,"gameSkinID":233,"GTRegistered":1,"classType":0,"daubToWin":false,"daubWait":-1,"showBingoCard":true,"maxWaitForPlayers":5,"bToothpickMode":false,"bIRSWinInfoTicket":false,"bIRSAutoCashOut":false,"bIRSDualTicketCashOut":false,"bQuickStop":true,"strPlayerCardNumber":"","_GameGroupData":[{"_iDenomGroupID":13,"_iGameDefID":10,"_strGameName":"LuckyDuckLoot","_strDLLName":"LuckyDuckLoot","DenomGroupID":13,"GameDefID":10,"GameName":"LuckyDuckLoot","DLLName":"LuckyDuckLoot"},{"_iDenomGroupID":23,"_iGameDefID":204,"_strGameName":"BucksAndBucks","_strDLLName":"BucksAndBucks","DenomGroupID":23,"GameDefID":204,"GameName":"BucksAndBucks","DLLName":"BucksAndBucks"},{"_iDenomGroupID":26,"_iGameDefID":204,"_strGameName":"Goldorado","_strDLLName":"Goldorado","DenomGroupID":26,"GameDefID":204,"GameName":"Goldorado","DLLName":"Goldorado"},{"_iDenomGroupID":9,"_iGameDefID":10,"_strGameName":"FourLeafCashPennyPick","_strDLLName":"FourLeafCashPennyPick","DenomGroupID":9,"GameDefID":10,"GameName":"FourLeafCashPennyPick","DLLName":"FourLeafCashPennyPick"},{"_iDenomGroupID":8,"_iGameDefID":10,"_strGameName":"FinishLinePennyPickem","_strDLLName":"FinishLinePennyPickem","DenomGroupID":8,"GameDefID":10,"GameName":"FinishLinePennyPickem","DLLName":"FinishLinePennyPickem"},{"_iDenomGroupID":14,"_iGameDefID":10,"_strGameName":"LuckyDuckyPennyPickem","_strDLLName":"LuckyDuckyPennyPickem","DenomGroupID":14,"GameDefID":10,"GameName":"LuckyDuckyPennyPickem","DLLName":"LuckyDuckyPennyPickem"},{"_iDenomGroupID":-24,"_iGameDefID":204,"_strGameName":"BustinVegas","_strDLLName":"BustinVegas","DenomGroupID":-24,"GameDefID":204,"GameName":"BustinVegas","DLLName":"BustinVegas"},{"_iDenomGroupID":4,"_iGameDefID":10,"_strGameName":"BigDawgzPennyPickem","_strDLLName":"BigDawgzPennyPickem","DenomGroupID":4,"GameDefID":10,"GameName":"BigDawgzPennyPickem","DLLName":"BigDawgzPennyPickem"},{"_iDenomGroupID":5,"_iGameDefID":10,"_strGameName":"BreakfastBonanzaPickm","_strDLLName":"BreakfastBonanzaPickm","DenomGroupID":5,"GameDefID":10,"GameName":"BreakfastBonanzaPickm","DLLName":"BreakfastBonanzaPickm"},{"_iDenomGroupID":27,"_iGameDefID":204,"_strGameName":"HotterThanHell","_strDLLName":"HotterThanHell","DenomGroupID":27,"GameDefID":204,"GameName":"HotterThanHell","DLLName":"HotterThanHell"},{"_iDenomGroupID":11,"_iGameDefID":10,"_strGameName":"Inferno7sPennyPickem","_strDLLName":"Inferno7sPennyPickem","DenomGroupID":11,"GameDefID":10,"GameName":"Inferno7sPennyPickem","DLLName":"Inferno7sPennyPickem"},{"_iDenomGroupID":18,"_iGameDefID":10,"_strGameName":"RitzyKitty","_strDLLName":"RitzyKitty","DenomGroupID":18,"GameDefID":10,"GameName":"RitzyKitty","DLLName":"RitzyKitty"},{"_iDenomGroupID":25,"_iGameDefID":204,"_strGameName":"DeepSeaParty","_strDLLName":"DeepSeaParty","DenomGroupID":25,"GameDefID":204,"GameName":"DeepSeaParty","DLLName":"DeepSeaParty"},{"_iDenomGroupID":19,"_iGameDefID":10,"_strGameName":"Sizzlin7s","_strDLLName":"Sizzlin7s","DenomGroupID":19,"GameDefID":10,"GameName":"Sizzlin7s","DLLName":"Sizzlin7s"}],"bKnockOffCasino":false,"bStClairBingo":false,"iDaubTimeout":0,"iClaimTimeout":0,"bSpinReelsBeforeDaub":true,"bShowBallDrawBeforeDaub":true,"bPrintBlankBingoCard":false,"bPrintDaubedBingoCard":false,"iRedTintTriggerTime":0,"bRequireDaubOnFreeSpin":false,"bMarylandPulltab":false,"iMinPinLength":8,"iMaxPinLength":8,"siteID":1,"paramDeviceID":1607,"enabledFlag":1,"encryptionFlag":0,"snapTime":0,"syncTime":30,"hbInterval":30,"overrideLimit":50000,"inputType":0,"sessionID":0,"playerMode":2,"_tpsBackendType":5,"timeout":false,"deviceID":0,"Version":2,"msgLength":0,"Retries":0,"Sequence":1,"MessageID":150,"ReplyID":0,"CashableBalance":0,"NonCashableBalance":0,"TotalBalance":0,"gtBalance":0,"SweepstakesBalance":0,"FirstName":"","LastName":"","CommunityPrizeAnte":5,"CommunityPrizePrize1Item":null,"CommunityPrizePrize2Item":null,"CommunityPrizeEligibilitySeconds":15,"LuckyWinnerPrizeValue":30000,"WinnerPrizeValue":2500,"AccumulatorGamesEnabled":false,"GameDefID":204,"OpenSessionsRemotely":false,"CmdId":8,"LogoFileName":"testfile.gif","CurGameName":"BustinVegas","CurGameVersion":"2.5.3400.1","CurGameDLL":"BustinVegas","DeviceGuid":"","InstallationName":"","InstallationGuid":"","ApplicationName":"","ApplicationGuid":"","ApplicationVersion":"","SiteName":"Horizon","Location":"","SerialNumber":"","MessageKey":"AAAAAAAAAAAAAAAAAAAAAA==","MessageInitVector":"AAAAAAAAAAA=","ProgramName":"GameTerminal.exe","ProgramVersion":"1.2.0807.1","DeviceName":"","tpsType":2,"tpsBackendType":5,"SweepstakesType":1,"IsGatewayPlayerCard":true,"MsgTime":"2020-10-02T22:09:37+09:00","MgsSendTime":"2020-10-02T22:09:37+09:00"}';
                            $result_tmp[] = '{"gameID":5,"uGameDefID":204,"denomination":1,"maxLines":20,"maxCredits":10,"MinBet":0,"redLightPayout":25,"ringBellPayout":25,"percentPayout":9200,"playType":0,"duration":0,"credits":0,"prize":0,"NofN":65537,"timeout":false,"deviceID":0,"Version":1,"msgLength":0,"Retries":0,"Sequence":2,"MessageID":0,"ReplyID":0,"GameLevelCount":200,"PatternCount":1,"GameLevels":[{"GameID":5,"LinesPlayed":20,"CreditsWagered":10,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":20,"CreditsWagered":9,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":20,"CreditsWagered":8,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":20,"CreditsWagered":7,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":20,"CreditsWagered":6,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":20,"CreditsWagered":5,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":20,"CreditsWagered":4,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":20,"CreditsWagered":3,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":20,"CreditsWagered":2,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":20,"CreditsWagered":1,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":19,"CreditsWagered":10,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":19,"CreditsWagered":9,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":19,"CreditsWagered":8,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":19,"CreditsWagered":7,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":19,"CreditsWagered":6,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":19,"CreditsWagered":5,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":19,"CreditsWagered":4,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":19,"CreditsWagered":3,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":19,"CreditsWagered":2,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":19,"CreditsWagered":1,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":18,"CreditsWagered":10,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":18,"CreditsWagered":9,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":18,"CreditsWagered":8,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":18,"CreditsWagered":7,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":18,"CreditsWagered":6,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":18,"CreditsWagered":5,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":18,"CreditsWagered":4,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":18,"CreditsWagered":3,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":18,"CreditsWagered":2,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":18,"CreditsWagered":1,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":17,"CreditsWagered":10,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":17,"CreditsWagered":9,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":17,"CreditsWagered":8,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":17,"CreditsWagered":7,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":17,"CreditsWagered":6,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":17,"CreditsWagered":5,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":17,"CreditsWagered":4,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":17,"CreditsWagered":3,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":17,"CreditsWagered":2,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":17,"CreditsWagered":1,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":16,"CreditsWagered":10,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":16,"CreditsWagered":9,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":16,"CreditsWagered":8,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":16,"CreditsWagered":7,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":16,"CreditsWagered":6,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":16,"CreditsWagered":5,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":16,"CreditsWagered":4,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":16,"CreditsWagered":3,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":16,"CreditsWagered":2,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":16,"CreditsWagered":1,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":15,"CreditsWagered":10,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":15,"CreditsWagered":9,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":15,"CreditsWagered":8,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":15,"CreditsWagered":7,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":15,"CreditsWagered":6,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":15,"CreditsWagered":5,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":15,"CreditsWagered":4,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":15,"CreditsWagered":3,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":15,"CreditsWagered":2,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":15,"CreditsWagered":1,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":14,"CreditsWagered":10,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":14,"CreditsWagered":9,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":14,"CreditsWagered":8,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":14,"CreditsWagered":7,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":14,"CreditsWagered":6,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":14,"CreditsWagered":5,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":14,"CreditsWagered":4,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":14,"CreditsWagered":3,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":14,"CreditsWagered":2,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":14,"CreditsWagered":1,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":13,"CreditsWagered":10,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":13,"CreditsWagered":9,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":13,"CreditsWagered":8,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":13,"CreditsWagered":7,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":13,"CreditsWagered":6,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":13,"CreditsWagered":5,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":13,"CreditsWagered":4,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":13,"CreditsWagered":3,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":13,"CreditsWagered":2,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":13,"CreditsWagered":1,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":12,"CreditsWagered":10,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":12,"CreditsWagered":9,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":12,"CreditsWagered":8,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":12,"CreditsWagered":7,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":12,"CreditsWagered":6,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":12,"CreditsWagered":5,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":12,"CreditsWagered":4,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":12,"CreditsWagered":3,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":12,"CreditsWagered":2,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":12,"CreditsWagered":1,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":11,"CreditsWagered":10,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":11,"CreditsWagered":9,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":11,"CreditsWagered":8,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":11,"CreditsWagered":7,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":11,"CreditsWagered":6,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":11,"CreditsWagered":5,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":11,"CreditsWagered":4,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":11,"CreditsWagered":3,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":11,"CreditsWagered":2,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":11,"CreditsWagered":1,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":10,"CreditsWagered":10,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":10,"CreditsWagered":9,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":10,"CreditsWagered":8,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":10,"CreditsWagered":7,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":10,"CreditsWagered":6,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":10,"CreditsWagered":5,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":10,"CreditsWagered":4,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":10,"CreditsWagered":3,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":10,"CreditsWagered":2,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":10,"CreditsWagered":1,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":9,"CreditsWagered":10,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":9,"CreditsWagered":9,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":9,"CreditsWagered":8,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":9,"CreditsWagered":7,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":9,"CreditsWagered":6,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":9,"CreditsWagered":5,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":9,"CreditsWagered":4,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":9,"CreditsWagered":3,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":9,"CreditsWagered":2,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":9,"CreditsWagered":1,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":8,"CreditsWagered":10,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":8,"CreditsWagered":9,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":8,"CreditsWagered":8,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":8,"CreditsWagered":7,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":8,"CreditsWagered":6,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":8,"CreditsWagered":5,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":8,"CreditsWagered":4,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":8,"CreditsWagered":3,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":8,"CreditsWagered":2,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":8,"CreditsWagered":1,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":7,"CreditsWagered":10,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":7,"CreditsWagered":9,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":7,"CreditsWagered":8,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":7,"CreditsWagered":7,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":7,"CreditsWagered":6,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":7,"CreditsWagered":5,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":7,"CreditsWagered":4,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":7,"CreditsWagered":3,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":7,"CreditsWagered":2,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":7,"CreditsWagered":1,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":6,"CreditsWagered":10,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":6,"CreditsWagered":9,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":6,"CreditsWagered":8,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":6,"CreditsWagered":7,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":6,"CreditsWagered":6,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":6,"CreditsWagered":5,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":6,"CreditsWagered":4,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":6,"CreditsWagered":3,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":6,"CreditsWagered":2,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":6,"CreditsWagered":1,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":5,"CreditsWagered":10,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":5,"CreditsWagered":9,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":5,"CreditsWagered":8,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":5,"CreditsWagered":7,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":5,"CreditsWagered":6,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":5,"CreditsWagered":5,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":5,"CreditsWagered":4,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":5,"CreditsWagered":3,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":5,"CreditsWagered":2,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":5,"CreditsWagered":1,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":4,"CreditsWagered":10,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":4,"CreditsWagered":9,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":4,"CreditsWagered":8,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":4,"CreditsWagered":7,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":4,"CreditsWagered":6,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":4,"CreditsWagered":5,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":4,"CreditsWagered":4,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":4,"CreditsWagered":3,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":4,"CreditsWagered":2,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":4,"CreditsWagered":1,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":3,"CreditsWagered":10,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":3,"CreditsWagered":9,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":3,"CreditsWagered":8,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":3,"CreditsWagered":7,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":3,"CreditsWagered":6,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":3,"CreditsWagered":5,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":3,"CreditsWagered":4,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":3,"CreditsWagered":3,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":3,"CreditsWagered":2,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":3,"CreditsWagered":1,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":2,"CreditsWagered":10,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":2,"CreditsWagered":9,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":2,"CreditsWagered":8,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":2,"CreditsWagered":7,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":2,"CreditsWagered":6,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":2,"CreditsWagered":5,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":2,"CreditsWagered":4,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":2,"CreditsWagered":3,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":2,"CreditsWagered":2,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":2,"CreditsWagered":1,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":1,"CreditsWagered":10,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":1,"CreditsWagered":9,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":1,"CreditsWagered":8,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":1,"CreditsWagered":7,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":1,"CreditsWagered":6,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":1,"CreditsWagered":5,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":1,"CreditsWagered":4,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":1,"CreditsWagered":3,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":1,"CreditsWagered":2,"ProgressiveID":2,"GameAccumulatorID":-1},{"GameID":5,"LinesPlayed":1,"CreditsWagered":1,"ProgressiveID":2,"GameAccumulatorID":-1}],"Patterns":[{"LinesPlayed":0,"CreditsWagered":0,"Pattern":0,"CreditsWon":0,"PrizeTableID":0,"BonusSequence":0}],"InstantBingo":false,"ClassIIIGame":false,"CmdId":10,"GameName":"BustinVegas","GameVersion":"2.5.3400.1","GameDLL":"BustinVegas","MsgTime":"2020-10-02T22:09:37+09:00","MgsSendTime":"2020-10-02T22:09:37+09:00"}';
                            break;
                        case '67':
                            $TotalWin = round($slotSettings->GetGameData($slotSettings->slotId . 'TotalWin') * 100);
                            $result_tmp[] = '{"progBalance":0,"progressiveLineValuesWon":[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],"progressiveWon":false,"iSaveASpinBalance":0,"timeout":false,"deviceID":0,"Version":1,"msgLength":0,"Retries":0,"Sequence":8,"MessageID":67,"ReplyID":0,"CashableBalance":' . $TotalWin . ',"NonCashableBalance":' . $balanceInCents . ',"ErrorMessage":null,"GameEndingPatternHit":false,"PrizeClaimed":true,"AccumulatorCentsWon":0,"currentBalance":' . $balanceInCents . ',"ProgressiveName":"","CmdId":83,"MsgTime":"2020-11-01T20:06:55+09:00","MgsSendTime":"2020-11-01T20:06:55+09:00"}';
                            break;
                        case '20':
                            $linesId = [];
                            $linesId[0] = [
                                2, 
                                2, 
                                2, 
                                2, 
                                2
                            ];
                            $linesId[1] = [
                                1, 
                                1, 
                                1, 
                                1, 
                                1
                            ];
                            $linesId[2] = [
                                3, 
                                3, 
                                3, 
                                3, 
                                3
                            ];
                            $linesId[3] = [
                                1, 
                                2, 
                                3, 
                                2, 
                                1
                            ];
                            $linesId[4] = [
                                3, 
                                2, 
                                1, 
                                2, 
                                3
                            ];
                            $linesId[5] = [
                                2, 
                                1, 
                                2, 
                                3, 
                                2
                            ];
                            $linesId[6] = [
                                2, 
                                3, 
                                2, 
                                1, 
                                2
                            ];
                            $linesId[7] = [
                                1, 
                                1, 
                                2, 
                                3, 
                                3
                            ];
                            $linesId[8] = [
                                3, 
                                3, 
                                2, 
                                1, 
                                1
                            ];
                            $linesId[9] = [
                                1, 
                                2, 
                                1, 
                                2, 
                                1
                            ];
                            $linesId[10] = [
                                3, 
                                2, 
                                3, 
                                2, 
                                3
                            ];
                            $linesId[11] = [
                                2, 
                                3, 
                                2, 
                                3, 
                                2
                            ];
                            $linesId[12] = [
                                2, 
                                1, 
                                2, 
                                1, 
                                2
                            ];
                            $linesId[13] = [
                                2, 
                                1, 
                                1, 
                                1, 
                                2
                            ];
                            $linesId[14] = [
                                2, 
                                3, 
                                3, 
                                3, 
                                2
                            ];
                            $linesId[15] = [
                                1, 
                                2, 
                                2, 
                                2, 
                                1
                            ];
                            $linesId[16] = [
                                3, 
                                2, 
                                2, 
                                2, 
                                3
                            ];
                            $linesId[17] = [
                                1, 
                                3, 
                                1, 
                                3, 
                                1
                            ];
                            $linesId[18] = [
                                3, 
                                1, 
                                3, 
                                1, 
                                3
                            ];
                            $linesId[19] = [
                                2, 
                                3, 
                                1, 
                                3, 
                                2
                            ];
                            if( $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') < $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') ) 
                            {
                                $postData['slotEvent'] = 'freespin';
                            }
                            else
                            {
                                $postData['slotEvent'] = 'bet';
                            }
                            $lines = 20;
                            $betline = $postData['creditsWagered'] / 100;
                            $allbet = ($postData['creditsWagered'] * 30 + 5) / 100;
                            if( $postData['slotEvent'] == 'bet' ) 
                            {
                                if( $allbet <= 0 ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid bet state"}';
                                    exit( $response );
                                }
                                if( $slotSettings->GetBalance() < $allbet ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"","serverResponse":"invalid balance"}';
                                    exit( $response );
                                }
                            }
                            if( $postData['slotEvent'] != 'freespin' ) 
                            {
                                if( !isset($postData['slotEvent']) ) 
                                {
                                    $postData['slotEvent'] = 'bet';
                                }
                                $slotSettings->SetBalance(-1 * $allbet, $postData['slotEvent']);
                                $bankSum = $allbet / 100 * $slotSettings->GetPercent();
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $bankSum, $postData['slotEvent']);
                                $jackState = $slotSettings->UpdateJackpots($allbet);
                                $slotSettings->SetGameData($slotSettings->slotId . 'JackWinID', $jackState['isJackId']);
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', 0);
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', 0);
                                $bonusMpl = 1;
                            }
                            else
                            {
                                $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') + 1);
                                $bonusMpl = $slotSettings->slotFreeMpl;
                            }
                            $winTypeTmp = $slotSettings->GetSpinSettings($postData['slotEvent'], $allbet, $lines);
                            $winType = $winTypeTmp[0];
                            $spinWinLimit = $winTypeTmp[1];
                            $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                            if( isset($jackState) && $jackState['isJackPay'] ) 
                            {
                                $jackRandom = 1;
                            }
                            else
                            {
                                $jackRandom = 0;
                            }
                            for( $i = 0; $i <= 2000; $i++ ) 
                            {
                                $totalWin = 0;
                                $lineWins = [];
                                $cWins = [
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0
                                ];
                                $cWinsCoins = [
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0
                                ];
                                $wild = ['2'];
                                $scatter = '1';
                                $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
                                for( $k = 0; $k < 5; $k++ ) 
                                {
                                    $tmpStringWin = '';
                                    for( $j = 0; $j < count($slotSettings->SymbolGame); $j++ ) 
                                    {
                                        $csym = (string)$slotSettings->SymbolGame[$j];
                                        if( $csym == $scatter || !isset($slotSettings->Paytable['SYM_' . $csym]) ) 
                                        {
                                        }
                                        else
                                        {
                                            $s = [];
                                            $s[0] = $reels['reel1'][$linesId[$k][0] - 1];
                                            $s[1] = $reels['reel2'][$linesId[$k][1] - 1];
                                            $s[2] = $reels['reel3'][$linesId[$k][2] - 1];
                                            $s[3] = $reels['reel4'][$linesId[$k][3] - 1];
                                            $s[4] = $reels['reel5'][$linesId[$k][4] - 1];
                                            if( ($s[0] == $csym || in_array($s[0], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) ) 
                                            {
                                                $mpl = 1;
                                                if( in_array($s[0], $wild) && in_array($s[1], $wild) ) 
                                                {
                                                    $mpl = 1;
                                                }
                                                else if( in_array($s[0], $wild) || in_array($s[1], $wild) ) 
                                                {
                                                    $mpl = $slotSettings->slotWildMpl;
                                                }
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][2] * $betline * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"line":' . $k . ',"winAmount":' . ($cWins[$k] * 100) . ',"cells":[0,' . ($linesId[$k][0] - 1) . ',1,' . ($linesId[$k][1] - 1) . '],"freespins":0,"card":' . $csym . '}';
                                                }
                                            }
                                            if( ($s[0] == $csym || in_array($s[0], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) && ($s[2] == $csym || in_array($s[2], $wild)) ) 
                                            {
                                                $mpl = 1;
                                                if( in_array($s[0], $wild) && in_array($s[1], $wild) && in_array($s[2], $wild) ) 
                                                {
                                                    $mpl = 1;
                                                }
                                                else if( in_array($s[0], $wild) || in_array($s[1], $wild) || in_array($s[2], $wild) ) 
                                                {
                                                    $mpl = $slotSettings->slotWildMpl;
                                                }
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][3] * $betline * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"line":' . $k . ',"winAmount":' . ($cWins[$k] * 100) . ',"cells":[0,' . ($linesId[$k][0] - 1) . ',1,' . ($linesId[$k][1] - 1) . ',2,' . ($linesId[$k][2] - 1) . '],"freespins":0,"card":' . $csym . '}';
                                                }
                                            }
                                            if( ($s[0] == $csym || in_array($s[0], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) && ($s[2] == $csym || in_array($s[2], $wild)) && ($s[3] == $csym || in_array($s[3], $wild)) ) 
                                            {
                                                $mpl = 1;
                                                if( in_array($s[0], $wild) && in_array($s[1], $wild) && in_array($s[2], $wild) && in_array($s[3], $wild) ) 
                                                {
                                                    $mpl = 1;
                                                }
                                                else if( in_array($s[0], $wild) || in_array($s[1], $wild) || in_array($s[2], $wild) || in_array($s[3], $wild) ) 
                                                {
                                                    $mpl = $slotSettings->slotWildMpl;
                                                }
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][4] * $betline * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"line":' . $k . ',"winAmount":' . ($cWins[$k] * 100) . ',"cells":[0,' . ($linesId[$k][0] - 1) . ',1,' . ($linesId[$k][1] - 1) . ',2,' . ($linesId[$k][2] - 1) . ',3,' . ($linesId[$k][3] - 1) . '],"freespins":0,"card":' . $csym . '}';
                                                }
                                            }
                                            if( ($s[0] == $csym || in_array($s[0], $wild)) && ($s[1] == $csym || in_array($s[1], $wild)) && ($s[2] == $csym || in_array($s[2], $wild)) && ($s[3] == $csym || in_array($s[3], $wild)) && ($s[4] == $csym || in_array($s[4], $wild)) ) 
                                            {
                                                $mpl = 1;
                                                if( in_array($s[0], $wild) && in_array($s[1], $wild) && in_array($s[2], $wild) && in_array($s[3], $wild) && in_array($s[4], $wild) ) 
                                                {
                                                    $mpl = 1;
                                                }
                                                else if( in_array($s[0], $wild) || in_array($s[1], $wild) || in_array($s[2], $wild) || in_array($s[3], $wild) || in_array($s[4], $wild) ) 
                                                {
                                                    $mpl = $slotSettings->slotWildMpl;
                                                }
                                                $tmpWin = $slotSettings->Paytable['SYM_' . $csym][5] * $betline * $mpl * $bonusMpl;
                                                if( $cWins[$k] < $tmpWin ) 
                                                {
                                                    $cWins[$k] = $tmpWin;
                                                    $tmpStringWin = '{"line":' . $k . ',"winAmount":' . ($cWins[$k] * 100) . ',"cells":[0,' . ($linesId[$k][0] - 1) . ',1,' . ($linesId[$k][1] - 1) . ',2,' . ($linesId[$k][2] - 1) . ',3,' . ($linesId[$k][3] - 1) . ',4,' . ($linesId[$k][4] - 1) . '],"freespins":0,"card":' . $csym . '}';
                                                }
                                            }
                                        }
                                    }
                                    if( $cWins[$k] > 0 && $tmpStringWin != '' ) 
                                    {
                                        array_push($lineWins, $tmpStringWin);
                                        $totalWin += $cWins[$k];
                                        $cWinsCoins[$k] = round($cWins[$k] * 100);
                                    }
                                }
                                $scattersWin = 0;
                                $scattersStr = '';
                                $scattersCount = 0;
                                $scPos = [];
                                for( $r = 1; $r <= 5; $r++ ) 
                                {
                                    for( $p = 0; $p <= 2; $p++ ) 
                                    {
                                        if( $reels['reel' . $r][$p] == $scatter ) 
                                        {
                                            $scattersCount++;
                                            $scPos[] = '' . ($r - 1) . ',' . $p . '';
                                        }
                                    }
                                }
                                $scattersWin = $slotSettings->Paytable['SYM_' . $scatter][$scattersCount] * $betline * $bonusMpl;
                                $altBonusCredits = [
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0, 
                                    0
                                ];
                                if( $scattersWin > 0 ) 
                                {
                                    if( $scattersCount >= 3 ) 
                                    {
                                        $altBonusCredits = [
                                            0, 
                                            round($scattersWin * 100), 
                                            400, 
                                            800, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0
                                        ];
                                    }
                                    else
                                    {
                                        $altBonusCredits = [
                                            round($scattersWin * 100), 
                                            0, 
                                            400, 
                                            800, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0, 
                                            0
                                        ];
                                    }
                                }
                                $totalWin += $scattersWin;
                                if( $i > 1000 ) 
                                {
                                    $winType = 'none';
                                }
                                if( $i > 1500 ) 
                                {
                                    $response = '{"responseEvent":"error","responseType":"' . $postData['slotEvent'] . '","serverResponse":"' . $totalWin . ' Bad Reel Strip"}';
                                    exit( $response );
                                }
                                if( $slotSettings->MaxWin < ($totalWin * $slotSettings->CurrentDenom) ) 
                                {
                                }
                                else
                                {
                                    $minWin = $slotSettings->GetRandomPay();
                                    if( $i > 700 ) 
                                    {
                                        $minWin = 0;
                                    }
                                    if( $slotSettings->increaseRTP && $winType == 'win' && $totalWin < ($minWin * $allbet) ) 
                                    {
                                    }
                                    else if( $scattersCount >= 3 && $winType != 'bonus' ) 
                                    {
                                    }
                                    else if( $totalWin <= $spinWinLimit && $winType == 'bonus' ) 
                                    {
                                        $cBank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                                        if( $cBank < $spinWinLimit ) 
                                        {
                                            $spinWinLimit = $cBank;
                                        }
                                        else
                                        {
                                            break;
                                        }
                                    }
                                    else if( $totalWin > 0 && $totalWin <= $spinWinLimit && $winType == 'win' ) 
                                    {
                                        $cBank = $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''));
                                        if( $cBank < $spinWinLimit ) 
                                        {
                                            $spinWinLimit = $cBank;
                                        }
                                        else
                                        {
                                            break;
                                        }
                                    }
                                    else if( $totalWin == 0 && $winType == 'none' ) 
                                    {
                                        break;
                                    }
                                }
                            }
                            if( $totalWin > 0 ) 
                            {
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), -1 * $totalWin);
                                $slotSettings->SetBalance($totalWin);
                            }
                            $reportWin = $totalWin;
                            if( $postData['slotEvent'] == 'freespin' ) 
                            {
                                $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') + $totalWin);
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $slotSettings->GetGameData($slotSettings->slotId . 'TotalWin') + $totalWin);
                                $balanceInCents = $slotSettings->GetGameData($slotSettings->slotId . 'FreeBalance');
                            }
                            else
                            {
                                $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $totalWin);
                            }
                            $fs = 0;
                            if( $scattersCount >= 3 ) 
                            {
                                if( $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') > 0 ) 
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') + 10);
                                }
                                else
                                {
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeStartWin', $totalWin);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $totalWin);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $slotSettings->slotFreeCount);
                                }
                                $fs = $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames');
                            }
                            $winString = implode(',', $lineWins);
                            $jsSpin = '' . json_encode($reels) . '';
                            $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                            $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"$scattersWin":' . $scattersWin . ',"slotLines":' . $lines . ',"slotBet":' . $betline . ',"totalFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') . ',"Balance":' . $balanceInCents . ',"afterBalance":' . $balanceInCents . ',"bonusWin":' . $slotSettings->GetGameData($slotSettings->slotId . 'BonusWin') . ',"totalWin":' . $totalWin . ',"winLines":[' . $winString . '],"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                            $slotSettings->SaveLogReport($response, $allbet, $lines, $reportWin, $postData['slotEvent']);
                            $winstring = '';
                            $curReels0 = $reels['reel1'][2] . ',' . $reels['reel1'][1] . ',' . $reels['reel1'][0] . ',' . rand(3, 6);
                            $curReels1 = $reels['reel2'][2] . ',' . $reels['reel2'][1] . ',' . $reels['reel2'][0] . ',' . rand(3, 6);
                            $curReels2 = $reels['reel3'][2] . ',' . $reels['reel3'][1] . ',' . $reels['reel3'][0] . ',' . rand(3, 6);
                            $curReels3 = $reels['reel4'][2] . ',' . $reels['reel4'][1] . ',' . $reels['reel4'][0] . ',' . rand(3, 6);
                            $curReels4 = $reels['reel5'][2] . ',' . $reels['reel5'][1] . ',' . $reels['reel5'][0] . ',' . rand(3, 6);
                            if( $postData['slotEvent'] == 'freespin' ) 
                            {
                                $bFreeSpin = 'true';
                            }
                            else
                            {
                                $bFreeSpin = 'false';
                            }
                            $result_tmp[] = '{"linesPlayed":20,"creditsWagered":' . $postData['creditsWagered'] . ',"creditsPlayed":20,"centsWon":' . ($totalWin * 100) . ',"reels":{"reel1":[' . $curReels0 . '],"reel2":[' . $curReels1 . '],"reel3":[' . $curReels2 . '],"reel4":[' . $curReels3 . '],"reel5":[' . $curReels4 . ']},"lineResults":"AAAAAAAAAAAAAAAAAAAAAAAAAAA=","lineCredits":[' . implode(',', $cWinsCoins) . '],"bonusWon":' . round($scattersWin * 100) . ',"gameNumber":20,"deckNumber":1,"cardNumber":1,"cardFace":[[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0]],"cardResults":0,"bonusResults":"AAAAAAAAAAAAAAAAAAAAAAAAAAA=","bonusCredits":[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],"progressiveID":2,"contribProgID":2,"progressiveWon":false,"progressivePrizeLevels":[false,false,false,false,false,false,false],"progressiveLinesWon":[false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false],"draw":[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],"altBonusCredits":[' . implode(',', $altBonusCredits) . '],"cancelPlay":false,"bSaveASpinWagered":false,"bSaveASpinWon":false,"iBonusSequence":0,"bFreeSpin":' . $bFreeSpin . ',"iAdditionalCentsWagered":10,"iFreeSpinsWon":0,"_iKenoBallDraw":[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],"iSpinDataID":-2146637097,"bGameEndingPattern":false,"bNoEligibleCommunityPrizeGTs":false,"gameID":4,"seqNo":0,"timeout":false,"deviceID":0,"Version":1,"msgLength":0,"Retries":0,"Sequence":7,"MessageID":20,"ReplyID":0,"Success":true,"ErrorMessage":null,"KenoPickedSpotsCount":20,"AntedForCommunityPrize":true,"DontBroadcastCommunityPrizeAnnouncement":false,"CmdId":19,"isCommunityPrize":false,"MsgTime":"2020-10-01T22:50:42+09:00","MgsSendTime":"2020-10-01T22:50:42+09:00"}';
                            break;
                    }
                    $response = implode('------:::', $result_tmp);
                    $slotSettings->SaveGameData();
                    $slotSettings->SaveGameDataStatic();
                    echo ':::' . $response;
                }
                catch( \Exception $e ) 
                {
                    $slotSettings->InternalErrorSilent($e);
                }
            }, 5);
        }
    }

}
