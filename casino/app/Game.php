<?php 
namespace VanguardLTE
{
    class Game extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'games';
        protected $hidden = [
            'created_at', 
            'updated_at'
        ];
        protected $fillable = [
            'name', 
            'title', 
            'shop_id', 
            'jpg_id', 
            'label', 
            'device', 
            'gamebank', 
            'chanceFirepot1', 
            'chanceFirepot2', 
            'chanceFirepot3', 
            'fireCount1', 
            'fireCount2', 
            'fireCount3', 
            'lines_percent_config_spin', 
            'lines_percent_config_spin_bonus', 
            'lines_percent_config_bonus', 
            'lines_percent_config_bonus_bonus', 
            'rezerv', 
            'cask', 
            'advanced', 
            'bet', 
            'scaleMode', 
            'slotViewState', 
            'view', 
            'denomination', 
            'category_temp', 
            'original_id', 
            'bids', 
            'stat_in', 
            'stat_out'
        ];
        public static $values = [
            'jp_1_percent' => [
                '1', 
                '0.9', 
                '0.8', 
                '0.7', 
                '0.6', 
                '0.5', 
                '0.4', 
                '0.3', 
                '0.2', 
                '0.1'
            ], 
            'jp_2_percent' => [
                '1', 
                '0.9', 
                '0.8', 
                '0.7', 
                '0.6', 
                '0.5', 
                '0.4', 
                '0.3', 
                '0.2', 
                '0.1'
            ], 
            'jp_3_percent' => [
                '1', 
                '0.9', 
                '0.8', 
                '0.7', 
                '0.6', 
                '0.5', 
                '0.4', 
                '0.3', 
                '0.2', 
                '0.1'
            ], 
            'jp_4_percent' => [
                '1', 
                '0.9', 
                '0.8', 
                '0.7', 
                '0.6', 
                '0.5', 
                '0.4', 
                '0.3', 
                '0.2', 
                '0.1'
            ], 
            'jp_5_percent' => [
                '1', 
                '0.9', 
                '0.8', 
                '0.7', 
                '0.6', 
                '0.5', 
                '0.4', 
                '0.3', 
                '0.2', 
                '0.1'
            ], 
            'jp_6_percent' => [
                '1', 
                '0.9', 
                '0.8', 
                '0.7', 
                '0.6', 
                '0.5', 
                '0.4', 
                '0.3', 
                '0.2', 
                '0.1'
            ], 
            'jp_7_percent' => [
                '1', 
                '0.9', 
                '0.8', 
                '0.7', 
                '0.6', 
                '0.5', 
                '0.4', 
                '0.3', 
                '0.2', 
                '0.1'
            ], 
            'jp_8_percent' => [
                '1', 
                '0.9', 
                '0.8', 
                '0.7', 
                '0.6', 
                '0.5', 
                '0.4', 
                '0.3', 
                '0.2', 
                '0.1'
            ], 
            'jp_9_percent' => [
                '1', 
                '0.9', 
                '0.8', 
                '0.7', 
                '0.6', 
                '0.5', 
                '0.4', 
                '0.3', 
                '0.2', 
                '0.1'
            ], 
            'jp_10_percent' => [
                '1', 
                '0.9', 
                '0.8', 
                '0.7', 
                '0.6', 
                '0.5', 
                '0.4', 
                '0.3', 
                '0.2', 
                '0.1'
            ], 
            'random_keys' => [
                '74_80' => [
                    74, 
                    80
                ], 
                '82_88' => [
                    82, 
                    88
                ], 
                '90_96' => [
                    90, 
                    96
                ]
            ], 
            'random_values' => [
                3, 
                4, 
                5, 
                6, 
                7, 
                8, 
                9, 
                10, 
                12, 
                15, 
                18, 
                20, 
                22, 
                25, 
                28, 
                30, 
                40, 
                50, 
                100
            ], 
            'bet' => [
                '0.01, 0.02, 0.05, 0.10, 0.20, 1.00, 5.00, 10.00, 20.00',
				'0.10, 0.20, 0.30, 0.40, 0.50, 0.60, 0.70, 0.80, 0.90, 1.00, 5.00, 10.00', 
				'10, 20, 50, 100',
				'1.00, 5.00, 10.00, 20.00',
            ], 
            'winline1' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'winline3' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'winline5' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'winline7' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'winline9' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'winline10' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'garant_win1' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'garant_win3' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'garant_win5' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'garant_win7' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'garant_win9' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'garant_win10' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'winbonus1' => [
                1, 
                50, 
                100, 
                200, 
                500, 
                1000
            ], 
            'winbonus3' => [
                1, 
                50, 
                100, 
                200, 
                500, 
                1000
            ], 
            'winbonus5' => [
                1, 
                50, 
                100, 
                200, 
                500, 
                1000
            ], 
            'winbonus7' => [
                1, 
                50, 
                100, 
                200, 
                500, 
                1000
            ], 
            'winbonus9' => [
                1, 
                50, 
                100, 
                200, 
                500, 
                1000
            ], 
            'winbonus10' => [
                1, 
                50, 
                100, 
                200, 
                500, 
                1000
            ], 
            'garant_bonus1' => [
                1, 
                50, 
                100, 
                200, 
                500, 
                1000
            ], 
            'garant_bonus3' => [
                1, 
                50, 
                100, 
                200, 
                500, 
                1000
            ], 
            'garant_bonus5' => [
                1, 
                50, 
                100, 
                200, 
                500, 
                1000
            ], 
            'garant_bonus7' => [
                1, 
                50, 
                100, 
                200, 
                500, 
                1000
            ], 
            'garant_bonus9' => [
                1, 
                50, 
                100, 
                200, 
                500, 
                1000
            ], 
            'garant_bonus10' => [
                1, 
                50, 
                100, 
                200, 
                500, 
                1000
            ], 
            'winline_bonus1' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'winline_bonus3' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'winline_bonus5' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'winline_bonus7' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'winline_bonus9' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'winline_bonus10' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'garant_win_bonus1' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'garant_win_bonus3' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'garant_win_bonus5' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'garant_win_bonus7' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'garant_win_bonus9' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'garant_win_bonus10' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'match_winline1' => [
                '2, 5, 5, 5, 4, 2, 1, 2, 2, 3, 4, 5, 3, 2, 2, 5, 4, 2, 3, 1, 10, 20', 
                '8, 8, 10, 10, 4, 4, 2, 4, 10, 10, 8, 2, 1, 3, 7, 10, 1, 3, 5, 5, 10, 20', 
                '1, 7, 12, 3, 17, 1, 9, 11, 13, 15, 14, 18, 9, 7, 3, 11, 2, 7, 5, 9, 10, 20'
            ], 
            'match_winline3' => [
                '2, 5, 5, 5, 4, 2, 1, 2, 2, 3, 4, 5, 3, 2, 2, 5, 4, 2, 3, 1, 10, 20', 
                '8, 8, 10, 10, 4, 4, 2, 4, 10, 10, 8, 2, 1, 3, 7, 10, 1, 3, 5, 5, 10, 20', 
                '1, 7, 12, 3, 17, 1, 9, 11, 13, 15, 14, 18, 9, 7, 3, 11, 2, 7, 5, 9, 10, 20'
            ], 
            'match_winline5' => [
                '2, 5, 5, 5, 4, 2, 1, 2, 2, 3, 4, 5, 3, 2, 2, 5, 4, 2, 3, 1, 10, 20', 
                '8, 8, 10, 10, 4, 4, 2, 4, 10, 10, 8, 2, 1, 3, 7, 10, 1, 3, 5, 5, 10, 20', 
                '1, 7, 12, 3, 17, 1, 9, 11, 13, 15, 14, 18, 9, 7, 3, 11, 2, 7, 5, 9, 10, 20'
            ], 
            'match_winline7' => [
                '2, 5, 5, 5, 4, 2, 1, 2, 2, 3, 4, 5, 3, 2, 2, 5, 4, 2, 3, 1, 10, 20', 
                '8, 8, 10, 10, 4, 4, 2, 4, 10, 10, 8, 2, 1, 3, 7, 10, 1, 3, 5, 5, 10, 20', 
                '1, 7, 12, 3, 17, 1, 9, 11, 13, 15, 14, 18, 9, 7, 3, 11, 2, 7, 5, 9, 10, 20'
            ], 
            'match_winline9' => [
                '2, 5, 5, 5, 4, 2, 1, 2, 2, 3, 4, 5, 3, 2, 2, 5, 4, 2, 3, 1, 10, 20', 
                '8, 8, 10, 10, 4, 4, 2, 4, 10, 10, 8, 2, 1, 3, 7, 10, 1, 3, 5, 5, 10, 20', 
                '1, 7, 12, 3, 17, 1, 9, 11, 13, 15, 14, 18, 9, 7, 3, 11, 2, 7, 5, 9, 10, 20'
            ], 
            'match_winline10' => [
                '2, 5, 5, 5, 4, 2, 1, 2, 2, 3, 4, 5, 3, 2, 2, 5, 4, 2, 3, 1, 10, 20', 
                '8, 8, 10, 10, 4, 4, 2, 4, 10, 10, 8, 2, 1, 3, 7, 10, 1, 3, 5, 5, 10, 20', 
                '1, 7, 12, 3, 17, 1, 9, 11, 13, 15, 14, 18, 9, 7, 3, 11, 2, 7, 5, 9, 10, 20'
            ], 
            'match_winbonus1' => [
                '101, 122, 82, 118, 134, 59, 86, 87, 73, 103, 121, 133, 75, 133, 97, 78, 150, 107, 98, 67', 
                '115, 105, 222, 199, 196, 170, 120, 189, 240, 126, 194, 210, 194, 143, 164, 147, 246, 104, 201, 245', 
                '185, 343, 450, 288, 315, 243, 302, 259, 376, 338, 431, 229, 272, 265, 150, 226, 441, 460, 478, 273'
            ], 
            'match_winbonus3' => [
                '101, 122, 82, 118, 134, 59, 86, 87, 73, 103, 121, 133, 75, 133, 97, 78, 150, 107, 98, 67', 
                '115, 105, 222, 199, 196, 170, 120, 189, 240, 126, 194, 210, 194, 143, 164, 147, 246, 104, 201, 245', 
                '185, 343, 450, 288, 315, 243, 302, 259, 376, 338, 431, 229, 272, 265, 150, 226, 441, 460, 478, 273'
            ], 
            'match_winbonus5' => [
                '101, 122, 82, 118, 134, 59, 86, 87, 73, 103, 121, 133, 75, 133, 97, 78, 150, 107, 98, 67', 
                '115, 105, 222, 199, 196, 170, 120, 189, 240, 126, 194, 210, 194, 143, 164, 147, 246, 104, 201, 245', 
                '185, 343, 450, 288, 315, 243, 302, 259, 376, 338, 431, 229, 272, 265, 150, 226, 441, 460, 478, 273'
            ], 
            'match_winbonus7' => [
                '101, 122, 82, 118, 134, 59, 86, 87, 73, 103, 121, 133, 75, 133, 97, 78, 150, 107, 98, 67', 
                '115, 105, 222, 199, 196, 170, 120, 189, 240, 126, 194, 210, 194, 143, 164, 147, 246, 104, 201, 245', 
                '185, 343, 450, 288, 315, 243, 302, 259, 376, 338, 431, 229, 272, 265, 150, 226, 441, 460, 478, 273'
            ], 
            'match_winbonus9' => [
                '101, 122, 82, 118, 134, 59, 86, 87, 73, 103, 121, 133, 75, 133, 97, 78, 150, 107, 98, 67', 
                '115, 105, 222, 199, 196, 170, 120, 189, 240, 126, 194, 210, 194, 143, 164, 147, 246, 104, 201, 245', 
                '185, 343, 450, 288, 315, 243, 302, 259, 376, 338, 431, 229, 272, 265, 150, 226, 441, 460, 478, 273'
            ], 
            'match_winbonus10' => [
                '101, 122, 82, 118, 134, 59, 86, 87, 73, 103, 121, 133, 75, 133, 97, 78, 150, 107, 98, 67', 
                '115, 105, 222, 199, 196, 170, 120, 189, 240, 126, 194, 210, 194, 143, 164, 147, 246, 104, 201, 245', 
                '185, 343, 450, 288, 315, 243, 302, 259, 376, 338, 431, 229, 272, 265, 150, 226, 441, 460, 478, 273'
            ], 
            'match_winline_bonus1' => [
                '2, 5, 5, 5, 4, 2, 1, 2, 2, 3, 4, 5, 3, 2, 2, 5, 4, 2, 3, 1, 10, 20', 
                '8, 8, 10, 10, 4, 4, 2, 4, 10, 10, 8, 2, 1, 3, 7, 10, 1, 3, 5, 5, 10, 20', 
                '1, 7, 12, 3, 17, 1, 9, 11, 13, 15, 14, 18, 9, 7, 3, 11, 2, 7, 5, 9, 10, 20'
            ], 
            'match_winline_bonus3' => [
                '2, 5, 5, 5, 4, 2, 1, 2, 2, 3, 4, 5, 3, 2, 2, 5, 4, 2, 3, 1, 10, 20', 
                '8, 8, 10, 10, 4, 4, 2, 4, 10, 10, 8, 2, 1, 3, 7, 10, 1, 3, 5, 5, 10, 20', 
                '1, 7, 12, 3, 17, 1, 9, 11, 13, 15, 14, 18, 9, 7, 3, 11, 2, 7, 5, 9, 10, 20'
            ], 
            'match_winline_bonus5' => [
                '2, 5, 5, 5, 4, 2, 1, 2, 2, 3, 4, 5, 3, 2, 2, 5, 4, 2, 3, 1, 10, 20', 
                '8, 8, 10, 10, 4, 4, 2, 4, 10, 10, 8, 2, 1, 3, 7, 10, 1, 3, 5, 5, 10, 20', 
                '1, 7, 12, 3, 17, 1, 9, 11, 13, 15, 14, 18, 9, 7, 3, 11, 2, 7, 5, 9, 10, 20'
            ], 
            'match_winline_bonus7' => [
                '2, 5, 5, 5, 4, 2, 1, 2, 2, 3, 4, 5, 3, 2, 2, 5, 4, 2, 3, 1, 10, 20', 
                '8, 8, 10, 10, 4, 4, 2, 4, 10, 10, 8, 2, 1, 3, 7, 10, 1, 3, 5, 5, 10, 20', 
                '1, 7, 12, 3, 17, 1, 9, 11, 13, 15, 14, 18, 9, 7, 3, 11, 2, 7, 5, 9, 10, 20'
            ], 
            'match_winline_bonus9' => [
                '2, 5, 5, 5, 4, 2, 1, 2, 2, 3, 4, 5, 3, 2, 2, 5, 4, 2, 3, 1, 10, 20', 
                '8, 8, 10, 10, 4, 4, 2, 4, 10, 10, 8, 2, 1, 3, 7, 10, 1, 3, 5, 5, 10, 20', 
                '1, 7, 12, 3, 17, 1, 9, 11, 13, 15, 14, 18, 9, 7, 3, 11, 2, 7, 5, 9, 10, 20'
            ], 
            'match_winline_bonus10' => [
                '2, 5, 5, 5, 4, 2, 1, 2, 2, 3, 4, 5, 3, 2, 2, 5, 4, 2, 3, 1, 10, 20', 
                '8, 8, 10, 10, 4, 4, 2, 4, 10, 10, 8, 2, 1, 3, 7, 10, 1, 3, 5, 5, 10, 20', 
                '1, 7, 12, 3, 17, 1, 9, 11, 13, 15, 14, 18, 9, 7, 3, 11, 2, 7, 5, 9, 10, 20'
            ], 
            'rezerv' => [
                2, 
                4, 
                6, 
                8, 
                10
            ], 
            'cask' => [
                9, 
                18, 
                36, 
                72, 
                90
            ], 
            'denomination' => [
                '0.01', 
                '0.02', 
                '0.05', 
                '0.10', 
                '0.20', 
                '0.25', 
                '0.50', 
                '1.00', 
                '2.00', 
                '2.50', 
                '5.00', 
                '10.00', 
                '20.00', 
                '25.00', 
                '50.00', 
                '100.00'
            ], 
            'gamebank' => [
                'slots', 
                'little', 
                'table_bank', 
                'fish'
            ], 
            'chanceFirepot1' => [
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
                20
            ], 
            'chanceFirepot2' => [
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
                20
            ], 
            'chanceFirepot3' => [
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
                20
            ], 
            'fireCount1' => [
                '1,1,1,1,1,2,2,2,2,2,3,3,3', 
                '1,1,1,2,2,2,3,3,3,3', 
                '1,1,2,2,3,3,3'
            ], 
            'fireCount2' => [
                '1,1,1,1,1,2,2,2,2,2,3,3,3', 
                '1,1,1,2,2,2,3,3,3,3', 
                '1,1,2,2,3,3,3'
            ], 
            'fireCount3' => [
                '1,1,1,1,1,2,2,2,2,2,3,3,3', 
                '1,1,1,2,2,2,3,3,3,3', 
                '1,1,2,2,3,3,3'
            ]
        ];
        public $shortNames = [
            'Low', 
            'Medium', 
            'High'
        ];
        public $labels = ['Exclusive' => 'Exclusive'];
        public $gamebankNames = [
            'slots' => 'Slots', 
            'little' => 'Little', 
            'table_bank' => 'Table', 
            'fish' => 'Fish'
        ];
        public static function boot()
        {
            parent::boot();
            self::saved(function($model)
            {
                Game::where('id', $model->id)->update(['title' => Lib\Functions::remove_emoji($model->title)]);
            });
            self::updated(function($model)
            {
                event(new Events\Game\GameEdited($model));
            });
            self::deleting(function($model)
            {
                UserActivity::where([
                    'item_id' => $model->id, 
                    'system' => 'game'
                ])->delete();
                GameCategory::where('game_id', $model->id)->delete();
                GameLog::where('game_id', $model->id)->delete();
                Security::where([
                    'type' => 'game', 
                    'item_id' => $model->id
                ])->delete();
                StatGame::where([
                    'shop_id' => $model->shop_id, 
                    'game' => $model->name
                ])->delete();
            });
        }
        public function get_values($key, $add_empty = false, $add_value = false)
        {
            $_obf_0D080D1D022939321A102A2608313704131B192D1F3E22 = Game::$values[$key];
            $_obf_0D0E0F25210E3F172C323526131E171E132433121A3811 = $_obf_0D080D1D022939321A102A2608313704131B192D1F3E22;
            if( strpos($key, 'match_winbonus') > -1 || strpos($key, 'match_winline') > -1 || strpos($key, 'match_winline_bonus') > -1 ) 
            {
                $_obf_0D0E0F25210E3F172C323526131E171E132433121A3811 = $this->shortNames;
                $add_value = false;
            }
            if( $add_empty ) 
            {
                $_obf_0D16393608061D2713341828211C09042A063E1F072201 = array_combine(array_merge([''], $_obf_0D080D1D022939321A102A2608313704131B192D1F3E22), array_merge(['---'], $_obf_0D0E0F25210E3F172C323526131E171E132433121A3811));
            }
            else
            {
                $_obf_0D16393608061D2713341828211C09042A063E1F072201 = array_combine($_obf_0D080D1D022939321A102A2608313704131B192D1F3E22, $_obf_0D0E0F25210E3F172C323526131E171E132433121A3811);
            }
            if( $add_value ) 
            {
                return [$add_value => $add_value] + $_obf_0D16393608061D2713341828211C09042A063E1F072201;
            }
            return $_obf_0D16393608061D2713341828211C09042A063E1F072201;
        }
        public function shop()
        {
            return $this->belongsTo('VanguardLTE\Shop', 'shop_id');
        }
        public function jpg()
        {
            return JPG::where('shop_id', $this->shop_id)->get();
        }
        public function jackpot()
        {
            return $this->hasOne('VanguardLTE\JPG', 'id', 'jpg_id');
        }
        public function game_bank()
        {
            return $this->hasOne('VanguardLTE\GameBank', 'shop_id', 'shop_id');
        }
        public function fish_bank()
        {
            return $this->hasOne('VanguardLTE\FishBank', 'shop_id', 'shop_id');
        }
        public function statistics()
        {
            $shop_id = (\Auth::check() ? \Auth::user()->shop_id : 0);
            return $this->hasMany('VanguardLTE\StatGame', 'game', 'name')->where('shop_id', $shop_id)->orderBy('date_time', 'DESC');
        }
        public function categories()
        {
            return $this->hasMany('VanguardLTE\GameCategory', 'category_id');
        }
        public function tournaments()
        {
            return $this->hasMany('VanguardLTE\TournamentGame', 'game_id', 'id');
        }
        public function name_ico()
        {
            return explode(' ', $this->name)[0];
        }
        public function get_gamebank($slotState = '', $bankType = '')
        {
            if( $slotState == 'bonus' ) 
            {
                return Lib\Banker::get_bank($this->shop_id, 'bonus');
            }
            if( $bankType != '' ) 
            {
                return Lib\Banker::get_bank($this->shop_id, $bankType);
            }
            if( $this->gamebank != null && $this->game_bank ) 
            {
                return Lib\Banker::get_bank($this->shop_id, $this->gamebank);
            }
            return 0;
        }
        public function tournament_stat($slotState, $user_id, $bet, $win)
        {
            $tournaments = TournamentGame::where('game_id', $this->id)->get();
            if( $tournaments ) 
            {
                foreach( $tournaments as $tournament ) 
                {
                    if( \Carbon\Carbon::now()->diffInSeconds(\Carbon\Carbon::parse($tournament->tournament->start), false) >= 0 ) 
                    {
                        continue;
                    }
                    else if( \Carbon\Carbon::now()->diffInSeconds(\Carbon\Carbon::parse($tournament->tournament->end), false) <= 0 ) 
                    {
                        continue;
                    }
                    if( $bet < $tournament->tournament->bet ) 
                    {
                        continue;
                    }
                    $_obf_0D5C0E330436160F1E3D275B11262E3837273C010E3732 = TournamentStat::where([
                        'tournament_id' => $tournament->tournament_id, 
                        'user_id' => $user_id, 
                        'is_bot' => 0
                    ])->first();
                    if( !$_obf_0D5C0E330436160F1E3D275B11262E3837273C010E3732 ) 
                    {
                        $_obf_0D5C0E330436160F1E3D275B11262E3837273C010E3732 = TournamentStat::create([
                            'tournament_id' => $tournament->tournament_id, 
                            'user_id' => $user_id, 
                            'is_bot' => 0
                        ]);
                    }
                    $_obf_0D5C0E330436160F1E3D275B11262E3837273C010E3732->increment('sum_of_bets', $bet);
                    $_obf_0D5C0E330436160F1E3D275B11262E3837273C010E3732->increment('sum_of_wins', $win);
                    $_obf_0D5C0E330436160F1E3D275B11262E3837273C010E3732->increment('spins');
                    switch( $tournament->tournament->type ) 
                    {
                        case 'amount_of_bets':
                            $_obf_0D5C0E330436160F1E3D275B11262E3837273C010E3732->increment('points', $bet);
                            break;
                        case 'amount_of_winnings':
                            $_obf_0D5C0E330436160F1E3D275B11262E3837273C010E3732->increment('points', $win);
                            break;
                        case 'win_to_bet_ratio':
                            $_obf_0D5C0E330436160F1E3D275B11262E3837273C010E3732->increment('points', $win / $bet);
                            break;
                        case 'profit':
                            $_obf_0D5C0E330436160F1E3D275B11262E3837273C010E3732->increment('points', $win - $bet);
                            break;
                    }
                }
            }
        }
        public function set_gamebank($balance, $type = 'update', $slotState = '')
        {
            if( $this->gamebank != null || $slotState == 'bonus' ) 
            {
                $gamebank = $this->gamebank;
                if( $slotState == 'bonus' ) 
                {
                    $gamebank = 'bonus';
                }
                Lib\Banker::update_bank($this->shop_id, $gamebank, $balance, $type);
            }
        }
        public function get_line_value($data, $index1, $index2, $return_empty = false)
        {
            $data = json_decode($data, true);
            if( isset($data[$index1][$index2]) ) 
            {
                return $data[$index1][$index2];
            }
            if( $return_empty ) 
            {
                return '';
            }
            return 1;
        }
        public function get_lines_percent_config($type)
        {
            $result = [];
            if( $type == 'spin' ) 
            {
                foreach( [
                    1, 
                    3, 
                    5, 
                    7, 
                    9, 
                    10
                ] as $_obf_0D0D0A14015C211A213C3B3F270C192B3C150C122A0422 ) 
                {
                    foreach( Game::$values['random_keys'] as $_obf_0D1A15373D212F3909220F0C01402E380B26353B011E01 => $values ) 
                    {
                        $result['line' . $_obf_0D0D0A14015C211A213C3B3F270C192B3C150C122A0422][$_obf_0D1A15373D212F3909220F0C01402E380B26353B011E01] = $this->get_line_value($this->lines_percent_config_spin, 'line' . $_obf_0D0D0A14015C211A213C3B3F270C192B3C150C122A0422, $_obf_0D1A15373D212F3909220F0C01402E380B26353B011E01);
                    }
                }
                foreach( [
                    1, 
                    3, 
                    5, 
                    7, 
                    9, 
                    10
                ] as $_obf_0D0D0A14015C211A213C3B3F270C192B3C150C122A0422 ) 
                {
                    foreach( Game::$values['random_keys'] as $_obf_0D1A15373D212F3909220F0C01402E380B26353B011E01 => $values ) 
                    {
                        $result['line' . $_obf_0D0D0A14015C211A213C3B3F270C192B3C150C122A0422 . '_bonus'][$_obf_0D1A15373D212F3909220F0C01402E380B26353B011E01] = $this->get_line_value($this->lines_percent_config_spin_bonus, 'line' . $_obf_0D0D0A14015C211A213C3B3F270C192B3C150C122A0422 . '_bonus', $_obf_0D1A15373D212F3909220F0C01402E380B26353B011E01);
                    }
                }
            }
            if( $type == 'bonus' ) 
            {
                foreach( [
                    1, 
                    3, 
                    5, 
                    7, 
                    9, 
                    10
                ] as $_obf_0D0D0A14015C211A213C3B3F270C192B3C150C122A0422 ) 
                {
                    foreach( Game::$values['random_keys'] as $_obf_0D1A15373D212F3909220F0C01402E380B26353B011E01 => $values ) 
                    {
                        $result['line' . $_obf_0D0D0A14015C211A213C3B3F270C192B3C150C122A0422][$_obf_0D1A15373D212F3909220F0C01402E380B26353B011E01] = $this->get_line_value($this->lines_percent_config_bonus, 'line' . $_obf_0D0D0A14015C211A213C3B3F270C192B3C150C122A0422, $_obf_0D1A15373D212F3909220F0C01402E380B26353B011E01);
                    }
                }
                foreach( [
                    1, 
                    3, 
                    5, 
                    7, 
                    9, 
                    10
                ] as $_obf_0D0D0A14015C211A213C3B3F270C192B3C150C122A0422 ) 
                {
                    foreach( Game::$values['random_keys'] as $_obf_0D1A15373D212F3909220F0C01402E380B26353B011E01 => $values ) 
                    {
                        $result['line' . $_obf_0D0D0A14015C211A213C3B3F270C192B3C150C122A0422 . '_bonus'][$_obf_0D1A15373D212F3909220F0C01402E380B26353B011E01] = $this->get_line_value($this->lines_percent_config_bonus_bonus, 'line' . $_obf_0D0D0A14015C211A213C3B3F270C192B3C150C122A0422 . '_bonus', $_obf_0D1A15373D212F3909220F0C01402E380B26353B011E01);
                    }
                }
            }
            return $result;
        }
        public function is_new()
        {
            $_obf_0D2613391B3B320D402A132D39332E150D033015211622 = 0;
            $_obf_0D14180B1438210C0C2D27371B22173212131E045C2201 = new \Detection\MobileDetect();
            if( $_obf_0D14180B1438210C0C2D27371B22173212131E045C2201->isMobile() || $_obf_0D14180B1438210C0C2D27371B22173212131E045C2201->isTablet() ) 
            {
                $_obf_0D2613391B3B320D402A132D39332E150D033015211622 = 1;
            }
            $_obf_0D2613391B3B320D402A132D39332E150D033015211622 = \Illuminate\Support\Facades\Cache::get('new_games:' . $this->shop_id . ':' . $_obf_0D2613391B3B320D402A132D39332E150D033015211622, []);
            if( $_obf_0D2613391B3B320D402A132D39332E150D033015211622 && count($_obf_0D2613391B3B320D402A132D39332E150D033015211622) ) 
            {
                foreach( $_obf_0D2613391B3B320D402A132D39332E150D033015211622 as $item ) 
                {
                    if( $item == $this->id ) 
                    {
                        return true;
                    }
                }
            }
            return false;
        }
        public function is_hot()
        {
            $_obf_0D2613391B3B320D402A132D39332E150D033015211622 = 0;
            $_obf_0D14180B1438210C0C2D27371B22173212131E045C2201 = new \Detection\MobileDetect();
            if( $_obf_0D14180B1438210C0C2D27371B22173212131E045C2201->isMobile() || $_obf_0D14180B1438210C0C2D27371B22173212131E045C2201->isTablet() ) 
            {
                $_obf_0D2613391B3B320D402A132D39332E150D033015211622 = 1;
            }
            $_obf_0D2613391B3B320D402A132D39332E150D033015211622 = \Illuminate\Support\Facades\Cache::get('hot_games:' . $this->shop_id . ':' . $_obf_0D2613391B3B320D402A132D39332E150D033015211622, []);
            if( $_obf_0D2613391B3B320D402A132D39332E150D033015211622 && count($_obf_0D2613391B3B320D402A132D39332E150D033015211622) ) 
            {
                foreach( $_obf_0D2613391B3B320D402A132D39332E150D033015211622 as $item ) 
                {
                    if( $item == $this->id ) 
                    {
                        return true;
                    }
                }
            }
            $_obf_0D08392F051D11291E212D3B2702262417332227112A22 = \Illuminate\Support\Facades\Cache::get('hot_games:' . $this->shop_id . ':0', []);
            if( $_obf_0D08392F051D11291E212D3B2702262417332227112A22 && count($_obf_0D08392F051D11291E212D3B2702262417332227112A22) ) 
            {
                foreach( $_obf_0D08392F051D11291E212D3B2702262417332227112A22 as $item ) 
                {
                    if( $item == $this->id ) 
                    {
                        return true;
                    }
                }
            }
            return false;
        }
    }

}
