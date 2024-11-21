<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    protected $data = [];

    protected $fms_id = null;

    public function __construct()
    {
        $this->middleware('multiple');
        $this->middleware(function ($request, $next) {
            if (Auth::check()) {
                $this->fms_id = Auth::user()->client_id;
            }

            return $next($request);
        });
    }

    public function index()
    {
        $this->data['payments'] = Payment::where('client_id', $this->fms_id)->get();
        return view('dashboards.admin.payments.index', $this->data);
    }

    public function create()
    {
        // $this->authorize('create', Payment::class);
        return view('dashboards.admin.payments.create', $this->data);
    }

    public function validated_payment(Request $request, Payment $payment = null): array
    {
        return $request->validate([
            'Bank_name' => ['required', 'string', 'max:255'],
            'account_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:255'],
        ]);
    }

    public function store(Request $request)
    {
        // $this->authorize('create', Payment::class);
        $validated = $this->validated_payment($request);
        try {
            DB::beginTransaction();
            $payment = new Payment();
            $validated['client_id'] = $this->fms_id;
            $payment->fill($validated);
            $payment->save();
            DB::commit();
        } catch (\Throwable $th) {
            \DB::rollback();
            \Log::error($th->getMessage() . '' . $th->getTraceAsString());
            return redirect()->back()->with('error', 'Something went wrong');
        }
        return redirect()->route('admin.list.payment')->with('success', 'payment Added Successfuly');
    }

    public function show(Request $request, Payment $payment): View
    {
        $this->authorize('view', $payment);
        $this->data['payment'] = $payment;
        return view('dashboards.admin.payments.show', $this->data);
    }

    public function edit(Payment $payment)
    {
        $this->authorize('update', $payment);
        $this->data['payment'] = $payment;
        return view('dashboards.admin.payments.edit', $this->data);
    }

    public function update(Request $request, Payment $payment)
    {
        $this->authorize('update', $payment);
        $validated = $request->validate([
            'Bank_name' => ['required', 'string', 'max:255'],
            'account_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:255'],
        ]);
        $validated['client_id'] = $this->fms_id;
        $payment->update($validated);
        return redirect()->route('admin.list.payment')->with('success', 'payment Updated Successfuly');
    }

    public function delete(Request $request, $payment)
    {
        $payment = Payment::where('id', $payment)
            ->where('client_id', $this->fms_id)
            ->first();
        $this->authorize('delete', $payment);
        $payment->delete();
        return redirect()->route('admin.list.payment')->with('success', 'payment Deleted Successfuly ');
    }
}