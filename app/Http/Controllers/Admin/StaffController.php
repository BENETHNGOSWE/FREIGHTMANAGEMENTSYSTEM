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

        if (auth()->user()->hasRole('super_admin')) {
            $staffTypes = StaffType::pluck('staff_type_name', 'id');
        } else {
            $staffTypes = StaffType::whereNotIn('staff_type_name', ['kodasmspackage', 'admin'])->pluck('staff_type_name', 'id');
        }

        $branches = Branch::where('client_id', $this->fms_id)->pluck('branch_name', 'id');
        $users = User::pluck('id');

        $this->data['clients'] = $clients;
        $this->data['staffTypes'] = $staffTypes;
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
            'staff_type_id' => ['required', 'max:255', 'exists:staff_types,id'],
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
            $staff_type = StaffType::find($validated['staff_type_id']);

            if (!$staff_type) {
                return redirect()->back()->with('error', 'Invalid staff type selected');
            }

            $staff_type_name = $staff_type->staff_type_name;
            $name = $validated['staff_firstname'] . ' ' . $validated['staff_middlename'] . ' ' . $validated['staff_lastname'];
            $password = Hash::make('123456');

            $user = User::create([
                'name' => $name,
                'email' => $validated['staff_email'],
                'phone' => $validated['phone'],
                'password' => $password,
                'user_type' => $staff_type_name,
                'client_id' => $this->fms_id,
            ]);

            $user->assignRole(\Spatie\Permission\Models\Role::findByName($staff_type_name));

            $id_number = createStaffidNumber($this->fms_id);
            $validated['user_id'] = $user->id;
            $validated['staff_no'] = $id_number;
            $validated['client_id'] = $user->client_id;

            $employee = Staff::create($validated);

            DB::commit();

            return redirect()->route('admin.list.staff')->with('success', 'Employee added successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating employee: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function show(Request $request, Staff $staff)
    {
        $this->data['staff'] = $staff;
        return view('dashboards.admin.staffs.show', $this->data);
    }

    public function edit($id)
    {
        $staff = Staff::findOrFail($id); // Fetch the staff member by ID
        $this->data['page_title'] = 'Edit Staff';
        $client_id = auth()->user()->client_id;
        $this->data['staff'] = $staff;
        $this->data['editing'] = true;

        $this->data['clients'] = Client::pluck('client_name', 'id');
        // $this->data['staffTypes'] = StaffType::pluck('staff_type_name', 'id');
        $this->data['branches'] = Branch::pluck('branch_name', 'id');
        $this->data['users'] = User::pluck('id');

        if (auth()->user()->hasRole('super_admin')) {
            $this->data['staffTypes'] = StaffType::pluck('staff_type_name', 'id');
        } else {
            $this->data['staffTypes'] = StaffType::whereNotIn('staff_type_name', ['kodasmspackage', 'admin'])->pluck('staff_type_name', 'id');
        }

        return view('dashboards.admin.staffs.edit', $this->data);
    }

    public function update(Request $request, $id)
    {
        $this->data['staff'] = $staff = Staff::find($id);
        $this->data['page_title'] = 'Edit Staff';
        if ($staff) {
            $validated = $this->validate($request, $this->rules($staff->user_id));
            $staff_type = StaffType::find($validated['staff_type_id']);
            if ($staff_type) {
                $staff_type_name = $staff_type->staff_type_name;
                $name = $validated['staff_firstname'] . ' ' . $validated['staff_middlename'] . ' ' . $validated['staff_lastname'];

                // Initialize updateData array
                $updateData = [
                    'name' => $name,
                    'email' => $validated['staff_email'],
                    'phone' => $validated['phone'],
                    'user_type' => $staff_type_name,
                    'client_id' => $this->fms_id,
                ];

                // Add password to updateData if provided
                if (!empty($validated['password'])) {
                    $updateData['password'] = Hash::make($validated['password']);
                }

                DB::beginTransaction();
                try {
                    $staff->user->update($updateData);
                    $user = $staff->user->syncRoles(\Spatie\Permission\Models\Role::findByName($staff_type_name));

                    if ($user) {
                        $validated['user_id'] = $user->id;
                        $employee = $staff->update($validated);
                        if ($employee) {
                            DB::commit();
                            return redirect()
                                ->route('admin.list.staff')
                                ->with('success', $user->name . ' Updated successfully');
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

            $this->authorize('update', $staff);
            $this->data['staffTypes'] = StaffType::pluck('staff_type_name', 'id');
            return view('dashboards.hr.employees.edit', $this->data);
        } else {
            return redirect()->back()->with('error', 'Something went wrong');
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
