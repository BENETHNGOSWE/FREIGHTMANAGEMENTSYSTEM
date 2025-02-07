<?php

namespace App\Http\Controllers\Chiefoperation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ClientAdmin;
use App\Models\Staff;
use App\Models\Customerlead;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\View\View;

class ChiefOperationController extends Controller
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

    public function index($fms_id): View
    {
        $clientid = auth()->user()->client_id;
        $this->data['total_pending'] = Invoice::where('client_id', $clientid)->where('status', 0)->sum('total_amount');

        $this->data['total_partial_paid'] = Invoice::where('client_id', $clientid)->where('status', 2)->sum('total_amount');

        $this->data['total_full_paid'] = Invoice::where('client_id', $clientid)->where('status', 1)->sum('total_amount');
        $this->data['staff'] = Staff::where('client_id', $clientid)->first();
        $this->data['customerCount'] = Customer::where('client_id', $clientid)->count();
        $this->data['leadCount'] = Customerlead::where('client_id', $clientid)->count();

        $invoicesByMonth = Invoice::where('client_id', $clientid)
        ->selectRaw('EXTRACT(YEAR FROM created_at) as year, EXTRACT(MONTH FROM created_at) as month, status, COUNT(*) as count')
        ->groupBy('year', 'month', 'status')
        ->orderBy('year', 'asc')
        ->orderBy('month', 'asc')
        ->get();

    // Organize data for the chart
    $chartData = [];
    foreach ($invoicesByMonth as $invoice) {
        $monthYear = date('F Y', mktime(0, 0, 0, $invoice->month, 1, $invoice->year));
        if (!isset($chartData[$monthYear])) {
            $chartData[$monthYear] = [
                'Pending' => 0,
                'Partial Paid' => 0,
                'Full Paid' => 0,
            ];
        }
        switch ($invoice->status) {
            case 0:
                $chartData[$monthYear]['Pending'] += $invoice->count;
                break;
            case 1:
                $chartData[$monthYear]['Full Paid'] += $invoice->count;
                break;
            case 2:
                $chartData[$monthYear]['Partial Paid'] += $invoice->count;
                break;
        }
    }

    // Pass chart data to the view
    $this->data['chartData'] = $chartData;
        return view('dashboards.chiefoperation.dashboard', $this->data);
    }
}
