<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Customer;
use App\Models\Client;
use App\Models\ClientAdmin;
use App\Models\Customerlead;
use App\Models\Customermessage;
use App\Models\Customercategory;
use App\Models\Sendername;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
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
        $this->data['messages'] = Message::where('client_id', $this->fms_id)->get();
        return view('dashboards.admin.messages.index', $this->data);
    }

    public function showSendMessageForm()
    {
        // $client_id = auth()->user()->client_id;
        $customers = Customer::where('client_id', $this->fms_id)->get();
        $this->data['customers'] = $customers;
        return view('dashboards.admin.messages.create', $this->data);
    }

    // ************************************************************************************
    // Multiple SenderID
    public function indexmultiple()
    {
        $this->data['multipesender'] = Message::where('client_id', $this->fms_id)->get();
        return view('dashboards.admin.messages.indexmultiple', $this->data);
    }

    public function showSendMessageFormmultiple()
    {
        // $client_id = auth()->user()->client_id;
        $customers = Customer::where('client_id', $this->fms_id)->get();
        $sendernames = Sendername::whereHas('clients', function ($query) {
            $query->where('clients.id', $this->fms_id);
        })->get();

        $this->data['customers'] = $customers;
        $this->data['sendernames'] = $sendernames;
        return view('dashboards.admin.messages.createmultiple', $this->data);
    }

    // ************************************************************************************
    // Customer Leads
    public function indexcustomerlead()
    {
        $this->data['customerleads'] = Customerlead::where('client_id', $this->fms_id)->get();
        return view('dashboards.kodasms.messages.indexlead', $this->data);
    }

    public function showSendMessageFormlead()
    {
        $customerleads = Customerlead::with('customercategory')
            ->where('client_id', $this->fms_id)
            ->get();
        $this->data['customerleads'] = $customerleads;

        $categories = Customercategory::all();
        $this->data['categories'] = $categories;

        $sendernames = Sendername::whereHas('clients', function ($query) {
            $query->where('clients.id', $this->fms_id);
        })->get();
        $this->data['sendernames'] = $sendernames;

        $clientMessageLimit = Customermessage::where('client_id', $this->fms_id)->value('messageamount');
        $messageCount = Message::where('client_id', $this->fms_id)->count();

        $this->data['canSendMessages'] = $messageCount < $clientMessageLimit;
        return view('dashboards.kodasms.messages.createleads', $this->data);
    }

    // ************************************************************************************

    public function sendMessage(Request $request)
    {
        $sendToAll = $request->input('send_to_all');
        $message = $request->input('message');
        $selectedPhones = $request->input('customers', []);

        $customers = $sendToAll
            ? Customer::where('client_id', $this->fms_id)->get()
            : Customer::whereIn('phone', $selectedPhones)
                ->where('client_id', $this->fms_id)
                ->get();

        if ($customers->isEmpty()) {
            return redirect()->back()->with('error', 'No customers selected.');
        }

        foreach ($customers as $customer) {
            $personalizedMessage = str_replace('{{ customer_firstname }}', $customer->customer_firstname, $message);
            $plainMessage = strip_tags($personalizedMessage);

            $response = sendMessage([$customer->phone], $plainMessage);

            Message::create([
                'customer_number' => $customer->phone,
                'content' => $plainMessage,
                'status' => $response['success'] ? 'delivered' : 'failed',
                'client_id' => $this->fms_id,
            ]);
        }

        return redirect()->back()->with('success', 'Messages processed. Check logs for delivery statuses.');
    }

    public function sendMessagemultipesenderid(Request $request)
    {
        $senderId = $request->input('sender_id');
        $sendToAll = $request->input('send_to_all');
        $message = $request->input('message');
        $selectedPhones = $request->input('customers', []);

        if ($sendToAll) {
            $customers = Customer::where('client_id', $this->fms_id)->get();
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

            $response = sendMessagemultipesenderid([$customer->phone], $plainMessage, $senderId);

            if (!$response['success']) {
                $allSuccess = false;
                $failedNumbers[] = $customer->phone;
            }

            Message::create([
                'customer_number' => $customer->phone,
                'content' => $plainMessage,
                'status' => $response['success'] ? 'delivered' : 'failed',
                'client_id' => $this->fms_id,
            ]);
        }

        if ($allSuccess) {
            return redirect()->back()->with('success', 'Messages sent successfully!');
        } else {
            $failedMessage = 'Some messages failed to send to the following numbers: ' . implode(', ', $failedNumbers);
            return redirect()->back()->with('error', $failedMessage);
        }
    }

    // ************************************
    public function sendMessageleadsenderid(Request $request)
    {
        $senderId = $request->input('sender_id');
        $sendToAll = $request->boolean('send_to_all');
        $message = $request->input('message');
        $selectedCustomers = $request->input('selected_customers', '');
    
        $request->validate([
            'sender_id' => 'required|string|max:11',
            'message' => 'required|string',
        ]);
    
        if ($sendToAll) {
            $customerleads = Customerlead::where('client_id', $this->fms_id)->get();
        } else {
            $selectedPhones = array_filter(explode(',', $selectedCustomers));
            if (empty($selectedPhones)) {
                return redirect()->back()->with('error', 'No customers selected.');
            }
            $customerleads = Customerlead::whereIn('phone', $selectedPhones)->get();
        }
    
        if ($customerleads->isEmpty()) {
            return redirect()->back()->with('error', 'No customers found.');
        }
    
        $allSuccess = true;
        $failedNumbers = [];
        $successCount = 0;
    
        foreach ($customerleads as $customer) {
            $personalizedMessage = str_replace('{{ customer_firstname }}', $customer->customer_firstname, $message);
            $plainMessage = strip_tags($personalizedMessage);
            $response = sendMessagemultipesenderid([$customer->phone], $plainMessage, $senderId);
    
            if (!$response['success']) {
                $allSuccess = false;
                $failedNumbers[] = $customer->phone;
            } else {
                $successCount++;
            }
    
            Message::create([
                'customer_number' => $customer->phone,
                'content' => $plainMessage,
                'status' => $response['success'] ? 'delivered' : 'failed',
                'client_id' => $this->fms_id,
            ]);
        }
    
        if ($allSuccess) {
            return redirect()->back()->with('success', "All messages sent successfully to {$successCount} customers!");
        } else {
            $failedMessage = "Some messages failed to send to the following numbers: " . implode(', ', $failedNumbers);
            return redirect()->back()->with([
                'error' => $failedMessage,
                'success' => "{$successCount} messages sent successfully.",
            ]);
        }
    }
    
}
