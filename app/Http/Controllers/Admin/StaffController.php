<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Client;
use App\Models\StaffType;
use App\Models\Staff;
use App\Models\User;
use App\Models\Branch;
use App\Models\ClientAdmin;
use Spatie\Permission\Models\Role;

class StaffController extends Controller
{
    //
    protected $data = [];

    protected $fms_id = null;

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

    public function list_staffs()
    {
        $this->data['page_title'] = 'List Staff';
        $employees = Staff::with('user')->where('client_id', $this->fms_id)->get();
        $this->data['employees'] = $employees;
        return view('dashboards.admin.staffs.list_staffs', $this->data);
    }

    public function create(Request $request)
    {
        $this->data['page_title'] = 'Create Staff';
        $clients = Client::pluck('client_name', 'id');

        $clientID = auth()->user()->client_id;

        $roles = Role::where('client_id', $clientID)
            ->orWhereNull('client_id')
            ->pluck('name', 'id');

        $branches = Branch::where('client_id', $this->fms_id)->pluck('branch_name', 'id');
        $users = User::pluck('id');

        $this->data['clients'] = $clients;
        $this->data['roles'] = $roles;
        $this->data['branches'] = $branches;
        $this->data['users'] = $users;

        return view('dashboards.admin.staffs.create', $this->data);  
    }

    private function rules($id = null)
    {
        $rules = [
            'staff_firstname' => ['required', 'max:255', 'string'],
            'staff_middlename' => ['nullable', 'max:255', 'string'],
            'staff_lastname' => ['required', 'max:255', 'string'],
            'staff_gender' => ['required', 'max:255', 'string'],
            'staff_email' => 'required|email|unique:users,email',
            'phone' => ['required', 'max:255', 'string', 'unique:users,phone' . ($id ? ',' . $id : '')],
            'role_id' => ['required', 'max:255', 'exists:roles,id'],
            'branch_id' => ['required', 'max:255', 'exists:branches,id'],
            'staff_address' => ['required', 'max:255', 'string'],
            'password' => 'nullable|string|min:8|confirmed',
        ];
        if ($id) {
            $rules['staff_email'] = 'required|email|unique:users,email,' . $id;
            $rules['phone'] = 'nullable|max:255|string|unique:users,phone,' . $id;
        }

        return $rules;
    }


    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            $validated = $this->validate($request, $this->rules());

            // Validate role against client_id scope
            $role = Role::where('id', $request->role_id)
                ->where(function ($query) {
                    $query->where('client_id', $this->fms_id)
                        ->orWhereNull('client_id');
                })
                ->first();

            if (!$role) {
                return redirect()->back()->with('error', 'Invalid role selected');
            }

            $name = trim($validated['staff_firstname'].' '.$validated['staff_middlename'].' '.$validated['staff_lastname']);
            $password = Hash::make('123456');

            $user = User::create([
                'name'      => $name,
                'email'     => $validated['staff_email'],
                'phone'     => $validated['phone'],
                'password'  => $password,
                'client_id' => $this->fms_id,
            ]);

            // Assign role directly
            $user->syncRoles($role->name);

            // Generate staff number & save staff record
            $id_number = createStaffidNumber($this->fms_id);
            $validated['user_id']   = $user->id;
            $validated['staff_no']  = $id_number;
            $validated['client_id'] = $user->client_id;
            unset($validated['role_id']);

            Staff::create($validated);

            DB::commit();

