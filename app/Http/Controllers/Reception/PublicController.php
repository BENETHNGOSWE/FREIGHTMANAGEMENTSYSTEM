<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ClientAdmin; 
use App\Models\Customer;
use App\Models\Loadpackage;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\Customerlead;

class PublicController extends Controller
{
    protected $fms_id = null;
    protected $data = [];
    public function __construct()
    {
        $this->middleware('receptionist');
        $this->middleware(function ($request, $next) {
            if (Auth::check()) {
                $this->fms_id = Auth::user()->client_id;
                $this->data['clientadmin'] = ClientAdmin::where('client_id', $this->fms_id)->first();
            }

            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.
     */
    public function reception_index()
    {
        $this->data['customers'] = Customer::where('client_id', $this->fms_id)
            ->latest()
            ->get();
        $rolesAllowedToSeeButtons = ['admin', 'chiefoperation','manager'];
        $this->data['canSeeActionButtons'] = auth()->user()->hasAnyRole($rolesAllowedToSeeButtons);    
        return view('dashboards.admin.customers.list_customers', $this->data);
    }


    public function loadpackage_index()
    {
        if (auth()->user()->hasRole('receptionist')) {
            $this->data['loadpackages'] = Loadpackage::where('client_id', $this->fms_id)
                ->with('container')
                ->get();
        } else {
            $this->data['loadpackages'] = auth()->user()->loadpackages;
        }

        return view('dashboards.admin.loadpackages.index', $this->data);
    }


    public function invoice_index()
    {
        $rolesAllowedToView = ['receptionist'];
        $rolesAllowedToPrint = ['admin', 'chiefoperation'];
        $rolesAllowedToPrintFinalInvoice = ['admin', 'chiefoperation', 'receptionist'];

        if (auth()->user()->hasAnyRole($rolesAllowedToView)) {
            $this->data['customer'] = Customer::where('client_id', $this->fms_id)->get();
            $this->data['Load'] = Loadpackage::where('client_id', $this->fms_id)->get();
            $this->data['invoices'] = Invoice::where('client_id', $this->fms_id)->get();
        } else {
            $this->data['invoices'] = auth()->user()->invoices;
        }

        $this->data['canPrintReleaseOrder'] = auth()->user()->hasAnyRole($rolesAllowedToPrint);
        $this->data['canPrintFinalInvoice'] = auth()->user()->hasAnyRole($rolesAllowedToPrintFinalInvoice);
        return view('dashboards.admin.invoices.index', $this->data);
    }

    public function invoice_show(Request $request, Invoice $invoice)
    {
        $this->data['invoice'] = Invoice::with('invoiceLoads.loadpackage')->findOrFail($invoice->id);
        $this->data['client'] = Client::findOrFail($invoice->client_id);
        return view('dashboards.admin.invoices.show', $this->data);
    }

    public function customerlead_index()
    {
        $roleAllowedToView = ['receptionist'];

        if (auth()->user()->hasAnyRole($roleAllowedToView)) {
            $this->data['customerleads'] = Customerlead::with('user')->where('client_id', $this->fms_id)->get();
        } else {
            $this->data['customerleads'] = auth()->user()->customerleads()->with('user')->get();
        }

        return view('dashboards.admin.customerleads.index', $this->data);
    }


}
