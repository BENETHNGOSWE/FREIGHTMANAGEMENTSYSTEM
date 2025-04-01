<?php

use App\Models\Message;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use App\Models\Clientactivename;


function trimValues(array $data): array
{
    return array_map('trim', $data);
}

function upload_image($image, $filename = null, $width = null, $height = null)
{
    $filename = $filename ? $filename : time() . '.' . 'jpg';

    $image_resize = Image::make($image->getRealPath());

    if ($width == null && $height == null) {
        if ($image->width() > $image->height()) {
            $image_resize->resize(920, null, function ($constraint) {
                $constraint->aspectRatio();
            });
        } else {
            $image_resize->resize(null, 540, function ($constraint) {
                $constraint->aspectRatio();
            });
        }
    } elseif ($height == null) {
        $image_resize->resize($width, null, function ($constraint) {
            $constraint->aspectRatio();
        });
    } elseif ($width == null) {
        $image_resize->resize(null, $height, function ($constraint) {
            $constraint->aspectRatio();
        });
    } else {
        $image_resize->resize($width, $height);
    }

    $path = public_path('storage/uploads/');
    if (!File::isDirectory($path)) {
        File::makeDirectory($path, 0755, true);
    }

    $quality = 80;
    do {
        $image_resize->encode('jpg', $quality);
        $image_resize->save($path . $filename);
        clearstatcache(); // Clear cache to get actual file size

        if (file_exists($path . $filename) && is_readable($path . $filename)) {
            $fileSize = filesize($path . $filename) / 1024;

            if ($fileSize > 100) {
                if ($quality - 10 < 0) {
                    // Ensure quality doesn't go below 0
                    break;
                }
                try {
                    $quality -= 10;
                    $image_resize->encode('jpg', $quality);
                } catch (InvalidArgumentException $e) {
                    echo 'Error: ' . $e->getMessage();
                }
            }
        } else {
            echo 'Error: File does not exist or is not readable.';
            break;
        }
    } while ($fileSize > 100);
}

function upload_file($file, $filename = null)
{
    $filename = $filename ? $filename : time() . '.' . $file->getClientOriginalExtension();
    $path = public_path('storage/uploads/');
    if (!File::isDirectory($path)) {
        File::makeDirectory($path, 0755, true);
    }
    $file->move($path, $filename);

    return $filename;
}

function delete_file($filename)
{
    $path = public_path('storage/uploads/');
    if (File::exists($path . $filename) && !File::isDirectory($path . $filename)) {
        File::delete($path . $filename);
    }
}

function generateInvoicePrefix()
{
    $numericPart = str_pad(mt_rand(0, 999), 3, '0', STR_PAD_LEFT);

    $capitalLetters = '';
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    for ($i = 0; $i < 3; $i++) {
        $capitalLetters .= $characters[rand(0, strlen($characters) - 1)];
    }
    $mixedPart = '';
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    for ($i = 0; $i < 3; $i++) {
        $mixedPart .= $characters[rand(0, strlen($characters) - 1)];
    }
    $prefix = 'HMS' . $numericPart . $capitalLetters . $mixedPart;

    return $prefix;
}

function return_status_badge($status)
{
    switch ($status) {
        case 'portstation':
            $badge = '<span class="badge badge-warning">Port Station</span>';
            break;
        case 'onmovement':
            $badge = '<span class="badge badge-info">On Movement</span>';
            break;
        case 'delivery':
            $badge = '<span class="badge badge-success">Delivery</span>';
            break;
        case 'closed':
            $badge = '<span class="badge badge-dark">Closed</span>';
            break;
        default:
            $badge = '<span class="badge badge-warning">Pending</span>';
    }

    return $badge;
}

function return_message_status_badge($status)
{
    switch ($status) {
        case 'pending':
            $badge = '<span class="badge badge-warning">Pending</span>';
            break;
            
        case 'delivered':
            $badge = '<span class="badge badge-success">Delivery</span>';
            break;
        default:
            $badge = '<span class="badge badge-warning">Pending</span>';
    }

    return $badge;
}


function user_status($status)
{
    match ($status) {
        '0' => ($badge = '<span class="badge badge-rounded badge-secondary" >Disabled</span>'),
        '1' => ($badge = '<span class="badge badge-rounded badge-success" >Active</span>'),
        '2' => ($badge = '<span class="badge badge-rounded badge-primary" >leave</span>'),
        '3' => ($badge = '<span class="badge badge-rounded badge-warning" >Sick</span>'),
        default => ($badge = '<span class="badge badge-rounded badge-info" >Normal</span>'),
    };
    return $badge;
}


