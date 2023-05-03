<?php 
namespace VanguardLTE\Games\DiceAndRoll40EGT
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
                        $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                        $result_tmp = [];
                        $aid = '';
                        if( $postData['command'] == 'bet' && $postData['bet']['gameCommand'] == 'collect' ) 
                        {
                            $postData['command'] = 'collect';
                        }
                        if( $postData['command'] == 'bet' && $postData['bet']['gameCommand'] == 'gamble' ) 
                        {
                            $postData['command'] = 'gamble';
                        }
                        if( $postData['command'] == 'bet' && $postData['bet']['gameCommand'] == 'jackpot' ) 
                        {
                            $postData['command'] = 'jackpot';
                        }
                        if( $postData['command'] == 'gamble' && $slotSettings->GetGameData($slotSettings->slotId . 'TotalWin') <= 0 ) 
                        {
                            $response = '{"responseEvent":"error","responseType":"' . $postData['command'] . '","serverResponse":"invalid gamble state"}';
                            exit( $response );
                        }
                        if( $postData['command'] == 'bet' ) 
                        {
                            $lines = $postData['bet']['lines'];
                            $betline = $postData['bet']['bet'] / 100;
                            if( $lines <= 0 || $betline <= 0.0001 ) 
                            {
                                $response = '{"responseEvent":"error","responseType":"' . $postData['command'] . '","serverResponse":"invalid bet state"}';
                                exit( $response );
                            }
                            if( $slotSettings->GetBalance() < ($lines * $betline) ) 
                            {
                                $response = '{"responseEvent":"error","responseType":"' . $postData['command'] . '","serverResponse":"invalid balance"}';
                                exit( $response );
                            }
                            if( $slotSettings->GetGameData($slotSettings->slotId . 'FreeGames') < $slotSettings->GetGameData($slotSettings->slotId . 'CurrentFreeGame') && $postData['bet']['bonus'] == 'true' ) 
                            {
                                $response = '{"responseEvent":"error","responseType":"' . $postData['command'] . '","serverResponse":"invalid bonus state"}';
                                exit( $response );
                            }
                        }
                        $aid = (string)$postData['command'];
                        switch( $aid ) 
                        {
                            case 'login':
                                $result_tmp[] = '{"playerName":"' . $slotSettings->username . '","balance":' . $balanceInCents . ',"currency":"' . $slotSettings->slotCurrency . '","languages":["en","bg","it","es","go","ru","fr","ro"],"groups":["all","cards","classic","diceSlots","extraline","fruit","keno","multiline","roulette"],"showRtp":false,"multigame":true,"sendTotalsInfo":false,"complex":{"CDJSlot":[{"gameIdentificationNumber":521,"recovery":"norecovery","gameName":"Caramel Dice","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":136,"name":"all"},{"order":14,"name":"diceSlots"},{"order":136,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"ZWJSlot":[{"gameIdentificationNumber":807,"recovery":"norecovery","gameName":"Zodiac Wheel","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":19,"name":"all"},{"order":1,"name":"classic"},{"order":19,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"TSHJSlot":[{"gameIdentificationNumber":803,"recovery":"norecovery","gameName":"20 Super Hot","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":10,"name":"all"},{"order":6,"name":"fruit"},{"order":10,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"ARJSlot":[{"gameIdentificationNumber":520,"recovery":"norecovery","gameName":"Almighty Ramses II","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":102,"name":"all"},{"order":23,"name":"multiline"},{"order":102,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"IGTJSlot":[{"gameIdentificationNumber":865,"recovery":"norecovery","gameName":"Inca Gold II","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":97,"name":"all"},{"order":20,"name":"multiline"},{"order":97,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"FBDJSlot":[{"gameIdentificationNumber":536,"recovery":"norecovery","gameName":"40 Burning Dice","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":138,"name":"all"},{"order":16,"name":"diceSlots"},{"order":138,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"TDRJSlot":[{"gameIdentificationNumber":882,"recovery":"norecovery","gameName":"2 Dragons","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":75,"name":"all"},{"order":15,"name":"classic"},{"order":75,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"CMJSlot":[{"gameIdentificationNumber":850,"recovery":"norecovery","gameName":"Casino Mania","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":106,"name":"all"},{"order":26,"name":"classic"},{"order":106,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"APJSlot":[{"gameIdentificationNumber":884,"recovery":"norecovery","gameName":"Aloha Party","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":57,"name":"all"},{"order":8,"name":"extraline"},{"order":57,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"FOKBJPoker":[{"gameIdentificationNumber":20210,"recovery":"norecovery","gameName":"Four of a Kind Bonus Poker","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":121,"name":"all"},{"order":2,"name":"cards"},{"order":121,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"RWJSlot":[{"gameIdentificationNumber":899,"recovery":"norecovery","gameName":"Rich World","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":61,"name":"all"},{"order":5,"name":"multiline"},{"order":61,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"FRSJSlot":[{"gameIdentificationNumber":853,"recovery":"norecovery","gameName":"Frog Story","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":25,"name":"all"},{"order":2,"name":"multiline"},{"order":25,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"Roulette":[{"subscriberName":"Roulette","automatic":true,"flashVideoStream":"rtmp://video.egtmgs.com/live","flashVideoSdp":"house-rlt03","rouletteCycleInterval":24,"gameNumber":"","denomination":100,"casinoName":"EGT","playerBet":{},"rouletteType":"AUTOMATIC","gameIdentificationNumber":20201,"recovery":"norecovery","gameName":"EGT Roulette","featured":false,"mlmJackpot":false,"totalBet":0,"groups":[{"order":2,"name":"all"},{"order":2,"name":"roulette"},{"order":2,"name":"myGames"}]}],"TBJJSlot":[{"gameIdentificationNumber":513,"recovery":"norecovery","gameName":"The Big Journey","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":78,"name":"all"},{"order":17,"name":"classic"},{"order":78,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"CIJSlot":[{"gameIdentificationNumber":514,"recovery":"norecovery","gameName":"Coral Island","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":73,"name":"all"},{"order":13,"name":"classic"},{"order":73,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"UHJSlot":[{"gameIdentificationNumber":802,"recovery":"norecovery","gameName":"Ultimate Hot","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":17,"name":"all"},{"order":13,"name":"fruit"},{"order":17,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"WHJSlot":[{"gameIdentificationNumber":523,"recovery":"norecovery","gameName":"Wonderheart","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":56,"name":"all"},{"order":4,"name":"multiline"},{"order":56,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"XSJSlot":[{"gameIdentificationNumber":822,"recovery":"norecovery","gameName":"Extra Stars","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":24,"name":"all"},{"order":17,"name":"fruit"},{"order":24,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"LADJSlot":[{"gameIdentificationNumber":515,"recovery":"norecovery","gameName":"Like a Diamond","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":114,"name":"all"},{"order":28,"name":"classic"},{"order":114,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"FHJSlot":[{"gameIdentificationNumber":805,"recovery":"norecovery","gameName":"Flaming Hot","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":12,"name":"all"},{"order":8,"name":"fruit"},{"order":12,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"KGJSlot":[{"gameIdentificationNumber":846,"recovery":"norecovery","gameName":"Kashmir Gold","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":91,"name":"all"},{"order":16,"name":"multiline"},{"order":91,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"TSOAJSlot":[{"gameIdentificationNumber":896,"recovery":"norecovery","gameName":"The Story of Alexander","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":43,"name":"all"},{"order":5,"name":"extraline"},{"order":43,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"FHSJSlot":[{"gameIdentificationNumber":879,"recovery":"norecovery","gameName":"50 Horses","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":67,"name":"all"},{"order":10,"name":"extraline"},{"order":67,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"GOLJSlot":[{"gameIdentificationNumber":823,"recovery":"norecovery","gameName":"Game of Luck","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":53,"name":"all"},{"order":8,"name":"classic"},{"order":53,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"SDJSlot":[{"gameIdentificationNumber":20224,"recovery":"norecovery","gameName":"Supreme Dice","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":126,"name":"all"},{"order":4,"name":"diceSlots"},{"order":126,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"RMJSlot":[{"gameIdentificationNumber":504,"recovery":"norecovery","gameName":"Route of Mexico","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":72,"name":"all"},{"order":11,"name":"multiline"},{"order":72,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"QORJSlot":[{"gameIdentificationNumber":512,"recovery":"norecovery","gameName":"Queen of Rio","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":86,"name":"all"},{"order":22,"name":"classic"},{"order":86,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"FMJSlot":[{"gameIdentificationNumber":501,"recovery":"norecovery","gameName":"Fast Money","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":87,"name":"all"},{"order":23,"name":"classic"},{"order":87,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"HDJSlot":[{"gameIdentificationNumber":893,"recovery":"norecovery","gameName":"100 Dice","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":133,"name":"all"},{"order":11,"name":"diceSlots"},{"order":133,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"TEJSlot":[{"gameIdentificationNumber":834,"recovery":"norecovery","gameName":"The Explorers","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":107,"name":"all"},{"order":26,"name":"multiline"},{"order":107,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"IDJSlot":[{"gameIdentificationNumber":867,"recovery":"norecovery","gameName":"Ice Dice","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":141,"name":"all"},{"order":19,"name":"diceSlots"},{"order":141,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"GEJSlot":[{"gameIdentificationNumber":864,"recovery":"norecovery","gameName":"Great Empire","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":71,"name":"all"},{"order":12,"name":"classic"},{"order":71,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"TWWJSlot":[{"gameIdentificationNumber":863,"recovery":"norecovery","gameName":"The White Wolf","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":82,"name":"all"},{"order":19,"name":"classic"},{"order":82,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"FBHTJSlot":[{"gameIdentificationNumber":532,"recovery":"norecovery","gameName":"5 Burning Heart","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":26,"name":"all"},{"order":18,"name":"fruit"},{"order":26,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"HSHJSlot":[{"gameIdentificationNumber":527,"recovery":"norecovery","gameName":"100 Super Hot","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":11,"name":"all"},{"order":7,"name":"fruit"},{"order":11,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"FBJSlot":[{"gameIdentificationNumber":835,"recovery":"norecovery","gameName":"Forest Band","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":79,"name":"all"},{"order":13,"name":"multiline"},{"order":79,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"NDJSlot":[{"gameIdentificationNumber":861,"recovery":"norecovery","gameName":"Neon Dice","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":137,"name":"all"},{"order":15,"name":"diceSlots"},{"order":137,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"ORJSlot":[{"gameIdentificationNumber":839,"recovery":"norecovery","gameName":"Ocean Rush","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":99,"name":"all"},{"order":21,"name":"multiline"},{"order":99,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"ABJSlot":[{"gameIdentificationNumber":859,"recovery":"norecovery","gameName":"Amazon\u0027s Battle","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":21,"name":"all"},{"order":1,"name":"extraline"},{"order":21,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"KLJSlot":[{"gameIdentificationNumber":836,"recovery":"norecovery","gameName":"Kangaroo Land","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":77,"name":"all"},{"order":12,"name":"multiline"},{"order":77,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"FURJSlot":[{"gameIdentificationNumber":531,"recovery":"norecovery","gameName":"40 Ultra Respin","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":98,"name":"all"},{"order":36,"name":"fruit"},{"order":98,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"GOCJSlot":[{"gameIdentificationNumber":817,"recovery":"norecovery","gameName":"Grace of Cleopatra","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":54,"name":"all"},{"order":9,"name":"classic"},{"order":54,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"FSJSlot":[{"gameIdentificationNumber":820,"recovery":"norecovery","gameName":"Fortune Spells","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":65,"name":"all"},{"order":11,"name":"classic"},{"order":65,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"IMDJSlot":[{"gameIdentificationNumber":874,"recovery":"norecovery","gameName":"Imperial Dice","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":142,"name":"all"},{"order":20,"name":"diceSlots"},{"order":142,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"TBDJSlot":[{"gameIdentificationNumber":537,"recovery":"norecovery","gameName":"20 Burning Dice","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":139,"name":"all"},{"order":17,"name":"diceSlots"},{"order":139,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"SHJSlot":[{"gameIdentificationNumber":821,"recovery":"norecovery","gameName":"Supreme Hot","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":14,"name":"all"},{"order":10,"name":"fruit"},{"order":14,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"OCJSlot":[{"gameIdentificationNumber":848,"recovery":"norecovery","gameName":"Oil Company II","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":55,"name":"all"},{"order":7,"name":"extraline"},{"order":55,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"TDJSlot":[{"gameIdentificationNumber":854,"recovery":"norecovery","gameName":"20 Diamonds","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":29,"name":"all"},{"order":3,"name":"classic"},{"order":29,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"SBJSlot":[{"gameIdentificationNumber":855,"recovery":"norecovery","gameName":"Summer Bliss","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":94,"name":"all"},{"order":17,"name":"multiline"},{"order":94,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"GAJSlot":[{"gameIdentificationNumber":837,"recovery":"norecovery","gameName":"Great Adventure","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":90,"name":"all"},{"order":15,"name":"multiline"},{"order":90,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"THBJSlot":[{"gameIdentificationNumber":558,"recovery":"norecovery","gameName":"20 Hot Blast","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":6,"name":"all"},{"order":2,"name":"fruit"},{"order":6,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"TBHTJSlot":[{"gameIdentificationNumber":533,"recovery":"norecovery","gameName":"10 Burning Heart","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":27,"name":"all"},{"order":19,"name":"fruit"},{"order":27,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"FSDJSlot":[{"gameIdentificationNumber":20221,"recovery":"norecovery","gameName":"40 Super Dice","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":124,"name":"all"},{"order":2,"name":"diceSlots"},{"order":124,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"RDJSlot":[{"gameIdentificationNumber":860,"recovery":"norecovery","gameName":"Rolling Dice","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":144,"name":"all"},{"order":22,"name":"diceSlots"},{"order":144,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"STJSlot":[{"gameIdentificationNumber":897,"recovery":"norecovery","gameName":"Super 20","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":47,"name":"all"},{"order":32,"name":"fruit"},{"order":47,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"HCJSlot":[{"gameIdentificationNumber":847,"recovery":"norecovery","gameName":"100 Cats","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":35,"name":"all"},{"order":3,"name":"extraline"},{"order":35,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"RLTJSlot":[{"gameIdentificationNumber":550,"recovery":"norecovery","gameName":"Virtual Roulette","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":1,"name":"all"},{"order":1,"name":"roulette"},{"order":1,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"VGJSlot":[{"gameIdentificationNumber":818,"recovery":"norecovery","gameName":"Versailles Gold","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":50,"name":"all"},{"order":5,"name":"classic"},{"order":50,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"TSOLJSlot":[{"gameIdentificationNumber":873,"recovery":"norecovery","gameName":"The Secrets of London","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":105,"name":"all"},{"order":25,"name":"multiline"},{"order":105,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"BCJSlot":[{"gameIdentificationNumber":525,"recovery":"norecovery","gameName":"Brave Cat","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":80,"name":"all"},{"order":11,"name":"extraline"},{"order":80,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"WCJSlot":[{"gameIdentificationNumber":815,"recovery":"norecovery","gameName":"Witches Charm","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":83,"name":"all"},{"order":20,"name":"classic"},{"order":83,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"DQJSlot":[{"gameIdentificationNumber":858,"recovery":"norecovery","gameName":"Dark Queen","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":95,"name":"all"},{"order":18,"name":"multiline"},{"order":95,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"FMCJSlot":[{"gameIdentificationNumber":547,"recovery":"norecovery","gameName":"40 Mega Clover","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":30,"name":"all"},{"order":20,"name":"fruit"},{"order":30,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"RORJSlot":[{"gameIdentificationNumber":806,"recovery":"norecovery","gameName":"Rise of Ra","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":28,"name":"all"},{"order":2,"name":"classic"},{"order":28,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"RSJSlot":[{"gameIdentificationNumber":809,"recovery":"norecovery","gameName":"Royal Secrets","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":38,"name":"all"},{"order":4,"name":"classic"},{"order":38,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"HSDJSlot":[{"gameIdentificationNumber":534,"recovery":"norecovery","gameName":"100 Super Dice","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":125,"name":"all"},{"order":3,"name":"diceSlots"},{"order":125,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"DGRJSlot":[{"gameIdentificationNumber":508,"recovery":"norecovery","gameName":"Dragon Reborn","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":109,"name":"all"},{"order":27,"name":"multiline"},{"order":109,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"SCHJSlot":[{"gameIdentificationNumber":517,"recovery":"norecovery","gameName":"Sweet Cheese","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":117,"name":"all"},{"order":31,"name":"multiline"},{"order":117,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"FDJSlot":[{"gameIdentificationNumber":509,"recovery":"norecovery","gameName":"Flaming Dice","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":135,"name":"all"},{"order":13,"name":"diceSlots"},{"order":135,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"THDJSlot":[{"gameIdentificationNumber":507,"recovery":"norecovery","gameName":"Thumbelina Dream","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":116,"name":"all"},{"order":16,"name":"extraline"},{"order":116,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"TSFJSlot":[{"gameIdentificationNumber":528,"recovery":"norecovery","gameName":"30 Spicy Fruits","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":18,"name":"all"},{"order":14,"name":"fruit"},{"order":18,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"FDHJSlot":[{"gameIdentificationNumber":810,"recovery":"norecovery","gameName":"5 Dazzling Hot","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":15,"name":"all"},{"order":11,"name":"fruit"},{"order":15,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"RQJSlot":[{"gameIdentificationNumber":840,"recovery":"norecovery","gameName":"Rainbow Queen","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":100,"name":"all"},{"order":13,"name":"extraline"},{"order":100,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"JOBJPoker":[{"gameIdentificationNumber":20202,"recovery":"norecovery","gameName":"Jacks or Better","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":122,"name":"all"},{"order":3,"name":"cards"},{"order":122,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"SHDJSlot":[{"gameIdentificationNumber":560,"recovery":"norecovery","gameName":"Shining Dice","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":123,"name":"all"},{"order":1,"name":"diceSlots"},{"order":123,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"CHJSlot":[{"gameIdentificationNumber":506,"recovery":"norecovery","gameName":"Caramel Hot","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":34,"name":"all"},{"order":23,"name":"fruit"},{"order":34,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"LAWJSlot":[{"gameIdentificationNumber":868,"recovery":"norecovery","gameName":"Lucky \u0026 Wild","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":58,"name":"all"},{"order":33,"name":"fruit"},{"order":58,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"DSJSlot":[{"gameIdentificationNumber":877,"recovery":"norecovery","gameName":"Dragon Spirit","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":145,"name":"all"},{"order":23,"name":"diceSlots"},{"order":145,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"VDJSlot":[{"gameIdentificationNumber":832,"recovery":"norecovery","gameName":"Venezia D\u0027oro","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":92,"name":"all"},{"order":12,"name":"extraline"},{"order":92,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"LBJSlot":[{"gameIdentificationNumber":881,"recovery":"norecovery","gameName":"Lucky Buzz","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":84,"name":"all"},{"order":14,"name":"multiline"},{"order":84,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"HBHJSlot":[{"gameIdentificationNumber":548,"recovery":"norecovery","gameName":"100 Burning Hot","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":20,"name":"all"},{"order":15,"name":"fruit"},{"order":20,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"MLAWJSlot":[{"gameIdentificationNumber":869,"recovery":"norecovery","gameName":"More Lucky \u0026 Wild","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":46,"name":"all"},{"order":31,"name":"fruit"},{"order":46,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"FTJSlot":[{"gameIdentificationNumber":876,"recovery":"norecovery","gameName":"Forest Tale","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":113,"name":"all"},{"order":29,"name":"multiline"},{"order":113,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"AOTJSlot":[{"gameIdentificationNumber":812,"recovery":"norecovery","gameName":"Age of Troy","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":48,"name":"all"},{"order":3,"name":"multiline"},{"order":48,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"SOAJSlot":[{"gameIdentificationNumber":828,"recovery":"norecovery","gameName":"Secrets of Alchemy","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":22,"name":"all"},{"order":1,"name":"multiline"},{"order":22,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"SLJSlot":[{"gameIdentificationNumber":892,"recovery":"norecovery","gameName":"Savanna\u0027s Life","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":119,"name":"all"},{"order":32,"name":"multiline"},{"order":119,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"BHJSlot":[{"gameIdentificationNumber":801,"recovery":"norecovery","gameName":"Burning Hot","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":7,"name":"all"},{"order":3,"name":"fruit"},{"order":7,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"TSDJSlot":[{"gameIdentificationNumber":20218,"recovery":"norecovery","gameName":"20 Super Dice","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":128,"name":"all"},{"order":6,"name":"diceSlots"},{"order":128,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"JPJPoker":[{"gameIdentificationNumber":20208,"recovery":"norecovery","gameName":"Joker Poker","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":120,"name":"all"},{"order":1,"name":"cards"},{"order":120,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"AGJSlot":[{"gameIdentificationNumber":511,"recovery":"norecovery","gameName":"Aztec Glory","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":76,"name":"all"},{"order":16,"name":"classic"},{"order":76,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"GEOLJSlot":[{"gameIdentificationNumber":872,"recovery":"norecovery","gameName":"Genius of Leonardo","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":103,"name":"all"},{"order":14,"name":"extraline"},{"order":103,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"BDJSlot":[{"gameIdentificationNumber":20215,"recovery":"norecovery","gameName":"Burning Dice","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":127,"name":"all"},{"order":5,"name":"diceSlots"},{"order":127,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"MPJSlot":[{"gameIdentificationNumber":502,"recovery":"norecovery","gameName":"Magellan Plus","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":49,"name":"all"},{"order":6,"name":"extraline"},{"order":49,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"AAJSlot":[{"gameIdentificationNumber":808,"recovery":"norecovery","gameName":"Amazing Amazonia","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":85,"name":"all"},{"order":21,"name":"classic"},{"order":85,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"CRBJSlot":[{"gameIdentificationNumber":518,"recovery":"norecovery","gameName":"Crazy Bugs II","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":115,"name":"all"},{"order":30,"name":"multiline"},{"order":115,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"PSJSlot":[{"gameIdentificationNumber":852,"recovery":"norecovery","gameName":"Penguin Style","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":62,"name":"all"},{"order":6,"name":"multiline"},{"order":62,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"WTJSlot":[{"gameIdentificationNumber":505,"recovery":"norecovery","gameName":"Wonder Tree","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":89,"name":"all"},{"order":25,"name":"classic"},{"order":89,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"MDJSlot":[{"gameIdentificationNumber":894,"recovery":"norecovery","gameName":"Magic Dice","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":130,"name":"all"},{"order":8,"name":"diceSlots"},{"order":130,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"GTSJSlot":[{"gameIdentificationNumber":555,"recovery":"norecovery","gameName":"Great 27","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":5,"name":"all"},{"order":1,"name":"fruit"},{"order":5,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"SCJSlot":[{"gameIdentificationNumber":831,"recovery":"norecovery","gameName":"Shining Crown","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":9,"name":"all"},{"order":5,"name":"fruit"},{"order":9,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"TDHJSlot":[{"gameIdentificationNumber":544,"recovery":"norecovery","gameName":"20 Dazzling Hot","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":37,"name":"all"},{"order":25,"name":"fruit"},{"order":37,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"FHNCJSlot":[{"gameIdentificationNumber":545,"recovery":"norecovery","gameName":"40 Hot \u0026 Cash","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":59,"name":"all"},{"order":59,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"DARJSlot":[{"gameIdentificationNumber":824,"recovery":"norecovery","gameName":"Dice \u0026 Roll","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":39,"name":"all"},{"order":26,"name":"fruit"},{"order":39,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"MLADJSlot":[{"gameIdentificationNumber":516,"recovery":"norecovery","gameName":"More Like a Diamond","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":110,"name":"all"},{"order":15,"name":"extraline"},{"order":110,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"DORJSlot":[{"gameIdentificationNumber":856,"recovery":"norecovery","gameName":"Dice of Ra","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":140,"name":"all"},{"order":18,"name":"diceSlots"},{"order":140,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"CBJSlot":[{"gameIdentificationNumber":829,"recovery":"norecovery","gameName":"Circus Brilliant","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":66,"name":"all"},{"order":7,"name":"multiline"},{"order":66,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"FHDJSlot":[{"gameIdentificationNumber":886,"recovery":"norecovery","gameName":"5 Hot Dice","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":129,"name":"all"},{"order":7,"name":"diceSlots"},{"order":129,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"OGJSlot":[{"gameIdentificationNumber":819,"recovery":"norecovery","gameName":"Olympus Glory","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":52,"name":"all"},{"order":7,"name":"classic"},{"order":52,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"SPJSlot":[{"gameIdentificationNumber":895,"recovery":"norecovery","gameName":"Spanish Passion","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":88,"name":"all"},{"order":24,"name":"classic"},{"order":88,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"CRJSlot":[{"gameIdentificationNumber":871,"recovery":"norecovery","gameName":"Cats Royal","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":69,"name":"all"},{"order":9,"name":"multiline"},{"order":69,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"FBHJSlot":[{"gameIdentificationNumber":529,"recovery":"norecovery","gameName":"40 Burning Hot","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":16,"name":"all"},{"order":12,"name":"fruit"},{"order":16,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"GETJSlot":[{"gameIdentificationNumber":885,"recovery":"norecovery","gameName":"The Great Egypt","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":81,"name":"all"},{"order":18,"name":"classic"},{"order":81,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"TSWJSlot":[{"gameIdentificationNumber":556,"recovery":"norecovery","gameName":"27 Wins","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":23,"name":"all"},{"order":16,"name":"fruit"},{"order":23,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"BOMJSlot":[{"gameIdentificationNumber":826,"recovery":"norecovery","gameName":"Book of Magic","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":51,"name":"all"},{"order":6,"name":"classic"},{"order":51,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"DHIJSlot":[{"gameIdentificationNumber":519,"recovery":"norecovery","gameName":"Dice High","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":112,"name":"all"},{"order":37,"name":"fruit"},{"order":112,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"IWJSlot":[{"gameIdentificationNumber":841,"recovery":"norecovery","gameName":"Imperial Wars","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":70,"name":"all"},{"order":10,"name":"multiline"},{"order":70,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"EHJSlot":[{"gameIdentificationNumber":880,"recovery":"norecovery","gameName":"Extremely Hot","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":45,"name":"all"},{"order":30,"name":"fruit"},{"order":45,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"FSHJSlot":[{"gameIdentificationNumber":804,"recovery":"norecovery","gameName":"40 Super Hot","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":8,"name":"all"},{"order":4,"name":"fruit"},{"order":8,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"BDIJSlot":[{"gameIdentificationNumber":524,"recovery":"norecovery","gameName":"Bonus Dice","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":147,"name":"all"},{"order":25,"name":"diceSlots"},{"order":147,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"EOWJSlot":[{"gameIdentificationNumber":549,"recovery":"norecovery","gameName":"81 Wins","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":44,"name":"all"},{"order":29,"name":"fruit"},{"order":44,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"ESJSlot":[{"gameIdentificationNumber":825,"recovery":"norecovery","gameName":"Egypt Sky","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":42,"name":"all"},{"order":4,"name":"extraline"},{"order":42,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"AMJSlot":[{"gameIdentificationNumber":851,"recovery":"norecovery","gameName":"Action Money","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":101,"name":"all"},{"order":22,"name":"multiline"},{"order":101,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"FJFJSlot":[{"gameIdentificationNumber":557,"recovery":"norecovery","gameName":"5 Juggle Fruits","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":4,"name":"all"},{"order":4,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"XJJSlot":[{"gameIdentificationNumber":857,"recovery":"norecovery","gameName":"Extra Joker","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":132,"name":"all"},{"order":10,"name":"diceSlots"},{"order":132,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"LRJSlot":[{"gameIdentificationNumber":510,"recovery":"norecovery","gameName":"Legendary Rome","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":104,"name":"all"},{"order":24,"name":"multiline"},{"order":104,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"ASJSlot":[{"gameIdentificationNumber":522,"recovery":"norecovery","gameName":"Amazons\u0027 Story","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":134,"name":"all"},{"order":12,"name":"diceSlots"},{"order":134,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"DRJSlot":[{"gameIdentificationNumber":813,"recovery":"norecovery","gameName":"Dragon Reels","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":68,"name":"all"},{"order":8,"name":"multiline"},{"order":68,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"GDJSlot":[{"gameIdentificationNumber":862,"recovery":"norecovery","gameName":"Gold Dust","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":111,"name":"all"},{"order":28,"name":"multiline"},{"order":111,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"FGSJSlot":[{"gameIdentificationNumber":553,"recovery":"norecovery","gameName":"5 Great Star","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":33,"name":"all"},{"order":22,"name":"fruit"},{"order":33,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"FKJSlot":[{"gameIdentificationNumber":811,"recovery":"norecovery","gameName":"Fruits Kingdom","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":74,"name":"all"},{"order":14,"name":"classic"},{"order":74,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"RGJSlot":[{"gameIdentificationNumber":883,"recovery":"norecovery","gameName":"Royal Gardens","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":143,"name":"all"},{"order":21,"name":"diceSlots"},{"order":143,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"BHTJSlot":[{"gameIdentificationNumber":814,"recovery":"norecovery","gameName":"Blue Heart","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":31,"name":"all"},{"order":2,"name":"extraline"},{"order":31,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"THSDJSlot":[{"gameIdentificationNumber":535,"recovery":"norecovery","gameName":"30 Spicy Dice","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":131,"name":"all"},{"order":9,"name":"diceSlots"},{"order":131,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"MSJSlot":[{"gameIdentificationNumber":898,"recovery":"norecovery","gameName":"Mayan Spirit","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":64,"name":"all"},{"order":9,"name":"extraline"},{"order":64,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"MDARJSlot":[{"gameIdentificationNumber":875,"recovery":"norecovery","gameName":"More Dice \u0026 Roll","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":41,"name":"all"},{"order":28,"name":"fruit"},{"order":41,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"UJKeno":[{"gameIdentificationNumber":701,"recovery":"norecovery","gameName":"Keno Universe","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":3,"name":"all"},{"order":1,"name":"keno"},{"order":3,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"RTSJSlot":[{"gameIdentificationNumber":889,"recovery":"norecovery","gameName":"Retro Style","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":108,"name":"all"},{"order":27,"name":"classic"},{"order":108,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"TJRJSlot":[{"gameIdentificationNumber":554,"recovery":"norecovery","gameName":"20 Joker Reels","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":40,"name":"all"},{"order":27,"name":"fruit"},{"order":40,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"RBDJSlot":[{"gameIdentificationNumber":870,"recovery":"norecovery","gameName":"Rainbow Dice","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":146,"name":"all"},{"order":24,"name":"diceSlots"},{"order":146,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"MFJSlot":[{"gameIdentificationNumber":830,"recovery":"norecovery","gameName":"Majestic Forest","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":60,"name":"all"},{"order":10,"name":"classic"},{"order":60,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"LHJSlot":[{"gameIdentificationNumber":849,"recovery":"norecovery","gameName":"Lucky Hot","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":36,"name":"all"},{"order":24,"name":"fruit"},{"order":36,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"TBHJSlot":[{"gameIdentificationNumber":530,"recovery":"norecovery","gameName":"20 Burning Hot","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":13,"name":"all"},{"order":9,"name":"fruit"},{"order":13,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"HNCJSlot":[{"gameIdentificationNumber":866,"recovery":"norecovery","gameName":"Hot \u0026 Cash","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":63,"name":"all"},{"order":34,"name":"fruit"},{"order":63,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"HWJSlot":[{"gameIdentificationNumber":833,"recovery":"norecovery","gameName":"Halloween","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":96,"name":"all"},{"order":19,"name":"multiline"},{"order":96,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"FLKJSlot":[{"gameIdentificationNumber":546,"recovery":"norecovery","gameName":"40 Lucky King","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":32,"name":"all"},{"order":21,"name":"fruit"},{"order":32,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"JAJSlot":[{"gameIdentificationNumber":503,"recovery":"norecovery","gameName":"Jungle Adventure","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":118,"name":"all"},{"order":29,"name":"classic"},{"order":118,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}],"DHJSlot":[{"gameIdentificationNumber":888,"recovery":"norecovery","gameName":"Dragon Hot","featured":false,"mlmJackpot":true,"totalBet":0,"groups":[{"order":93,"name":"all"},{"order":35,"name":"fruit"},{"order":93,"name":"myGames"}],"jackpotGameType":"MLMJackpot"}]},"sessionKey":"41be9e65e0ff03a65e8c93576bf61130","msg":"success","messageId":"' . $postData['messageId'] . '","qName":"app.services.messages.response.LoginResponse","command":"login","eventTimestamp":' . time() . '}';
                                break;
                            case 'settings':
                                $gameBets = [
                                    1, 
                                    2, 
                                    5, 
                                    10, 
                                    20
                                ];
                                $denoms = [];
                                if( count($slotSettings->Denominations) > 5 ) 
                                {
                                    $slotSettings->Denominations = array_slice($slotSettings->Denominations, 0, 5);
                                }
                                foreach( $slotSettings->Denominations as &$b ) 
                                {
                                    $denoms[] = '[' . ($b * 100) . ',70,3000000]';
                                }
                                $result_tmp[] = '{"complex":{"balance":' . $balanceInCents . ',"balanceRaw":' . round($balanceInCents * $slotSettings->CurrentDenom) . ',"paytableCoef":{"0":{"coef":[5,20,50],"multiplier":1},"1":{"coef":[5,20,50],"multiplier":1},"2":{"coef":[5,20,50],"multiplier":1},"3":{"coef":[5,20,50],"multiplier":1},"4":{"coef":[15,40,150],"multiplier":1},"5":{"coef":[15,40,150],"multiplier":1},"6":{"coef":[20,45,300],"multiplier":1},"7":{"coef":[20,50,400],"multiplier":1},"8":{"coef":[50,150,750],"multiplier":1}},"bets":[' . implode(',', $gameBets) . '],"jackpotMinBet":1,"jackpot":true,"mainFakeReels":[[2,4,1,3,8,7,0,6,1,4,3,0,5,1,8,1,0,4,5,6,0,7,8,0,3,6,5,2,3,1],[2,4,1,3,8,7,0,6,1,4,3,0,5,6,8,2,0,0,1,1,2,2,8,6,4,1,0,2,0,2],[2,4,1,3,8,7,0,6,1,4,3,0,5,6,8,1,1,0,5,4,1,4,8,5,0,3,4,0,1,6],[2,4,1,3,8,7,0,6,1,4,3,0,5,6,8,0,0,1,5,7,3,6,8,5,0,5,0,2,1,1],[2,4,1,3,8,7,0,6,1,4,3,0,5,6,8,2,1,4,7,2,0,6,8,0,4,4,5,0,0,2]],"jackpotMaxBet":100000,"denominations":[' . implode(',', $denoms) . ']},"gameIdentificationNumber":' . $postData['gameIdentificationNumber'] . ',"gameNumber":-1,"sessionKey":"41be9e65e0ff03a65e8c93576bf61130","msg":"success","messageId":"' . $postData['messageId'] . '","qName":"app.services.messages.response.GameResponse","command":"settings","eventTimestamp":' . time() . '}';
                                break;
                            case 'subscribe':
                                $hist = [
                                    78, 
                                    30, 
                                    46, 
                                    62, 
                                    46, 
                                    30
                                ];
                                shuffle($hist);
                                $slotSettings->SetGameData('DiceAndRoll40EGTCards', $hist);
                                $lastEvent = $slotSettings->GetHistory();
                                $slotSettings->SetGameData('DiceAndRoll40EGTBonusWin', 0);
                                $slotSettings->SetGameData('DiceAndRoll40EGTFreeGames', 0);
                                $slotSettings->SetGameData('DiceAndRoll40EGTCurrentFreeGame', 0);
                                $slotSettings->SetGameData('DiceAndRoll40EGTTotalWin', 0);
                                $slotSettings->SetGameData('DiceAndRoll40EGTFreeBalance', 0);
                                if( $lastEvent != 'NULL' ) 
                                {
                                    if( isset($lastEvent->serverResponse->bonusWin) ) 
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $lastEvent->serverResponse->bonusWin);
                                    }
                                    else
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'BonusWin', $lastEvent->serverResponse->totalWin);
                                    }
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeGames', $lastEvent->serverResponse->totalFreeGames);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'CurrentFreeGame', $lastEvent->serverResponse->currentFreeGames);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'TotalWin', $lastEvent->serverResponse->totalWin);
                                    $slotSettings->SetGameData($slotSettings->slotId . 'FreeBalance', $lastEvent->serverResponse->Balance);
                                    $reels = $lastEvent->serverResponse->reelsSymbols;
                                    $curReels = '' . rand(0, 6) . ',' . $reels->reel1[0] . ',' . $reels->reel1[1] . ',' . $reels->reel1[2] . ',' . $reels->reel1[3] . ',' . rand(0, 6);
                                    $curReels .= (',' . rand(0, 6) . ',' . $reels->reel2[0] . ',' . $reels->reel2[1] . ',' . $reels->reel2[2] . ',' . $reels->reel2[3] . ',' . rand(0, 6));
                                    $curReels .= (',' . rand(0, 6) . ',' . $reels->reel3[0] . ',' . $reels->reel3[1] . ',' . $reels->reel3[2] . ',' . $reels->reel3[3] . ',' . rand(0, 6));
                                    $curReels .= (',' . rand(0, 6) . ',' . $reels->reel4[0] . ',' . $reels->reel4[1] . ',' . $reels->reel4[2] . ',' . $reels->reel4[3] . ',' . rand(0, 6));
                                    $curReels .= (',' . rand(0, 6) . ',' . $reels->reel5[0] . ',' . $reels->reel5[1] . ',' . $reels->reel5[2] . ',' . $reels->reel5[3] . ',' . rand(0, 6));
                                    $lines = $lastEvent->serverResponse->slotLines;
                                    $bet = $lastEvent->serverResponse->slotBet * $lines * 100;
                                    $gtype = 1;
                                    if( $slotSettings->GetGameData('DiceAndRoll40EGTCurrentFreeGame') < $slotSettings->GetGameData('DiceAndRoll40EGTFreeGames') && $slotSettings->GetGameData('DiceAndRoll40EGTFreeGames') > 0 ) 
                                    {
                                        $gtype = 2;
                                    }
                                }
                                else
                                {
                                    $gtype = 1;
                                    $curReels = '' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . ',' . rand(0, 6) . '';
                                    $lines = 10;
                                }
                                $result_tmp[] = '{"complex":{"currentState":{"gamblesUsed":0,"freespinsUsed":0,"previousGambles":[],"bet":100,"numberOfLines":40,"denomination":100,"state":"idle","winAmount":0,"reels":[' . $curReels . '],"lines":[],"scatters":[],"expand":[],"specialExpand":[],"gambles":5,"freespins":0,"freespinScatters":[],"jackpot":false},"jackpotState":{"levelI":' . ($slotSettings->slotJackpot[0] * 100) . ',"levelII":' . ($slotSettings->slotJackpot[1] * 100) . ',"levelIII":' . ($slotSettings->slotJackpot[2] * 100) . ',"levelIV":' . ($slotSettings->slotJackpot[3] * 100) . ',"winsLevelI":0,"largestWinLevelI":0,"largestWinDateLevelI":"","largestWinUserLevelI":"player","lastWinLevelI":0,"lastWinDateLevelI":"","lastWinUserLevelI":"","winsLevelII":0,"largestWinLevelII":0,"largestWinDateLevelII":"","largestWinUserLevelII":"","lastWinLevelII":0,"lastWinDateLevelII":"","lastWinUserLevelII":"","winsLevelIII":0,"largestWinLevelIII":0,"largestWinDateLevelIII":"","largestWinUserLevelIII":"player","lastWinLevelIII":0,"lastWinDateLevelIII":"","lastWinUserLevelIII":"","winsLevelIV":0,"largestWinLevelIV":0,"largestWinDateLevelIV":"","largestWinUserLevelIV":"","lastWinLevelIV":0,"lastWinDateLevelIV":"","lastWinUserLevelIV":""}},"gameIdentificationNumber":' . $postData['gameIdentificationNumber'] . ',"gameNumber":-1,"sessionKey":"41be9e65e0ff03a65e8c93576bf61130","msg":"success","messageId":"' . $postData['messageId'] . '","qName":"app.services.messages.response.GameEventResponse","command":"subscribe","eventTimestamp":' . time() . '}';
                                break;
                            case 'ping':
                                $cashBackBtn = 0;
                                $result_tmp[] = '{"sessionKey":"41be9e65e0ff03a65e8c93576bf61130","msg":"success","messageId":"' . $postData['messageId'] . '","qName":"app.services.messages.response.BaseResponse","balance_":' . $balanceInCents . ',"balanceRaw":' . round($balanceInCents * $slotSettings->CurrentDenom) . ',"cashBackBtn":' . $cashBackBtn . ',"command":"ping","eventTimestamp":' . time() . '}';
                                $result_tmp[] = '{"complex":{"levelI":' . ($slotSettings->slotJackpot[0] * 100) . ',"levelII":' . ($slotSettings->slotJackpot[1] * 100) . ',"levelIII":' . ($slotSettings->slotJackpot[2] * 100) . ',"levelIV":' . ($slotSettings->slotJackpot[3] * 100) . ',"winsLevelI":2,"largestWinLevelI":0,"largestWinDateLevelI":"","largestWinUserLevelI":"","lastWinLevelI":0,"lastWinDateLevelI":"","lastWinUserLevelI":"player","winsLevelII":0,"largestWinLevelII":0,"largestWinDateLevelII":"","largestWinUserLevelII":"","lastWinLevelII":0,"lastWinDateLevelII":"","lastWinUserLevelII":"player","winsLevelIII":0,"largestWinLevelIII":0,"largestWinDateLevelIII":"","largestWinUserLevelIII":"","lastWinLevelIII":0,"lastWinDateLevelIII":"","lastWinUserLevelIII":"","winsLevelIV":0,"largestWinLevelIV":0,"largestWinDateLevelIV":"","largestWinUserLevelIV":"","lastWinLevelIV":0,"lastWinDateLevelIV":"","lastWinUserLevelIV":""},"gameIdentificationNumber":1,"gameNumber":"","msg":"success","messageId":"f73a429df116252e537e403d12bcdb92","qName":"app.services.messages.response.GameEventResponse","command":"event","eventTimestamp":' . time() . '}';
                                break;
                            case 'bet':
                                $linesId = [];
                                $linesId[0] = [
                                    2, 
                                    2, 
                                    2, 
                                    2, 
                                    2
                                ];
                                $linesId[1] = [
                                    3, 
                                    3, 
                                    3, 
                                    3, 
                                    3
                                ];
                                $linesId[2] = [
                                    1, 
                                    1, 
                                    1, 
                                    1, 
                                    1
                                ];
                                $linesId[3] = [
                                    4, 
                                    4, 
                                    4, 
                                    4, 
                                    4
                                ];
                                $linesId[4] = [
                                    2, 
                                    3, 
                                    4, 
                                    3, 
                                    2
                                ];
                                $linesId[5] = [
                                    3, 
                                    2, 
                                    1, 
                                    2, 
                                    3
                                ];
                                $linesId[6] = [
                                    1, 
                                    1, 
                                    2, 
                                    3, 
                                    4
                                ];
                                $linesId[7] = [
                                    4, 
                                    4, 
                                    3, 
                                    2, 
                                    1
                                ];
                                $linesId[8] = [
                                    3, 
                                    4, 
                                    4, 
                                    4, 
                                    3
                                ];
                                $linesId[9] = [
                                    2, 
                                    1, 
                                    1, 
                                    1, 
                                    2
                                ];
                                $linesId[10] = [
                                    1, 
                                    2, 
                                    3, 
                                    4, 
                                    4
                                ];
                                $linesId[11] = [
                                    4, 
                                    3, 
                                    2, 
                                    1, 
                                    1
                                ];
                                $linesId[12] = [
                                    3, 
                                    4, 
                                    3, 
                                    2, 
                                    3
                                ];
                                $linesId[13] = [
                                    2, 
                                    1, 
                                    2, 
                                    3, 
                                    2
                                ];
                                $linesId[14] = [
                                    1, 
                                    2, 
                                    1, 
                                    2, 
                                    1
                                ];
                                $linesId[15] = [
                                    4, 
                                    3, 
                                    4, 
                                    3, 
                                    4
                                ];
                                $linesId[16] = [
                                    2, 
                                    3, 
                                    2, 
                                    1, 
                                    2
                                ];
                                $linesId[17] = [
                                    3, 
                                    2, 
                                    3, 
                                    4, 
                                    3
                                ];
                                $linesId[18] = [
                                    1, 
                                    2, 
                                    2, 
                                    2, 
                                    1
                                ];
                                $linesId[19] = [
                                    4, 
                                    3, 
                                    3, 
                                    3, 
                                    4
                                ];
                                $linesId[20] = [
                                    2, 
                                    2, 
                                    3, 
                                    4, 
                                    4
                                ];
                                $linesId[21] = [
                                    3, 
                                    3, 
                                    2, 
                                    1, 
                                    1
                                ];
                                $linesId[22] = [
                                    1, 
                                    2, 
                                    3, 
                                    3, 
                                    4
                                ];
                                $linesId[23] = [
                                    4, 
                                    3, 
                                    2, 
                                    2, 
                                    1
                                ];
                                $linesId[24] = [
                                    2, 
                                    3, 
                                    3, 
                                    3, 
                                    4
                                ];
                                $linesId[25] = [
                                    3, 
                                    2, 
                                    2, 
                                    2, 
                                    1
                                ];
                                $linesId[26] = [
                                    1, 
                                    1, 
                                    2, 
                                    1, 
                                    1
                                ];
                                $linesId[27] = [
                                    4, 
                                    4, 
                                    3, 
                                    4, 
                                    4
                                ];
                                $linesId[28] = [
                                    3, 
                                    3, 
                                    4, 
                                    3, 
                                    3
                                ];
                                $linesId[29] = [
                                    2, 
                                    2, 
                                    1, 
                                    2, 
                                    2
                                ];
                                $linesId[30] = [
                                    1, 
                                    1, 
                                    1, 
                                    2, 
                                    3
                                ];
                                $linesId[31] = [
                                    4, 
                                    4, 
                                    4, 
                                    3, 
                                    2
                                ];
                                $linesId[32] = [
                                    3, 
                                    4, 
                                    4, 
                                    3, 
                                    2
                                ];
                                $linesId[33] = [
                                    2, 
                                    1, 
                                    1, 
                                    2, 
                                    3
                                ];
                                $linesId[34] = [
                                    1, 
                                    2, 
                                    2, 
                                    3, 
                                    4
                                ];
                                $linesId[35] = [
                                    4, 
                                    3, 
                                    3, 
                                    2, 
                                    1
                                ];
                                $linesId[36] = [
                                    3, 
                                    4, 
                                    3, 
                                    2, 
                                    1
                                ];
                                $linesId[37] = [
                                    2, 
                                    1, 
                                    2, 
                                    3, 
                                    4
                                ];
                                $linesId[38] = [
                                    1, 
                                    2, 
                                    3, 
                                    4, 
                                    3
                                ];
                                $linesId[39] = [
                                    4, 
                                    3, 
                                    2, 
                                    1, 
                                    2
                                ];
                                $lines = $postData['bet']['lines'];
                                $betline = $postData['bet']['bet'] / 100;
                                $allbet = $betline * $lines;
                                $postData['slotEvent'] = 'bet';
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
                                    if( is_array($jackState) ) 
                                    {
                                        $slotSettings->SetGameData($slotSettings->slotId . 'JackWinID', $jackState['isJackId']);
                                    }
                                    $slotSettings->SetGameData('DiceAndRoll40EGTBonusWin', 0);
                                    $slotSettings->SetGameData('DiceAndRoll40EGTFreeGames', 0);
                                    $slotSettings->SetGameData('DiceAndRoll40EGTCurrentFreeGame', 0);
                                    $slotSettings->SetGameData('DiceAndRoll40EGTTotalWin', 0);
                                    $slotSettings->SetGameData('DiceAndRoll40EGTFreeBalance', 0);
                                    $bonusMpl = 1;
                                }
                                else
                                {
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
                                    $wild = ['8'];
                                    $scatter = '9';
                                    $reels = $slotSettings->GetReelStrips($winType, $postData['slotEvent']);
                                    $tmpReels = $reels;
                                    $wlPos = [];
                                    $wlPosTmp = [];
                                    for( $r = 1; $r <= 5; $r++ ) 
                                    {
                                        for( $p = 0; $p <= 3; $p++ ) 
                                        {
                                            if( $reels['reel' . $r][$p] == '8' ) 
                                            {
                                                $wlPos[] = '' . ($r - 1) . ',' . $p . '';
                                                $reels['reel' . $r][$p] = '-1';
                                                break;
                                            }
                                        }
                                    }
                                    for( $r = 1; $r <= 5; $r++ ) 
                                    {
                                        for( $p = 0; $p <= 3; $p++ ) 
                                        {
                                            if( $reels['reel' . $r][$p] == '-1' ) 
                                            {
                                                $reels['reel' . $r][$p] = '8';
                                                if( isset($reels['reel' . $r][$p - 1]) ) 
                                                {
                                                    $reels['reel' . $r][$p - 1] = '8';
                                                }
                                                if( isset($reels['reel' . $r][$p + 1]) ) 
                                                {
                                                    $reels['reel' . $r][$p + 1] = '8';
                                                }
                                                if( isset($reels['reel' . ($r - 1)][$p]) ) 
                                                {
                                                    $reels['reel' . ($r - 1)][$p] = '8';
                                                }
                                                if( isset($reels['reel' . ($r + 1)][$p]) ) 
                                                {
                                                    $reels['reel' . ($r + 1)][$p] = '8';
                                                }
                                                if( isset($reels['reel' . ($r - 1)][$p + 1]) ) 
                                                {
                                                    $reels['reel' . ($r - 1)][$p + 1] = '8';
                                                }
                                                if( isset($reels['reel' . ($r + 1)][$p + 1]) ) 
                                                {
                                                    $reels['reel' . ($r + 1)][$p + 1] = '8';
                                                }
                                                if( isset($reels['reel' . ($r - 1)][$p - 1]) ) 
                                                {
                                                    $reels['reel' . ($r - 1)][$p - 1] = '8';
                                                }
                                                if( isset($reels['reel' . ($r + 1)][$p - 1]) ) 
                                                {
                                                    $reels['reel' . ($r + 1)][$p - 1] = '8';
                                                }
                                                break;
                                            }
                                        }
                                    }
                                    $wldPos = [];
                                    for( $r = 1; $r <= 5; $r++ ) 
                                    {
                                        for( $p = 0; $p <= 3; $p++ ) 
                                        {
                                            if( $reels['reel' . $r][$p] == '8' ) 
                                            {
                                                $wldPos[] = '' . ($r - 1) . ',' . $p . '';
                                            }
                                        }
                                    }
                                    for( $k = 0; $k < $lines; $k++ ) 
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
                                        }
                                    }
                                    $scattersWin = 0;
                                    $scattersStr = '';
                                    $scattersCount = 0;
                                    $scPos = [];
                                    for( $r = 1; $r <= 5; $r++ ) 
                                    {
                                        for( $p = 0; $p <= 3; $p++ ) 
                                        {
                                            if( $reels['reel' . $r][$p] == $scatter ) 
                                            {
                                                $scattersCount++;
                                                $scPos[] = '' . ($r - 1) . ',' . $p . '';
                                            }
                                        }
                                    }
                                    $scattersWin = $slotSettings->Paytable['SYM_' . $scatter][$scattersCount] * $allbet * $bonusMpl;
                                    $sgwin = 0;
                                    if( $scattersWin > 0 ) 
                                    {
                                        $sgwin = 0;
                                        if( $scattersCount >= 3 ) 
                                        {
                                            $sgwin = $slotSettings->slotFreeCount;
                                        }
                                        $scattersStr = '{"scatterName":' . $scatter . ',"cells":[' . implode(',', $scPos) . '],"winAmount":' . ($scattersWin * 100) . ',"freespins":0}';
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
                                $winString = implode(',', $lineWins);
                                $jsSpin = '' . json_encode($reels) . '';
                                $jsJack = '' . json_encode($slotSettings->Jackpots) . '';
                                $response = '{"responseEvent":"spin","responseType":"' . $postData['slotEvent'] . '","serverResponse":{"slotLines":' . $lines . ',"slotBet":' . $betline . ',"totalFreeGames":' . $slotSettings->GetGameData('DiceAndRoll40EGTFreeGames') . ',"currentFreeGames":' . $slotSettings->GetGameData('DiceAndRoll40EGTCurrentFreeGame') . ',"Balance":' . $slotSettings->GetBalance() . ',"afterBalance":' . $slotSettings->GetBalance() . ',"bonusWin":' . $slotSettings->GetGameData('DiceAndRoll40EGTBonusWin') . ',"totalWin":' . $totalWin . ',"winLines":[' . $winString . '],"Jackpots":' . $jsJack . ',"reelsSymbols":' . $jsSpin . '}}';
                                $slotSettings->SaveLogReport($response, $allbet, $lines, $reportWin, $postData['slotEvent']);
                                $winstring = '';
                                $reels = $tmpReels;
                                $slotSettings->SetGameData('DiceAndRoll40EGTTotalWin', $totalWin);
                                $slotSettings->SetGameData('DiceAndRoll40EGTGambleStep', 5);
                                $hist = $slotSettings->GetGameData('DiceAndRoll40EGTCards');
                                $curReels = '' . rand(0, 6) . ',' . $reels['reel1'][0] . ',' . $reels['reel1'][1] . ',' . $reels['reel1'][2] . ',' . $reels['reel1'][3] . ',' . rand(0, 6);
                                $curReels .= (',' . rand(0, 6) . ',' . $reels['reel2'][0] . ',' . $reels['reel2'][1] . ',' . $reels['reel2'][2] . ',' . $reels['reel2'][3] . ',' . rand(0, 6));
                                $curReels .= (',' . rand(0, 6) . ',' . $reels['reel3'][0] . ',' . $reels['reel3'][1] . ',' . $reels['reel3'][2] . ',' . $reels['reel3'][3] . ',' . rand(0, 6));
                                $curReels .= (',' . rand(0, 6) . ',' . $reels['reel4'][0] . ',' . $reels['reel4'][1] . ',' . $reels['reel4'][2] . ',' . $reels['reel4'][3] . ',' . rand(0, 6));
                                $curReels .= (',' . rand(0, 6) . ',' . $reels['reel5'][0] . ',' . $reels['reel5'][1] . ',' . $reels['reel5'][2] . ',' . $reels['reel5'][3] . ',' . rand(0, 6));
                                if( $jackRandom == 1 ) 
                                {
                                    $state = 'jackpot';
                                    $isJack = 'true';
                                    $slotSettings->SetGameData('DiceAndRoll40EGTJackSteps', [
                                        0, 
                                        0, 
                                        0, 
                                        0
                                    ]);
                                }
                                else
                                {
                                    $isJack = 'false';
                                    if( $totalWin > 0 ) 
                                    {
                                        $state = 'gamble';
                                    }
                                    else
                                    {
                                        $state = 'idle';
                                    }
                                }
                                if( $winType == 'bonus' ) 
                                {
                                    $state = 'freespin';
                                }
                                if( !isset($sgwin) ) 
                                {
                                    $fs = 0;
                                }
                                else if( $sgwin > 0 ) 
                                {
                                    $fs = $sgwin;
                                }
                                else
                                {
                                    $fs = 0;
                                }
                                $result_tmp[] = '{"complex":{"levelI":' . ($slotSettings->slotJackpot[0] * 100) . ',"levelII":' . ($slotSettings->slotJackpot[1] * 100) . ',"levelIII":' . ($slotSettings->slotJackpot[2] * 100) . ',"levelIV":' . ($slotSettings->slotJackpot[3] * 100) . ',"winsLevelI":2,"largestWinLevelI":0,"largestWinDateLevelI":"","largestWinUserLevelI":"","lastWinLevelI":0,"lastWinDateLevelI":"","lastWinUserLevelI":"player","winsLevelII":0,"largestWinLevelII":0,"largestWinDateLevelII":"","largestWinUserLevelII":"","lastWinLevelII":0,"lastWinDateLevelII":"","lastWinUserLevelII":"player","winsLevelIII":0,"largestWinLevelIII":0,"largestWinDateLevelIII":"","largestWinUserLevelIII":"","lastWinLevelIII":0,"lastWinDateLevelIII":"","lastWinUserLevelIII":"","winsLevelIV":0,"largestWinLevelIV":0,"largestWinDateLevelIV":"","largestWinUserLevelIV":"","lastWinLevelIV":0,"lastWinDateLevelIV":"","lastWinUserLevelIV":""},"gameIdentificationNumber":' . $postData['gameIdentificationNumber'] . ',"gameNumber":"","msg":"success","messageId":"f73a429df116252e537e403d12bcdb92","qName":"app.services.messages.response.GameEventResponse","command":"event","eventTimestamp":' . time() . '}';
                                $result_tmp[] = '{"complex":{"reels":[' . $curReels . '],"lines":[' . $winString . '],"scatters":[' . $scattersStr . '],"expand":[' . implode(',', $wldPos) . '],"specialExpand":[' . implode(',', $wlPos) . '],"gambles":5,"freespins":0,"jackpot":false,"gameCommand":"bet"},"state":"' . $state . '","winAmount":' . ($totalWin * 100) . ',"gameIdentificationNumber":875,"gameNumber":1899303059536,"balance":' . $balanceInCents . ',"sessionKey":"db27898656b5796787d11606e5b0902c","msg":"success","messageId":"' . $postData['messageId'] . '","qName":"app.services.messages.response.GameEventResponse","command":"bet","eventTimestamp":1593953023817}';
                                break;
                            case 'collect':
                                $result_tmp[] = '{"complex":{"gameCommand":"collect"},"state":"idle","winAmount":' . ($slotSettings->GetGameData('DiceAndRoll40EGTTotalWin') * 100) . ',"gameIdentificationNumber":' . $postData['gameIdentificationNumber'] . ',"gameNumber":"","balance":' . $balanceInCents . ',"sessionKey":"41be9e65e0ff03a65e8c93576bf61130","msg":"success","messageId":"' . $postData['messageId'] . '","qName":"app.services.messages.response.GameEventResponse","command":"bet","eventTimestamp":' . time() . '}';
                                $slotSettings->SetGameData('DiceAndRoll40EGTTotalWin', 0);
                                break;
                            case 'jackpot':
                                $jackStep = $slotSettings->GetGameData('DiceAndRoll40EGTJackSteps');
                                $jackWinID = $slotSettings->GetGameData($slotSettings->slotId . 'JackWinID');
                                if( $jackStep[0] >= 3 || $jackStep[1] >= 3 || $jackStep[2] >= 3 || $jackStep[3] >= 3 || !$slotSettings->HasGameData('DiceAndRoll40EGTJackSteps') ) 
                                {
                                    exit();
                                }
                                $winAmount = 0;
                                $jackState = 'jackpot';
                                $cardsArr = [
                                    '12', 
                                    '24', 
                                    '36', 
                                    '48'
                                ];
                                $jID = rand(0, 3);
                                if( $jackStep[$jID] == 2 ) 
                                {
                                    $jID = $jackWinID;
                                }
                                $isCard = $cardsArr[$jID];
                                $jackStep[$jID]++;
                                if( $jackStep[$jID] == 3 ) 
                                {
                                    $winAmount = $slotSettings->slotJackpot[$jID] * 100;
                                    $jackState = 'idle';
                                    $slotSettings->SetBalance($slotSettings->slotJackpot[$jID]);
                                    $slotSettings->ClearJackpot($jID);
                                    $response = '{"responseEvent":"jackpot","responseType":"jackpot","serverResponse":{}}';
                                    $slotSettings->SaveLogReport($response, 0, 0, $slotSettings->slotJackpot[$jID], 'JPG');
                                    $balanceInCents = sprintf('%01.2f', $slotSettings->GetBalance()) * 100;
                                    $slotSettings->slotJackpot[$jID] = 0;
                                }
                                $result_tmp[] = '{"sessionKey": "41be9e65e0ff03a65e8c93576bf61130", "qName": "app.services.messages.response.GameEventResponse", "winAmount":' . $winAmount . ', "eventTimestamp": ' . time() . ', "gameNumber":"", "state": "' . $jackState . '", "complex": {"gameCommand": "jackpot","pos":' . $postData['bet']['pos'] . ',"winLevel":' . $jID . ',"card":' . $isCard . '}, "command": "bet", "messageId": "' . $postData['messageId'] . '", "msg": "success", "balance": ' . $balanceInCents . ', "gameIdentificationNumber":' . $postData['gameIdentificationNumber'] . '}';
                                $result_tmp[] = '{"complex":{"levelI":' . ($slotSettings->slotJackpot[0] * 100) . ',"levelII":' . ($slotSettings->slotJackpot[1] * 100) . ',"levelIII":' . ($slotSettings->slotJackpot[2] * 100) . ',"levelIV":' . ($slotSettings->slotJackpot[3] * 100) . ',"screenName":"","winLevel":-1,"winAmount":0,"winsLevelI":0,"largestWinLevelI":0,"largestWinDateLevelI":"","largestWinUserLevelI":"","lastWinLevelI":0,"lastWinDateLevelI":"","lastWinUserLevelI":"","winsLevelII":0,"largestWinLevelII":0,"largestWinDateLevelII":"","largestWinUserLevelII":"","lastWinLevelII":0,"lastWinDateLevelII":"","lastWinUserLevelII":"","winsLevelIII":0,"largestWinLevelIII":0,"largestWinDateLevelIII":"","largestWinUserLevelIII":"","lastWinLevelIII":0,"lastWinDateLevelIII":"","lastWinUserLevelIII":"","winsLevelIV":0,"largestWinLevelIV":0,"largestWinDateLevelIV":"","largestWinUserLevelIV":"","lastWinLevelIV":0,"lastWinDateLevelIV":"","lastWinUserLevelIV":""},"gameIdentificationNumber":' . $postData['gameIdentificationNumber'] . ',"gameNumber":"","msg":"success","messageId":"","qName":"app.services.messages.response.GameEventResponse","command":"event","eventTimestamp":' . time() . '}';
                                $slotSettings->SetGameData('DiceAndRoll40EGTJackSteps', $jackStep);
                                break;
                            case 'gamble':
                                $Balance = $slotSettings->GetBalance();
                                $isGambleWin = rand(1, $slotSettings->GetGambleSettings());
                                $dealerCard = '';
                                $totalWin = $slotSettings->GetGameData('DiceAndRoll40EGTTotalWin');
                                $gambleWin = 0;
                                $statBet = $totalWin;
                                $slotSettings->SetGameData('DiceAndRoll40EGTGambleStep', $slotSettings->GetGameData('DiceAndRoll40EGTGambleStep') - 1);
                                if( $slotSettings->GetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : '')) < ($totalWin * 2) ) 
                                {
                                    $isGambleWin = 0;
                                }
                                if( $slotSettings->MaxWin < ($totalWin * $slotSettings->CurrentDenom) ) 
                                {
                                    $isGambleWin = 0;
                                }
                                $sndID = 'gamble2lost';
                                if( $isGambleWin == 1 ) 
                                {
                                    $gambleState = 'gamble';
                                    $sndID = 'gamble2win1';
                                    $gambleWin = $totalWin;
                                    $totalWin = $totalWin * 2;
                                    if( $postData['bet']['color'] == '1' ) 
                                    {
                                        $tmpCards = [
                                            '0', 
                                            '3'
                                        ];
                                        $dealerCard = $tmpCards[rand(0, 1)];
                                    }
                                    else
                                    {
                                        $tmpCards = [
                                            '2', 
                                            '1'
                                        ];
                                        $dealerCard = $tmpCards[rand(0, 1)];
                                    }
                                }
                                else
                                {
                                    $gambleState = 'idle';
                                    $sndID = 'gamble2lost';
                                    $gambleWin = -1 * $totalWin;
                                    $totalWin = 0;
                                    $slotSettings->SetGameData('DiceAndRoll40EGTGambleStep', 0);
                                    if( $postData['bet']['color'] == '1' ) 
                                    {
                                        $tmpCards = [
                                            '1', 
                                            '2'
                                        ];
                                        $dealerCard = $tmpCards[rand(0, 1)];
                                    }
                                    else
                                    {
                                        $tmpCards = [
                                            '3', 
                                            '0'
                                        ];
                                        $dealerCard = $tmpCards[rand(0, 1)];
                                    }
                                }
                                $slotSettings->SetGameData('DiceAndRoll40EGTTotalWin', $totalWin);
                                $slotSettings->SetBalance($gambleWin);
                                $slotSettings->SetBank((isset($postData['slotEvent']) ? $postData['slotEvent'] : ''), $gambleWin * -1);
                                $afterBalance = $slotSettings->GetBalance();
                                $jsSet = '{"dealerCard":"' . $dealerCard . '","gambleState":"' . $gambleState . '","totalWin":' . $totalWin . ',"afterBalance":' . $afterBalance . ',"Balance":' . $Balance . '}';
                                $response = '{"responseEvent":"gambleResult","serverResponse":' . $jsSet . '}';
                                $slotSettings->SaveLogReport($response, $statBet, 1, $gambleWin, 'slotGamble');
                                $hist = $slotSettings->GetGameData('DiceAndRoll40EGTCards');
                                array_pop($hist);
                                array_unshift($hist, $dealerCard);
                                $slotSettings->SetGameData('DiceAndRoll40EGTCards', $hist);
                                $gtype = 1;
                                $gtype0 = 1;
                                if( $slotSettings->GetGameData('DiceAndRoll40EGTBonusStart') == 1 ) 
                                {
                                    $gtype0 = 2;
                                }
                                $result_tmp[] = '{"complex":{"gambles":' . $slotSettings->GetGameData('DiceAndRoll40EGTGambleStep') . ',"card":' . $dealerCard . ',"jackpot":false,"gameCommand":"gamble"},"state":"' . $gambleState . '","winAmount":' . ($totalWin * 100) . ',"gameIdentificationNumber":' . $postData['gameIdentificationNumber'] . ',"gameNumber":"","sessionKey":"88e2b82e6537db4a339e4e1b5ce462cd","msg":"success","messageId":"' . $postData['messageId'] . '","qName":"app.services.messages.response.GameEventResponse","command":"bet","eventTimestamp":' . time() . '}';
                                break;
                        }
                        $response = implode('------:::', $result_tmp);
                        $slotSettings->SaveGameData();
                        $slotSettings->SaveGameDataStatic();
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
