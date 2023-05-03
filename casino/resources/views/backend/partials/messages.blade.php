@if(!(isset ($errors) && count($errors) > 0) && !Session::get('success', false) && Auth::check() && auth()->user()->shop_id > 0)
    @php
        $infos = [];
        $allInfos = \VanguardLTE\Info::get();
        if( count($allInfos) ){
            foreach($allInfos AS $infoItem){
                $toAdd = false;
                if($infoItem->user){
                    if($infoItem->user->hasRole('admin')){
                        $toAdd = true;
                    }
                    if($infoItem->user->hasRole('agent')){
                        if( in_array(auth()->user()->id, $infoItem->user->availableUsers()) ){
                            $toAdd = true;
                        }
                    }
                }
                if($toAdd){
                    if($infoItem->roles == '' || auth()->user()->hasRole(strtolower($infoItem->roles))){
                        $infos[] = $infoItem;
                    }
                }
            }
        }
        if( count($infos) > 1 ){
            $infos = [$infos[rand(1, count($infos))-1]];
        }
    @endphp
    @if($infos)
        @foreach($infos as $info)
            <div class="alert alert-warning">
                <h4>{{ $info->title  }}</h4>
                <p>{!! $info->text !!}</p>
            </div>
        @endforeach
    @endif
@endif

@php

    $messages = [];

    if( Auth::check() ){
        $infoShop = \VanguardLTE\Shop::find(auth()->user()->shop_id);
        $infoGames = \VanguardLTE\JPG::select(\DB::raw('SUM(percent) AS percent'))->where(['shop_id' => auth()->user()->shop_id])->first();

        if( $infoShop && ($infoGames->percent+$infoShop->percent) >= 100 ){
            $text = '<p>JPG = <b>' .$infoGames->percent. '%</b></p>';
            $text .= '<p>'.$infoShop->name.' = <b>' .$infoShop->percent. '%</b></p>';
            $text .= '<p>' . __('app.total_percentage', ['name' => $infoShop->name, 'percent' => $infoGames->percent+$infoShop->percent]).'</p>';
            $messages[] = $text;
        }
    }

    if( file_exists( resource_path() . '/views/system/pages/new_license.blade.php' ) ){
        $messages[] = __('app.new_license');
    }

@endphp

@if (session('blockError'))
    <div class="alert alert-danger">
        Errors in block {{ strtoupper(session('blockError')) }}
    </div>
@endif

@if(!isset($hide_block))
    @if(isset ($messages) && count($messages) > 0)
        <div class="alert alert-danger">
            <h4>@lang('app.error')</h4>
            <p>{!!  $messages[array_rand($messages)];  !!}</p>
        </div>
    @endif
@endif

@if(isset ($errors) && count($errors) > 0)
    <div class="alert alert-danger">
        <h4>@lang('app.error')</h4>
        @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif



@if(settings('siteisclosed'))
	<div class="alert alert-danger">
         <h4>@lang('app.turned_off')</h4>
         <p>@lang('app.site_is_turned_off')</p>
    </div>
@endif



@if(Session::get('success', false))
    <?php $data = Session::get('success'); ?>
    @if (is_array($data))
        @foreach ($data as $msg)
	        <div class="alert alert-success">
                <h4>@lang('app.success')</h4>
                <p>{{ $msg }}</p>
            </div>
        @endforeach
    @else
	        <div class="alert alert-success">
                <h4>@lang('app.success')</h4>
            <p>{{ $data }}</p>
            </div>
    @endif
@endif
