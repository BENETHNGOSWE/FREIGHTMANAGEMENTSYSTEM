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
        // The platform owner runs the subscriptions, not one client's cargo: show them
        // the whole book rather than an empty freight dashboard.
        if (app(\App\Services\Platform\ModuleAccess::class)->isOwner()) {
            return $this->platformOverview();
        }

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
        // What this client actually bought: the dashboard follows the package, not a fixed template.
        $access = app(\App\Services\Platform\ModuleAccess::class);
        $client = auth()->user()->client ?? null;
        $subs = app(\App\Services\Platform\SubscriptionService::class);
        $this->data['subscriptionStatus'] = $subscriptionStatus = $subs->status($client);
        $this->data['hasCargo'] = $access->allows('cargo');
        $this->data['hasAccounts'] = $access->allows('accounts');
        $this->data['hasSms'] = $access->allows('sms_bulk');
        // the product badge only makes sense once a package is actually subscribed
        $this->data['productLabel'] = ($client && $subscriptionStatus !== 'none')
            ? (\App\Services\Platform\ProductCatalog::PRODUCTS[$client->account_type] ?? null)
            : null;
        $this->data['subscriptionDaysLeft'] = $subs->daysLeft($client);
        $this->data['smsQuota'] = $client ? $subs->smsQuota($client) : 0;
        $this->data['smsLeft'] = $client ? $subs->smsLeft($client) : 0;
        // what the owner assigned, so "allowance" on screen is the package's, not a fixed column
        $this->data['loadcreate'] = \App\Models\Loadpackage::where('client_id', $clientid)->count();
        $this->data['customersPerMonth'] = Loadpackage::where('client_id', $clientid)->selectRaw('EXTRACT(YEAR FROM created_at) as year, EXTRACT(MONTH FROM created_at) as month, COUNT(DISTINCT customer_id) as customer_count')->groupByRaw('EXTRACT(YEAR FROM created_at), EXTRACT(MONTH FROM created_at)')->orderByRaw('EXTRACT(YEAR FROM created_at), EXTRACT(MONTH FROM created_at)')->get();
        return view('dashboards.dynamic_dashboard',$this->data);
    }

    /** The owner's home: every client, where each subscription stands, what is running out. */
    private function platformOverview()
    {
        $subscriptions = app(\App\Services\Platform\SubscriptionService::class);
        $clients = \App\Models\Client::with(['clientTypeRelation', 'currentSubscription.kodapackages'])->get();

        $rows = $clients->map(function ($client) use ($subscriptions) {
            $subscription = $subscriptions->current($client->id);

            return (object) [
                'client' => $client,
                'subscription' => $subscription,
                'product' => $subscription->product ?? ($client->account_type ?: 'freight'),
                'status' => $subscriptions->status($client),
                'days_left' => $subscriptions->daysLeft($client),
                'sms_left' => $subscriptions->smsLeft($client),
                'sms_quota' => $subscriptions->smsQuota($client),
                'expires' => optional($subscription)->expire_date,
            ];
        });

        $count = fn ($status) => $rows->where('status', $status)->count();

        return view('dashboards.platform_owner', [
            'rows' => $rows,
            'totals' => [
                'clients' => $rows->count(),
                'active' => $count('active'),
                'grace' => $count('grace'),
                'expired' => $count('expired'),
                'locked' => $count('locked'),
                'none' => $count('none'),
                'sms' => $rows->whereIn('product', ['sms', 'both'])->count(),
                'freight' => $rows->whereIn('product', ['freight', 'both'])->count(),
            ],
            'expiringSoon' => $rows->filter(fn ($row) => $row->days_left !== null && $row->days_left >= 0 && $row->days_left <= 30)->sortBy('days_left'),
            'renewals' => $rows->filter(fn ($row) => in_array($row->status, ['expired', 'locked', 'grace'], true)),
            // signed up but never bought: assign a package, then they need a token to get in
            'noSubscription' => $rows->where('status', 'none'),
            // summed in PHP on purpose: an aggregate over an empty list is the classic
            // "column does not exist" 500 on PostgreSQL, and this dashboard must never fall over
            'subscriptionValue' => round((float) $rows->pluck('subscription')->filter()
                ->sum(fn ($subscription) => (float) ($subscription->amount ?? 0)), 2),
            'recentInvoices' => \App\Models\SubscriptionInvoice::with('client')->latest('id')->take(6)->get(),
            'products' => \App\Services\Platform\ProductCatalog::PRODUCTS,
            'graceDays' => $subscriptions->graceDays(),
            'enforceAll' => $subscriptions->enforceForEveryone(),
        ]);
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
