<?php 
namespace VanguardLTE\Http\Controllers\Api\Player
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');

    use Illuminate\Foundation\Auth\User;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Hash;
    use VanguardLTE\User as VanguardLTEUser;


class StatusController extends \VanguardLTE\Http\Controllers\Api\ApiController
    {
        public function __construct(){
            // $this->middleware('auth');
        }
        public function checkUserLogin(\VanguardLTE\Http\Requests\Auth\LoginRequest $request)
        {
            $username = $request->input('username');
            $password = $request->input('password');
            $user = VanguardLTEUser::where('username', '=', $username)->first();

            if (!$user) {
                return response()->json([
                    'success'   => false,
                    'errormsg'  => 'Login Fail, please check username',
                    'data'      => ''
                ]);
            }
            if (!Hash::check($password, $user->password)) {
                return response()->json([
                    'success'   => false,
                    'errormsg'  => 'Login Fail, pls check password',
                    'data'      => ''
                ]);
            }
            try{
                if( !($token = \JWTAuth::attempt(['username'=> $username, 'password'=>$password])) ){
                    return $this->errorUnauthorized('Invalid credentials.');
                }
            }
            catch( \Tymon\JWTAuth\Exceptions\JWTException $e ) {
                return $this->errorInternalError('Could not create token.');
            }
            $id = $user->id;
            VanguardLTEUser::where('id', '=', $id)->update(['api_token' => $token]);
            return $this->respondWithItem($user, new \VanguardLTE\Transformers\StatusTransformer());
        }

        public function checkUsecheckUserOnline(Request $request){
            $this->validate($request,[ 'userid'  =>  'required' ]);
            $value  = VanguardLTEUser::where('id', '=', $request->input('userid'))->first();
            if( !$value ){
                return $this->respondWithArray(
                    [
                        'success'   => false,
                        'errormsg'  => 'Invalid User Id',
                        'data'      => ''
                    ]
                );
            }
            $current_time   = time();
            $last_online    = strtotime($value->last_online);
            if( round(abs($current_time - $last_online) / 60, 2) <= 5 ) {
                $is_online = true;
            }else{
                $is_online = false;
            }
            
            $score = $value->balance;
            $idel_sec = round(abs($current_time - $last_online), 2);
            return $this->respondWithArray([
                'success'   => true,
                'errormsg'  => '',
                    'data'      => [
                        'is_online'     => $is_online, 
                        'player_score'  => $score ,
                        'idleseconds'   => $idel_sec,
                    ]
                ]
            );
        }

        public function checkUserLoginSyn(\VanguardLTE\Http\Requests\Auth\LoginRequest $request){
            $username = $request->input('username');
            $password = $request->input('password');
            $user = VanguardLTEUser::where('username', '=', $username)->first();
            if (!$user) {
                return response()->json(
                    [
                        'status'    => false, 
                        'errormsg'  => 'Login Fail, please check username',
                        'login'     => false, 
                    ]
                );
            }
            if (!Hash::check($password, $user->password)) {
                return response()->json(
                    [
                        'status'    => false,
                        'errormsg'  => 'Login Fail, pls check password',
                        'login'     => false, 
                    ]
                );
            }
            return $this->respondWithArray(
                [
                    'status'    => true ,
                    'errormsg'  => '',
                    'login'     => true, 
                ]
            );
        }


        public function apiLogin($token){
            if( \Auth::check() ) 
            {
                event(new \VanguardLTE\Events\User\LoggedOut());
                \Auth::logout();
            }
            Info('auth.logout.frontend.api');
            $us = \VanguardLTE\User::where('api_token', '=', $token)->get();
            if( isset($us[0]->id) ) 
            {
                \Auth::loginUsingId($us[0]->id, true);
                $ref = request()->server('HTTP_REFERER');
                $gameUrl = '?api_exit=' . $ref;
                return redirect()->to($gameUrl);
            }
            else
            {
                return redirect()->to('');
            }
        }


        public function getUserData(Request $request){
            $this->validate($request,[ 'username'  =>  'required' ]);
            $value          = VanguardLTEUser::where('username', '=', $request->input('username'))->first();            
            if( !$value ){
                return $this->respondWithArray(
                    [
                        'success'   => false,
                        'errormsg'  => 'Invalid Username',
                        'data'      => ''
                    ]
                );
            }
            return $this->respondWithArray(
                [
                    'success'   => true,
                    'errormsg'  => '',
                    'data'      => [
                        'player_score'  => $value->balance ,
                    ]
                ]
            );
        }

        public function checkUserScore(Request $request){
            $this->validate($request,[ 'userid'  =>  'required' ]);
            $value          = VanguardLTEUser::where('id', '=', $request->input('userid'))->first();
            if( !$value ){
                return $this->respondWithArray(
                    [
                        'success'   => false,
                        'errormsg'  => 'Invalid User Id',
                        'data'      => ''
                    ]
                );
            }
            return $this->respondWithArray(
                [
                    'success'   => true,
                    'errormsg'  => '',
                    'data'      => [
                        'player_score'  => $value->balance ,
                    ]
                ]
            );
        }

        public function loadShopBalance(Request $request){
            $this->validate($request,[ 'parent_id'  =>  'required' ]);
            $Balance = User::where('parent_id', $request->input('parent_id'))->sum('balance');
            
            if($Balance){
                return $this->respondWithArray(
                    [
                        'success'   => true,
                        'errormsg'  => '',
                        'balance'   => $Balance
                    ]
                );
            }
            return $this->respondWithArray(
                [
                    'success'   => false,
                    'errormsg'  => 'No data found',
                    'balance'   => ''
                ]
            );
        }

        public function loadInAmounts(Request $request){
            $this->validate($request,[ 'parent_id'  =>  'required' ]);
            $Total_in = User::where('parent_id', $request->input('parent_id'))->sum('total_in');
            
            if($Total_in){
                return $this->respondWithArray(
                    [
                        'success'       => true,
                        'errormsg'      => '',
                        'player_score'   => $Total_in
                    ]
                );
            }
            return $this->respondWithArray(
                [
                    'success'       => false,
                    'errormsg'      => 'No data found',
                    'player_score'   => ''
                ]
            );
        }
    }
}
