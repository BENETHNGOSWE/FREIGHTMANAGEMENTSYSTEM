<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Demorequest;

class DemorequestController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'full_name' => 'required|string|max:255',
            'email_address' => 'required|email|unique:demorequests,email_address|max:255',
            'phone_number' => 'required|string|max:20',
            'company_name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $demorequest = Demorequest::create($validatedData);
        return response()->json(
            [
                'message' => 'Demo request submitted Successfully',
                'data' => $demorequest,
            ],
            Response::HTTP_CREATED,
        );
    }
}
