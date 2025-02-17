<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use App\Models\Client;
use App\Models\ClientAdmin;
use App\Models\Loadpackage;
use App\Models\Loadpackagetype;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;


class CustomerController extends Controller
{
    protected $fms_id = null;
    protected $data = [];
    public function __construct()
    {
        $this->middleware('multiple');
        $this->middleware(function ($request, $next) {
            if (Auth::check()) {
                $this->fms_id = Auth::user()->client_id;
                $this->data['clientadmin'] = ClientAdmin::where('client_id', $this->fms_id)->first();
            }

            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->data['customers'] = Customer::where('client_id', $this->fms_id)
            ->latest()
            ->get();
        return view('dashboards.admin.customers.list_customers', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Customer::class);
        $client_id = auth()->user()->client_id;
        $clients = Client::pluck('client_name', 'id');
        $users = User::pluck('id');

        $this->data['clients'] = $clients;
        $this->data['users'] = $users;

        return view('dashboards.admin.customers.create', $this->data);
    }

    private function rules($id = null)
    {
        $rules = [
            'customer_firstname' => ['required', 'max:255', 'string'],
            'customer_middlename' => ['nullable', 'max:255', 'string'],
            'customer_lastname' => ['nullable', 'max:255', 'string'],
            'customer_email' => 'nullable|email|unique:users,email' . $id,
            'phone' => ['required', 'max:255', 'string', 'unique:users,phone' . ($id ? ',' . $id : '')],
        ];
        if ($id) {
            $rules['customer_email'] = 'nullable|email|unique:users,email,' . $id;
            $rules['phone'] = ['required', 'max:255', 'string', 'unique:users,phone,' . $id];
        }

        return $rules;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Customer::class);
        $validated = $this->validate($request, $this->rules());
    
        if ($validated) {
            $name = $validated['customer_firstname'] . ' ' . $validated['customer_middlename'] . ' ' . $validated['customer_lastname'];
            $password = Hash::make('123456');
    
            // Create a new user
            $user = User::create([
                'name' => $name,
                'email' => $validated['customer_email'],
                'phone' => $validated['phone'],
                'password' => $password,
                'client_id' => $this->fms_id,
            ]);
    
            if ($user) {
                $id_number = createcustomeridNumber($this->fms_id);
                $validated['user_id'] = $user->id;
                $validated['customer_no'] = $id_number;
                $validated['client_id'] = $user->client_id;
                $customer = Customer::create($validated);
    
                if ($customer) {
                    return redirect()->route('admin.create.loadpackage', ['customer_id' => $customer->id])
                        ->with('success', 'Customer added successfully! You can now add a load package.');
                }
            }
        }
    
        return redirect()->back()->with('error', 'Something went wrong');
    }
    
    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        $this->data['customer'] = $customer;
        $this->data['loadpackage'] = Loadpackage::all();
        return view('dashboards.admin.customers.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        $this->authorize('update', $customer);
        $this->data['page_title'] = 'Edit Staff';
        $client_id = auth()->user()->client_id;
        $this->data['customer'] = $customer;
        $this->data['clients'] = Client::pluck('client_name', 'id');
        $this->data['users'] = User::pluck('id');

        return view('dashboards.admin.customers.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $this->authorize('update', $customer);
        $validated = $this->validate($request, $this->rules($customer->user->id));
    
        try {
            DB::beginTransaction();
            $user = $customer->user;
            $user->name = $validated['customer_firstname'] . ' ' . $validated['customer_middlename'] . ' ' . $validated['customer_lastname'];
            $user->email = $validated['customer_email'];
            $user->phone = $validated['phone'];
            $user->save();
            $customer->update($validated);
    
            DB::commit(); 
        } catch (\Throwable $th) {
            DB::rollback();
            \Log::error($th->getMessage() . ' ' . $th->getTraceAsString());
            return back()->with('error', $th->getMessage());
        }
    
        return redirect()->route('admin.list.customer')->with('success', 'Customer updated successfully');
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request, $customer)
    {
        $customer = Customer::where('id', $customer)
            ->where('client_id', $this->fms_id)
            ->first();
        $this->authorize('delete', $customer);
        $customer->delete();
        return redirect()->route('admin.list.customer', $customer)->with('success', 'customer Deleted Successful');
    }
}
