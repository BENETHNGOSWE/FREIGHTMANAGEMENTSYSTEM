<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\LoadPackageHelper;
use App\Models\Loadpackage;
use App\Models\Loadpackagetype;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\ClientAdmin;
use App\Models\Container;
use App\Models\InvoiceLoad;
use App\Models\UnitType;
use App\Models\Staff;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use App\Models\SystemSetting;


class LoadpackageController extends Controller
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
        if (auth()->user()->hasRole('admin')) {
            $this->data['loadpackages'] = Loadpackage::where('client_id', $this->fms_id)
                ->with('containers')
                ->get();
        } else {
            $this->data['loadpackages'] = auth()->user()->loadpackages;
        }

        return view('dashboards.admin.loadpackages.index', $this->data);
    }

    public function create(Request $request)
    {
        $client_id = auth()->user()->client_id;
        $this->authorize('create', Loadpackage::class);

        $customer_id = $request->input('customer_id');
        $this->data['loadpackagetypes'] = Loadpackagetype::where('client_id', $this->fms_id)->pluck('load_type_name', 'id');
        $this->data['unittypes'] = UnitType::where('client_id', $this->fms_id)->pluck('unittype_name', 'id');
        $this->data['containers'] = Container::where('client_id', $this->fms_id)->pluck('container_number', 'id');
        $this->data['customer_id'] = $customer_id;
        return view('dashboards.admin.loadpackages.create', $this->data);
    }

    public function validate_loadpackage(Request $request)
    {
        return $request->validate([
            'is_ocean.*' => 'nullable|boolean',
            'mode.*' => 'nullable|string|in:ocean,air',
            'load_name.*' => ['required', 'string', 'max:255'],
            'load_trucking_number.*' => ['required', 'string', 'max:255'],
            'load_description.*' => ['required', 'string', 'max:255'],
            'loadpackagetype_id.*' => ['required', 'integer', 'exists:loadpackagetypes,id'],
            'load_price.*' => ['required', 'string', 'max:255'],
            'load_transport_date.*' => ['required', 'date'],
            'load_delivery_date.*' => ['required', 'date'],
            'load_weight_value.*' => ['required', 'numeric'],
            'unittype_id.*' => ['required', 'max:255', 'exists:unit_types,id'],
            'container_id.*' => ['nullable', 'required_if:is_ocean.*,true', 'exists:containers,id'],
            'load_length.*' => ['nullable', 'numeric', 'min:0'],
            'load_width.*' => ['nullable', 'numeric', 'min:0'],
            'load_height.*' => ['nullable', 'numeric', 'min:0'],
            'load_volume.*' => ['nullable', 'numeric'],
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Loadpackage::class);
        $validated = $this->validate_loadpackage($request);

        try {
            DB::beginTransaction();

            $customerId = $request->input('customer_id');
            $clientId = $this->fms_id;
            $client = Client::find($clientId);

            $invoice = LoadPackageHelper::generateInvoice($customerId, $clientId);

            $userId = auth()->user()->id;
            $staff = Staff::where('user_id', $userId)->first();
            $branchCode = $staff ? $staff->branch->branch_code : 'TZ';

            foreach ($validated['load_name'] as $index => $load_name) {
                $load = LoadPackageHelper::createLoad($validated, $index, $clientId, $customerId, $userId, $branchCode, $invoice->id);
                LoadPackageHelper::updateInvoiceTotals($invoice, $load);

                $response = LoadPackageHelper::sendCustomerNotification(
                    $customerId,
                    $load->load_no,
                    $client->client_name,   
                    $load->load_weight_value,    
                    $load->load_price,   
                    $load->load_name,     
                    $load->unittype->unittype_name,    
                    $load->id
                );


                if (!$response['success']) {
                    return redirect()
                        ->back()
                        ->with('error', $response['message']);
                }
            }

                DB::commit();
            } catch (\Throwable $th) {
                DB::rollback();
                \Log::error($th->getMessage() . ' ' . $th->getTraceAsString());
                return redirect()->back()->with('error', $th->getMessage());
        }

        return redirect()->route('admin.list.loadpackage')->with('success', 'Load packages added and invoice generated successfully');
    }

    public function show(Request $request, Loadpackage $loadpackage): View
    {
        $this->authorize('view', $loadpackage);
        $this->data['loadpackage'] = $loadpackage;
        return view('dashboards.admin.loadpackages.show', $this->data);
    }

    public function edit(Request $request, Loadpackage $loadpackage): View
    {
        $this->authorize('update', $loadpackage);
        $this->data['loadpackage'] = $loadpackage;
        $this->data['unittypes'] = UnitType::pluck('unittype_name', 'id');
        $this->data['containers'] = Container::pluck('container_number', 'id');
        $this->data['loadpackagetypes'] = Loadpackagetype::pluck('load_type_name', 'id');
        return view('dashboards.admin.loadpackages.edit', $this->data);
    }

    public function update(Request $request, Loadpackage $loadpackage)
    {
        $this->authorize('update', $loadpackage);

        $oldPrice = $loadpackage->load_price;

        $validate = $request->validate([
            'load_name' => ['required', 'string', 'max:255'],
            'load_trucking_number' => ['required', 'string', 'max:255'],
            'load_description' => ['required', 'string', 'max:255'],
            'loadpackagetype_id' => ['required', 'integer', 'exists:loadpackagetypes,id'],
            'load_price' => ['required', 'string', 'max:255'],
            'load_transport_date' => ['required', 'date'],
            'load_delivery_date' => ['required', 'date'],
            'load_weight_value' => ['nullable', 'string', 'max:255'],
            'unittype_id' => ['required', 'max:255', 'exists:unit_types,id'],
            'container_id.*' => ['required', 'max:255', 'exists:containers,id'],
        ]);

        $loadpackage->update($validate);

        $priceDifference = floatval($validate['load_price']) - floatval($oldPrice);

        $invoiceLoad = InvoiceLoad::where('loadpackage_id', $loadpackage->id)->first();

        if ($invoiceLoad) {
            $invoice = Invoice::find($invoiceLoad->invoice_id);

            if ($invoice) {
                $invoice->total_amount += $priceDifference;
                $invoice->total_balance += $priceDifference;
                $invoice->save();

                $invoiceLoad->load_price = $validate['load_price'];
                $invoiceLoad->save();
            }
        }

        return redirect()->route('admin.list.loadpackage')->with('success', 'Load updated successfully');
    }

    public function delete(Request $request, $load)
    {
        $load = loadpackage::where('id', $load)
            ->where('client_id', $this->fms_id)
            ->first();
        $this->authorize('delete', $load);
        $load->delete();
        return redirect()->route('admin.list.loadpackage', $load)->with('success', 'load Deleted Successful');
    }

    public function loadtrackingpage()
    {
        return view('dashboards.admin.loadpackages.track', $this->data);
    }

    public function loadtracking(Request $request)
    {
        $request->validate([
            'load_no' => 'required|string|max:255',
        ]);
        $loadpackage = LoadPackage::where('load_no', $request->load_no)->first();
        if ($loadpackage) {
            $this->data['loadpackage'] = $loadpackage;
            return view('dashboards.admin.loadpackages.track', $this->data);
        } else {
            return redirect()->back()->with('error', 'Load not found. Please check your load number.');
        }
    }

    public function toggleNotification(Request $request)
    {
        $enabled = $request->input('enabled') === 'true' || $request->input('enabled') === true;
        
        session(['send_notifications' => $enabled]);
        
        return response()->json([
            'success' => true,
            'enabled' => $enabled,
            'message' => $enabled ? 'Notifications enabled' : 'Notifications disabled'
        ]);
    }
}


