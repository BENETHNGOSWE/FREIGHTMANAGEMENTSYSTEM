<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoadpackageController extends Controller
{
    //
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

    public function list_staffs()
    {

        $clientId = auth()->user()->client_id;
        $this->data['page_title'] = 'List Staff';
        $employees = Staff::with('user')
            ->where('client_id', $clientId) // Filter by hospital ID
            ->get();
        $this->data['employees'] = $employees;
        return view('dashboards.admin.staffs.list_staffs', $this->data);
    }

    public function create(Request $request)
    {

        $this->data['page_title'] = 'Create Loadpackagetype';
        return view('dashboards.admin.loadpackagetypes.create', $this->data);
    }

    public function validated_loadpackage(Request $request){
        return $request->validate([
            'load_type_name'=> ['required','string','max:255'],
            'load_type_description'=> ['nullable','string', 'max:255'],
        ]);
    }

    public function store(Request $request)
    {
        $validate = $this->validated_loadpackage($request);

        try {
            DB::beginTransaction();
            $loadpackagetype = new Loadpackagetype();
            $loadpackagetype ->fill($validate);
            $loadpackagetype ->save();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::error($th->getMessage().''.$th->getTraceAsString());
            return back()->with('eror', $th->getMessage());
            // return redirect()->back()->with('error', 'Something went wrong');
        }
        return redirect()->route('list.loadpackagetypes')->with('success', 'Ward type Added Successfuly');
    }

    public function edit($employee)
    {

        $this->data['page_title'] = 'Edit Staff';
        $client_id = auth()->user()->client_id;
        $staff = Staff::find($employee);
        $this->data['staff'] = $staff;
        
        $this->data['departments'] = Department::where('client_id', $client_id)->pluck('department_name')->all();;
        //$this->data['departments']= Department::pluck('department_name', 'id');
        $this->data['hospitals'] = Hospital::pluck('hospital_name', 'id');
        $this->data['staffTypes'] = StaffType::pluck('staff_type_name', 'id');
        $this->data['users'] = User::pluck('id');

        return view('dashboards.admin.staffs.edit', $this->data);
    }

    public function update(Request $request,$id){
        $this->data['staff'] =$staff = Staff::find($id);
        $this->data['page_title'] = 'Edit Employee';
        if($staff){
            
                $validated = $this->validate($request, $this->rules($staff->user_id));
                $staff_type = StaffType::find($validated['staff_type_id']);
            if ($staff_type) {
                $staff_type_name = $staff_type->staff_type_name;
                $name = $validated['staff_firstname'].' '.$validated['staff_middlename'].' '.$validated['staff_lastname'];
                DB::beginTransaction();
                try {
                    $staff->user->update([
                        'name' => $name,
                        'email' => $validated['staff_email'],
                        'phone' => $validated['phone'],
                        'user_type' => $staff_type_name,
                        'client_id' => $this->fms_id,
                    ]);
                    $user = $staff->user->syncRoles(\Spatie\Permission\Models\Role::findByName($staff_type_name));

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
            
        $this->authorize('update', $staff);
        $this->data['departments'] = Department::pluck('department_name', 'id');
        $this->data['staffTypes'] = StaffType::pluck('staff_type_name', 'id');
        return view('dashboards.hr.employees.edit', $this->data);
        }else{
            return redirect()->back()->with('error', 'Something went wrong');
        }
        
    }

    public function delete($id)
    {

        try {
            // Start a database transaction
            DB::beginTransaction();
            $asset = Staff::find($id);

            if (!$asset) {
                // If the category doesn't exist, you can handle the error here
                return redirect()
                    ->route('admin.list.staffs')
                    ->withError(__('crud.common.not_found'));
            }

            $asset->delete();

            // Commit the transaction if everything is successful
            DB::commit();

            return redirect()
                ->route('admin.list.staffs')
                ->withSuccess(__('crud.common.removed'));
        } catch (\Exception $e) {
            // If an error occurs, roll back the transaction
            DB::rollBack();

            // Handle the error or return a response accordingly
            return redirect()
                ->route('admin.list.staffs')
                ->withError(__('crud.common.error'));
        }
    }
}
