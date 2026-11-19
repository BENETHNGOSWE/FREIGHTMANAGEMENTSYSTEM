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
use App\Services\Platform\SubscriptionService;

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

    /**
     * The SMS allowance of this client: the subscription's quota minus what was already sent.
     * When it is finished, sending must stop - that is the whole "assign message amount" rule.
     */
    private function guardSmsAllowance(int $howMany = 1): ?\Illuminate\Http\RedirectResponse
    {
        $service = app(SubscriptionService::class);
        $client = optional(auth()->user())->client;

        if (! $client) {
            return null;
        }

        if (! $service->isUsable($client)) {
            return redirect()->route('subscription.unlock')
                ->with('error', 'Your subscription has ended, so SMS cannot be sent. Enter the unlock token to continue.');
        }

        if ($service->smsQuota($client) <= 0) {
            return redirect()->back()->with('error', 'This account has no SMS balance. Ask your provider to add SMS to the subscription.');
        }

        if ($service->smsLeft($client) < $howMany) {
            return redirect()->back()->with('error', sprintf(
                'SMS balance finished: %s of %s used. Ask your provider to renew before sending again.',
                number_format($service->smsUsed($client)),
                number_format($service->smsQuota($client))
            ));
        }

        return null;
    }

    /** Shared by every send action: stop when the balance cannot cover this batch. */
    private function smsBalanceBlocked(\Illuminate\Support\Collection $recipients): ?\Illuminate\Http\RedirectResponse
    {
        return $this->guardSmsAllowance(max($recipients->count(), 1));
    }

    public function index()
    {
        $this->data['messages'] = Message::where('client_id', $this->fms_id)->get();
        $this->data['smsQuota'] = app(SubscriptionService::class)->smsQuota(optional(auth()->user())->client);
        $this->data['smsUsed'] = app(SubscriptionService::class)->smsUsed(optional(auth()->user())->client);
        return view('dashboards.admin.messages.index', $this->data);
    }

    public function showSendMessageForm()
    {
        // $client_id = auth()->user()->client_id;
        $customers = Customer::where('client_id', $this->fms_id)->get();
        $this->data['customers'] = $customers;
        return view('dashboards.admin.messages.create', $this->data);
    }


    public function sendMessage(Request $request)
    {
        $sendToAll = $request->input('send_to_all');
        $message = $request->input('message');
        $selectedPhones = $request->input('customers', []);

        $customers = $sendToAll ? Customer::where('client_id', $this->fms_id)->get() : Customer::whereIn('phone', $selectedPhones)->where('client_id', $this->fms_id)->get();

        if ($customers->isEmpty()) {
            return redirect()->back()->with('error', 'No customers selected.');
        }

        // Subscription + SMS balance: no messages once the allowance is finished.
        if ($blocked = $this->smsBalanceBlocked($customers)) {
            return $blocked;
        }

        // The sender ID is required by the SMS gateway; it comes from the active name of
        // this client (Settings -> Active Name).
        $activeName = \App\Models\Clientactivename::where('client_id', $this->fms_id)->first();
        $senderId = $activeName
            ? \App\Models\Sendername::find($activeName->sendername_id)->name ?? null
            : null;

        foreach ($customers as $customer) {
            $personalizedMessage = str_replace('{{ customer_firstname }}', $customer->customer_firstname, $message);
            $plainMessage = strip_tags($personalizedMessage);

            // A chat message is an explicit action by a staff member, so it is not affected by
            // the automatic "Customer SMS Notifications" switch. One failed number must not
            // stop the rest of the batch.
            try {
                $response = sendMessage([$customer->phone], $plainMessage, $senderId);
                $delivered = (bool) ($response['success'] ?? false);
            } catch (\Throwable $e) {
                \Log::warning('Manual SMS failed for ' . $customer->phone . ': ' . $e->getMessage());
                $delivered = false;
            }

            Message::create([
                'customer_number' => $customer->phone,
                'content' => $plainMessage,
                'status' => $delivered ? 'delivered' : 'failed',
                'client_id' => $this->fms_id,
            ]);
        }

        return redirect()->back()->with('success', 'Messages processed. Check logs for delivery statuses.');
    }








    // *********************************** Chat with existing Customer Logic Starts Here *************************************************
   
    public function existinCustomerIndex()
    {
        $this->data['multipesender'] = Message::where('client_id', $this->fms_id)->get();
        return view('dashboards.admin.messages.indexmultiple', $this->data);
    }

    public function showsendMessageToExistingCustomerform()
    {
        // $client_id = auth()->user()->client_id;
        $customers = Customer::where('client_id', $this->fms_id)->get();
        $sendernames = Sendername::whereHas('clients', function ($query) {
            $query->where('clients.id', $this->fms_id);
        })->get();

        $clientMessageLimit = Customermessage::where('client_id', $this->fms_id)->value('messageamount');
        $messageCount = Message::where('client_id', $this->fms_id)->count();

        $this->data['canSendMessages'] = $messageCount < $clientMessageLimit;

        $this->data['customers'] = $customers;
        $this->data['sendernames'] = $sendernames;
        return view('dashboards.admin.messages.createmultiple', $this->data);
    }

    public function sendMessageToExistingCustomer(Request $request)
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
            $customers = Customer::where('client_id', $this->fms_id)
            ->whereIn('phone', $selectedPhones)
            ->get();
        }

        if ($customers->isEmpty()) {
            return redirect()->back()->with('error', 'No customers found.');
        }

        if ($blocked = $this->smsBalanceBlocked($customers)) {
            return $blocked;
        }

        $allSuccess = true;
        $failedNumbers = [];

        foreach ($customers as $customer) {
            $personalizedMessage = str_replace('{{ customer_firstname }}', $customer->customer_firstname, $message);

            $plainMessage = strip_tags($personalizedMessage);

            $response = sendMessageToExistingCustomer([$customer->phone], $plainMessage, $senderId);

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








    // ******************************** Chat with Leads Logic Starts Here *******************************************************

    public function indexcustomerlead()
    {
        $this->data['customerleads'] = Customerlead::where('client_id', $this->fms_id)->get();
        return view('dashboards.kodasms.messages.indexlead', $this->data);
    }

    public function showSendMessageFormlead()
    {
        $roleAllowedToView = ['admin', 'kodasmspackage','receptionist'];

        if (auth()->user()->hasAnyRole($roleAllowedToView)) {
            $customerleads = Customerlead::with('customercategories')->where('client_id', $this->fms_id)->get();
        } else {
            $customerleads = Customerlead::with('customercategories')
                ->where('client_id', $this->fms_id)
                ->where('user_id', auth()->id())
                ->get();
        }
        $this->data['customerleads'] = $customerleads;

        $categories = Customercategory::where('client_id', $this->fms_id)->get();
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
    
    // public function sendMessageleadsenderid(Request $request)
    // {
    //     $senderId = $request->input('sender_id');
    //     $sendToAll = $request->boolean('send_to_all');
    //     $message = $request->input('message');
    //     $selectedCustomers = $request->input('selected_customers', '');

    //     $request->validate([
    //         'sender_id' => 'required|string|max:11',
    //         'message' => 'required|string',
    //     ]);

    //     if ($sendToAll) {
    //         $customerleads = Customerlead::where('client_id', $this->fms_id)->get();
    //     } else {
    //         $selectedPhones = array_filter(explode(',', $selectedCustomers));
    //         if (empty($selectedPhones)) {
    //             return redirect()->back()->with('error', 'No customers selected.');
    //         }
    //         $customerleads = Customerlead::where('client_id', $this->fms_id)
    //         ->whereIn('phone', $selectedPhones)
    //         ->get();
    //     }

    //     if ($customerleads->isEmpty()) {
    //         return redirect()->back()->with('error', 'No customers found.');
    //     }

    //     $allSuccess = true;
    //     $failedNumbers = [];
    //     $successCount = 0;

    //     foreach ($customerleads as $customer) {
    //         $personalizedMessage = str_replace('{{ customer_firstname }}', $customer->customer_firstname, $message);
    //         $plainMessage = strip_tags($personalizedMessage);
    //         $response = sendMessageLeadPending([$customer->phone], $plainMessage, $senderId);

    //         if (!$response['success']) {
    //             $allSuccess = false;
    //             $failedNumbers[] = $customer->phone;
    //         } else {
    //             $successCount++;
    //         }

    //         Message::create([
    //             'customer_number' => $customer->phone,
    //             'content' => $plainMessage,
    //             'status' => $response['success'] ? 'delivered' : 'failed',
    //             'client_id' => $this->fms_id,
    //         ]);
    //     }

    //     if ($allSuccess) {
    //         return redirect()
    //             ->back()
    //             ->with('success', "All messages sent successfully to {$successCount} customers!");
    //     } else {
    //         $failedMessage = 'Some messages failed to send to the following numbers: ' . implode(', ', $failedNumbers);
    //         return redirect()
    //             ->back()
    //             ->with([
    //                 'error' => $failedMessage,
    //                 'success' => "{$successCount} messages sent successfully.",
    //             ]);
    //     }
    // }


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
    
        $isAdmin = auth()->user()->hasAnyRole(['admin', 'kodasmspackage']);
    
        if ($sendToAll) {
            if ($isAdmin) {
                $customerleads = Customerlead::where('client_id', $this->fms_id)->get();
            } else {
                $customerleads = Customerlead::where('client_id', $this->fms_id)
                    ->where('user_id', auth()->id())
                    ->get();
            }
        } else {
            $selectedPhones = array_filter(explode(',', $selectedCustomers));
            if (empty($selectedPhones)) {
                return redirect()->back()->with('error', 'No customers selected.');
            }
            
            if ($isAdmin) {
                $customerleads = Customerlead::where('client_id', $this->fms_id)
                    ->whereIn('phone', $selectedPhones)
                    ->get();
            } else {
                $customerleads = Customerlead::where('client_id', $this->fms_id)
                    ->where('user_id', auth()->id())
                    ->whereIn('phone', $selectedPhones)
                    ->get();
            }
        }
    
        if ($customerleads->isEmpty()) {
            return redirect()->back()->with('error', 'No customers found.');
        }

        if ($blocked = $this->smsBalanceBlocked($customerleads)) {
            return $blocked;
        }

        $messageCount = 0;
    
        foreach ($customerleads as $customer) {
            $personalizedMessage = str_replace('{{ customer_firstname }}', $customer->customer_firstname, $message);
            $plainMessage = strip_tags($personalizedMessage);
    
            Message::create([
                'customer_number' => $customer->phone,
                'content' => $plainMessage,
                'status' => 'pending',
                'sender_id' => $senderId,
                'client_id' => $this->fms_id,
            ]);
    
            $messageCount++;
        }
    
        return redirect()->back()->with('success', "$messageCount messages queued for sending. They will be processed shortly.");
    }
}
