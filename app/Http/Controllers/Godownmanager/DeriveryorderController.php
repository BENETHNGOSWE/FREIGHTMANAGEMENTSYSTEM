<?php

namespace App\Http\Controllers\Godownmanager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeriveryOrder;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use Barryvdh\DomPDF\Facade\Pdf;

class DeriveryorderController extends Controller
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

    public function index()
    {
        $this->data['deriveryorder'] = DeriveryOrder::where('client_id', $this->fms_id)->get();
        return view('dashboards.godownmanager.deriveryorders.index', $this->data);
    }

    public function create()
    {
        // $this->authorize('create', Loadpackagetype::class);
        $this->data['invoices'] = Invoice::with('customer')->pluck('invoice_no', 'id');
        return view('dashboards.godownmanager.deriveryorders.create', $this->data);
    }

    public function validated_deriveryorder(Request $request, DeriveryOrder $deriveryorder = null): array
    {
        return $request->validate([
            'driver_name' => ['required', 'string', 'max:255'],
            'driver_number' => ['required', 'string', 'max:255'],
            'vehicle_reg_no' => ['required', 'string', 'max:255'],
            'invoice_id' => ['required', 'string', 'exists:invoices,id'],
        ]);
    }

    public function store(Request $request)
    {
        // $this->authorize('create', Loadpackagetype::class);
        $validated = $this->validated_deriveryorder($request);
        try {
            DB::beginTransaction();
            $deriveryorder = new DeriveryOrder();
            $validated['client_id'] = $this->fms_id;
            $deriveryorder->fill($validated);
            $deriveryorder->save();
            DB::commit();
        } catch (\Throwable $th) {
            \DB::rollback();
            \Log::error($th->getMessage() . '' . $th->getTraceAsString());
            return redirect()->back()->with('error', 'Something went wrong');
        }
        return redirect()->route('godownmanager.list.deriveryorder')->with('success', 'Derivery Order Created Successfuly');
    }

    public function show(Request $request, Loadpackagetype $loadpackagetype): View
    {
        $this->authorize('view', $loadpackagetype);
        $this->data['loadpackagetype'] = $loadpackagetype;
        return view('dashboards.admin.loadpackagetypes.show', $this->data);
    }

    public function edit(Loadpackagetype $loadpackagetype)
    {
        $this->authorize('update', $loadpackagetype);
        $this->data['loadpackagetype'] = $loadpackagetype;
        return view('dashboards.admin.loadpackagetypes.edit', $this->data);
    }

    public function update(Request $request, Loadpackagetype $loadpackagetype)
    {
        $this->authorize('update', $loadpackagetype);
        $validated = $request->validate([
            'load_type_name' => ['required', 'string', 'max:255'],
            'load_type_description' => ['nullable', 'string', 'max:255'],
        ]);
        $validated['client_id'] = $this->fms_id;
        $loadpackagetype->update($validated);
        return redirect()->route('admin.list.loadpackagetpye')->with('success', 'Load Type Updated Successfuly');
    }

    public function delete(Request $request, $Loadpackagetype)
    {
        $Loadpackagetype = Loadpackagetype::where('id', $Loadpackagetype)
            ->where('client_id', $this->fms_id)
            ->first();
        $this->authorize('delete', $Loadpackagetype);
        $Loadpackagetype->delete();
        return redirect()->route('admin.list.loadpackagetpye')->with('success', 'Load Type Successfuly deleted');
    }

    public function generateDeriveryOrder($id)
    {
        $deliveryOrder = DeriveryOrder::with('invoice.invoiceLoads.loadpackage', 'invoice.customer')->findOrFail($id);
        $client = $deliveryOrder->client;
        $customer = $deliveryOrder->invoice->customer;

        $pdf = Pdf::loadView('dashboards.godownmanager.deriveryorders.deliveryorderpdf', [
            'deliveryOrder' => $deliveryOrder,
            'client' => $client,
            'customer' => $customer,
        ]);

        return $pdf->download('delivery-order-' . $deliveryOrder->invoice->invoice_no . '.pdf');
    }
}
