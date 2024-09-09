<?php

use App\Models\Message;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

function trimValues(array $data): array
{
    return array_map('trim', $data);
}

function upload_image($image, $filename = null, $width = null, $height = null)
{

    $filename = $filename ? $filename : time().'.'.'jpg';

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
    } elseif($height == null) {
        $image_resize->resize($width, null, function ($constraint) {
            $constraint->aspectRatio();
        });
    } elseif($width == null) {
        $image_resize->resize(null, $height, function ($constraint) {
            $constraint->aspectRatio();
        });
    } else {
        $image_resize->resize($width, $height);
    }

    $path = public_path('storage/uploads/');
    if (! File::isDirectory($path)) {
        File::makeDirectory($path, 0755, true);
    }

    $quality = 80;
    do {
        $image_resize->encode('jpg', $quality);
        $image_resize->save($path.$filename);
        clearstatcache(); // Clear cache to get actual file size

        if (file_exists($path.$filename) && is_readable($path.$filename)) {
            $fileSize = filesize($path.$filename) / 1024;

            if ($fileSize > 100) {
                if ($quality - 10 < 0) { // Ensure quality doesn't go below 0
                    break;
                }
                try {
                    $quality -= 10;
                    $image_resize->encode('jpg', $quality);
                } catch (InvalidArgumentException $e) {
                    echo 'Error: '.$e->getMessage();
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
    $filename = $filename ? $filename : time().'.'.$file->getClientOriginalExtension();
    $path = public_path('storage/uploads/');
    if (! File::isDirectory($path)) {
        File::makeDirectory($path, 0755, true);
    }
    $file->move($path, $filename);

    return $filename;
}

function delete_file($filename)
{
    $path = public_path('storage/uploads/');
    if (File::exists($path.$filename) && ! File::isDirectory($path.$filename)) {
        File::delete($path.$filename);
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
    $prefix = 'HMS'.$numericPart.$capitalLetters.$mixedPart;

    return $prefix;
}

function return_status_badge($status)
{
    if ($status == 1) {
        $badge = '<span class="badge badge-success" >Active</span>';
    } elseif ($status == 2) {
        $badge = '<span class="badge badge-danger" >Failed</span>';
    } else {
        $badge = '<span class="badge badge-warning" >Pending</span>';
    }

    return $badge;
}

function user_status($status){
    match ($status) {
        '0' => $badge = '<span class="badge badge-rounded badge-secondary" >Disabled</span>',
        '1' => $badge = '<span class="badge badge-rounded badge-success" >Active</span>',
        '2' => $badge = '<span class="badge badge-rounded badge-primary" >leave</span>',
        '3' => $badge = '<span class="badge badge-rounded badge-warning" >Sick</span>',
        default => $badge = '<span class="badge badge-rounded badge-info" >Normal</span>',
    };
    return $badge;
}

function leave_status($status){
    match ($status) {
        '0' => $badge = '<span class="badge badge-rounded badge-warning" >new request</span>',
        '1' => $badge = '<span class="badge badge-rounded badge-success" >accepted</span>',
        '2' => $badge = '<span class="badge badge-rounded badge-danger" >Rejected</span>',
        '3' => $badge = '<span class="badge badge-rounded badge-warning" >Sick</span>',
        default => $badge = '<span class="badge badge-rounded badge-warning" >pending</span>',
    };
    return $badge;
}

function sick_status($status){
    
    match ($status) {
        true => $badge = '<span class="badge badge-rounded badge-warning" >SICK</span>',
        false => $badge = '<span class="badge badge-rounded badge-success" >RECOVERED</span>',
        default => $badge = '<span class="badge badge-rounded badge-warning" >SICK</span>',
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

function createStaffidNumber($hospital_id)
{
    $staff = \App\Models\Staff::where('hospital_id', $hospital_id)->latest()->first();
    if ($staff) {
        $staff_id = $staff->staff_no;
        $staff_id = explode('-', $staff_id);
        $staff_id = $staff_id[1];
        $staff_id = $staff_id + 1;
        $staff_id = 'STF-'.$staff_id;
    } else {
        $staff_id = 'STF-1';
    }

    return $staff_id;
}

function createDoctoridNumber($hospital_id)
{
    $doctor = \App\Models\Doctors::where('hospital_id', $hospital_id)->latest()->first();
    if ($doctor) {
        $doctor_id = $doctor->doctor_id_number;
        $doctor_id = explode('-', $doctor_id);
        $doctor_id = $doctor_id[1];
        $doctor_id = $doctor_id + 1;
        $doctor_id = 'DOC-'.$doctor_id;
    } else {
        $doctor_id = 'DOC-1';
    }

    return $doctor_id;
}

function createPatientNumber($hospital_id)
{
    $patient = \App\Models\Patient::where('hospital_id', $hospital_id)->latest()->first();
    if ($patient) {
        $patient_id = $patient->patient_no;
        $patient_id = explode('-', $patient_id);
        $patient_id = $patient_id[1];
        $patient_id = $patient_id + 1;
        $patient_id = 'PAT-'.$patient_id;
    } else {
        $patient_id = 'PAT-1';
    }

    return $patient_id;
}

function createRandomEmail($hospital_id,$name){
    $db_emails = \App\Models\User::where('hospital_id',$hospital_id)->pluck('email')->toArray();
    do {
        $new_email = strtolower($name).$hospital_id.rand(10,99).'@hms.com';
    } while (in_array($new_email, $db_emails));
    return $new_email;
    
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

function check_if_client_validate_token($hospital_id)
{
    $token = \App\Models\SubscriptionToken::where('hospital_id',$hospital_id)->latest()->first();
    if(!empty($token)){
        $validated = \App\Models\SubscriptionToken::where('hospital_id', $hospital_id)->where('token_', $token->token_)->where('active', 0)->latest()->first();
    if ($validated) {
        return true;
    } else {
        return false;
    }

    }else{
        return false;
    }
   
}

function sendSMS($user_id,$sms,$h_id=null){
    return Message::create([
        'user_id' => $user_id,
        'message' => $sms,
        'hospital_id' => $h_id,
        'status' => 0,
    ]);
}

function patient_status($status){
    //return badge to to show patient status
}
use Carbon\Carbon;
function date_time($data){
    $carbonDate = Carbon::parse($data);
    $formattedDate = $carbonDate->format('d/m/Y, h:i A');
    return $formattedDate;
}
