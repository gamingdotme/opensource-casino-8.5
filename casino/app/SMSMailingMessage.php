<?php 
namespace VanguardLTE
{
    class SMSMailingMessage extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'sms_mailing_messages';
        protected $fillable = [
            'sms_mailing_id', 
            'user_id', 
            'sent', 
            'shop_id'
        ];
        public $timestamps = false;
        public static function boot()
        {
            parent::boot();
        }
        public function user()
        {
            return $this->belongsTo('VanguardLTE\User');
        }
        public function sms_mailing()
        {
            return $this->belongsTo('VanguardLTE\SMSMailing');
        }
    }

}
