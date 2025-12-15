<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\ClientAdmin;
use App\Models\OfficeExpense;
use App\Models\officeExpenseCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\OfficeExpenseExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class OfficeExpenseController extends Controller
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
        $this->data['officeexpenses'] = OfficeExpense::where('client_id', $this->fms_id)->get();
        return view('dashboards.admin.officeexpenses.index', $this->data);
    }

    public function create()
    {
        $this->authorize('create', OfficeExpense::class);
        
        $this->data['categories_expenses'] = officeExpenseCategory::where('client_id', $this->fms_id)->pluck('name', 'id');
        return view('dashboards.admin.officeexpenses.create', $this->data);
    }

    public function validated_expenses(Request $request, OfficeExpense $officeexpense = null): array
    {
        return $request->validate([
            'expenses_description' => ['required', 'string', 'max:255'],
            'expenses_cost' => ['required', 'string', 'max:255'],
            'status' => 'in:0,1,2',
            'office_expense_categories_id' => 'required|exists:office_expense_categories,id',
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', OfficeExpense::class);
        $validated = $this->validated_expenses($request);
        try {
            DB::beginTransaction();
            $officeexpense = new OfficeExpense();
            $validated['client_id'] = $this->fms_id;
            $officeexpense->fill($validated);
            $officeexpense->save();
            DB::commit();
        } catch (\Throwable $th) {
            \DB::rollback();
            \Log::error($th->getMessage() . '' . $th->getTraceAsString());
            return redirect()->back()->with('error', 'Something went wrong');
        }
        return redirect()->route('admin.list.officeexpense')->with('success', 'Office Expense Added Successfuly');
    }

    public function show(Request $request, OfficeExpense $officeexpense): View
    {
        $this->authorize('view', $officeexpense);
        $this->data['officeexpense'] = $officeexpense;
        return view('dashboards.admin.officeexpenses.show', $this->data);
    }

    public function edit(OfficeExpense $officeexpense)
    {
        $this->authorize('update', $officeexpense);
        $rolesAllowedToUpdateInvoiceStatus = ['admin', 'chiefoperation'];
        $this->data['canUpdateExpensesStatus'] = auth()->user()->hasAnyRole($rolesAllowedToUpdateInvoiceStatus);
        $this->data['officeexpense'] = $officeexpense;
        $this->data['categories_expenses'] = officeExpenseCategory::where('client_id', $this->fms_id)->pluck('name', 'id');
        return view('dashboards.admin.officeexpenses.edit', $this->data);
    }

    public function update(Request $request, OfficeExpense $officeexpense)
    {
        $this->authorize('update', $officeexpense);
        $rolesAllowedToUpdateInvoiceStatus = ['admin', 'chiefoperation'];
        $this->data['canUpdateExpensesStatus'] = auth()->user()->hasAnyRole($rolesAllowedToUpdateInvoiceStatus);
        $validated = $request->validate([
            'expenses_description' => ['required', 'string', 'max:255'],
            'expenses_cost' => ['required', 'string', 'max:255'],
            'status' => 'required|in:0,1,2',
            'office_expense_categories_id' => 'required|exists:office_expense_categories,id',
        ]);
        $validated['client_id'] = $this->fms_id;
        $officeexpense->update($validated);
        return redirect()->route('admin.list.officeexpense')->with('success', 'Office Expense Updated Successfuly');
    }

    public function delete(Request $request, $officeexpense)
    {
        $officeexpense = OfficeExpense::where('id', $officeexpense)->where('client_id', $this->fms_id)->first();
        $this->authorize('delete', $officeexpense);
        $officeexpense->delete();
        return redirect()->route('admin.list.officeexpense')->with('success', 'Office Expense Deleted Successfuly ');
    }


    public function pdfgenerate($id){
        $expenseinvoice = OfficeExpense::where('id', $id)->first();
    
    // Force fresh client data as well
        $client = $expenseinvoice->client()->first();
        $pdf = Pdf::loadView('dashboards.admin.officeexpenses.expenseinvoicepdf', ['expenseinvoice' => $expenseinvoice, 'client' => $client]);
        return $pdf->download('expenseinv-' . $id . '.pdf');
    }


    public function expenseReport(Request $request){
        $query = OfficeExpense::query();

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();

            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        if($request->filled('office_expense_categories_id')) {
            $query->where('office_expense_categories_id', $request->office_expense_categories_id);
        }

        $officeExpenses = $query->get();
        $totalExpenses = $officeExpenses->sum(function ($item) {
            return (float) str_replace(',', '', $item->expenses_cost);
        });

        $categories = officeExpenseCategory::pluck('name', 'id');

         return view('dashboards.admin.officeexpenses.report', compact('officeExpenses', 'totalExpenses', 'categories'));
    }

    public function exportReport(Request $request) {
        $startDate = $request->input('start_date');
        $endDate = $request ->input('end_date');
        $categoryId = $request->input('office_expense_categories_id');
        return Excel::download(new OfficeExpenseExport($startDate, $endDate, $categoryId), 'office-expenses-report.xlsx');
    }



public function printExpenseInvoice($expenseinvoice_ids)
{
    // Check if we're dealing with multiple expense invoices
    $expenseInvoiceIds = explode(',', $expenseinvoice_ids);
    $isMultiple = count($expenseInvoiceIds) > 1;
    
    if ($isMultiple) {
        // MERGED EXPENSES
        $allExpenses = collect();
        $totalAmount = 0;
        $firstExpenseInvoice = null;
        $mergedExpenseNumbers = [];
        
        foreach ($expenseInvoiceIds as $id) {
            $expenseInvoice = OfficeExpense::with(['client', 'categoryexpense'])
                ->where('id', $id)
                ->first();
            
            if (!$expenseInvoice) {
                continue;
            }
            
            // Store first expense invoice for client details
            if (!$firstExpenseInvoice) {
                $firstExpenseInvoice = $expenseInvoice;
            }
            
            // Collect expense numbers for reference
            $mergedExpenseNumbers[] = $expenseInvoice->expenses_invoicenumber;
            
            // Add this expense to collection
            $allExpenses->push([
                'expense_number' => $expenseInvoice->expenses_invoicenumber,
                'description' => $expenseInvoice->expenses_description,
                'cost' => $expenseInvoice->expenses_cost,
                'category' => $expenseInvoice->categoryexpense->name ?? '',
            ]);
            
            // Add to total
            $totalAmount += $expenseInvoice->expenses_cost;
        }
        
        $client = $firstExpenseInvoice->client;
        
        // Generate merged PDF
        $pdf = PDF::loadView('dashboards.admin.officeexpenses.mergedexpenseinvoice', [
            'client' => $client,
            'expenses' => $allExpenses,
            'mergedExpenseNumbers' => implode(', ', $mergedExpenseNumbers),
            'totalAmount' => $totalAmount
        ]);
        
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('merged_expense_invoice.pdf');
        
    } else {
        // SINGLE EXPENSE - Your existing code
        $expenseinvoice = OfficeExpense::with(['client', 'categoryexpense'])
            ->where('id', $expenseinvoice_ids)
            ->first();
        
        $client = $expenseinvoice->client;
        
        $pdf = PDF::loadView('Invoices.expenseinvoice', [
            'client' => $client,
            'expenseinvoice' => $expenseinvoice
        ]);
        
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('expense_invoice.pdf');
    }
}

  
}