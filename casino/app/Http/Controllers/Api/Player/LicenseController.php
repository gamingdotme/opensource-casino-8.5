<?php 
namespace VanguardLTE\Http\Controllers\Api\Player
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');

    use Illuminate\Http\Request;

class LicenseController extends \VanguardLTE\Http\Controllers\Controller
    {
        public function __construct(){
            // $this->middleware('auth');
            // $this->middleware('permission:credits.manage');
        }
        public function AskForLicense(Request $request){
            $license = \VanguardLTE\User::where( 'username', $request->input('username') )->first();
            if( $license ){
                if( $license->api_license ){
                $encrypter  = new \Illuminate\Encryption\Encrypter('aq1LOdXbvN4uJAUHNmdCileMzz8zxyPB', 'AES-256-CBC');
                $lic = $encrypter->decrypt($license->api_license);
                    return response()->json(
                        [
                            'status'    => true ,
                            'errormsg'  => '',
                            'licdata'   => $license->api_license, 
                            'lic'       => $lic
                        ]
                    );
                }
                return response()->json(
                    [
                        'status'    => false ,
                        'errormsg'  => 'No license found',
                    ]
                );
            }
            return response()->json(
                [
                    'status'    => false ,
                    'errormsg'  => 'Invalid user',
                ]
            );
        }

        public function LicSaved(Request $request){
            $user = \VanguardLTE\User::where( 'username', $request->input('username') )->first();
            if( $user ){
                $encrypter  = new \Illuminate\Encryption\Encrypter('aq1LOdXbvN4uJAUHNmdCileMzz8zxyPB', 'AES-256-CBC');
                $license    = $encrypter->encrypt($user->username);
                // $license = \Crypt::encryptString($user->username);
                $user->api_license = $license;
                if( $user->save() ){
                    return response()->json(
                        [
                            'result'    => true ,
                            'status'    => true ,
                            'errormsg'  => '',
                        ]
                    );
                }
                return response()->json(
                    [
                        'status'    => false ,
                        'errormsg'  => 'Unable to create license',
                    ]
                );
            }
            return response()->json(
                [
                    'status'    => false ,
                    'errormsg'  => 'Invalid user',
                ]
            );
        }       
    }

}
