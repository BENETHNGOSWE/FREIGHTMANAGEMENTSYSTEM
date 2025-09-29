<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Owner\OwnerController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Manager\ManagerDashboardController;
use App\Http\Controllers\KodaSMS\SMSManagerDashboardController;
use App\Http\Controllers\Chiefoperation\ChiefOperationController;
use App\Http\Controllers\Godownmanager\GodownmanagerDashboardController;
use App\Http\Controllers\Sales\SalesDashboardController;
use App\Http\Controllers\Reception\ReceptionDashboardController;
use App\Http\Controllers\Cashier\CashierDashboardController;

use App\Models\Customer;
use App\Models\Customercategory;
use App\Models\Invoice;
use App\Models\Loadpackage;
use App\Models\Message;
use App\Models\Customermessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HomeController extends Controller
{
    protected $data = [];

    protected $fms_id = null;
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('set_freight');
        $this->middleware(function ($request, $next) {
           if (Auth::check()) {
                $this->fms_id = Auth::user()->client_id;
            }

            return $next($request);
        });
    }

    public function index() {
         $clientid = auth()->user()->client_id;
        //  $role = auth()->user()->getRoleNames();
        //  dd($role);
        $this->data['total_customer']=Customer::where('client_id', $this->fms_id)->count();
        $this->data['total_categories']=Customercategory::where('client_id', $this->fms_id)->count();
        $this->data['messentsent'] = Message::where('client_id', $clientid)->where('status', 'delivered')->count();
        $this->data['messagefailed'] = Message::where('client_id', $clientid)->where('status', 'failed')->count();
        $this->data['messageLimit'] = Customermessage::where('client_id', $clientid)->value('messageamount') ?? 0;
        $this->data['pendingInvoices'] = Invoice::where('client_id', $clientid)->where('status', 0)->count();
        $this->data['fullPaidInvoices'] = Invoice::where('client_id', $clientid)->where('status', 1)->count();
        $this->data['partialPaidInvoices'] = Invoice::where('client_id', $clientid)->where('status', 2)->count();
        $this->data['topCustomers'] = Customer::where('client_id', $clientid)->withCount('loadpackages')->orderByDesc('loadpackages_count')->take(5)->get();
        $this->data['customersPerMonth'] = Loadpackage::where('client_id', $clientid)->selectRaw('EXTRACT(YEAR FROM created_at) as year, EXTRACT(MONTH FROM created_at) as month, COUNT(DISTINCT customer_id) as customer_count')->groupByRaw('EXTRACT(YEAR FROM created_at), EXTRACT(MONTH FROM created_at)')->orderByRaw('EXTRACT(YEAR FROM created_at), EXTRACT(MONTH FROM created_at)')->get();
        return view('dashboards.dynamic_dashboard',$this->data);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    // public function index()
    // {
    //     $role = auth()->user()->getRoleNames()->first();
    //     if (!$role && session()->has('impersonator_id')) {
    //         $role = 'admin';
    //     }

    //     switch ($role) {
    //         case 'owner':
    //             return $this->ownerDashboard();

    //         case 'admin':
    //             return $this->adminDashboard();

    //         case 'chiefoperation':
    //             return $this->chiefoperationDashboard();

    //         case 'manager':
    //             return $this->managerDashboard();

    //         case 'godownmanager':
    //             return $this->GodownmanagerDashboard();

    //         case 'kodasmspackage':
    //             return $this->smsadminDashboard();

    //         case 'salesperson':
    //             return $this->salesadminDashboard();

    //         case 'SalesManager':
    //             return $this->salesadminDashboard();  
                
    //         case 'receptionist':
    //             return $this->receptionDashboard();   
                
    //         case 'Cashier':
    //             return $this->cashierDashboard();    

    //         default:
    //             return view('home');
    //     }
    // }

    // public function ownerDashboard()
    // {
    //     $dashboard = new OwnerController();
    //     return $dashboard->index($this->fms_id);
    // }

    // public function adminDashboard()
    // {
    //     $dashboard = new AdminDashboardController();
    //     return $dashboard->index($this->fms_id);
    // }

    // public function chiefoperationDashboard()
    // {
    //     $dashboard = new ChiefOperationController();
    //     return $dashboard->index($this->fms_id);
    // }

    // public function managerDashboard()
    // {
    //     $dashboard = new ManagerDashboardController();
    //     return $dashboard->index($this->fms_id);
    // }

    // public function GodownmanagerDashboard()
    // {
    //     $dashboard = new GodownmanagerDashboardController();
    //     return $dashboard->index($this->fms_id);
    // }

    // public function smsadminDashboard()
    // {
    //     $dashboard = new SMSManagerDashboardController();
    //     return $dashboard->index($this->fms_id);
    // }

    // public function salesadminDashboard()
    // {
    //     $dashboard = new SalesDashboardController();
    //     return $dashboard->index($this->fms_id);
    // }

    // public function receptionDashboard()
    // {
    //     $dashboard = new ReceptionDashboardController();
    //     return $dashboard->index($this->fms_id);
    // }

    // public function cashierDashboard()
    // {
    //     $dashboard = new CashierDashboardController();
    //     return $dashboard->index($this->fms_id);
    // }
}
