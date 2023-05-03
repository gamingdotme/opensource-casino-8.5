<?php 
namespace VanguardLTE
{
    class Rule extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'rules';
        protected $fillable = [
            'title', 
            'href', 
            'keywords', 
            'description', 
            'text'
        ];
        public static function boot()
        {
            parent::boot();
        }
    }

}
