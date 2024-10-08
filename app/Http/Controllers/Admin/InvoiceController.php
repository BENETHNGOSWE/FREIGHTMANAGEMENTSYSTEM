<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoiceLoad;
use App\Models\Customer;
use App\Models\Loadpackage;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    protected $fms_id = null;
    protected $data = [];
    public function __construct()
    {
        $this->middleware('admin');
        $this->middleware(function ($request, $next) {
            if (Auth::check()) {
                $this->fms_id = Auth::user()->client_id;
            }

            return $next($request);
        });
    }

    public function index()
    {
        $this->data['customer'] = Customer::all();
        $this->data['Load'] = Loadpackage::all();
        $this->data['invoice'] = Invoice::where('client_id', $this->fms_id)->get();
        return view('dashboards.admin.invoices.index', $this->data);
    }

    public function show(Request $request, Invoice $invoice)
    {
        $this->data['invoice'] = Invoice::with('invoiceLoad.loadpackage')->findOrFail($invoice->id);
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
        $invoice = Invoice::with('invoiceLoad.loadpackage', 'customer')->findOrFail($id);
        $client = Client::findOrFail($invoice->client_id);

        $data = [
            'invoice' => $invoice,
            'client' => $client
        ];

        $pdf = Pdf::loadView('dashboards.admin.invoices.invoicepdf', $data);
        return $pdf->download('invoice_' . $invoice->invoice_no . '.pdf');
    }
    
}
