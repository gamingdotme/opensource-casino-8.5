<?php 
namespace VanguardLTE\Http\Controllers\Web\Backend
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class SMSMailingController extends \VanguardLTE\Http\Controllers\Controller
    {
        public function __construct()
        {
            $this->middleware('auth');
        }
        public function index(\Illuminate\Http\Request $request)
        {
            $mailings = \VanguardLTE\SMSMailing::where('shop_id', auth()->user()->shop_id)->orderBy('id', 'DESC');
            if( $request->theme != '' ) 
            {
                $mailings = $mailings->where('theme', 'like', '%' . $request->theme . '%');
            }
            if( $request->date_start != '' ) 
            {
                $dates = explode(' - ', $request->date_start);
                $mailings = $mailings->where('date_start', '>=', $dates[0]);
                $mailings = $mailings->where('date_start', '<=', $dates[1]);
            }
            if( $request->support != '' ) 
            {
                $mailings = $mailings->where('mailings.support', $request->support);
            }
            if( $request->status != '' ) 
            {
                $mailings = $mailings->where('mailings.status', $request->status);
            }
            if( $request->products != '' ) 
            {
                $mailings = $mailings->where('mailings.product_id', $request->products);
            }
            $mailings = $mailings->get();
            $roles = \VanguardLTE\Role::where('level', '<', auth()->user()->level())->pluck('name')->toArray();
            return view('backend.sms_mailings.list', compact('mailings', 'roles'));
        }
        public function create()
        {
            $roles = [];
            $statuses = [];
            return view('backend.sms_mailings.add', compact('roles', 'statuses'));
        }
        public function store(\Illuminate\Http\Request $request)
        {
            $data = $request->only([
                'theme', 
                'message', 
                'roles', 
                'date_start', 
                'statuses'
            ]);
            $request->validate([
                'theme' => 'required', 
                'message' => 'required', 
                'roles' => 'required', 
                'date_start' => 'required'
            ]);
            $users = \VanguardLTE\User::where('role_id', '!=', 3);
            if( isset($data['roles']) ) 
            {
                $roles = \VanguardLTE\Role::whereIn('slug', $data['roles'])->get();
                $data['roles'] = implode('|', $data['roles']);
                if( $roles ) 
                {
                    $users = $users->whereIn('role_id', $roles->pluck('id'))->where('phone', '!=', '')->where('phone_verified', 1);
                }
            }
            $users = $users->where('status', \VanguardLTE\Support\Enum\UserStatus::ACTIVE);
            $data['statuses'] = \VanguardLTE\Support\Enum\UserStatus::ACTIVE;
            $users = $users->get();
            if( !$users || !count($users) ) 
            {
                return redirect()->back()->withErrors([__('app.no_users_found')]);
            }
            $mailing = \VanguardLTE\SMSMailing::create($data + ['shop_id' => auth()->user()->shop_id]);
            if( $users ) 
            {
                foreach( $users as $user ) 
                {
                    if( $user->phone && $user->phone_verified ) 
                    {
                        \VanguardLTE\SMSMailingMessage::create([
                            'sms_mailing_id' => $mailing->id, 
                            'user_id' => $user->id, 
                            'shop_id' => auth()->user()->shop_id
                        ]);
                    }
                }
            }
            return redirect()->route('backend.sms_mailing.list')->withSuccess(__('app.sms_mailing_created'));
        }
        public function edit(\VanguardLTE\SMSMailing $mailing)
        {
            $roles = explode('|', $mailing->roles);
            $statuses = explode('|', $mailing->statuses);
            return view('backend.sms_mailings.edit', compact('mailing', 'roles', 'statuses'));
        }
        public function update(\Illuminate\Http\Request $request, \VanguardLTE\SMSMailing $mailing)
        {
            if( \Carbon\Carbon::now()->diffInSeconds(\Carbon\Carbon::parse($mailing->date_start), false) <= 0 ) 
            {
                return redirect()->back()->withErrors('Mailing started. Editing denied');
            }
            $request->validate([
                'theme' => 'required', 
                'message' => 'required', 
                'roles' => 'required', 
                'date_start' => 'required'
            ]);
            $data = $request->only([
                'theme', 
                'message', 
                'roles', 
                'date_start', 
                'statuses'
            ]);
            $users = \VanguardLTE\User::where('role_id', '!=', 3);
            if( isset($data['roles']) ) 
            {
                $roles = \VanguardLTE\Role::whereIn('slug', $data['roles'])->get();
                $data['roles'] = implode('|', $data['roles']);
                if( $roles ) 
                {
                    $users = $users->whereIn('role_id', $roles->pluck('id'));
                }
            }
            $users = $users->where('status', \VanguardLTE\Support\Enum\UserStatus::ACTIVE);
            $data['statuses'] = \VanguardLTE\Support\Enum\UserStatus::ACTIVE;
            $mailing->update($data);
            if( $users = $users->get() ) 
            {
                \VanguardLTE\SMSMailingMessage::where(['sms_mailing_id' => $mailing->id])->delete();
                foreach( $users as $user ) 
                {
                    if( $user->phone && $user->phone_verified ) 
                    {
                        \VanguardLTE\SMSMailingMessage::create([
                            'sms_mailing_id' => $mailing->id, 
                            'user_id' => $user->id, 
                            'shop_id' => auth()->user()->shop_id
                        ]);
                    }
                }
            }
            return redirect()->route('backend.sms_mailing.list')->withSuccess(__('app.sms_mailing_updated'));
        }
        public function delete(\VanguardLTE\SMSMailing $mailing)
        {
            $mailing->delete();
            return redirect()->route('backend.sms_mailing.list')->withSuccess(__('app.sms_mailing_deleted'));
        }
    }

}
