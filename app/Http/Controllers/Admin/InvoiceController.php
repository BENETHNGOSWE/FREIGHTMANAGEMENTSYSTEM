<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Loadpackage;
use App\Models\Client;
use App\Models\ClientAdmin;
use App\Models\Companyinvoicedetail;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    protected $fms_id = null;
    protected $data = [];
    public function __construct()
    {
        $this->middleware('multiple');
        $this->middleware(function ($request, $next) {
            if (Auth::check()) {
                $this->fms_id = Auth::user()->client_id;
                $this->data['clientadmin'] = ClientAdmin::where('client_id', $this->fms_id)->first();
            }

            return $next($request);
        });
    }

    public function index()
    {
        $rolesAllowedToView = ['admin','chiefoperation', 'godownmanager', 'Cashier'];
        $rolesAllowedToPrint = ['admin', 'chiefoperation','Cashier'];
        $rolesAllowedToPrintFinalInvoice = ['admin', 'chiefoperation', 'receptionist', 'Cashier'];

        if (auth()->user()->hasAnyRole($rolesAllowedToView)) {
            $this->data['customer'] = Customer::all();
            $this->data['Load'] = Loadpackage::all();
            $this->data['invoices'] = Invoice::where('client_id', $this->fms_id)->get();
        } else {
            $this->data['invoices'] = auth()->user()->invoices;
        }

        $this->data['canPrintReleaseOrder'] = auth()->user()->hasAnyRole($rolesAllowedToPrint);
        $this->data['canPrintFinalInvoice'] = auth()->user()->hasAnyRole($rolesAllowedToPrintFinalInvoice);
        return view('dashboards.admin.invoices.index', $this->data);
    }

    public function show(Request $request, Invoice $invoice)
    {
        $this->data['invoice'] = Invoice::with('invoiceLoads.loadpackage')->findOrFail($invoice->id);
        $this->data['client'] = Client::findOrFail($invoice->client_id);
        return view('dashboards.admin.invoices.show', $this->data);
    }

    public function edit(Request $request, Invoice $invoice)
    {
        $rolesAllowedToUpdateInvoiceStatus = ['admin', 'chiefoperation'];
        $this->data['invoice'] = $invoice;
        $this->data['invoice'] = $invoice->load('invoiceLoads.loadPackage');
        $this->data['canUpdateInvoiceStatus'] = auth()->user()->hasAnyRole($rolesAllowedToUpdateInvoiceStatus);
        return view('dashboards.admin.invoices.edit', $this->data);
    }

   public function update(Request $request, Invoice $invoice)
    {
        $rolesAllowedToUpdateInvoiceStatus = ['admin', 'chiefoperation'];
        $canUpdateInvoiceStatus = auth()->user()->hasAnyRole($rolesAllowedToUpdateInvoiceStatus);

        // build validation rules
        $rules = [
            'invoice_no' => 'required|string|unique:invoices,invoice_no,' . $invoice->id,
            'cbm_price' => 'nullable|numeric|min:0',
            'exchange_rate' => 'nullable|numeric|min:0',
        ];

        if ($canUpdateInvoiceStatus) {
            $rules['status'] = 'required|in:0,1,2';
        }

        $request->validate($rules);

        // If not allowed, lock status to old value
        $status = $canUpdateInvoiceStatus ? $request->status : $invoice->status;

        // Load related invoice_loads to calculate total CBM
        $invoice->load('invoiceLoads.loadPackage');
        $loadWeightValue = $invoice->invoiceLoads->first()->loadPackage->load_weight_value ?? 0;
        $cbmPrice = $request->input('cbm_price', $invoice->cbm_price ?? 0);
        $exchangeRate = $request->input('exchange_rate', $invoice->exchange_rate ?? 1);
        $totalAmount = $loadWeightValue * $cbmPrice * $exchangeRate;

        // Update invoice
        $invoice->update([
            'invoice_no' => $request->invoice_no,
            'status' => $status,
            'cbm_price' => $cbmPrice,
            'exchange_rate' => $exchangeRate,
            'total_amount' => $totalAmount,
        ]);

        return redirect()->route('admin.list.invoice')->with('success', 'Invoice updated successfully.');
    }

    public function generateInvoicePDF($id)
    {
        $invoice = Invoice::with('invoiceLoads.loadpackage', 'customer')->findOrFail($id);
        $client = Client::findOrFail($invoice->client_id);
        $paymentInfo = Payment::where('client_id', $invoice->client_id)->get();

        $data = [
            'invoice' => $invoice,
            'client' => $client,
            'paymentInfo' => $paymentInfo,
        ];

        $pdf = Pdf::loadView('dashboards.admin.invoices.invoicepdf', $data);
        return $pdf->download('cargo_receipt.pdf');
    }


    public function UpdatedInvoicePDF($id)
    {
        $invoice = Invoice::with('invoiceLoads.loadpackage', 'customer')->findOrFail($id);
        $client = Client::findOrFail($invoice->client_id);
        $paymentInfo = Payment::where('client_id', $invoice->client_id)->get();
        $company = Companyinvoicedetail::where('client_id', $this->fms_id)->first();

        $data = [
            'invoice' => $invoice,
            'client' => $client,
            'paymentInfo' => $paymentInfo,
            'company' => $company,
        ];

        $pdf = Pdf::loadView('dashboards.admin.invoices.finalinvoicePDF', $data);
        return $pdf->download('finalInvoice_' . $invoice->invoice_no . '.pdf');
    }

    public function generatereleasePDF($id)
    {
        $invoice = Invoice::findOrFail($id);

        if ($invoice->status != 1) {
            return redirect()->back()->with('error', 'Invoice must be fully paid to generate a release order.');
        }

        $client = $invoice->client;

        $pdf = Pdf::loadview('dashboards.admin.invoices.releasepdf', ['invoice' => $invoice, 'client' => $client]);
        return $pdf->download('release_order.pdf');
    }
    
}