            return redirect()->route('admin.list.staff')->with('success', 'Employee added successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating employee: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

  
    

    public function show(Request $request, Staff $staff)
    {
        $this->data['staff'] = $staff;
        return view('dashboards.admin.staffs.show', $this->data);
    }

    public function edit($id)
    {
        $staff = Staff::findOrFail($id);
        $this->data['page_title'] = 'Edit Staff';
        $client_id = auth()->user()->client_id;

        $this->data['staff'] = $staff;
        $this->data['editing'] = true;
        $this->data['clients'] = Client::pluck('client_name', 'id');
        $this->data['branches'] = Branch::where('client_id', $this->fms_id)->pluck('branch_name', 'id');
        $this->data['users'] = User::pluck('id');

        // ✅ Load roles (client-specific or global)
        $this->data['roles'] = Role::where('client_id', $client_id)
            ->orWhereNull('client_id')
            ->pluck('name', 'id');

        return view('dashboards.admin.staffs.edit', $this->data);
    }


   public function update(Request $request, $id)
{
    $staff = Staff::findOrFail($id);

    $validated = $this->validate($request, $this->rules($staff->user_id));

    // ✅ Ensure selected role belongs to the same client or is global
    $role = Role::where('id', $request->role_id)
        ->where(function ($query) {
            $query->where('client_id', $this->fms_id)
                  ->orWhereNull('client_id');
        })
        ->first();

    if (!$role) {
        return redirect()->back()->with('error', 'Invalid role selected');
    }

    $name = trim($validated['staff_firstname'] . ' ' . $validated['staff_middlename'] . ' ' . $validated['staff_lastname']);

    // Prepare update data for User model
    $updateData = [
        'name' => $name,
        'email' => $validated['staff_email'],
        'phone' => $validated['phone'],
        'client_id' => $this->fms_id,
    ];

    if (!empty($validated['password'])) {
        $updateData['password'] = Hash::make($validated['password']);
    }

    DB::beginTransaction();
    try {
        // ✅ Update the user record
        $staff->user->update($updateData);

        // ✅ Sync the new role
        $staff->user->syncRoles([$role->name]);

        // ✅ Update the staff table (excluding role_id)
        unset($validated['role_id']);
        $staff->update($validated);

        DB::commit();

        return redirect()
            ->route('admin.list.staff')
            ->with('success', $staff->user->name . ' updated successfully');
    } catch (\Throwable $th) {
        DB::rollBack();
        \Log::error('Error updating staff: ' . $th->getMessage());
        return redirect()->back()->with('error', 'Something went wrong: ' . $th->getMessage());
    }
}


    public function delete($id)
    {
        try {
            DB::beginTransaction();
            $asset = Staff::find($id);

            if (!$asset) {
                return redirect()->route('admin.list.staff')->with('success', 'Staff Deleted Successful');
            }

            $asset->delete();
            DB::commit();

            return redirect()->route('admin.list.staff')->with('success', 'Staff Deleted Successful');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.list.staff')->withError(__('crud.common.error'));
        }
    }

    public function viewProfile()
    {
        $this->data['user'] = auth()->user();
        $this->data['staff'] = Staff::where('user_id', $this->data['user']->id)->first();
        return view('dashboards.admin.staffs.editprofile', $this->data);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $staff = Staff::where('user_id', $user->id)->first();

        $validated = $request->validate([
            'staff_firstname' => 'required|string|max:255',
            'staff_middlename' => 'nullable|string|max:255',
            'staff_lastname' => 'required|string|max:255',
            'staff_email' => 'required|email|unique:users,email,' . $user->id,
            'staff_gender' => 'required|in:male,female,others',
            'phone' => 'required|string|max:15',
            'staff_address' => 'nullable|string|max:255',
            'password' => 'nullable|min:6|confirmed',
        ]);

        DB::beginTransaction();
        try {
            $userData = [
                'name' => $validated['staff_firstname'] . ' ' . $validated['staff_middlename'] . ' ' . $validated['staff_lastname'],
                'email' => $validated['staff_email'],
                'phone' => $validated['phone'],
            ];

            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            $user->update($userData);

            $staff->update([
                'staff_firstname' => $validated['staff_firstname'],
                'staff_middlename' => $validated['staff_middlename'],
                'staff_lastname' => $validated['staff_lastname'],
                'staff_email' => $validated['staff_email'],
                'staff_gender' => $validated['staff_gender'],
                'staff_address' => $validated['staff_address'],
                'phone' => $validated['phone'],
            ]);

            DB::commit();
            return redirect()->route('dashboard')->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e);
            return redirect()
                ->back()
                ->with('error', 'Failed to update profile: ' . $e->getMessage());
        }
    }
}







  