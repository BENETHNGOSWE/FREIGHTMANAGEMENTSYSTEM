<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\ClientAdmin;
use App\Models\Kodapackage;
use App\Models\StaffType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PackageController extends Controller
{
    public $data = [];
    public function __construct()
    {
        $this->middleware(['auth', 'owner']);
        // $this->data['clienttypes'] = clientType::pluck('name', 'id');
    }

    public function index()
    {
        $this->data['kodapackages'] = Kodapackage::all();
        $this->data['clientadmins'] = ClientAdmin::all();
        return view('dashboards.owner.packages.index', $this->data);
    }

    public function create()
    {
        $this->data['clientadmins'] = ClientAdmin::pluck('firstname', 'id');
        $this->data['staffTypes'] = StaffType::pluck('staff_type_name', 'id');

        return view('dashboards.owner.packages.create', $this->data);
    }

    public function validate_package(Request $request)
    {
        return $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'monthly_fee' => ['required', 'max:255', 'string'],
            'yearly_fee' => ['required', 'max:255', 'string'],
            'staff_type_id' => ['required', 'max:255', 'exists:staff_types,id'],
            'client_admin_id' => ['required', 'max:255', 'exists:client_admins,id'],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validate_package($request);

        try {
            DB::beginTransaction();

            $package = new Kodapackage();
            $package->fill($validated);
            $package->save();

            $clientAdmin = ClientAdmin::find($validated['client_admin_id']);
            if (!$clientAdmin) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Invalid client admin selected');
            }

            $staffType = StaffType::find($validated['staff_type_id']);
            if (!$staffType) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Invalid staff type selected');
            }

            $roleName = $staffType->staff_type_name;

            $user = $clientAdmin->user;
            if ($user) {
                $user->syncRoles([$roleName]);
            }

            DB::commit();

            return redirect()->route('owner.list.package')->with('success', 'Package created and client admin role updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating package: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function edit(Request $request, Kodapackage $kodapackage)
    {
        $this->data['clientadmins'] = ClientAdmin::pluck('firstname', 'id');
        $this->data['staffTypes'] = StaffType::pluck('staff_type_name', 'id');
        $this->data['kodapackage'] = $kodapackage;
        return view('dashboards.owner.packages.edit', $this->data);
    }

    public function update(Request $request, Kodapackage $kodapackage)
    {
        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'monthly_fee' => ['required', 'max:255', 'string'],
            'yearly_fee' => ['required', 'max:255', 'string'],
            'staff_type_id' => ['required', 'max:255', 'exists:staff_types,id'],
            'client_admin_id' => ['required', 'max:255', 'exists:client_admins,id'],
        ]);

        try {
            DB::beginTransaction();
            $kodapackage->update($validated);

            $clientAdmin = ClientAdmin::find($validated['client_admin_id']);
            if (!$clientAdmin) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Invalid client admin selected');
            }

            $staffType = StaffType::find($validated['staff_type_id']);
            if (!$staffType) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Invalid staff type selected');
            }

            $roleName = $staffType->staff_type_name;

            $user = $clientAdmin->user;
            if ($user) {
                $user->syncRoles([$roleName]);
            }

            DB::commit();

            return redirect()->route('owner.list.package')->with('success', 'Package updated and client admin role updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating package: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
}
