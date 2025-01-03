<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Customer;
use App\Models\User;
use App\Models\Loadpackage;

class ManagerDashboardController extends Controller
{
    protected $data = [];

    protected $fms_id = null;

    public function __construct()
    {
        $this->middleware('manager');
        $this->middleware(function ($request, $next) {
            if (Auth::check()) {
                $this->fms_id = Auth::user()->client_id;
                $this->data['clientadmin'] = ClientAdmin::where('client_id', $this->fms_id)->first();
            }

            return $next($request);
        });
    }

    public function index($fms_id): View
    {
        $clientid = auth()->user()->client_id;
        // $clientadmin = ClientAdmin::where('client_id', $clientid)->first();

        $this->data['customerCount'] = Customer::where('client_id', $clientid)->count();
        $this->data['loadcreate'] = Loadpackage::where('client_id', $clientid)->count();
        return view('dashboards.manager.dashboard', $this->data);
    }
}
