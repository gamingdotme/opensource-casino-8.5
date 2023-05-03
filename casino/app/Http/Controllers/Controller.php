<?php 
namespace VanguardLTE\Http\Controllers
{
    abstract class Controller extends \Illuminate\Routing\Controller
    {
        use \Illuminate\Foundation\Auth\Access\AuthorizesRequests, 
            \Illuminate\Foundation\Bus\DispatchesJobs, 
            \Illuminate\Foundation\Validation\ValidatesRequests;
    }

}
