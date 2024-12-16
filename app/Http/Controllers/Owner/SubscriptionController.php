<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kodapackage;
use App\Models\Client;
use App\Models\Subscription;
use App\Models\SubscriptionInvoice;
use App\Models\SubscriptionToken;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;


class SubscriptionController extends Controller
{
    protected $data = [];

    public function __construct()
    {
        $this->middleware('owner');
        $this->data['kodapackages'] = Kodapackage::pluck('name', 'id');
        $this->data['clients'] = Client::pluck('client_name', 'id');
    }

    //
    public function list_subscriptions()
    {
        $this->data['subscriptions'] = Subscription::all();
        $this->data['page_title'] = 'Subscription';

        return view('dashboards.owner.subscriptions.index', $this->data);
    }

    public function create()
    {
        $this->authorize('create', Subscription::class);
        $this->data['kodapackages'] = Kodapackage::pluck('name', 'id');
        $this->data['page_title'] = 'Add subscription';

        return view('dashboards.owner.subscriptions.create', $this->data);
    }


    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Subscription::class);

        $validated = $request->validate([
            'hospital_id' => ['required', 'max:255'],
            'package_id' => ['required', 'max:255', 'exists:packages,id'],
            'start_date' => ['required', 'max:255'],
            'duration_in_month' => ['required', 'integer', 'max:48'],
        ]);

        //check package price
        $package = Package::find($request->package_id);
        $validated['package_name'] = $package->package_name;

        $monthly_fee = $package->package_monthly_fee;
        $yearly_fee = $package->package_year_fee;
        $duration = $validated['duration_in_month'];

        $yearly = floor($duration / 12);
        $monthly = $duration % 12;

        $balance = 0;

        if ($yearly > 0) {
            $balance += $yearly * $yearly_fee;
        }

        if ($monthly > 0) {
            $balance += $monthly * $monthly_fee;
        }

        //calculate expiry date
        $start_date = $validated['start_date'];
        $expiry_date = date('Y-m-d', strtotime($start_date.' + '.$duration.' months'));
        $validated['expire_date'] = $expiry_date;

       

        DB::beginTransaction();
        try {
            $subscription = Subscription::create($validated);
            if ($subscription) {
                $invoiceData = [
                    'subscription_id' => $subscription->id,
                    'balance' => $balance,
                    'amount' => $balance,
                    'bill_date' => date('Y-m-d'),
                    'due_date' => date('Y-m-d', strtotime('+7 days')),
                    'prefix' => generateInvoicePrefix(),
                    'remark' => null,
                    'status' => '0',
                    'hospital_id' => $subscription->hospital_id,
                ];

                SubscriptionInvoice::create($invoiceData);

                $onboardhospital = new \App\Models\Onboardhospital();
                $onboardhospital->onboardhospital_name = $subscription->hospital->hospital_name;
                $onboardhospital->hospital_id = $subscription->hospital_id;
                $onboardhospital->package_id = $subscription->package_id;
                $onboardhospital->save();
                DB::commit();
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::error($th->getMessage().''.$th->getTraceAsString());

            return redirect()->back()->with('error', 'Something went wrong');
        }

        return to_route('list.subscriptions')->with('success', 'subscription added successfull');
    }

    public function show(Request $request, Subscription $subscription): View
    {
        $this->authorize('view', $subscription);

        return view('dashboards.owner.subscriptions.show', compact('subscription'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Subscription $subscription): View
    {
        $this->authorize('update', $subscription);

        return view('dashboards.owner.subscriptions.edit', compact('subscription'));
    }

    public function get_modules_by_id(Request $request)
    {
        $id = $request->package_id;
        // $package_modules = Package::find($id)->modules()->get();
        // $package_modules = PackageModules::where('package_id',$id)->with('module:id,module_name')->get(['module_id', 'package_id']);
        $package_modules = PackageModules::where('package_id', $id)
            ->join('modules', 'module_package.module_id', '=', 'modules.id')
            ->get(['module_package.module_id', 'module_package.package_id', 'modules.module_name']);

        $all_modules = Module::pluck('module_name', 'id');

        return response()->json(['package_modules' => $package_modules, 'all_modules' => $all_modules]);
    }

    public function store_package_by_ajax(Request $request)
    {
        if ($request->ajax()) {
            $package_name = $request->package_name;
            $package_description = $request->package_description;
            $package_monthly_fee = $request->package_monthly_fee;
            $package_year_fee = $request->package_year_fee;
            $modules = $request->modules;
            if ($package_name == '') {
                return response()->json(['status' => 'error', 'message' => 'Package Name is required']);
            } elseif ($package_description == '') {
                return response()->json(['status' => 'error', 'message' => 'Package Description is required']);
            } elseif ($package_monthly_fee == '') {
                return response()->json(['status' => 'error', 'message' => 'Package Monthly Fee is required']);
            } elseif ($package_year_fee == '') {
                return response()->json(['status' => 'error', 'message' => 'Package Yearly Fee is required']);
            } elseif ($modules == '') {
                return response()->json(['status' => 'error', 'message' => 'Package Modules is required']);
            } else {
                \DB::beginTransaction();
                try {
                    $package = new Package();
                    $package->package_name = $package_name;
                    $package->package_description = $package_description;
                    $package->package_monthly_fee = $package_monthly_fee;
                    $package->package_year_fee = $package_year_fee;
                    $package->save();
                    $package->modules()->sync($request->input('modules', []));
                    \DB::commit();
                } catch (\Throwable $th) {
                    \DB::rollback();
                    \Log::error($th->getMessage().''.$th->getTraceAsString());

                    return response()->json(['status' => 'error', 'message' => 'Something went wrong']);
                }

                return response()->json(['status' => 'success', 'message' => 'Package saved successfully']);
            }
        }
    }

    public function getSubscriptionDetails(Request $request)
    {
        $id = $request->id;
        $subscription = Subscription::where('subscriptions.id', $id)->join('hospitals', 'subscriptions.client_id', '=', 'hospitals.id')->join('packages', 'subscriptions.package_id', '=', 'packages.id')->join('subscription_invoices', 'subscriptions.id', '=', 'subscription_invoices.subscription_id')->select('subscriptions.id', 'subscriptions.start_date', 'subscriptions.expire_date', 'subscriptions.duration_in_month', 'subscriptions.package_name', 'subscriptions.client_id', 'subscriptions.package_id', 'hospitals.hospital_name', 'packages.package_name', 'packages.package_monthly_fee', 'packages.package_year_fee', 'subscription_invoices.balance', 'subscription_invoices.amount', 'subscription_invoices.bill_date', 'subscription_invoices.due_date', 'subscription_invoices.prefix', 'subscription_invoices.remark', 'subscription_invoices.status')->first();
        if ($subscription) {
            return response()->json(['status' => 'success', 'data' => $subscription]);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Subscription not found']);
        }
    }

    // $package_modules = PackageModules::where('package_id', $id)
    //         ->join('modules', 'module_package.module_id', '=', 'modules.id')
    //         ->get(['module_package.module_id', 'module_package.package_id', 'modules.module_name']);

    public function get_subscription_status()
    {
        $status = request('type_id');
        if ($status == '1') {
            $this->data['page_heading'] = 'Full Paid';
        } elseif ($status == '2') {
            $this->data['page_heading'] = 'Partial Paid ';
        } elseif ($status == '0') {
            $this->data['page_heading'] = 'Pending';
        } else {
            $this->data['page_heading'] = 'Subscription';
        }
        if ($status !== '') {
            $this->data['subscriptions'] = Subscription::where('status', $status)->get();
        } else {
            $this->data['subscriptions'] = Subscription::all();
        }

        return view('dashboards.owner.subscriptions.index', $this->data);
    }



    public function generate_subscriptions_tokens($id)
    {
        $token_ = generateSubscriptionToken();
        $subscription = Subscription::find($id);
        $invoice  = SubscriptionInvoice::where('subscription_id', $subscription->id)->first();
        if($subscription && $invoice){
            $data = [
                'token_'=> $token_,
                'subscription_id'=> $subscription->id,
                'client_id'=> $subscription->client_id,
                'invoice_id'=> $invoice->id,
                'active'=> 1,
             ];
             try {
                \DB::beginTransaction();
                //check if there is any token generated for this subscription before
                $token = SubscriptionToken::where('subscription_id', $subscription->id)->where('client_id', $subscription->client_id)->where('invoice_id', $invoice->id)->where('active', 1)->latest()->first();
                if($token){
                    return redirect()->back()->with('warning', 'This Subscription has Active token, "'.$token->token_.'" Inform your client to use that token for unlocking');

                }else{
                    $token = SubscriptionToken::create($data);
                }
                \DB::commit();
             } catch (\Throwable $th) {
                \DB::rollBack();
                \Log::error($th->getMessage().''.$th->getTraceAsString());
                return redirect()->back()->with('error', 'Something went wrong');
             }
        }
         

        return redirect()->back()->with('success', 'Tokens generated successfully');
    }
}

