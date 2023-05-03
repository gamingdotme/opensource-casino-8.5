<?php 
namespace VanguardLTE\Http\Requests\Game
{
    class CreateGameRequest extends \VanguardLTE\Http\Requests\Request
    {
        public function rules()
        {
            $rules = [
                'name' => 'required', 
                'title' => 'required', 
                'percent' => 'required', 
                'gameline' => 'required', 
                'bet' => 'required', 
                'winline' => 'required', 
                'garant_win' => 'required', 
                'winbonus' => 'required', 
                'garant_bonus' => 'required', 
                'gamebank' => 'required', 
                'match_winline' => 'required', 
                'match_winbonus' => 'required'
            ];
            return $rules;
        }
    }

}
