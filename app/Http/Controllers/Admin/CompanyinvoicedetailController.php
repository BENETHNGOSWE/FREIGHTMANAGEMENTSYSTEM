<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientAdmin;
use App\Models\CompanyInvoiceDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CompanyinvoicedetailController extends Controller
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
        $this->data['companyinvoicedetails'] = Companyinvoicedetail::where('client_id', $this->fms_id)->get();
        return view('dashboards.admin.companyinvoicedetails.index', $this->data);
    }

    public function create()
    {
        $this->authorize('create', Companyinvoicedetail::class);
        return view('dashboards.admin.companyinvoicedetails.create', $this->data);
    }

    public function validated_company_invoice_detail(Request $request, Companyinvoicedetail $Companyinvoicedetail = null): array
    {
        return $request->validate([
            'company_name' => ['nullable', 'string', 'max:255'],
            'office_phone' => ['required', 'string', 'max:255'],
            'tin_number' => ['nullable', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'file', 'image', 'max:255'],
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Companyinvoicedetail::class);
        $validated = $this->validated_company_invoice_detail($request);
        try {
            DB::beginTransaction();
    
            $company = new Companyinvoicedetail();
            $validated['client_id'] = $this->fms_id;
            $fillable = $company->getFillable();
    
            foreach ($fillable as $field) {
                if ($field !== 'logo') {
                    $company->$field = $validated[$field] ?? null;
                }
            }
    
            if ($request->hasFile('logo')) {
                $image = $request->file('logo');
                $filename = \Str::slug($validated['company_name'] ?? 'company') . '.' . $image->extension();
                upload_image($image, $filename, 200);
                $company->logo = $filename;
            } else {
                $company->logo = 'client_default.png';
            }
    
            $company->fill($validated);
            $company->save();
    
            DB::commit();
            return to_route('admin.list.companyinvoicedetail')->with('success', 'Company Invoice Details Added Successfully');
            
        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::error($th->getMessage() . '' . $th->getTraceAsString());
    
            return redirect()->back()->with('error', 'Something went wrong');
        }
    }

    public function show(Request $request, Companyinvoicedetail $Companyinvoicedetail): View
    {
        $this->authorize('view', $Companyinvoicedetail);
        $this->data['Companyinvoicedetail'] = $Companyinvoicedetail;
        return view('dashboards.admin.companyinvoicedetails.show', $this->data);
    }

    public function edit(Companyinvoicedetail $companyinvoicedetail)
    {
        $this->authorize('update', $companyinvoicedetail);
        $this->data['companyinvoicedetail'] = $companyinvoicedetail;
        return view('dashboards.admin.companyinvoicedetails.edit', $this->data);
    }

    public function update(Request $request, Companyinvoicedetail $companyinvoicedetail)
    {
        $this->authorize('update', $companyinvoicedetail);
        $validated = $this->validated_company_invoice_detail($request);
        
        if ($request->hasFile('logo')) {
            if ($companyinvoicedetail->logo) {
                Storage::disk('public')->delete($companyinvoicedetail->logo);
            }
            $logoPath = $request->file('logo')->store('company_logos', 'public');
            $validated['logo'] = $logoPath;
        } else {
            // Keep the existing logo by removing it from the validated array
            unset($validated['logo']);
        }
        
        $validated['client_id'] = $this->fms_id;
        $companyinvoicedetail->update($validated);
        return redirect()->route('admin.list.companyinvoicedetail')->with('success', 'Company Invoice Details Updated Successfully');
    }

    public function delete(Request $request, $companyinvoicedetail)
    {
        $companyinvoicedetail = Companyinvoicedetail::where('id', $companyinvoicedetail)->where('client_id', $this->fms_id)->first();
        $this->authorize('delete', $companyinvoicedetail);
        $companyinvoicedetail->delete();
        return redirect()->route('admin.list.companyinvoicedetail')->with('success', 'Company Invoice Details Deleted Successfully');
    }
}
