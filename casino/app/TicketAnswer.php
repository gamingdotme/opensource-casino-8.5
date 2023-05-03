<?php 
namespace VanguardLTE
{
    class TicketAnswer extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'tickets_answers';
        protected $fillable = [
            'ticket_id', 
            'message', 
            'user_id', 
            'shop_id', 
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
                $ticket = Ticket::find($model->ticket_id);
                $ticket->update(['updated_at' => \Carbon\Carbon::now()]);
            });
        }
        public function user()
        {
            return $this->belongsTo('VanguardLTE\User');
        }
        public function ticket()
        {
            return $this->belongsTo('VanguardLTE\Ticket');
        }
    }

}
