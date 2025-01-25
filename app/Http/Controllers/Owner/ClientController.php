<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClientType;
use App\Models\ClientAdmin;
use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Session;

class ClientController extends Controller
{
    public $data = [];
    public function __construct()
    {
        $this->middleware(['auth', 'owner']);
        $this->data['clienttypes'] = clientType::pluck('name', 'id');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->data['client_list'] = client::all();
        $this->data['clientTypes'] = clientType::pluck('name', 'id');
        return view('dashboards.owner.clients.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->data['clients'] = client::all();
        $this->data['clientTypes'] = clientType::pluck('name', 'id');
        return view('dashboards.owner.clients.create', $this->data);
    }

    public function validate_client(Request $request, client $client = null)
    {
        $rules = [
            'client_name' => ['required', 'max:255', 'string', 'unique:clients,client_name' . ($client ? ',' . $client->id : '')],
            'client_location' => ['required', 'max:255', 'string'],
            'client_phonenumber' => ['required', 'max:255', 'string'],
            'client_city' => ['required', 'max:255', 'string'],
            'client_country' => ['required', 'max:255', 'string'],
            'client_type_id' => ['required', 'exists:client_types,id'],
            'client_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'client_address_one' => ['nullable', 'max:255', 'string'],
            'client_address_two' => ['nullable', 'max:255', 'string'],
            'client_tinnumber' => ['nullable', 'max:255', 'string'],
            'client_email' => ['nullable', 'max:255', 'string'],
            'client_zipcode' => ['nullable', 'max:255', 'string'],
            'client_fax' => ['nullable', 'max:255', 'string'],
            'client_website_link' => ['nullable', 'max:255', 'string'],
        ];

        if (!$client) {
            $rules = array_merge($rules, [
                'firstname' => ['required', 'string', 'max:255'],
                'middlename' => ['nullable', 'string', 'max:255'],
                'lastname' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', 'max:255', 'confirmed'],
                'phone' => ['required', 'string', 'max:15'],
            ]);
        }

        return $request->validate($rules);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validate_client($request);
        try {
            DB::beginTransaction();
            $client = new client();
            $data = $client->getFillable();
            foreach ($data as $key => $value) {
                if ($value != 'client_logo') {
                    $client->$value = $validated[$value];
                }
            }

            $client->save();

            if ($client) {
                $userData = [
                    'name' => $validated['firstname'] . ' ' . $validated['middlename'] . ' ' . $validated['lastname'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'client_id' => $client->id,
                    'phone' => $validated['phone'],
                    'user_type' => 'user',
                    'status' => '1',
                    'photo' => 'user_default.png',
                ];

                $user = new User();
                $user_data = $user->getFillable();
                foreach ($user_data as $key => $value) {
                    $user->$value = $userData[$value];
                }
                $user->save();
                // $user->assignRole(Role::findByName('admin'));

                if ($user) {
                    $adminData = [
                        'firstname' => $validated['firstname'],
                        'middlename' => $validated['middlename'],
                        'lastname' => $validated['lastname'],
                        'username' => $validated['firstname'] . rand(9, 9999),
                        'email' => $user->email,
                        'phone' => $validated['phone'],
                        'password' => $validated['password'],
                        'client_id' => $client->id,
                        'user_id' => $user->id,
                    ];

                    $clientAdmin = new \App\Models\ClientAdmin();
                    $data = $clientAdmin->getFillable();
                    foreach ($data as $key => $value) {
                        $clientAdmin->$value = $adminData[$value];
                    }
                    $clientAdmin->save();
                }
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::error($th->getMessage() . '' . $th->getTraceAsString() . '');

            return redirect()->back()->with('error', 'Something went wrong');
        }

        // Handle the client logo upload
        if ($request->hasFile('client_logo')) {
            $image = $request->file('client_logo');
            $filename = \Str::slug($validated['client_name']) . '.' . $image->extension();
            upload_image($image, $filename, 200);
            $validated['client_logo'] = $filename;
        } else {
            $validated['client_logo'] = 'client_default.png';
        }
        $client->update(['client_logo' => $validated['client_logo']]);

        return to_route('list.clients')->with('success', 'client Added Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, client $client)
    {
        $this->data['client'] = $client;
        $this->data['client_list'] = client::all();
        $this->data['clienttypes'] = clientType::pluck('name', 'id');
        $this->data['admin'] = clientAdmin::where('client_id', $client->id)->first();
        return view('dashboards.owner.clients.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(client $client)
    {
        $this->data['client'] = $client;
        $this->data['client_list'] = client::all();
        $this->data['clientTypes'] = clientType::pluck('name', 'id');
        return view('dashboards.owner.clients.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, client $client)
    {
        $validated = $this->validate_client($request, $client);

        try {
            DB::beginTransaction();
            $client->getFillable();
            foreach ($client->getFillable() as $key => $value) {
                if ($value != 'client_logo') {
                    $client->$value = $validated[$value]; // Handle missing attributes gracefully
                }
            }

            $client->save();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::error($th->getMessage() . ' ' . $th->getTraceAsString() . '');
            return redirect()->back()->with('error', $th->getMessage());
        }

        if ($request->hasFile('client_logo')) {
            $old_image = $client->client_logo;
            delete_file($old_image);
            $image = $request->file('client_logo');
            $filename = \Str::slug($validated['client_name']) . '.' . $image->getClientOriginalExtension();
            upload_image($image, $filename, 200);
            $validated['client_logo'] = $filename;
            $client->update(['client_logo' => $validated['client_logo']]);
            $client->save();
        }

        return redirect()->route('list.clients')->with('success', 'client updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(client $client)
    {
        try {
            // Begin a database transaction
            DB::beginTransaction();

            // Optionally: Delete associated admin/user records
            $clientAdmin = \App\Models\ClientAdmin::where('client_id', $client->id)->first();
            if ($clientAdmin) {
                $user = User::find($clientAdmin->user_id);
                if ($user) {
                    $user->delete(); // Delete associated user
                }
                $clientAdmin->delete(); // Delete the admin record
            }

            // Handle client logo deletion if it exists
            if ($client->client_logo && $client->client_logo !== 'client_default.png') {
                delete_file($client->client_logo); // Assuming you have a helper for deleting files
            }

            // Delete the client record
            $client->delete();

            // Commit the transaction
            DB::commit();

            return redirect()->route('list.clients')->with('success', 'Client deleted successfully');
        } catch (\Throwable $th) {
            // Roll back the transaction if something goes wrong
            DB::rollBack();
            \Log::error($th->getMessage() . ' ' . $th->getTraceAsString());

            return redirect()->back()->with('error', 'Something went wrong while deleting the client');
        }
    }

    public function assignPackage(Client $client, $stafftype_id)
    {
        try {
            DB::beginTransaction();

            // Find the package
            $package = Stafftype::findOrFail($stafftype_id);

            // Get the roles associated with this package
            $packageRoles = $package->roles;

            // Find the primary admin user for this client
            $adminUser = User::where('client_id', $client->id)
                ->where('user_type', 'user')
                ->first();

            if ($adminUser) {
                // Remove any existing roles
                $adminUser->roles()->detach();

                // Assign new roles from the package
                foreach ($packageRoles as $role) {
                    $adminUser->assignRole($role);
                }

                // Optionally update user type to admin if the package warrants it
                $adminUser->user_type = 'admin';
                $adminUser->save();

                // Optionally attach package to client
                $client->package_id = $stafftype_id;
                $client->save();
            }

            DB::commit();

            return redirect()->back()->with('success', 'Package assigned successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::error($th->getMessage() . '' . $th->getTraceAsString() . '');

            return redirect()->back()->with('error', 'Failed to assign package');
        }
    }

    public function impersonate(Client $client)
    {
        // Store the current admin ID in the session
        Session::put('admin_id', Auth::id());

        // Get the client admin
        $clientAdmin = $client->admin;

        // Check if the client admin exists
        if ($clientAdmin) {
            $adminUser = User::find($clientAdmin->user_id);
            if ($adminUser) {
                // Login as the client admin
                Auth::login($adminUser);

                return redirect()
                    ->route('dashboard')
                    ->with('success', 'You are now logged in as ' . $client->client_name);
            } else {
                return redirect()->route('client.list')->with('error', 'Client admin user not found.');
            }
        } else {
            return redirect()->route('client.list')->with('error', 'Client admin not found.');
        }
    }
}
