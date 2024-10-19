<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MessageController extends Controller
{
    
    public function showSendMessageForm()
    {
        return view('dashboards.admin.messages.create');
    }

  
    public function sendMessage(Request $request)
    {
        $phoneNumbers = $request->input('phone_number');
        $message = $request->input('message');
       
    
       
        $response = sendMessage($phoneNumbers, $message);
    
        if ($response['success']) {
            return redirect()->back()->with('success', $response['message']);
        } else {
            return redirect()->back()->with('error', $response['message']);
        }
    }
}
