<?php 
namespace VanguardLTE
{
    class Article extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'articles';
        protected $fillable = [
            'title', 
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
