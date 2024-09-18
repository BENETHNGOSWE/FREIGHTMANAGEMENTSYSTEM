<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Loadpackage;
use App\Models\Loadpackagetype;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            'load_no' => ['required', 'string', 'max:255'],
            'load_name' => ['required', 'string', 'max:255'],
            'load_location' => ['required', 'string', 'max:255'],
            'load_description' => ['required', 'string', 'max:255'],
            'loadpackagetype_id' => ['required', 'string', 'max:255'],
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Loadpackage::class);
        $validate = $this->validate_loadpackage($request);
    
        try {
            DB::beginTransaction();
            $load = new Loadpackage();
            $validate['client_id'] = $this->fms_id;
            $validate['customer_id'] = $request->input('customer_id');  
            $load->fill($validate);
            $load->save();
            
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            \Log::error($th->getMessage() . '' . $th->getTraceAsString());
            return redirect()->back()->with('error', $th->getMessage());
        }
    
        return redirect()->route('admin.list.loadpackage')->with('success', 'Load package added successfully');
    }
    

    public function show(Request $request, loadpackage $loadpackage ): View
    {
        $this->authorize('view', $loadpackage);
        $this->data['loadpackage'] = $loadpackage;
        return view('dashboards.admin.loadpackages.show', $this->data);
    }
    public function edit(Request $request, loadpackage $loadpackage): View
    {
        $this->authorize('update', $loadpackage);
        $this->data['loadpackage'] = $loadpackage;
        $this->data['loadtypes'] = loadpackagetype::all();
        return view('dashboards.admin.loadpackages.edit', $this->data);
    }

    public function update(Request $request, loadpackage $loadpackage)
    {
        $this->authorize('update', $loadpackage);
        $validate = $this->validated_load($request);
        $validated = $request->validate([
            'load_no' => 'required',
            'load_name' => 'required',
            'load_location' => 'required',
            'load_description' => 'required',
            'load_type_id' => 'required|array',
        ]);

        $load = new loadloadpackage([
            'load_no' => $validated['load_no'],
            'load_name' => $validated['load_name'],
            'load_location' => $validated['load_location'],
            'load_description' => $validated['load_description'],
        ]);

        $load['client_id'] = $this->fms_id;

        $load->save();

        if ($load->loadtypes()->sync($request->input('load_type_id', []))) {
            return redirect()->back()->with('success', 'load Updated Successfuly');
        } else {
            dd('motto unawaka');
        }
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
}
