<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Customer;

class MessageController extends Controller
{
    public function showSendMessageForm()
    {
        $customers = Customer::all();
        return view('dashboards.admin.messages.create', compact('customers'));
    }

    public function sendMessage(Request $request)
    {
        $sendToAll = $request->input('send_to_all');
        $message = $request->input('message');
        $selectedPhones = $request->input('customers', []);

        if ($sendToAll) {
            $customers = Customer::all();
        } else {
            $customers = Customer::whereIn('phone', $selectedPhones)->get();
        }

        if ($customers->isEmpty()) {
            return redirect()->back()->with('error', 'No customers selected.');
        }

        $success = true;
        foreach ($customers as $customer) {
            $personalizedMessage = str_replace('{{ customer_firstname }}', $customer->customer_firstname, $message);
            $plainMessage = strip_tags($personalizedMessage);
            $response = sendMessage([$customer->phone], $plainMessage);
            if (!$response['success']) {
                $success = false;
            }
        }

        return redirect()
            ->back()
            ->with($success ? 'success' : 'error', $success ? 'Messages sent successfully' : 'Some messages failed to send');
    }



    public function sendMessagemultipesenderid(Request $request)
    {
        $senderId = $request->input('sender_id');
        $sendToAll = $request->input('send_to_all');
        $message = $request->input('message');
        $selectedPhones = $request->input('customers', []);
    
        if ($sendToAll) {
            $customers = Customer::all();
        } else {
            if (empty($selectedPhones)) {
                return redirect()->back()->with('error', 'No customers selected.');
            }
            $customers = Customer::whereIn('phone', $selectedPhones)->get();
        }
    
        if ($customers->isEmpty()) {
            return redirect()->back()->with('error', 'No customers found.');
        }
    
        $allSuccess = true;
        $failedNumbers = [];
    
        foreach ($customers as $customer) {
            $personalizedMessage = str_replace('{{ customer_firstname }}', $customer->customer_firstname, $message);
            
            $plainMessage = strip_tags($personalizedMessage);
    
            $response = sendMessage([$customer->phone], $plainMessage, $senderId);
    
            if (!$response['success']) {
                $allSuccess = false;
                $failedNumbers[] = $customer->phone;
            }
        }
    
        if ($allSuccess) {
            return redirect()->back()->with('success', 'Messages sent successfully!');
        } else {
            $failedMessage = 'Some messages failed to send to the following numbers: ' . implode(', ', $failedNumbers);
            return redirect()->back()->with('error', $failedMessage);
        }
    }
}