function jason_date($date)
{
    // return date('d-m-Y', strtotime($date));
    return \Carbon\Carbon::parse($date)->format('d-m-Y');
}

function invoice_status($status)
{
    switch ($status) {
        case '1':
            $badge = '<span class="badge badge-rounded badge-outline-success" >Full paid</span>';
            break;
        case '2':
            $badge = '<span class="badge badge-rounded badge-outline-warning" >Partial Paid</span>';
            break;
        case '0':
            $badge = '<span class="badge badge-rounded badge-outline-danger" >Not paid</span>';
            break;

        default:
            $badge = '<span class="badge badge-rounded badge-outline-primary" >Nan</span>';
            break;
    }

    return $badge;
}

function generateSubscriptionToken($length = 30)
{
    $characters = '0123456tuw789ABCDEFizklksGHIJKLMdefmNOPQRSTUVWXYZacn';
    $charactersLength = strlen($characters);
    $token = '';
    for ($i = 0; $i < $length; $i++) {
        $token .= $characters[rand(0, $charactersLength - 1)];
    }

    return $token;
}

function createStaffidNumber($client_id)
{
    $staff = \App\Models\Staff::where('client_id', $client_id)->latest()->first();
    if ($staff) {
        $staff_id = $staff->staff_no;
        $staff_id = explode('-', $staff_id);
        $staff_id = $staff_id[1];
        $staff_id = $staff_id + 1;
        $staff_id = 'STF-' . $staff_id;
    } else {
        $staff_id = 'STF-1';
    }

    return $staff_id;
}

function createcustomeridNumber($client_id)
{
    $customer = \App\Models\Customer::where('client_id', $client_id)->latest()->first();
    if ($customer) {
        $customer_id = $customer->customer_no;
        $customer_id = explode('-', $customer_id);
        $customer_id = $customer_id[1];
        $customer_id = $customer_id + 1;
        $customer_id = 'CST-' . $customer_id;
    } else {
        $customer_id = 'CST-1';
    }

    return $customer_id;
}

function createLoadidNumber($client_id)
{
    $load = \App\Models\Loadpackage::where('client_id', $client_id)->latest()->first();
    if ($load) {
        $load_id = $load->load_id_number;
        $load_id = explode('-', $load_id);
        $load_id = $load_id[1];
        $load_id = $load_id + 1;
        $load_id = 'DOC-' . $load_id;
    } else {
        $load_id = 'DOC-1';
    }

    return $load_id;
}


function check_if_client_has_subscription($hospital_id)
{
    $subscription = \App\Models\Subscription::where('hospital_id', $hospital_id)->where('expire_date', '>=', date('Y-m-d'))->latest()->first();

    if ($subscription) {
        return true;
    } else {
        return false;
    }
}


// function sendSMS($user_id, $sms, $h_id = null)
// {
//     return Message::create([
//         'user_id' => $user_id,
//         'message' => $sms,
//         'hospital_id' => $h_id,
//         'status' => 0,
//     ]);
// }

use Carbon\Carbon;
function date_time($data)
{
    $carbonDate = Carbon::parse($data);
    $formattedDate = $carbonDate->format('d/m/Y, h:i A');
    return $formattedDate;
}

function getStatusLabel($status)
{
    switch ($status) {
        case 0:
            return 'Pending';
        case 1:
            return 'Full Paid';
        case 2:
            return 'Partial Paid';
        default:
            return 'Unknown Status';
    }
}


// dynamic senderid changing function
// function sendMessageToExistingCustomer($phoneNumbers, $message, $senderId)
// {
//     if (!is_array($phoneNumbers)) {
//         $phoneNumbers = explode(',', $phoneNumbers);
//     }

//     foreach ($phoneNumbers as $phoneNumber) {
//         $phoneNumber = trim($phoneNumber);
//         $postData = [
//             'from' => $senderId,
//             'to' => $phoneNumber,
//             'text' => $message,
//             'reference' => $senderId,
//         ];

