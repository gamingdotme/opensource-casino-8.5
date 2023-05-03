<?php 
namespace VanguardLTE\Http\Controllers\Api\Player
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');

    use Illuminate\Http\Request;
    use VanguardLTE\PayTicket;

class CreditController extends \VanguardLTE\Http\Controllers\Controller
    {
        public function __construct(){
            // $this->middleware('auth');
            // $this->middleware('permission:credits.manage');
        }
        public function index(){
            $credits = \VanguardLTE\Credit::orderBy('credit', 'desc')->get();
            if( !$credits ){
                return response()->json(
                    [
                        'status'    => false ,
                        'errormsg'  => 'Sorry No data Found',
                        'data'      => '', 
                    ]
                );
            }
            return response()->json(
                [
                    'status'    => true ,
                    'errormsg'  => '',
                    'data'      => $credits, 
                ]
            );
        }
       
        // deposit credits
        public function creditsDeposit(\Illuminate\Http\Request $request){
            $this->validate($request,[
                'userhash'=>'required',
                'amount'  =>'required',
                'ts_id'   =>'required'
            ]);
            $us = \VanguardLTE\User::where(['auth_token' =>  request()->input('userhash') ])->first();
            
            // Check user role is cashier
            if( $us && $us->hasRole(['cashier', 'user'])){
                $Ticket = PayTicket::where([
                    'id'            => request()->input('ts_id'), 
                    'ticket_amount' => request()->input('amount'),
                    'ticket_status' => 0
                ])->first();
                if( $Ticket ){
                    $data = $request->all();
                    $credit = \VanguardLTE\User::where([
                        'credit' => $data['amount']
                    ]);
                    if($credit){
                        PayTicket::where('id', $Ticket->id)->Update(['ticket_status' => 1]);
                        return response()->json(
                            [
                                'status'    => true ,
                                'errormsg'  => '',
                            ]
                        );
                    }
                }
                return response()->json(
                    [
                        'status'    => false ,
                        'errormsg'  => 'Please check Ticket ID or Amount',
                    ]
                );
            }
            return response()->json(
                [
                    'status'    => false ,
                    'errormsg'  => 'Sorry No data Found',
                ]
            );
            
        }

        // pending credits
        public function pendingCashIN(\Illuminate\Http\Request $request){
            $this->validate($request,[
                'userhash'=>'required',
                'amount'  =>'required',
                'ts_id'   =>'required'
            ]);
            $us = \VanguardLTE\User::where(['auth_token' =>  request()->input('userhash') ])->first();
            if( $us && $us->hasRole(['cashier', 'user'])){
                $Ticket = PayTicket::where([
                    'id'            => request()->input('ts_id'), 
                    'ticket_amount' => request()->input('amount'),
                    'ticket_status' => 0
                ])->first();
                if( $Ticket ){
                    return response()->json(
                        [
                            'status'    => true ,
                            'errormsg'  => '',
                        ]
                    );
                }
                return response()->json(
                    [
                        'status'    => false ,
                        'errormsg'  => 'Please check Ticket ID or Amount',
                    ]
                );
            }
            return response()->json(
                [
                    'status'    => false ,
                    'errormsg'  => 'Invalid details',
                ]
            );
        }

        
        // public function edit($credit){
        //     if( !auth()->user()->hasRole('admin') ) 
        //     {
        //         return redirect()->route('backend.credit.list')->withErrors(trans('app.no_permission'));
        //     }
        //     $credit = \VanguardLTE\Credit::where(['id' => $credit])->firstOrFail();
        //     return view('backend.credits.edit', compact('credit'));
        // }

        // public function update(\Illuminate\Http\Request $request, \VanguardLTE\Credit $credit)
        // {
        //     if( !auth()->user()->hasRole('admin') ) 
        //     {
        //         return redirect()->route('backend.credit.list')->withErrors(trans('app.no_permission'));
        //     }
        //     $data = $request->only([
        //         'price', 
        //         'credit'
        //     ]);
        //     \VanguardLTE\Credit::where('id', $credit->id)->update($data);
        //     return redirect()->route('backend.credit.list')->withSuccess(trans('app.credit_updated'));
        // }

        // public function buy(\VanguardLTE\Credit $credit)
        // {
        //     if( !auth()->user()->hasRole('agent') ) 
        //     {
        //         return redirect()->route('backend.credit.list')->withErrors(trans('app.no_permission'));
        //     }
        //     $interkassa = (settings('payment_interkassa') ? \VanguardLTE\Lib\Interkassa::get_systems(auth()->user()->id, 0) : '');
        //     return view('backend.credits.buy', compact('credit', 'interkassa'));
        // }
        
        // public function delete(\VanguardLTE\Credit $credit)
        // {
        //     if( !auth()->user()->hasRole('admin') ) 
        //     {
        //         return redirect()->route('backend.credit.list')->withErrors(trans('app.no_permission'));
        //     }
        //     \VanguardLTE\Credit::where('id', $credit->id)->delete();
        //     return redirect()->route('backend.credit.list')->withSuccess(trans('app.credit_deleted'));
        // }

    }

}
