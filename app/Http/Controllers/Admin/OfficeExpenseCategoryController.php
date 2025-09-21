<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\ClientAdmin;
use App\Models\officeExpenseCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OfficeExpenseCategoryController extends Controller
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
        $this->data['categoryexpense'] = officeExpenseCategory::where('client_id', $this->fms_id)->get();
        return view('dashboards.admin.expensescategory.index', $this->data);
    }

    public function create()
    {
        // $this->authorize('create', officeExpenseCategory::class);
        return view('dashboards.admin.expensescategory.create', $this->data);
    }

    public function validated_expenses(Request $request, officeExpenseCategory $office_expense = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', officeExpenseCategory::class);
        $validated = $this->validated_expenses($request);
        try {
            DB::beginTransaction();
            $office_expense = new officeExpenseCategory();
            $validated['client_id'] = $this->fms_id;
            $office_expense->fill($validated);
            $office_expense->save();
            DB::commit();
        } catch (\Throwable $th) {
            \DB::rollback();
            \Log::error($th->getMessage() . '' . $th->getTraceAsString());
            return redirect()->back()->with('error', 'Something went wrong');
        }
        return redirect()->route('admin.list.categoryexpense')->with('success', 'Expense Category Added Successfuly');
    }

    public function show(Request $request, officeExpenseCategory $officeexpense): View
    {
        $this->authorize('view', $officeexpense);
        $this->data['officeexpense'] = $officeexpense;
        return view('dashboards.admin.expensescategory.show', $this->data);
    }

    public function edit(officeExpenseCategory $officeexpense)
    {
        $this->authorize('update', $officeexpense);
        $this->data['officeexpense'] = $officeexpense;
        return view('dashboards.admin.expensescategory.edit', $this->data);
    }

    public function update(Request $request, officeExpenseCategory $officeexpense)
    {
        $this->authorize('update', $officeexpense);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);
        $validated['client_id'] = $this->fms_id;
        $officeexpense->update($validated);
        return redirect()->route('admin.list.categoryexpense')->with('success', 'Expense Category Updated Successfuly');
    }

    public function delete(Request $request, $officeexpense)
    {
        $officeexpense = officeExpenseCategory::where('id', $officeexpense)
            ->where('client_id', $this->fms_id)
            ->first();
        $this->authorize('delete', $officeexpense);
        $officeexpense->delete();
        return redirect()->route('admin.list.categoryexpense')->with('success', 'Expense Category Successfuly deleted');
    }
}

