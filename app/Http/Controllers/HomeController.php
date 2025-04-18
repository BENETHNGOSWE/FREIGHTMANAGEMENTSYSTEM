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

class HomeController extends Controller
{
    public $fms_id;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('set_freight');
        $this->middleware(function ($request, $next) {
            $this->fms_id = $request->get('fms_id');
            return $next($request);
        });
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $role = auth()->user()->getRoleNames()[0];
        if (!$role && session()->has('impersonator_id')) {
            $role = 'admin';
        }

        switch ($role) {
            case 'owner':
                return $this->ownerDashboard();

            case 'admin':
                return $this->adminDashboard();

            case 'chiefoperation':
                return $this->chiefoperationDashboard();

            case 'manager':
                return $this->managerDashboard();

            case 'godownmanager':
                return $this->GodownmanagerDashboard();

            case 'kodasmspackage':
                return $this->smsadminDashboard();

            case 'salesperson':
                return $this->salesadminDashboard();

            case 'SalesManager':
                return $this->salesadminDashboard();  
                
            case 'receptionist':
                return $this->receptionDashboard();    

            default:
                return view('home');
        }
    }

    public function ownerDashboard()
    {
        $dashboard = new OwnerController();
        return $dashboard->index($this->fms_id);
    }

    public function adminDashboard()
    {
        $dashboard = new AdminDashboardController();
        return $dashboard->index($this->fms_id);
    }

    public function chiefoperationDashboard()
    {
        $dashboard = new ChiefOperationController();
        return $dashboard->index($this->fms_id);
    }

    public function managerDashboard()
    {
        $dashboard = new ManagerDashboardController();
        return $dashboard->index($this->fms_id);
    }

    public function GodownmanagerDashboard()
    {
        $dashboard = new GodownmanagerDashboardController();
        return $dashboard->index($this->fms_id);
    }

    public function smsadminDashboard()
    {
        $dashboard = new SMSManagerDashboardController();
        return $dashboard->index($this->fms_id);
    }

    public function salesadminDashboard()
    {
        $dashboard = new SalesDashboardController();
        return $dashboard->index($this->fms_id);
    }

    public function receptionDashboard()
    {
        $dashboard = new ReceptionDashboardController();
        return $dashboard->index($this->fms_id);
    }
}
