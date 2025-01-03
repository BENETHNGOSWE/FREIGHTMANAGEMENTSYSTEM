<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UnitType;
use App\Models\ClientAdmin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;

class UnittypeController extends Controller
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
        $this->data['unittypes'] = UnitType::where('client_id', $this->fms_id)->get();
        return view('dashboards.admin.unittypes.index', $this->data);
    }

    public function create()
    {
        $this->authorize('create', unittype::class);
        return view('dashboards.admin.unittypes.create', $this->data);
    }

    public function validated_unittype(Request $request, UnitType $unittype = null): array
    {
        return $request->validate([
            'unittype_name' => ['required', 'string', 'max:255'],
            'unitytype_initial' => ['required', 'string', 'max:255'],
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', unittype::class);
        $validated = $this->validated_unittype($request);
        try {
            DB::beginTransaction();
            $unittype = new UnitType();
            $validated['client_id'] = $this->fms_id;
            $unittype->fill($validated);
            $unittype->save();
            DB::commit();
        } catch (\Throwable $th) {
            \DB::rollback();
            \Log::error($th->getMessage() . '' . $th->getTraceAsString());
            return redirect()->back()->with('error', 'Something went wrong');
        }
        return redirect()->route('admin.list.unittype')->with('success', 'Unit Type Added Successfuly');
    }

    public function show(Request $request, UnitType $unittype): View
    {
        $this->authorize('view', $unittype);
        $this->data['unittype'] = $unittype;
        return view('dashboards.admin.unittypes.show', $this->data);
    }

    public function edit(UnitType $unittype)
    {
        $this->authorize('update', $unittype);
        $this->data['unittype'] = $unittype;
        return view('dashboards.admin.unittypes.edit', $this->data);
    }

    public function update(Request $request, UnitType $unittype)
    {
        $this->authorize('update', $unittype);
        $validated = $request->validate([
            'unittype_name' => ['required', 'string', 'max:255'],
            'unitytype_initial' => ['required', 'string', 'max:255'],
        ]);
        $validated['client_id'] = $this->fms_id;
        $unittype->update($validated);
        return redirect()->route('admin.list.unittype')->with('success', 'Unit Type Updated Successfuly');
    }

    public function delete(Request $request, $unittype)
    {
        $unittype = UnitType::where('id', $unittype)
            ->where('client_id', $this->fms_id)
            ->first();
        $this->authorize('delete', $unittype);
        $unittype->delete();
        return redirect()->route('admin.list.unittype')->with('success', 'Unit Type Deleted Successfuly ');
    }
}