//         $curl = curl_init();
//         curl_setopt_array($curl, [
//             CURLOPT_URL => 'https://messaging-service.co.tz/api/sms/v1/text/single',
//             CURLOPT_RETURNTRANSFER => true,
//             CURLOPT_ENCODING => '',
//             CURLOPT_MAXREDIRS => 10,
//             CURLOPT_TIMEOUT => 0,
//             CURLOPT_FOLLOWLOCATION => true,
//             CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//             CURLOPT_CUSTOMREQUEST => 'POST',
//             CURLOPT_POSTFIELDS => json_encode($postData),
//             CURLOPT_HTTPHEADER => ['Authorization:Basic ' . base64_encode(env('SMS_API_KEY') . ':' . env('SMS_SECRET_KEY')), 'Content-Type: application/json', 'Accept: application/json'],
//             CURLOPT_SSL_VERIFYPEER => false,
//         ]);

//         $response = curl_exec($curl);
//         curl_close($curl);

//         $response = json_decode($response, true);

//         if (isset($response['success']) && !$response['success']) {
//             return [
//                 'success' => false,
//                 'message' => 'Failed to send message to ' . $phoneNumber,
//             ];
//         } elseif ($response['messages'][0]['status']['groupName'] != 'PENDING') {
//             return [
//                 'success' => false,
//                 'message' => 'Failed to send message to ' . $phoneNumber,
//             ];
//         }
//     }
//     // Return success if all messages are sent
//     return [
//         'success' => true,
//         'message' => 'Messages sent successfully!',
//     ];
// }

function sendMessage($phoneNumbers, $message, $senderName)
{
    if (!is_array($phoneNumbers)) {
        $phoneNumbers = explode(',', $phoneNumbers);
    }

    foreach ($phoneNumbers as $phoneNumber) {
        $phoneNumber = trim($phoneNumber);
        $postData = [
            'from' => $senderName,
            'to' => $phoneNumber,
            'text' => $message,
            'reference' => 'KODA TECH',
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://messaging-service.co.tz/api/sms/v1/text/single',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_HTTPHEADER => ['Authorization:Basic ' . base64_encode(env('SMS_API_KEY') . ':' . env('SMS_SECRET_KEY')), 'Content-Type: application/json', 'Accept: application/json'],
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        $response = json_decode($response, true);

        if (isset($response['success']) && !$response['success']) {
            return [
                'success' => false,
                'message' => 'Failed to send message to ' . $phoneNumber,
            ];
        } elseif ($response['messages'][0]['status']['groupName'] != 'PENDING') {
            return [
                'success' => false,
                'message' => 'Failed to send message to ' . $phoneNumber,
            ];
        }
    }

    // Return success if all messages are sent
    return [
        'success' => true,
        'message' => 'Messages sent successfully!',
    ];
}
function sendMessageToExistingCustomer($phoneNumbers, $message, $senderId)
{
    // API credentials
    $apiKey = env('SMS_API_KEY2');
    $apiSecret = env('SMS_API_SECRET2');
    $url = "https://messaging.kilakona.co.tz/api/v1/vendor/message/send";

    // Prepare headers
    $headers = [
        "api_key: $apiKey",
        "api_secret: $apiSecret",
        "Content-Type: application/json"
    ];

    if (!is_array($phoneNumbers)) {
        $contacts = $phoneNumbers; 
    } else {
        $contacts = implode(',', array_map('trim', $phoneNumbers)); 
    }

    $data = [
        "senderId" => $senderId,
        "messageType" => "text",
        "message" => $message,
        "contacts" => $contacts
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);

    $responseData = json_decode($response, true);

    if (isset($responseData['success']) && $responseData['success']) {
        return [
            'success' => true,
            'message' => 'Messages sent successfully!'
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Failed to send message: ' . ($responseData['error'] ?? 'Unknown error')
        ];
    }
}

function sendMessageLeadPending($phoneNumber, $message, $senderId)
{
    // API credentials
    $apiKey = "developerbeneth";
    $apiSecret = "VBGBW1HNk3tBYT0nSqu0";
    $url = "https://messaging.kilakona.co.tz/api/v1/vendor/message/send";

    $headers = [
        "api_key: $apiKey",
        "api_secret: $apiSecret",
        "Content-Type: application/json"
    ];

    $data = [
        "senderId" => $senderId,
        "messageType" => "text",
        "message" => $message,
        "contacts" => $phoneNumber
    ];

    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    curl_close($ch);

    $responseData = json_decode($response, true);

    if (isset($responseData['success']) && $responseData['success']) {
        return [
            'success' => true,
            'message' => 'Message sent successfully!'
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Failed to send message: ' . ($responseData['error'] ?? 'Unknown error')
        ];
    }
}