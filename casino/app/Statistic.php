<?php 
namespace VanguardLTE
{
    class Statistic extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'statistics';
        protected $fillable = [
            'title', 
            'user_id', 
            'payeer_id', 
            'system', 
            'type', 
            'sum', 
            'sum2', 
            'old', 
            'item_id', 
            'shop_id', 
            'credit_in', 
            'credit_out', 
            'money_in', 
            'money_out', 
            'hh_multiplier', 
            'created_at', 
            'user_agent', 
            'ip_address', 
            'country', 
            'city', 
            'os', 
            'device', 
            'browser'
        ];
        public function user()
        {
            return $this->belongsTo('VanguardLTE\User', 'user_id');
        }
        public function payeer()
        {
            return $this->hasOne('VanguardLTE\User', 'id', 'payeer_id');
        }
        public function shop()
        {
            return $this->hasOne('VanguardLTE\Shop', 'id', 'shop_id');
        }
        public function add()
        {
            return $this->hasOne('VanguardLTE\StatisticAdd');
        }
        public static function boot()
        {
            parent::boot();
            self::created(function($model)
            {
                $data = Lib\GeoData::get_data(true, true);
                $model->update($data);
                $data = [];
                if( $model->system == 'user' || $model->system == 'handpay' || in_array($model->system, [
                    'interkassa', 
                    'coinbase', 
                    'btcpayserver'
                ]) ) 
                {
                    if( $model->payeer->hasRole('admin') ) 
                    {
                        if( $model->type == 'add' ) 
                        {
                            $data['agent_in'] = $model->sum;
                        }
                        else
                        {
                            $data['agent_out'] = $model->sum;
                        }
                    }
                    if( $model->payeer->hasRole('agent') ) 
                    {
                        if( $model->type == 'add' ) 
                        {
                            $data['agent_out'] = $model->sum;
                            $data['distributor_in'] = $model->sum;
                        }
                        else
                        {
                            $data['distributor_out'] = $model->sum;
                            $data['agent_in'] = $model->sum;
                        }
                    }
                    if( $model->payeer->hasRole('cashier') ) 
                    {
                        if( $model->type == 'add' ) 
                        {
                            $data['credit_out'] = $model->sum;
                            $data['money_in'] = $model->sum;
                        }
                        else
                        {
                            $data['money_out'] = $model->sum;
                            $data['credit_in'] = $model->sum;
                        }
                    }
                }
                if( $model->system == 'shop' ) 
                {
                    if( $model->type == 'add' ) 
                    {
                        $data['distributor_out'] = $model->sum;
                        $data['credit_in'] = $model->sum;
                    }
                    else
                    {
                        $data['distributor_in'] = $model->sum;
                        $data['credit_out'] = $model->sum;
                    }
                }
                if( $model->system == 'pincode' ) 
                {
                    if( $model->type == 'add' ) 
                    {
                        $data['credit_out'] = $model->sum;
                        $data['money_in'] = $model->sum;
                    }
                    else
                    {
                        $data['money_out'] = $model->sum;
                        $data['credit_in'] = $model->sum;
                    }
                }
                if( in_array($model->system, [
                    'happyhour', 
                    'progress', 
                    'tournament', 
                    'refund', 
                    'invite', 
                    'daily_entry', 
                    'welcome_bonus', 
                    'sms_bonus', 
                    'wheelfortune'
                ]) ) 
                {
                    if( $model->type == 'add' ) 
                    {
                        $data['money_in'] = $model->sum;
                        if( $model->system == 'happyhour' ) 
                        {
                            $data['credit_out'] = $model->sum2;
                        }
                    }
                    else
                    {
                        $data['money_out'] = $model->sum;
                        if( $model->system == 'happyhour' ) 
                        {
                            $data['credit_in'] = $model->sum2;
                        }
                    }
                }
                if( in_array($model->system, [
                    'bank', 
                    'jpg'
                ]) ) 
                {
                    if( $model->type == 'add' ) 
                    {
                        $data['type_in'] = $model->sum;
                    }
                    else
                    {
                        $data['type_out'] = $model->sum;
                    }
                }
                if( count($data) ) 
                {
                    StatisticAdd::create($data + [
                        'statistic_id' => $model->id, 
                        'shop_id' => $model->shop_id, 
                        'user_id' => $model->user_id
                    ]);
                }
            });
        }
    }

}
