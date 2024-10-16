<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Loadpackagetype;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Exception;

class LoadpackagetypeController extends Controller
{
    protected $data = [];

    protected $fms_id = null;

    public function __construct()
    {
        $this->middleware('multiple');
        $this->middleware(function ($request, $next) {
            if (Auth::check()) {
                $this->fms_id = Auth::user()->client_id;
            }

            return $next($request);
        });
    }

    public function index()
    {
        $this->data['loadpackagetypes'] = Loadpackagetype::where('client_id', $this->fms_id)->get();
        return view('dashboards.admin.loadpackagetypes.index', $this->data);
    }

    public function create()
    {
        $this->authorize('create', Loadpackagetype::class);
        return view('dashboards.admin.loadpackagetypes.create', $this->data);
    }

    public function validated_loadpackage(Request $request, Loadpackagetype $load_type = null): array
    {
        return $request->validate([
            'load_type_name' => ['required', 'string', 'max:255'],
            'load_type_description' => ['nullable', 'string', 'max:255'],
        ]);
    }

    public function store(Request $request)
    {
        // $this->authorize('create', Loadpackagetype::class);
        $validated = $this->validated_loadpackage($request);
        try {
            DB::beginTransaction();
            $load_type = new Loadpackagetype();
            $validated['client_id'] = $this->fms_id;
            $load_type->fill($validated);
            $load_type->save();
            DB::commit();
        } catch (\Throwable $th) {
            \DB::rollback();
            \Log::error($th->getMessage() . '' . $th->getTraceAsString());
            return redirect()->back()->with('error', 'Something went wrong');
        }
        return redirect()->route('admin.list.loadpackagetpye')->with('success', 'Load type Added Successfuly');
    }

    public function show(Request $request, Loadpackagetype $loadpackagetype): View
    {
        $this->authorize('view', $loadpackagetype);
        $this->data['loadpackagetype'] = $loadpackagetype;
        return view('dashboards.admin.loadpackagetypes.show', $this->data);
    }

    public function edit(Loadpackagetype $loadpackagetype)
    {
        $this->authorize('update', $loadpackagetype);
        $this->data['loadpackagetype'] = $loadpackagetype;
        return view('dashboards.admin.loadpackagetypes.edit', $this->data);
    }

    public function update(Request $request, Loadpackagetype $loadpackagetype)
    {
        $this->authorize('update', $loadpackagetype);
        $validated = $request->validate([
            'load_type_name' => ['required', 'string', 'max:255'],
            'load_type_description' => ['nullable', 'string', 'max:255'],
        ]);
        $validated['client_id'] = $this->fms_id;
        $loadpackagetype->update($validated);
        return redirect()->route('admin.list.loadpackagetpye')->with('success', 'Load Type Updated Successfuly');
    }

    public function delete(Request $request, $Loadpackagetype)
    {
        $Loadpackagetype = Loadpackagetype::where('id', $Loadpackagetype)
            ->where('client_id', $this->fms_id)
            ->first();
        $this->authorize('delete', $Loadpackagetype);
        $Loadpackagetype->delete();
        return redirect()->route('admin.list.loadpackagetpye')->with('success', 'Load Type Successfuly deleted');
    }
}
