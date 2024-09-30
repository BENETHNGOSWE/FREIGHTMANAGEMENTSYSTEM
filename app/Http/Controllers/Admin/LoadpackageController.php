<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Loadpackage;
use App\Models\Loadpackagetype;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Support\Str;

class LoadpackageController extends Controller
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

    public function index()
    {
        $this->data['loadpackages'] = Loadpackage::where('client_id', $this->fms_id)->get();
        return view('dashboards.admin.loadpackages.index', $this->data);
    }

    public function create(Request $request)
    {
        $this->authorize('create', Loadpackage::class);

        $customer_id = $request->input('customer_id');
        $this->data['loadpackagetypes'] = Loadpackagetype::pluck('load_type_name', 'id');
        $this->data['customer_id'] = $customer_id;

        return view('dashboards.admin.loadpackages.create', $this->data);
    }

    public function validate_loadpackage(Request $request)
    {
        return $request->validate([
            // 'load_no.*' => ['required', 'string', 'max:255'],
            'load_name.*' => ['required', 'string', 'max:255'],
            'load_location.*' => ['required', 'string', 'max:255'],
            'load_description.*' => ['required', 'string', 'max:255'],
            'loadpackagetype_id.*' => ['required', 'integer', 'exists:loadpackagetypes,id'],
            'load_price.*' => ['required', 'string', 'max:255'],
            'load_transportfee.*' => ['required', 'string', 'max:255'],
            'load_transport_date.*' => ['required', 'date'],
            'load_delivery_date.*' => ['required', 'date'],
            'load_status.*' => 'required|in:instock,onmovement,delivery,closed',
            'comment_operator.*' => ['nullable', 'string', 'max:255'],
            'load_weight_kg.*' => ['required', 'string', 'max:255'],
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Loadpackage::class);
        $validated = $this->validate_loadpackage($request);

        try {
            DB::beginTransaction();

            foreach ($validated['load_name'] as $index => $load_name) {
                $load_no = 'LOAD-' . date('Ymd') . '-' . strtoupper(Str::random(5));

                $load = new Loadpackage();
                $load->client_id = $this->fms_id;
                $load->customer_id = $request->input('customer_id');
                $load->load_no = $load_no;
                $load->load_name = $validated['load_name'][$index];
                $load->load_location = $validated['load_location'][$index];
                $load->load_description = $validated['load_description'][$index];
                $load->load_price = $validated['load_price'][$index];
                $load->load_transportfee = $validated['load_transportfee'][$index];
                $load->load_transport_date = $validated['load_transport_date'][$index];
                $load->load_delivery_date = $validated['load_delivery_date'][$index];
                $load->load_status = $validated['load_status'][$index];
                $load->comment_operator = $validated['comment_operator'][$index];
                $load->load_weight_kg = $validated['load_weight_kg'][$index];
                $load->loadpackagetype_id = $validated['loadpackagetype_id'][$index];

                // Log each load package before saving
                \Log::info('Saving Load Package:', $load->toArray());

                $load->save();
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            \Log::error($th->getMessage() . ' ' . $th->getTraceAsString());
            return redirect()->back()->with('error', $th->getMessage());
        }

        return redirect()->route('admin.list.loadpackage')->with('success', 'Load packages added successfully');
    }

    public function show(Request $request, Loadpackage $loadpackage): View
    {
        $this->authorize('view', $loadpackage);
        $this->data['loadpackage'] = $loadpackage;
        return view('dashboards.admin.loadpackages.show', $this->data);
    }

    public function edit(Request $request, Loadpackage $loadpackage): View
    {
        $this->authorize('update', $loadpackage);
        $this->data['loadpackage'] = $loadpackage;
        $this->data['loadpackagetypes'] = Loadpackagetype::pluck('load_type_name', 'id');
        return view('dashboards.admin.loadpackages.edit', $this->data);
    }

    public function update(Request $request, Loadpackage $loadpackage)
    {
        $this->authorize('update', $loadpackage);

        $validate = $request->validate([
            // 'load_no' => ['required', 'string', 'max:255'],
            'load_name' => ['required', 'string', 'max:255'],
            'load_location' => ['required', 'string', 'max:255'],
            'load_description' => ['required', 'string', 'max:255'],
            'loadpackagetype_id' => ['required', 'integer', 'exists:loadpackagetypes,id'],
            'load_price' => ['required', 'string', 'max:255'],
            'load_transportfee' => ['required', 'string', 'max:255'],
            'load_transport_date' => ['required', 'date'],
            'load_delivery_date' => ['required', 'date'],
            'load_status' => ['required', 'string', 'max:255'],
            'comment_operator' => ['nullable', 'string', 'max:255'],
            'load_weight_kg' => ['required', 'string', 'max:255'],
        ]);

        $loadpackage->update($validate);
        return redirect()->route('admin.list.loadpackage')->with('success', 'Load updated successfully');
    }

    public function delete(Request $request, $load)
    {
        $load = loadpackage::where('id', $load)
            ->where('client_id', $this->fms_id)
            ->first();
        $this->authorize('delete', $load);
        $load->delete();
        return redirect()->route('admin.list.loadpackage', $load)->with('success', 'load Deleted Successful');
    }

    public function loadtrackingpage()
    {
        return view('dashboards.admin.loadpackages.track', $this->data);
    }

    public function loadtracking(Request $request)
    {
        $request->validate([
            'load_no' => 'required|string|max:255',
        ]);
        $loadpackage = LoadPackage::where('load_no', $request->load_no)->first();
        if ($loadpackage) {
            $this->data['loadpackage'] = $loadpackage;
            return view('dashboards.admin.loadpackages.track', $this->data);
        } else {
            return redirect()->back()->with('error', 'Load not found. Please check your load number.');
        }
    }
}
