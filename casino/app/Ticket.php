<?php 
namespace VanguardLTE
{
    class Ticket extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'tickets';
        protected $fillable = [
            'theme', 
            'text', 
            'user_id', 
            'status', 
            'admin', 
            'shop_id', 
            'temp_id', 
            'country', 
            'city', 
            'os', 
            'device', 
            'browser', 
            'ip_address'
        ];
        public static function boot()
        {
            parent::boot();
            self::created(function($model)
            {
                $data = Lib\GeoData::get_data(true);
                $model->update($data);
            });
        }
        public function answers()
        {
            return $this->hasMany('VanguardLTE\TicketAnswer');
        }
        public function user()
        {
            return $this->belongsTo('VanguardLTE\User');
        }
        public function shop()
        {
            return $this->belongsTo('VanguardLTE\Shop');
        }
    }

}
