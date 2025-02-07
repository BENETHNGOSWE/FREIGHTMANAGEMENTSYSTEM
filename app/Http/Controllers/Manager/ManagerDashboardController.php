<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Customer;
use App\Models\Customerlead;
use App\Models\User;
use App\Models\Loadpackage;
use App\Models\Message;
use App\Models\Customermessage;
use App\Models\Invoice;

class ManagerDashboardController extends Controller
{
    protected $data = [];

    protected $fms_id = null;

    public function __construct()
    {
        $this->middleware('manager');
        $this->middleware(function ($request, $next) {
            if (Auth::check()) {
                $this->fms_id = Auth::user()->client_id;
                $this->data['clientadmin'] = ClientAdmin::where('client_id', $this->fms_id)->first();
            }

            return $next($request);
        });
    }

    public function index($fms_id): View
    {
        $clientid = auth()->user()->client_id;
        // $clientadmin = ClientAdmin::where('client_id', $clientid)->first();

        $this->data['customerCount'] = Customer::where('client_id', $clientid)->count();
        $this->data['leadCount'] = Customerlead::where('client_id', $clientid)->count();
        $this->data['loadcreate'] = Loadpackage::where('client_id', $clientid)->count();
        $this->data['messentsent'] = Message::where('client_id', $clientid)->count();
        $this->data['invoiceCount'] = Invoice::where('client_id', $clientid)->count();
        $this->data['messagefailed'] = Message::where('client_id', $clientid)->where('status', 'failed')->count();
        $this->data['messageLimit'] = Customermessage::where('client_id', $clientid)->value('messageamount') ?? 0;

        $this->data['loadsPerMonth'] = Loadpackage::selectRaw("DATE_PART('year', created_at) as year, DATE_PART('month', created_at) as month, COUNT(*) as load_count")->groupBy('year', 'month')->orderBy('year', 'desc')->orderBy('month', 'desc')->get();
        return view('dashboards.manager.dashboard', $this->data);
    }
}
