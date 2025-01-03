<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\ClientAdmin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;

class BranchController extends Controller
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
        $this->data['branches'] = Branch::where('client_id', $this->fms_id)->get();
        return view('dashboards.admin.branches.index', $this->data);
    }

    public function create()
    {
        $this->authorize('create', Branch::class);
        return view('dashboards.admin.branches.create', $this->data);
    }

    public function validated_branch(Request $request, Branch $branch = null): array
    {
        return $request->validate([
            'branch_name' => ['required', 'string', 'max:255'],
            'branch_code' => ['required', 'string', 'max:255'],
        ]);
    }

    public function store(Request $request)
    {
        // $this->authorize('create', Branch::class);
        $validated = $this->validated_branch($request);
        try {
            DB::beginTransaction();
            $branch = new Branch();
            $validated['client_id'] = $this->fms_id;
            $branch->fill($validated);
            $branch->save();
            DB::commit();
        } catch (\Throwable $th) {
            \DB::rollback();
            \Log::error($th->getMessage() . '' . $th->getTraceAsString());
            return redirect()->back()->with('error', 'Something went wrong');
        }
        return redirect()->route('admin.list.branch')->with('success', 'Branch Added Successfuly');
    }

    public function show(Request $request, Branch $branch): View
    {
        $this->authorize('view', $branch);
        $this->data['branch'] = $branch;
        return view('dashboards.admin.branches.show', $this->data);
    }

    public function edit(Branch $branch)
    {
        $this->authorize('update', $branch);
        $this->data['branch'] = $branch;
        return view('dashboards.admin.branches.edit', $this->data);
    }

    public function update(Request $request, Branch $branch)
    {
        $this->authorize('update', $branch);
        $validated = $request->validate([
            'branch_name' => ['required', 'string', 'max:255'],
            'branch_code' => ['nullable', 'string', 'max:255'],
        ]);
        $validated['client_id'] = $this->fms_id;
        $branch->update($validated);
        return redirect()->route('admin.list.branch')->with('success', 'Branch Updated Successfuly');
    }

    public function delete(Request $request, $branch)
    {
        $branch = Branch::where('id', $branch)
            ->where('client_id', $this->fms_id)
            ->first();
        $this->authorize('delete', $branch);
        $branch->delete();
        return redirect()->route('admin.list.branch')->with('success', 'Branch Deleted Successfuly ');
    }
}
