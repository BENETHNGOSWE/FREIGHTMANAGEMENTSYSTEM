<?php

namespace App\Http\Controllers\Chiefoperation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Staff;
use Auth;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InvoiceExport;

class FinancialreportController extends Controller
{
    protected $data = [];
    protected $fms_id = null;

    public function __construct()
    {
        $this->middleware('chiefoperation');
        $this->middleware(function ($request, $next) {
            if (Auth::check()) {
                $this->fms_id = Auth::user()->client_id;
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $query = Invoice::where('client_id', $this->fms_id);

        // Filter by Start and End Date
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Filter by Customer Name
        if ($request->filled('customer_name')) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('customer_firstname', 'like', '%' . $request->customer_name . '%');
            });
        }

        // Filter by Staff Name
        if ($request->filled('staff_name')) {
            $query->whereHas('staff', function ($q) use ($request) {
                $q->where('staff_firstname', 'like', '%' . $request->staff_name . '%');
            });
        }

        $this->data['invoices'] = $query->get();
        $this->data['customers'] = Customer::where('client_id', $this->fms_id)->pluck('customer_firstname', 'id');
        $this->data['staffs'] = Staff::where('client_id', $this->fms_id)->pluck('staff_firstname', 'id');

        return view('dashboards.chiefoperation.financialreports.index', $this->data);
    }

    public function exportExcel(Request $request)
    {
        $fileName = 'Invoices_Report_' . Carbon::now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new InvoiceExport($request), $fileName);
    }
}
