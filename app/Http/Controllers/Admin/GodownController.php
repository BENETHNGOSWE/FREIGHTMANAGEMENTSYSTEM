<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\GodownAddress;
use App\Models\ClientAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;

class GodownController extends Controller
{
    protected $data = [];

    protected $fms_id = null;

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
        $this->data['godownaddresses'] = GodownAddress::where('client_id', $this->fms_id)->get();
        return view('dashboards.admin.godownaddress.index', $this->data);
    }

    public function create()
    {
        $this->authorize('create', GodownAddress::class);
        return view('dashboards.admin.godownaddress.create', $this->data);
    }

    public function validated_godownaddress(Request $request, GodownAddress $godownaddress = null): array
    {
        return $request->validate([
            'godown_location' => ['required', 'string', 'max:255'],
            'godown_address_one' => ['required', 'string', 'max:255'],
            'godown_address_two' => ['string', 'max:255'],
            'godown_phonenumber' => ['required', 'string', 'max:255'],
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', GodownAddress::class);
        $validated = $this->validated_godownaddress($request);
        try {
            DB::beginTransaction();
            $godownaddress = new GodownAddress();
            $validated['client_id'] = $this->fms_id;
            $godownaddress->fill($validated);
            $godownaddress->save();
            DB::commit();
        } catch (\Throwable $th) {
            \DB::rollback();
            \Log::error($th->getMessage() . '' . $th->getTraceAsString());
            return redirect()->back()->with('error', 'Something went wrong');
        }
        return redirect()->route('admin.list.godownaddress')->with('success', 'godownaddress Added Successfuly');
    }

    public function show(Request $request, GodownAddress $godownaddress): View
    {
        $this->authorize('view', $godownaddress);
        $this->data['godownaddress'] = $godownaddress;
        return view('dashboards.admin.godownaddress.show', $this->data);
    }

    public function edit(GodownAddress $godownaddress)
    {
        $this->authorize('update', $godownaddress);
        $this->data['godownaddress'] = $godownaddress;
        return view('dashboards.admin.godownaddress.edit', $this->data);
    }

    public function update(Request $request, GodownAddress $godownaddress)
    {
        $this->authorize('update', $godownaddress);
        $validated = $request->validate([
            'godown_location' => ['required', 'string', 'max:255'],
            'godown_address_one' => ['required', 'string', 'max:255'],
            'godown_address_two' => ['string', 'max:255'],
            'godown_phonenumber' => ['required', 'string', 'max:255'],
        ]);
        $validated['client_id'] = $this->fms_id;
        $godownaddress->update($validated);
        return redirect()->route('admin.list.godownaddress')->with('success', 'godownaddress Updated Successfuly');
    }

    public function delete(Request $request, $godownaddress)
    {
        $godownaddress = GodownAddress::where('id', $godownaddress)
            ->where('client_id', $this->fms_id)
            ->first();
        $this->authorize('delete', $godownaddress);
        $godownaddress->delete();
        return redirect()->route('admin.list.godownaddress')->with('success', 'godownaddress Deleted Successfuly ');
    }
}
