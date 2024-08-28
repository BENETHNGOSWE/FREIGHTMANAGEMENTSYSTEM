<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Owner\OwnerController;

class HomeController extends Controller
{
    public $fmh_id;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('set_freight');
        $this->middleware(function ($request, $next) {
            $this->fmh_id = $request->get('fmh_id');
            return $next($request);
        });
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $role = auth()->user()->getRoleNames()[0];

        switch ($role){
            case 'owner':
                return $this->ownerDashboard();
            default:
                return view('home'); 
            }       
    }

    public function ownerDashboard()
    {
        $dashboard = new OwnerController();
        return $dashboard ->index($this->fmh_id);
    }
}
