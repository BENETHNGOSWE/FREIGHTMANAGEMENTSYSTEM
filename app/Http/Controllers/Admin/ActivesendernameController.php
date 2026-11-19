<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Clientactivename;
use App\Models\ClientAdmin;
use App\Models\Sendername;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ActivesendernameController extends Controller
{
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

    public function index()
    {
        // Load Clientactivename for the client, including the associated sendernames and client details
        $this->data['clientActivenames'] = Clientactivename::where('client_id', $this->fms_id)
            ->with('client.sendernames') // Eager load the client and sendernames
            ->get();

        // The SMS switch is stored per client, so it looks the same to every user and after
        // every logout.
        $this->data['smsNotificationsEnabled'] = \App\Models\SystemSetting::notificationsEnabled($this->fms_id);

        return view('dashboards.admin.activenames.index', $this->data);
    }

    public function create()
    {
        // $this->authorize('create', Branch::class);
        $clients = Client::with('sendernames')->get();

        // Pass the clients with sendernames to the view
        $this->data['clients'] = $clients;
        $this->data['senderNames'] = Sendername::all();
        return view('dashboards.admin.activenames.create', $this->data);
    }

    public function validated_activename(Request $request, Clientactivename $clientactivename = null): array
    {
        return $request->validate([
            'sendername_id' => 'required|exists:sendernames,id',
        ]);
    }

    public function store(Request $request)
    {
        // $this->authorize('create', Branch::class);
        $validated = $this->validated_activename($request);
        try {
            DB::beginTransaction();
            $activename = new Clientactivename();
            $validated['client_id'] = $this->fms_id;
            $activename->fill($validated);
            $activename->save();
            DB::commit();
        } catch (\Throwable $th) {
            \DB::rollback();
            \Log::error($th->getMessage() . '' . $th->getTraceAsString());
            return redirect()->back()->with('error', 'Something went wrong');
        }
        return redirect()->route('admin.list.branch')->with('success', 'Branch Added Successfuly');
    }

    public function edit(Clientactivename $activename)
    {
        $this->data['activename'] = $activename; 
        $this->data['senderNames'] = Sendername::all();

        return view('dashboards.admin.activenames.edit', $this->data);
    }

    public function update(Request $request, Clientactivename $activename)
    {
        
        $validated = $request->validate([
           'sendername_id' => ['required', 'exists:sendernames,id'],
        ]);
        $validated['client_id'] = $this->fms_id;
        $activename->update($validated);
        return redirect()->route('admin.list.activename')->with('success', 'Branch Updated Successfuly');
    }

    
}
