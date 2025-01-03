<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customercategory;
use App\Models\ClientAdmin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Exception;

class CustomercategoryController extends Controller
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
        $this->data['customercategories'] = Customercategory::where('client_id', $this->fms_id)->get();

        return view('dashboards.admin.customercategories.index', $this->data);
    }

    public function create()
    {
        $this->authorize('create', Customercategory::class);
        return view('dashboards.admin.customercategories.create', $this->data);
    }

    public function validated_customercategory(Request $request, Customercategory $customer_category = null): array
    {
        return $request->validate([
            'categoryname' => ['required', 'string', 'max:255'],
            'categorydescription' => ['nullable', 'string', 'max:255'],
        ]);
    }

    public function store(Request $request)
    {
        // $this->authorize('create', Customercategory::class);
        $validated = $this->validated_customercategory($request);
        try {
            DB::beginTransaction();
            $customer_category = new Customercategory();
            $validated['client_id'] = $this->fms_id;
            $customer_category->fill($validated);
            $customer_category->save();
            DB::commit();
        } catch (\Throwable $th) {
            \DB::rollback();
            \Log::error($th->getMessage() . '' . $th->getTraceAsString());
            return redirect()->back()->with('error', 'Something went wrong');
        }
        return redirect()->route('admin.list.customercategory')->with('success', 'Category Added Successfuly');
    }

    public function show(Request $request, Customercategory $customercategory): View
    {
        $this->authorize('view', $customercategory);
        $this->data['customercategory'] = $customercategory;
        return view('dashboards.admin.customercategories.show', $this->data);
    }

    public function edit(Customercategory $customercategory)
    {
        $this->authorize('update', $customercategory);
        $this->data['customercategory'] = $customercategory;
        return view('dashboards.admin.customercategories.edit', $this->data);
    }

    public function update(Request $request, Customercategory $customercategory)
    {
        $this->authorize('update', $customercategory);
        $validated = $request->validate([
            'categoryname' => ['required', 'string', 'max:255'],
            'categorydescription' => ['nullable', 'string', 'max:255'],
        ]);
        $validated['client_id'] = $this->fms_id;
        $customercategory->update($validated);
        return redirect()->route('admin.list.customercategory')->with('success', 'Customer Category Updated Successfuly');
    }

    public function delete(Request $request, $customercategory)
    {
        $customercategory = Customercategory::where('id', $customercategory)
            ->where('client_id', $this->fms_id)
            ->first();
        $this->authorize('delete', $customercategory);
        $customercategory->delete();
        return redirect()->route('admin.list.customercategory')->with('success', 'Customer Catgory Deleted Successfuly ');
    }
}
