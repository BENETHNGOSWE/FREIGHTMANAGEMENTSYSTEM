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
use App\Models\GodownAddress;
use App\Models\Payment;
use App\Models\InvoicePayment;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


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

    private function ensureOwnInvoice(Invoice $invoice): void{
        if ( (int) $invoice->client_id !== (int) $this->fms_id) {
            abort(404);
        }
    }

    public function index()
    {
        $rolesAllowedToView = ['admin','chiefoperation', 'godownmanager', 'Cashier'];
        // $rolesAllowedToPrint = ['admin', 'chiefoperation','Cashier'];
        // $rolesAllowedToPrintFinalInvoice = ['admin', 'chiefoperation', 'receptionist', 'Cashier'];

        if (auth()->user()->hasAnyRole($rolesAllowedToView)) {
            $this->data['customer'] = Customer::where('client_id', $this->fms_id)->get();
            $this->data['Load'] = Loadpackage::where('client_id', $this->fms_id)->get();
            $this->data['invoices'] = Invoice::where('client_id', $this->fms_id)->get();
        } else {
            $this->data['invoices'] = auth()->user()->invoices;
        }

        $this->data['canPrintReleaseOrder'] = auth()->user()->can('print release order');
        $this->data['canPrintFinalInvoice'] = auth()->user()->can('print final invoice');
        $this->data['canEditInvoice']       = auth()->user()->can('update invoices');

        // $this->data['canPrintReleaseOrder'] = auth()->user()->hasAnyRole($rolesAllowedToPrint);
        // $this->data['canPrintFinalInvoice'] = auth()->user()->hasAnyRole($rolesAllowedToPrintFinalInvoice);
        return view('dashboards.admin.invoices.index', $this->data);
    }

    public function show(Request $request, Invoice $invoice)
    {
        $this->ensureOwnInvoice($invoice);
        $this->data['invoice'] = Invoice::with('invoiceLoads.loadpackage')->findOrFail($invoice->id);
        $this->data['client'] = Client::findOrFail($invoice->client_id);
        return view('dashboards.admin.invoices.show', $this->data);
    }

    public function edit(Request $request, Invoice $invoice)
    {
        $this->ensureOwnInvoice($invoice);
        $rolesAllowedToUpdateInvoiceStatus = ['admin', 'chiefoperation'];
        $this->data['invoice'] = $invoice;
        $this->data['invoice'] = $invoice->load('invoiceLoads.loadPackage');
        $this->data['canUpdateInvoiceStatus'] = auth()->user()->hasAnyRole($rolesAllowedToUpdateInvoiceStatus);
        $this->data['payments'] = $invoice->invoicePayments()->with('receivedBy')->orderByDesc('payment_date')->orderByDesc('id')->get();
        $this->data['amountDue'] = $invoice->amountDue();
        $this->data['totalReceived'] = $invoice->totalReceived();
        $this->data['balanceDue'] = $invoice->balanceDue();
        return view('dashboards.admin.invoices.edit', $this->data);
    }

   public function update(Request $request, Invoice $invoice)
    {
        $this->ensureOwnInvoice($invoice);
        $rolesAllowedToUpdateInvoiceStatus = ['admin', 'chiefoperation'];
        $canUpdateInvoiceStatus = auth()->user()->hasAnyRole($rolesAllowedToUpdateInvoiceStatus);

        // build validation rules
        $rules = [
            'invoice_no' => 'required|string|unique:invoices,invoice_no,' . $invoice->id,
            'cbm_price' => 'nullable|numeric|min:0',
            'exchange_rate' => 'nullable|numeric|min:0',
        ];

        $request->validate($rules);

        // Load related invoice_loads to calculate total CBM
        $invoice->load('invoiceLoads.loadPackage');
        $loadWeightValue = $invoice->invoiceLoads->sum(function ($invoiceLoad) {
            return (float) ($invoiceLoad->loadPackage->load_weight_value ?? 0);
        });
        $cbmPrice = $request->input('cbm_price', $invoice->cbm_price ?? 0);
        $exchangeRate = $request->input('exchange_rate', $invoice->exchange_rate ?? 1);
        $totalAmount = $loadWeightValue * $cbmPrice * $exchangeRate;
        $received = $invoice->totalReceived();
        $balance = $invoice->balanceDue();

        // Update invoice
        $invoice->update([
            'invoice_no' => $request->invoice_no,
            'cbm_price' => $cbmPrice,
            'exchange_rate' => $exchangeRate,
            'total_amount' => $totalAmount,
            'total_balance' => max($balance, 0),
            'status' => $received <= 0 ? 0 : ($balance <= 0 ? 1 : 2),
        ]);

        

        return redirect()->route('admin.list.invoice')->with('success', 'Invoice updated successfully.');
    }


    public function recordPayment(Request $request, Invoice $invoice)
    {
        // Tenant safety + permission (reuse the existing 'update invoices' permission)
        if ($invoice->client_id !== $this->fms_id || !auth()->user()->can('update invoices')) {
            abort(403);
        }

        $validated = $request->validate([
            'amount_received' => ['required', 'numeric', 'gt:0'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', 'in:CASH,BANK,MOBILE_MONEY,CHEQUE,OTHER'],
            'reference_no' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // Never receive more than the customer owes
        if ((float) $validated['amount_received'] > $invoice->balanceDue()) {
            return redirect()->back()->with('error', 'Amount received cannot be greater than the balance due: ' . number_format($invoice->balanceDue(), 2));
        }

        DB::transaction(function () use ($validated, $invoice) {
            InvoicePayment::create([
                'invoice_id' => $invoice->id,
                'client_id' => $invoice->client_id,
                'amount_received' => $validated['amount_received'],
                'payment_date' => $validated['payment_date'],
                'payment_method' => $validated['payment_method'],
                'reference_no' => $validated['reference_no'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'received_by' => auth()->id(),
            ]);

            // Keep invoice totals + status as calculated FACTS
            $received = $invoice->totalReceived();
            $balance = $invoice->balanceDue();

            $invoice->update([
                'total_balance' => max($balance, 0),
                'status' => $received <= 0 ? 0 : ($balance <= 0 ? 1 : 2),
            ]);
        });

        return redirect()->back()->with('success', 'Payment recorded successfully.');
    }

    public function generateInvoicePDF($id)
    {
        $invoice = Invoice::with('invoiceLoads.loadpackage', 'customer')->findOrFail($id);
        $this->ensureOwnInvoice($invoice);
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
        $this->ensureOwnInvoice($invoice);
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
        if (!auth()->user()->can('print release order')) {
        abort(403);
        }

        $invoice = Invoice::findOrFail($id);
        $this->ensureOwnInvoice($invoice);
        if ($invoice->status != 1) {
            return redirect()->back()->with('error', 'Invoice must be fully paid to generate a release order.');
        }
        $client = $invoice->client;
        $godowndetails = GodownAddress::where('client_id', $this->fms_id)->first();
        $pdf = Pdf::loadview('dashboards.admin.invoices.releasepdf', ['invoice' => $invoice, 'client' => $client, 'godowndetails' => $godowndetails]);
        return $pdf->download('release_order.pdf');
    }

    public function InvoiceMerge(Request $request)
    {
        $invoiceIds = $request->input('invoice_ids');  

        if (empty($invoiceIds)) {
            return redirect()->back()->with('error', 'No invoices selected for merge.');
        }

        $invoices = Invoice::with(['customer', 'invoiceLoads.loadpackage', 'invoiceLoads.invoice'])
                           ->where('client_id', $this->fms_id)
                           ->whereIn('id', $invoiceIds)
                           ->get();

        if ($invoices->count() !== count(array_unique((array) $invoiceIds))) {
            return redirect()->back()->with('error', 'One or more selected invoices were not found.');
        }

        $company = Companyinvoicedetail::where('client_id', $this->fms_id)->first() ?? (object)['logo' => null, 'company_name' => 'N/A', 'office_phone' => 'N/A', 'tin_number' => 'N/A', 'tagline' => 'N/A', 'company_address_one' => 'N/A', 'company_city' => 'N/A', 'company_country' => 'N/A', 'company_email' => 'N/A'];
        
        $paymentInfo = Payment::where('client_id', $this->fms_id)->get();

        $firstInvoice = $invoices->first();
        $customer = $firstInvoice->customer;
        
        $allItems = collect();
        $totalAmountDue = 0; 
        $totalAmount = 0; 
        $invoiceNos = [];

        foreach ($invoices as $invoice) {
            $allItems = $allItems->merge($invoice->invoiceLoads);
            
            $totalAmountDue += $invoice->total_amount + $invoice->total_transportfee;
            $totalAmount += $invoice->total_amount;
            
            $invoiceNos[] = $invoice->invoice_no;
        }

        $totalInvoiceNo = implode(', ', $invoiceNos);
        
        $data = [
            'company' => $company, 
            'customer' => $customer,
            'allItems' => $allItems,
            'totalInvoiceNo' => $totalInvoiceNo,
            'totalAmountDue' => $totalAmountDue,
            'totalAmount' => $totalAmount,
            'exchangeRate' => $firstInvoice->exchange_rate, 
            'paymentInfo' => $paymentInfo,
        ];

        $pdf = Pdf::loadView('dashboards.admin.invoices.merged_pdf', $data);
        
        return $pdf->download('Consolidated_Invoice_' . $customer->id . '.pdf');
    }
    
}
