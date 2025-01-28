<?php

namespace App\Http\Controllers\Godownmanager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\DeriveryOrder;

class GodownmanagerDashboardController extends Controller
{
    protected $data = [];

    protected $fms_id = null;

    public function __construct()
    {
        $this->middleware('godownmanager');
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

        $this->data['deriveryorderCount'] = DeriveryOrder::where('client_id', $clientid)->count();
        return view('dashboards.godownmanager.dashboard', $this->data);
    }
}
