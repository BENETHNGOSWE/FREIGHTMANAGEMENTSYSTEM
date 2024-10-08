<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Customer;
use App\Models\Loadpackage;
use App\Models\ClientAdmin;
use illuminate\Support\Facades\Auth;

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
        $this->data['clientadmin'] = ClientAdmin::where('client_id', $clientid)->count();
        return view('dashboards.admin.dashboard', $this->data);
    }

    public function show(Request $request, ClientAdmin $clientadmin)
    {
        $this->data['clientadmin'] = $clientadmin;
        return view('dashboards.admin.profile', $this->data);

    }

    public function edit(Request $request, ClientAdmin $clientadmin)
    {
        $this->data['clientadmin'] = $clientadmin;
        return view('dashboards.admin.edit', $this->data);
    }

    public function update(Request $request, ClientAdmin $clientadmin)
    {
        $validate = $request->validate([
            'firstname' => ['required', 'max:255', 'string'],
            'middlename' => ['required', 'max:255', 'string'],
            'lastname' => ['required', 'max:255', 'string'],
            'username' => ['required', 'max:255', 'string'],
            'email' => ['required', 'max:255', 'string'],
            'phone' => ['required', 'max:255', 'string'],
            'password' => ['required', 'max:255', 'string'],
        ]);

        $clientadmin->update($validate);
        return redirect()->route('admin.list.admin')->with('success', 'Invoice Status Updated Successfully');
    }
}
