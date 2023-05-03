<?php 
namespace VanguardLTE\Http\Requests\Activity
{
    class GetActivitiesRequest extends \VanguardLTE\Http\Requests\Request
    {
        public function rules()
        {
            return ['per_page' => 'integer|max:100'];
        }
        public function messages()
        {
            return ['per_page.max' => 'Maximum number of records per page is 100.'];
        }
    }

}
