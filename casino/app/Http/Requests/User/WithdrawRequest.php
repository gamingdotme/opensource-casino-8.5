<?php 
namespace VanguardLTE\Http\Requests\User
{
    class WithdrawRequest extends \VanguardLTE\Http\Requests\Request
    {
        public function rules()
        {
            return [
                'txtamount' => 'required', 
                'txtcurrency' => 'required', 
			];
        }
        public function messages()
        {
            return [

			];
        }
    }

}
