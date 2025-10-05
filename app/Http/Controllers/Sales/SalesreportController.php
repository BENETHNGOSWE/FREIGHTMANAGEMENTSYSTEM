<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SalesReport;
use App\Models\Customer;
use App\Models\Staff;
use App\Models\Client;
use App\Models\Role;
use App\Exports\SalesReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesreportController extends Controller
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

    public function index(Request $request)
    {
        $query = SalesReport::where('client_id', $this->fms_id);
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        $this->data['salesreports'] = $query->get();
        $this->data['customers'] = Customer::where('client_id', $this->fms_id)->pluck('customer_firstname', 'id');
        $this->data['staffs'] = Staff::where('client_id', $this->fms_id)->pluck('staff_firstname', 'id');

        return view('dashboards.sales.salesreports.index', $this->data);
    }

    public function create()
    {

        $this->data['customers'] = Customer::where('client_id', $this->fms_id)->pluck('customer_firstname', 'id');
        $this->data['staffs'] = Staff::where('client_id', $this->fms_id)
            ->whereHas('user.roles', function ($query) {
                $query->where('name', 'salesperson');
            })
            ->pluck('staff_firstname', 'id');

        return view('dashboards.sales.salesreports.create', $this->data);
    }

    public function validate_salesreport(Request $request)
    {
        return $request->validate([
            'receipt' => 'required|string|max:255',
            'date' => 'required|date',
            'cbm' => 'required|numeric',
            'nature' => 'nullable|string|max:255',
            'hub' => 'nullable|string|max:255',
            'amount1' => 'required|numeric',
            'amount2' => 'nullable|numeric',
            'customer_id' => 'required|exists:customers,id',
            'staff_id' => 'required|exists:staffs,id',
        ]);
    }

    public function store(Request $request)
    {

        $validated = $this->validate_salesreport($request);

        try {
            DB::beginTransaction();
            $salesreport = new SalesReport();
            $validated['client_id'] = $this->fms_id;
            $salesreport->fill($validated);
            $salesreport->save();
            DB::commit();

            return redirect()->route('sales.list.salesreport')->with('success', 'Salesreport Added Successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::error($th->getMessage() . '' . $th->getTraceAsString());;

            return redirect()->back()->with('error', 'Something went wrong');
        }
    }

    public function edit(Request $request, SalesReport $salesreport)
    {
        $this->data['salesreport'] = $salesreport;
        $this->data['customers'] = Customer::where('client_id', $this->fms_id)->pluck('customer_firstname', 'id');
        $this->data['staffs'] = Staff::where('client_id', $this->fms_id)
            ->whereHas('stafftype', function ($query) {
                $query->where('staff_type_name', 'salesperson');
            })
            ->pluck('staff_firstname', 'id');

        return view('dashboards.sales.salesreports.edit', $this->data);
    }

    public function update(Request $request, Salesreport $salesreport)
    {
        $validated = $this->validate_salesreport($request);

        try {
            DB::beginTransaction(); // Corrected typo

            $validated['client_id'] = $this->fms_id;
            $salesreport->fill($validated);
            $salesreport->save(); // `update()` is redundant after `fill()`

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::error($th->getMessage() . ' ' . $th->getTraceAsString());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }

        return redirect()->route('sales.list.salesreport')->with('success', 'Sales Report Updated Successfully');
    }

    public function delete(Request $request, $salesreport)
    {
        $salesreport = salesreport::where('id', $salesreport)->where('client_id', $this->fms_id)->first();
        $this->authorize('delete', $salesreport);
        $salesreport->delete();
        return redirect()->route('sales.list.salesreport')->with('success', 'Sales Report Successfuly deleted');
    }

    public function exportExcel(Request $request)
    {
        $fileName = 'CBM_COMMISION_REPORT_' . Carbon::now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new SalesReportExport($request->start_date, $request->end_date), $fileName);
    }
}
