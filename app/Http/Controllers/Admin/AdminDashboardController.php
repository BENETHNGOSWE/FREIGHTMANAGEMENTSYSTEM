<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Customer;
use App\Models\User;
use App\Models\Staff;
use App\Models\Loadpackage;
use App\Models\ClientAdmin;
use App\Models\Container;
use illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminDashboardController extends Controller
{
    protected $data = [];

    protected $fms_id = null;

    public function __construct()
    {
        // $this->middleware('admin');
        $this->middleware(function ($request, $next) {
            if (Auth::check()) {
                $this->fms_id = Auth::user()->client_id;
            }

            return $next($request);
        });
    }

    public function index($fms_id): View
    {
        $clientid = auth()->user()->client_id;
        $clientadmin = ClientAdmin::where('client_id', $clientid)->first();

        $this->data['customerCount'] = Customer::where('client_id', $clientid)->count();
        $this->data['loadcreate'] = Loadpackage::where('client_id', $clientid)->count();
        $this->data['staffCount'] = Staff::where('client_id', $clientid)->count();
        $this->data['containerCount'] = Container::where('client_id', $clientid)->count();

        $this->data['clientadmin'] = $clientadmin;

        return view('dashboards.admin.dashboard', $this->data);
    }

    public function show(Request $request, $id)
    {
        $clientadmin = ClientAdmin::findOrFail($id);
        $this->data['clientadmin'] = $clientadmin;
        return view('dashboards.admin.profile', $this->data);
    }

    public function edit(Request $request, $id)
    {
        // $this->authorize('update', $clientAdmin);
        $clientadmin = ClientAdmin::findOrFail($id);

        $this->data['clientadmin'] = $clientadmin;
        return view('dashboards.admin.edit', $this->data);
    }

    public function update(Request $request, ClientAdmin $clientadmin)
    {
        // $this->authorize('update', $clientAdmin);
        $validated = $request->validate([
            'firstname' => ['required', 'max:255', 'string'],
            'middlename' => ['required', 'max:255', 'string'],
            'lastname' => ['required', 'max:255', 'string'],
            'username' => ['required', 'max:255', 'string'],
            'email' => ['required', 'max:255', 'string', 'email'],
            'phone' => ['required', 'max:255', 'string'],
            'password' => ['nullable', 'max:255', 'string'],
        ]);
    
        try {
            DB::beginTransaction();
            
            $clientadmin->update([
                'firstname' => $validated['firstname'],
                'middlename' => $validated['middlename'],
                'lastname' => $validated['lastname'],
                'username' => $validated['username'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
            ]);

            $user = User::find($clientadmin->user_id);
            if ($user) {
                $user->update([
                    'name' => $validated['firstname'] . ' ' . $validated['middlename'] . ' ' . $validated['lastname'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'password' => $request->filled('password') ? Hash::make($validated['password']) : $user->password,
                ]);
            }
    
            DB::commit();
            if (auth()->id() === $clientadmin->user_id) {
                auth()->user()->refresh();
            }
    
            return redirect()->route('admin.show.profile', $clientadmin)->with('success', 'Your Profile Updated Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong while updating');
        }
    }
    
}
