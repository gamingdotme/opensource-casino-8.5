<?php

namespace VanguardLTE\Transformers;

use League\Fractal\TransformerAbstract;
use VanguardLTE\Category;
use VanguardLTE\HappyHour;
use VanguardLTE\Progress;
use VanguardLTE\Shop;
use VanguardLTE\ShopCategory;
use VanguardLTE\JPG;

class ShopTransformer extends TransformerAbstract
{
    public function transform(Shop $shop)
    {

        $categories = ['All'];

        $ids = ShopCategory::where('shop_id', $shop->id)->pluck('category_id');
        if($ids){
            $cats = Category::whereIn('id', $ids)->get();
            if(count($cats)){
                foreach($cats AS $cat){
                    $categories[] = $cat->title;
                }
            }
        }

        if( auth()->user()->hasRole('cashier') ){
            return [
                'id' => $shop->id,
                'name' => $shop->name,
                'balance' => $shop->balance,
                'currency' => $shop->currency,
                'is_blocked' => $shop->is_blocked,
            ];
        } elseif( auth()->user()->hasRole('user') ){

            return [
                'currency' => $shop->currency,
                'refunds' => (auth()->user()->shop && auth()->user()->shop->progress_active) ? Progress::where(['shop_id' => auth()->user()->shop_id, 'rating' => auth()->user()->rating])->get() : [],
                'happy_hour' => HappyHour::where('shop_id', auth()->user()->shop_id)->get(),
                'jackpots' => JPG::select('id','date_time','name','balance','shop_id')->where('shop_id', auth()->user()->shop_id)->get(),
            ];

        } {
            return [
                'id' => $shop->id,
                'name' => $shop->name,
                'balance' => $shop->balance,
                'percent' => $shop->percent,
                'frontend' => $shop->frontend,
                'categories' => $categories,
                'max_win' => $shop->max_win,
                'currency' => $shop->currency,
                'is_blocked' => $shop->is_blocked,
                'orderby' => $shop->orderby,
                'distributor' => $shop->creator ? $shop->creator->username : ''
                //'created_at' => (string) $shop->created_at,
                //'updated_at' => (string) $shop->updated_at
            ];
        }


    }
}
