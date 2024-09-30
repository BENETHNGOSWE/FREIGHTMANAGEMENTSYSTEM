<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Customer;
use App\Models\Loadpackage;

class AdminDashboardController extends Controller
{
    protected $data = [];

    protected $fms_id = null;

    public function __construct()
    {
        $this->middleware('admin');
        $this->middleware(function ($request, $next) {
            if (Auth::check()) {
                $this->fms_id = Auth::user()->client_id;
            }

            return $next($request);
        });
    }

    public function index($fms_id): View
    {
        $clientid = auth()-> user()->client_id;
        $this->data['customerCount'] = Customer::where('client_id', $clientid )->count();
        $this->data['loadcreate'] = Loadpackage::where('client_id', $clientid)->count();
        return view('dashboards.admin.dashboard', $this->data);
    }
}
