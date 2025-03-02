<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customercategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomercategoryController extends Controller
{
    protected $fms_id = null;

    public function __construct()
    {
        // Use API auth middleware
        $this->middleware('auth:sanctum');
        $this->middleware(function ($request, $next) {
            if (Auth::check()) {
                $this->fms_id = Auth::user()->client_id;
            }
            return $next($request);
        });
    }

    /**
     * Get validated data for customer category
     */
    protected function validated_customercategory(Request $request): array
    {
        return $request->validate([
            'categoryname' => ['required', 'string', 'max:255'],
            'categorydescription' => ['nullable', 'string', 'max:255'],
        ]);
    }

    /**
     * Get all customer categories for the authenticated client
     */
    public function index()
    {
        $customercategories = Customercategory::where('client_id', $this->fms_id)->get();
        
        return response()->json([
            'success' => true,
            'data' => $customercategories
        ]);
    }

    /**
     * Create a new customer category
     */
    public function store(Request $request)
    {
        $validated = $this->validated_customercategory($request);
        
        try {
            DB::beginTransaction();
            
            $customer_category = new Customercategory();
            $validated['client_id'] = $this->fms_id;
            $customer_category->fill($validated);
            $customer_category->save();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Category Added Successfully',
                'data' => $customer_category
            ], 201);
        } catch (\Throwable $th) {
            DB::rollback();
            \Log::error($th->getMessage() . '' . $th->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific customer category
     */
    public function show($id)
    {
        $customercategory = Customercategory::where('id', $id)
            ->where('client_id', $this->fms_id)
            ->first();
            
        if (!$customercategory) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $customercategory
        ]);
    }

    /**
     * Update a specific customer category
     */
    public function update(Request $request, $id)
    {
        $customercategory = Customercategory::where('id', $id)
            ->where('client_id', $this->fms_id)
            ->first();
            
        if (!$customercategory) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        $validated = $this->validated_customercategory($request);
        $validated['client_id'] = $this->fms_id;
        
        try {
            $customercategory->update($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Customer Category Updated Successfully',
                'data' => $customercategory
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a specific customer category
     */
    public function destroy($id)
    {
        $customercategory = Customercategory::where('id', $id)
            ->where('client_id', $this->fms_id)
            ->first();
            
        if (!$customercategory) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        try {
            $customercategory->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Customer Category Deleted Successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}