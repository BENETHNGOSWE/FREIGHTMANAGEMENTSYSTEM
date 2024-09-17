<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use App\Models\Client;
use App\Models\Loadpackagetype;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;

class CustomerController extends Controller
{
    protected $fms_id = null;
    protected $data = [];
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
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->data['customers'] = Customer::where('client_id', $this->fms_id)->latest()->get();
        // $this->data['loadpackagetypes'] = Loadpackagetype::where('client_id', $this->fms_id)->get();
        return view('dashboards.admin.customers.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
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
            'customer_lastname' => ['required', 'max:255', 'string'],
            'customer_gender' => ['required', 'in:male,female'],
            'customer_email' => 'required|email|unique:users,email',
            'phone' => ['nullable', 'max:255', 'string', 'unique:users,phone'],
            'customer_address' => ['required', 'max:255', 'string'],
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
        $validated = $this->validate($request, $this->rules());
        // $customer_type = customertype::find($validated['customer_type_id']);

        if ($validated) {

            // $customer_type_name = $customer_type->customer_type_name;
            $name = $validated['customer_firstname'] . ' ' . $validated['customer_middlename'] . ' ' . $validated['customer_lastname'];
            $password = Hash::make('123456');

            $user = User::create([
                'name' => $name,
                'email' => $validated['customer_email'],
                'phone' => $validated['phone'],
                'password' => $password,
                // 'user_type' => $customer_type_name,
                'client_id' => $this->fms_id,
            ]);



            // $user->assignRole(\Spatie\Permission\Models\Role::findByName($customer_type_name));

            if ($user) {
                $id_number = createcustomeridNumber($this->fms_id);
                $validated['user_id'] = $user->id;
                $validated['customer_no'] = $id_number;
                $validated['client_id'] = $user->client_id;



                $employee = customer::create($validated);
                if ($employee) {
                    // $message = 'Dear ' . $user->name . ', your account has been created successfully. Your have been Registered as '.strtoupper($user->user_type).'. Your login details are as follows: Email: ' . $user->email . ' Password: 123456 , Please change your password after login. Thank you.';
                    // sendSMS($user->id,$message,$this->h_id);
                    return redirect()->route('admin.list.customer')->with('success', 'Customer added successfully');
                } else {
                    return redirect()->back()->with('error', 'Something went wrong');
                }
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        
        $this->data['page_title'] = 'Edit Staff';
        $client_id = auth()->user()->client_id;
        $customer = Customer::find($employee);
        $this->data['customer'] = $customer;
        
        // $this->data['departments'] = Department::where('client_id', $client_id)->pluck('department_name')->all();;
        //$this->data['departments']= Department::pluck('department_name', 'id');
        $this->data['clients'] = Client::pluck('client_name', 'id');
        $this->data['users'] = User::pluck('id');

        return view('dashboards.admin.staffs.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $this->data['customer'] =$customer = Customer::find($id);
        $this->data['page_title'] = 'Edit Employee';
        if($customer){
            
                $validated = $this->validate($request, $this->rules($customer->user_id));
                // $staff_type = StaffType::find($validated['staff_type_id']);
            if ($validated) {
                // $staff_type_name = $staff_type->staff_type_name;
                $name = $validated['customer_firstname'].' '.$validated['customer_middlename'].' '.$validated['customer_lastname'];
                DB::beginTransaction();
                try {
                    $staff->user->update([
                        'name' => $name,
                        'email' => $validated['customer_email'],
                        'phone' => $validated['phone'],
                        // 'user_type' => $staff_type_name,
                        'client_id' => $this->fms_id,
                    ]);
                    // $user = $cust->user->syncRoles(\Spatie\Permission\Models\Role::findByName($staff_type_name));

                    if ($user) {
                        $validated['user_id'] = $user->id;
                        $employee = $staff->update($validated);
                        if ($employee) {
                            DB::commit();
                            return redirect()->route('admin.list.staffs')->with('success', $user->name.' Updated successfully');
                        } else {
                            DB::rollBack();

                            return redirect()->back()->with('error', 'Something went wrong');
                        }
                    }
                } catch (\Throwable $th) {
                    DB::rollBack();
                    \Log::error($th);

                    return redirect()->back()->with('error', $th->getMessage());
                }

            }
            
        $this->authorize('update', $customer);
        return view('dashboards.hr.employees.edit', $this->data);
        }else{
            return redirect()->back()->with('error', 'Something went wrong');
        }
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(customer $customer)
    {
        //
    }
}
