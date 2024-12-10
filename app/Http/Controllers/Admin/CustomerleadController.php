<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Customerlead;
use App\Models\Customercategory;
use App\Models\Client;
use App\Models\ClientAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;


class CustomerleadController extends Controller
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
        $this->data['customerleads'] = Customerlead::where('client_id', $this->fms_id)
            ->latest()
            ->get();
        return view('dashboards.admin.customerleads.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Customerlead::class);
        $client_id = auth()->user()->client_id;
        $clients = Client::pluck('client_name', 'id');
        $users = User::pluck('id');
        $this->data['customercategories'] = Customercategory::where('client_id', $this->fms_id)->pluck('categoryname', 'id');

        $this->data['clients'] = $clients;
        $this->data['users'] = $users;

        return view('dashboards.admin.customerleads.create', $this->data);
    }

    private function rules($id = null)
    {
        $rules = [
            'customer_firstname' => ['required', 'max:255', 'string'],
            'customer_middlename' => ['nullable', 'max:255', 'string'],
            'customer_lastname' => ['required', 'max:255', 'string'],
            'category_id' => ['required', 'exists:customercategories,id'],
            'customer_email' => 'required|email|unique:users,email' . $id,
            'phone' => ['nullable', 'max:255', 'string', 'unique:users,phone'],
            // 'customer_address' => ['required', 'max:255', 'string'],
        ];
        if ($id) {
            $rules['customer_email'] = 'required|email|unique:users,email,' . $id;
            $rules['phone'] = 'nullable|max:255|string|unique:users,phone,' . $id;
        }

        return $rules;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Customerlead::class);
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
                $customer = Customerlead::create($validated);
    
                return redirect()->route('admin.list.customerlead')->with('success', 'Customer Leads created Successfully');
            }
        }
    
        return redirect()->back()->with('error', 'Something went wrong');
    }
    
    /**
     * Display the specified resource.
     */
    public function show(Customerlead $customerlead)
    {
        $this->data['customer'] = $customerlead;
        $this->data['customercategory'] = Customercategory::where('client_id', $this->fms_id)->get();
        return view('dashboards.admin.customerleads.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customerlead $customerlead)
    {
        $this->authorize('update', $customerlead);
        $this->data['page_title'] = 'Edit Staff';
        $client_id = auth()->user()->client_id;
        $this->data['customerlead'] = $customerlead;
        $this->data['clients'] = Client::pluck('client_name', 'id');
        $this->data['users'] = User::pluck('id');
        $this->data['customercategories'] = Customercategory::where('client_id', $this->fms_id)->pluck('categoryname', 'id');

        return view('dashboards.admin.customerleads.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customerlead $customerlead)
    {
        $this->authorize('update', $customerlead);
        $validated = $this->validate($request, $this->rules($customerlead->user->id));
    
        try {
            DB::beginTransaction();
            $user = $customerlead->user;
            $user->name = $validated['customer_firstname'] . ' ' . $validated['customer_middlename'] . ' ' . $validated['customer_lastname'];
            $user->email = $validated['customer_email'];
            $user->phone = $validated['phone'];
            $user->save();
            $customerlead->update($validated);
    
            DB::commit(); 
        } catch (\Throwable $th) {
            DB::rollback();
            \Log::error($th->getMessage() . ' ' . $th->getTraceAsString());
            return back()->with('error', $th->getMessage());
        }
    
        return redirect()->route('admin.list.customerlead')->with('success', 'Customer Lead updated successfully');
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request, $customerlead)
    {
        $customer = Customerlead::where('id', $customerlead)
            ->where('client_id', $this->fms_id)
            ->first();
        $this->authorize('delete', $customerlead);
        $customerlead->delete();
        return redirect()->route('admin.list.customerlead', $customerlead)->with('success', 'Customer Lead Deleted Successful');
    }
}

