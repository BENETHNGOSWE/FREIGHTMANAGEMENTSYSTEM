<?php

namespace App\Http\Controllers\KodaSMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
        return view('dashboards.kodasms.dashboard', $this->data);
    }

    public function validate_subscription_token(): View
    {
            return view('dashboards.admin.validate_subscription_token',$this->data);

    }
}
