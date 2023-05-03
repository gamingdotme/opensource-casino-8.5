<?php 
namespace VanguardLTE\Http\Controllers\Web\Backend
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class SupportController extends \VanguardLTE\Http\Controllers\Controller
    {
        public function __construct()
        {
            $this->middleware([
                'auth', 
                '2fa'
            ]);
            $this->middleware('permission:tickets.manage');
        }
        public function index()
        {
            if( auth()->user()->hasRole('admin') ) 
            {
                $tickets = \VanguardLTE\Ticket::orderByRaw('FIELD(status, "awaiting", "answered", "closed")')->orderBy('updated_at', 'ASC')->get();
            }
            else
            {
                $tickets = \VanguardLTE\Ticket::where('user_id', auth()->user()->id)->orderByRaw('FIELD(status, "answered", "awaiting", "closed")')->orderBy('updated_at', 'DESC')->get();
            }
            return view('backend.tickets.list', compact('tickets'));
        }
        public function create()
        {
            if( !auth()->user()->hasRole('admin') ) 
            {
                $active = \VanguardLTE\Ticket::where(['user_id' => auth()->user()->id])->where('status', '!=', 'closed')->count();
                if( $active >= 2 ) 
                {
                    return redirect()->route('backend.support.index')->withErrors(['Only 2 active tickets']);
                }
            }
            $ids = auth()->user()->hierarchyUsers();
            $users = \VanguardLTE\User::whereNotIn('role_id', [
                1, 
                6
            ])->whereIn('id', $ids)->pluck('username', 'id')->toArray();
            return view('backend.tickets.create', compact('users'));
        }
        public function store(\Illuminate\Http\Request $request)
        {
            $data = $request->only([
                'theme', 
                'text', 
                'user_id'
            ]);
            if( auth()->user()->hasRole('admin') ) 
            {
                $ticket = \VanguardLTE\Ticket::create($data + [
                    'admin' => 1, 
                    'shop_id' => auth()->user()->shop_id, 
                    'status' => 'answered', 
                    'temp_id' => rand(10, 100)
                ]);
            }
            else
            {
                $ticket = \VanguardLTE\Ticket::create($data + [
                    'user_id' => auth()->user()->id, 
                    'shop_id' => auth()->user()->shop_id, 
                    'temp_id' => rand(10, 100)
                ]);
                $admin = \VanguardLTE\User::where('role_id', 6)->first();
            }
            return redirect()->route('backend.support.index')->withSuccess('Ticket is created');
        }
        public function answer(\Illuminate\Http\Request $request, \VanguardLTE\Ticket $ticket)
        {
            if( !$ticket || !auth()->user()->hasRole('admin') && $ticket->user_id != auth()->user()->id ) 
            {
                return redirect()->route('backend.support.index')->withErrors(['Wrong link']);
            }
            $data = $request->all();
            $answer = \VanguardLTE\TicketAnswer::create([
                'ticket_id' => $ticket->id, 
                'message' => $data['message'], 
                'shop_id' => auth()->user()->shop_id, 
                'user_id' => \Auth::user()->id
            ]);
            if( auth()->user()->hasRole('admin') ) 
            {
                $ticket->update(['status' => 'answered']);
            }
            else
            {
                $ticket->update(['status' => 'awaiting']);
            }
            return redirect()->back()->withSuccess('Message sent');
        }
        public function view(\VanguardLTE\Ticket $ticket)
        {
            if( !$ticket || !auth()->user()->hasRole('admin') && $ticket->user_id != auth()->user()->id ) 
            {
                return redirect()->route('backend.support.index')->withErrors(['Wrong link']);
            }
            return view('backend.tickets.view', compact('ticket'));
        }
        public function close(\Illuminate\Http\Request $request, $ticket)
        {
            $ticket = \VanguardLTE\Ticket::find($ticket);
            if( !$ticket || !auth()->user()->hasRole('admin') && $ticket->user_id != auth()->user()->id ) 
            {
                return redirect()->route('backend.support.index')->withErrors(['Wrong link']);
            }
            $ticket->update(['status' => 'closed']);
            return redirect()->route('backend.support.index')->withSuccess('Ticket closed');
        }
        public function security()
        {
        }
    }

}
