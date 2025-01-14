<?php

namespace App\Http\Controllers\Chiefoperation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ClientAdmin;
use App\Models\Staff;
use Illuminate\View\View;

class ChiefOperationController extends Controller
{
    protected $data = [];

    protected $fms_id = null;

    public function __construct()
    {
        $this->middleware('chiefoperation');
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
        $this->data['staff'] = Staff::where('client_id', $clientid)->first();
        return view('dashboards.chiefoperation.dashboard', $this->data);
    }
}
