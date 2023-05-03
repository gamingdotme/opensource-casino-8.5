<?php 
namespace VanguardLTE
{
    class Faq extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'faqs';
        protected $fillable = [
            'question', 
            'answer', 
            'rank'
        ];
        public $timestamps = false;
        public static function boot()
        {
            parent::boot();
        }
    }

}
