<?php 
namespace VanguardLTE\Http\Controllers\Api\Player
{
    use Illuminate\Http\Request;
    use VanguardLTE\PayTicket;

    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    
    class TicketController extends \VanguardLTE\Http\Controllers\Api\ApiController
    {
        public function __construct(){
            // $this->middleware('auth');
        }
        public function PayoutTicket(Request $request){
            $this->validate($request,[
                'userhash'=>'required',
                'amount'  =>'required'
            ]);
            $us = \VanguardLTE\User::where(['auth_token' =>  request()->input('userhash') ])->first();
            // Check user role is cashier
            if($us){
                if( !$us->hasRole('cashier') ) {
                    return response()->json([
                        'success'   => false,
                        'errormsg'  => 'Access Denied!!',
                        'data'      => ''
                    ]);
                }
                $TicketPin = 'TEST_TICKET';
                $Ticket = PayTicket::create(['user_id' => $us->id , 'ticket_amount' => request()->input('amount'), 'ticket_pin'=> $TicketPin ]);
                return \response()->json([
                    'success'   => true,
                    'errormsg'  => '',
                    'data'      => [
                        'ticket_id'     => $Ticket->id,
                        'ticket_pin'    => $Ticket->ticket_pin,
                        'ticket_amount' => $Ticket->ticket_amount,
                        'ticket_date'   => date_format($Ticket->created_at ,"Y/m/d"),
                    ]
                ]);
            }
            return response()->json([
                'success'   => false,
                'errormsg'  => 'Sorry not a valid user',
                'data'      => ''
            ]);
        }
        
    }
}
