<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Customercategory;
use App\Models\Invoice;
use App\Models\Loadpackage;
use App\Models\Message;
use App\Models\Customermessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CashierDashboardController extends Controller
{
     protected $data = [];

    protected $fms_id = null;

    public function __construct()
    {
        $this->middleware('cashier');
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
        $this->data['total_customer']=Customer::where('client_id', $fms_id)->count();
        $this->data['total_categories']=Customercategory::where('client_id', $fms_id)->count();
        $this->data['messentsent'] = Message::where('client_id', $clientid)->where('status', 'delivered')->count();
        $this->data['messagefailed'] = Message::where('client_id', $clientid)->where('status', 'failed')->count();
        $this->data['messageLimit'] = Customermessage::where('client_id', $clientid)->value('messageamount') ?? 0;
        $this->data['pendingInvoices'] = Invoice::where('client_id', $clientid)->where('status', 0)->count();
        $this->data['fullPaidInvoices'] = Invoice::where('client_id', $clientid)->where('status', 1)->count();
        $this->data['partialPaidInvoices'] = Invoice::where('client_id', $clientid)->where('status', 2)->count();
        $this->data['topCustomers'] = Customer::where('client_id', $clientid)->withCount('loadpackages')->orderByDesc('loadpackages_count')->take(5)->get();
        $this->data['customersPerMonth'] = Loadpackage::where('client_id', $clientid)->selectRaw('EXTRACT(YEAR FROM created_at) as year, EXTRACT(MONTH FROM created_at) as month, COUNT(DISTINCT customer_id) as customer_count')->groupByRaw('EXTRACT(YEAR FROM created_at), EXTRACT(MONTH FROM created_at)')->orderByRaw('EXTRACT(YEAR FROM created_at), EXTRACT(MONTH FROM created_at)')->get();
        return view('dashboards.reception.dashboard', $this->data);
    }
}
