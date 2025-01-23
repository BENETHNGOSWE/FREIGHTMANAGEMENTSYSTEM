<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoiceLoad;
use App\Models\Customer;
use App\Models\Loadpackage;
use App\Models\Client;
use App\Models\ClientAdmin;
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
        $rolesAllowedToView = ['admin','chiefoperation', 'godownmanager'];
        $rolesAllowedToPrint = ['admin', 'chiefoperation'];

        if (auth()->user()->hasAnyRole($rolesAllowedToView)) {
            $this->data['customer'] = Customer::all();
            $this->data['Load'] = Loadpackage::all();
            $this->data['invoices'] = Invoice::where('client_id', $this->fms_id)->get();
        } else {
            $this->data['invoices'] = auth()->user()->invoices;
        }

        $this->data['canPrintReleaseOrder'] = auth()->user()->hasAnyRole($rolesAllowedToPrint);
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
        $this->data['invoice'] = $invoice;
        return view('dashboards.admin.invoices.edit', $this->data);
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validate = $request->validate([
            'invoice_no' => ['required', 'string', 'max:255'],
            'status' => ['required', 'integer'],
        ]);

        $invoice->update($validate);
        return redirect()->route('admin.list.invoice')->with('success', 'Invoice Status Updated Successfully');
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
        return $pdf->download('invoice_' . $invoice->invoice_no . '.pdf');
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
