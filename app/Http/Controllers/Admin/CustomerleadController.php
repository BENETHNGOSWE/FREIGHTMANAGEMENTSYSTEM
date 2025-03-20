<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Customerlead;
use App\Models\Customercategory;
use App\Models\Client;
use App\Models\ClientAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;

class CustomerleadController extends Controller
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
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roleAllowedToView = ['admin', 'kodasmspackage'];

        if (auth()->user()->hasAnyRole($roleAllowedToView)) {
            $this->data['customerleads'] = Customerlead::with('user')->where('client_id', $this->fms_id)->get();
        } else {
            $this->data['customerleads'] = auth()->user()->customerleads()->with('user')->get();
        }

        return view('dashboards.admin.customerleads.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Customerlead::class);
        $client_id = auth()->user()->client_id;
        $clients = Client::pluck('client_name', 'id');
        $users = User::pluck('id');
        $this->data['customercategories'] = Customercategory::where('client_id', $this->fms_id)->pluck('categoryname', 'id');

        $this->data['clients'] = $clients;
        $this->data['users'] = $users;

        return view('dashboards.admin.customerleads.create', $this->data);
    }

    public function validate_customerlead(Request $request, $id = null)
    {
        return $request->validate([
            'customer_firstname' => ['required', 'max:255', 'string'],
            'customer_middlename' => ['nullable', 'max:255', 'string'],
            'customer_lastname' => ['nullable', 'max:255', 'string'],
            'category_id.*' => ['exists:customercategories,id'],
            'customer_email' => [
                'nullable',
                'email',
                Rule::unique('customerleads', 'customer_email')
                    ->where(function ($query) {
                        return $query->where('client_id', $this->fms_id);
                    })
                    ->ignore($id, 'id'),
            ],
            'phone' => [
                'required',
                'max:255',
                'string',
                Rule::unique('customerleads', 'phone')
                    ->where(function ($query) {
                        return $query->where('client_id', $this->fms_id);
                    })
                    ->ignore($id, 'id'),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Customerlead::class);
        $validated = $this->validate_customerlead($request);

        try {
            DB::beginTransaction();

            $categoryIds = $validated['category_id'];
            unset($validated['category_id']);

            $customerlead = new Customerlead();
            $validated['client_id'] = $this->fms_id;
            $validated['user_id'] = auth()->id();
            $customerlead->fill($validated);
            $customerlead->save();

            $customerlead->customercategories()->attach($categoryIds);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::error($th->getMessage() . '' . $th->getTraceAsString());
            return redirect()->back()->with('error', 'Something went wrong');
        }

        return redirect()->route('admin.list.customerlead')->with('success', 'Customer Lead created Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customerlead $customerlead)
    {
        $this->data['customerlead'] = $customerlead;
        $this->data['customercategory'] = Customercategory::where('client_id', $this->fms_id)->get();
        return view('dashboards.admin.customerleads.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $customerlead = Customerlead::findOrFail($id); // Fetch the customer lead by ID
        $this->data['page_title'] = 'Edit Customer Lead';
        $client_id = auth()->user()->client_id;
        $this->data['customerlead'] = $customerlead;
        $this->data['editing'] = true;

        $this->data['clients'] = Client::pluck('client_name', 'id');
        $this->data['users'] = User::pluck('id');

        // Fetch customer categories for the current client
        $this->data['customercategories'] = Customercategory::where('client_id', $this->fms_id)->pluck('categoryname', 'id');

        return view('dashboards.admin.customerleads.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $customerlead = Customerlead::findOrFail($id);
        $this->authorize('update', $customerlead);

        $validated = $this->validate_customerlead($request, $id);

        try {
            DB::beginTransaction();

            $validated['client_id'] = $this->fms_id;
            $customerlead->update($validated);

            DB::commit();
            return redirect()->route('admin.list.customerlead')->with('success', 'Customer lead updated successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::error($th->getMessage() . ' ' . $th->getTraceAsString());
            return redirect()->back()->with('error', 'Something went wrong');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request, $customerlead)
    {
        $customerlead = Customerlead::where('id', $customerlead)->where('client_id', $this->fms_id)->first();
        $this->authorize('delete', $customerlead);
        $customerlead->delete();
        return redirect()->route('admin.list.customerlead', $customerlead)->with('success', 'Customer Lead Deleted Successful');
    }
}
