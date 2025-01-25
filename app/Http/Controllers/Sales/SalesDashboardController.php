<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Customerlead;
use App\Models\ClientAdmin;
use App\Models\Customercategory;
use App\Models\Customermessage;
use App\Models\Message;

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
    
        // Check if the user has one of the allowed roles
        if (auth()->user()->hasAnyRole($rolesAllowedToView)) {
            // Users with allowed roles can see all data for the client
            $this->data['clientadmin'] = ClientAdmin::where('client_id', $clientId)->first();
            $this->data['customerleads'] = Customerlead::where('client_id', $clientId)->count();
            $this->data['customercategories'] = Customercategory::where('client_id', $clientId)->count();
            $this->data['messentsent'] = Message::where('client_id', $clientId)->count();
            $this->data['messagefailed'] = Message::where('client_id', $clientId)->where('status', 'failed')->count();
            $this->data['messageLimit'] = Customermessage::where('client_id', $clientId)->value('messageamount') ?? 0;
        } else {
            // Users without allowed roles can only see their own data
            $this->data['customercategories'] = Customercategory::where('client_id', $clientId)->count();
            $this->data['messentsent'] = Message::where('client_id', $clientId)->count();
            $this->data['messagefailed'] = Message::where('client_id', $clientId)->where('status', 'failed')->count();
            $this->data['messageLimit'] = Customermessage::where('client_id', $clientId)->value('messageamount') ?? 0;
            $this->data['clientadmin'] = ClientAdmin::where('client_id', $clientId)->first();
            $this->data['customerleads'] = Customerlead::where('client_id', $clientId)
                ->where('user_id', auth()->id()) // Only leads created by the current user
                ->count();
        }
    
        // Render the view with the fetched data
        return view('dashboards.sales.dashboard', $this->data);
    }
    
}
