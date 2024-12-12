<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Customer;
use App\Models\ClientAdmin;
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

    public function index(){
        $this->data['messages'] = Message::where('client_id', $this->fms_id)->get();
        return view('dashboards.admin.messages.index', $this->data);
    }

    public function indexmultiple(){
        $this->data['multipesender'] = Message::where('client_id', $this->fms_id)->get();
        return view('dashboards.admin.messages.indexmultiple', $this->data);
    }

    public function showSendMessageForm()
    {
        // $client_id = auth()->user()->client_id;
        $customers = Customer::where('client_id', $this->fms_id)->get();
        $this->data['customers'] = $customers;
        return view('dashboards.admin.messages.create', $this->data);
    }

    public function showSendMessageFormmultiple()
    {
        // $client_id = auth()->user()->client_id;
        $customers = Customer::where('client_id', $this->fms_id)->get();
        $this->data['customers'] = $customers;
        return view('dashboards.admin.messages.createmultiple', $this->data);
    }

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
        }

        if ($allSuccess) {
            return redirect()->back()->with('success', 'Messages sent successfully!');
        } else {
            $failedMessage = 'Some messages failed to send to the following numbers: ' . implode(', ', $failedNumbers);
            return redirect()->back()->with('error', $failedMessage);
        }
    }
}
