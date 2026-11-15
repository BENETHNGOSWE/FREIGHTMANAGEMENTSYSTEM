<?php

namespace App\Http\Controllers\Cargo;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\MergedPayment;
use App\Models\InvoiceLoad;
use App\Models\Loadpackage;
use App\Services\Cargo\MergePaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Merge payment: one payment for several cargo of the same customer, with the 1 KG minimum
 * applied to the combined weight of KG cargo.
 */
class MergePaymentController extends CargoController
{
    public function __construct(private MergePaymentService $merge)
    {
    }

    public function create(Request $request): View
    {
        $this->allow('update invoices');

        $clientId = $this->clientId();
        $customerId = $request->integer('customer_id') ?: null;

        $customers = Customer::where('client_id', $clientId)->orderBy('customer_firstname')->get();

        // total_balance is varchar in the legacy schema: comparing it to 0 works on MySQL but not
        // on PostgreSQL, so the "still owing" filter is done in PHP.
        $invoices = Invoice::where('client_id', $clientId)
            ->when($customerId, fn ($query) => $query->where('customer_id', $customerId))
            ->with(['customer', 'invoicePayments'])
            ->orderBy('created_at')
            ->get()
            ->filter(fn ($invoice) => $invoice->balanceDue() > 0)
            ->take(60)
            ->values();

        $this->withWeights($invoices);

        return view('dashboards.invoices.merge', [
            'customers' => $customers,
            'selectedCustomer' => $customerId,
            'invoices' => $invoices,
            'preview' => null,
            'recent' => MergedPayment::where('client_id', $clientId)->with('lines')->latest('id')->limit(10)->get(),
        ]);
    }

    public function preview(Request $request): View
    {
        $this->allow('update invoices');

        $data = $request->validate([
            'customer_id' => ['required', 'integer'],
            'invoice_ids' => ['required', 'array', 'min:1'],
            'invoice_ids.*' => ['integer'],
        ]);

        $clientId = $this->clientId();

        $invoices = Invoice::where('client_id', $clientId)
            ->where('customer_id', $data['customer_id'])
            ->with(['customer', 'invoicePayments'])
            ->orderBy('created_at')
            ->get()
            ->filter(fn ($invoice) => $invoice->balanceDue() > 0)
            ->take(60)
            ->values();

        $this->withWeights($invoices);

        return view('dashboards.invoices.merge', [
            'customers' => Customer::where('client_id', $clientId)->orderBy('customer_firstname')->get(),
            'selectedCustomer' => (int) $data['customer_id'],
            'invoices' => $invoices,
            'preview' => $this->merge->preview($clientId, $data['invoice_ids']),
            'selectedIds' => $data['invoice_ids'],
            'recent' => MergedPayment::where('client_id', $clientId)->with('lines')->latest('id')->limit(10)->get(),
        ]);
    }

    /** Total weight of the cargo on each invoice, so the list can show it before the preview. */
    private function withWeights($invoices): void
    {
        foreach ($invoices as $invoice) {
            $invoice->weight = Loadpackage::weightForInvoice($invoice->id);
        }
    }

    public function store(Request $request): RedirectResponse
    {
        $this->allow('update invoices');

        $data = $request->validate([
            'customer_id' => ['required', 'integer'],
            'invoice_ids' => ['required', 'array', 'min:1'],
            'invoice_ids.*' => ['integer'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', 'in:CASH,BANK,MOBILE_MONEY,CHEQUE,OTHER'],
            'reference_no' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:250'],
        ]);

        $clientId = $this->clientId();

        abort_unless(
            Customer::where('client_id', $clientId)->whereKey($data['customer_id'])->exists(),
            404
        );

        // Only this tenant's invoices may be merged.
        $count = Invoice::where('client_id', $clientId)
            ->where('customer_id', $data['customer_id'])
            ->whereIn('id', $data['invoice_ids'])
            ->count();

        abort_unless($count === count($data['invoice_ids']), 422, 'One of the invoices does not belong to this customer.');

        $payment = $this->merge->apply($clientId, $data['invoice_ids'], $data);

        $message = 'Merged payment of ' . number_format((float) $payment->amount, 2) . ' recorded on ' . $payment->lines->count() . ' invoice(s).';

        if ($payment->minimum_applied) {
            $message .= ' The 1 KG minimum was applied (combined weight ' . $payment->combined_weight . ' KG → billable ' . $payment->billable_weight . ' KG).';
        }

        return redirect()->route('invoices.merge.create')->with('success', $message);
    }
}
