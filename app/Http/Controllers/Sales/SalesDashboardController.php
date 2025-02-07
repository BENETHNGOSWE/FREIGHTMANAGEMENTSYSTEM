<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Customerlead;
use App\Models\ClientAdmin;
use App\Models\Staff;
use App\Models\Customercategory;
use App\Models\Customermessage;
use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalesDashboardController extends Controller
{
    protected $data = [];

    protected $fms_id = null;

    public function __construct()
    {
        $this->middleware('salesperson');
        $this->middleware(function ($request, $next) {
            if (Auth::check()) {
                $this->fms_id = Auth::user()->client_id;
            }

            return $next($request);
        });
    }

    public function index($fms_id): View
{
    $rolesAllowedToView = ['admin', 'chiefoperation'];
    $clientId = auth()->user()->client_id;

    // Query to get the leads count per month
    $leadsPerMonth = Customerlead::selectRaw('EXTRACT(YEAR FROM created_at) as year, EXTRACT(MONTH FROM created_at) as month, COUNT(*) as lead_count')
        ->where('client_id', $clientId)
        ->groupBy('year', 'month')
        ->orderBy('year', 'asc')
        ->orderBy('month', 'asc')
        ->get();

    // Pass the data to the view
    $this->data['leadsPerMonth'] = $leadsPerMonth;

    // Existing logic for handling roles
    if (auth()->user()->hasAnyRole($rolesAllowedToView)) {
        $this->data['clientadmin'] = ClientAdmin::where('client_id', $clientId)->first();
        $this->data['customerleads'] = Customerlead::where('client_id', $clientId)->count();
        $this->data['customercategories'] = Customercategory::where('client_id', $clientId)->count();
        $this->data['messentsent'] = Message::where('client_id', $clientId)->count();
        $this->data['messagefailed'] = Message::where('client_id', $clientId)->where('status', 'failed')->count();
        $this->data['messageLimit'] = Customermessage::where('client_id', $clientId)->value('messageamount') ?? 0;
    } else {
        $this->data['customercategories'] = Customercategory::where('client_id', $clientId)->count();
        $this->data['messentsent'] = Message::where('client_id', $clientId)->count();
        $this->data['messagefailed'] = Message::where('client_id', $clientId)->where('status', 'failed')->count();
        $this->data['messageLimit'] = Customermessage::where('client_id', $clientId)->value('messageamount') ?? 0;
        $this->data['clientadmin'] = ClientAdmin::where('client_id', $clientId)->first();
        $this->data['customerleads'] = Customerlead::where('client_id', $clientId)
            ->where('user_id', auth()->id()) // Only leads created by the current user
            ->count();
    }

    $this->data['staff'] = Staff::with('user')->where('client_id', $this->fms_id)->get();

    // Render the view with the fetched data
    return view('dashboards.sales.dashboard', $this->data);
}

    
}
