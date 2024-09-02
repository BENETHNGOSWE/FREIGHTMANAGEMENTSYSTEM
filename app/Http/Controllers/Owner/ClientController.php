<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClientType;
use App\Models\ClientAdmin;
use App\Models\Client;

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
        $this->data['clienttypes'] = clientType::pluck('name', 'id');
        return view('dashboards.owner.clients.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->data['clients'] = client::all();
        $this->data['clienttypes'] = clientType::all();
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
                // 'address' => ['required', 'string', 'max:255'],
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
                    'user_type' => 'admin',
                    'status' => '1',
                    'photo' => 'user_default.png',
                ];

                $user = new User();
                $user_data = $user->getFillable();
                foreach ($user_data as $key => $value) {
                    $user->$value = $userData[$value];
                }
                $user->save();
                $user->assignRole(Role::findByName('admin'));

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

                    $clientAdmin = new \App\Models\clientAdmin();
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
        $this->data['clients'] = $client;
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
        $this->data['clienttypes'] = clientType::all();
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
            $client ->getFillable();
            foreach($client->getFillable() as $key => $value) {
                if($value != 'client_logo') {
                    $client->$value = $validated[$value]; // Handle missing attributes gracefully
                }
            }
    
            $client->save();
            DB::commit();
    
        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::error($th->getMessage(). ' ' . $th->getTraceAsString().'');
            return redirect()->back()->with('error', $th->getMessage());
        }

        if ($request->hasFile('client_logo')) {
            $old_image = $client->client_logo;
            delete_file($old_image); 
            $image = $request->file('client_logo');
            $filename = \Str::slug($validated['client_name']).'.'.$image->getClientOriginalExtension();
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
    public function destroy(client $client)
    {
        //
    }
}
