<?php

namespace App\Http\Controllers\KodaSMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Customerlead;
use App\Models\ClientAdmin;
use App\Models\Customercategory;
use App\Models\Customermessage;
use App\Models\Message;

class SMSManagerDashboardController extends Controller
{
    protected $data = [];

    protected $fms_id = null;

    public function __construct()
    {
        $this->middleware('mutliple');
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

        // Fetching relevant data
        $this->data['clientadmin'] = ClientAdmin::where('client_id', $clientid)->first();
        $this->data['customerleads'] = Customerlead::where('client_id', $clientid)->count();
        $this->data['customercategories'] = Customercategory::where('client_id', $clientid)->count();
        $this->data['messentsent'] = Message::where('client_id', $clientid)->count();
        $this->data['messagefailed'] = Message::where('client_id', $clientid)->where('status', 'failed')->count();
        $this->data['messageLimit'] = Customermessage::where('client_id', $clientid)->value('messageamount') ?? 0;

        return view('dashboards.kodasms.dashboard', $this->data);
    }

    public function validate_subscription_token(): View
    {
        return view('dashboards.admin.validate_subscription_token', $this->data);
    }
}
