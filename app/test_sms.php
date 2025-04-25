<?php

function sendMessageLeadPending($phoneNumbers, $message, $senderId)
{
    $clientId = '276db4a4-b02d-4a74-b8d3-10f0ff9bd2b1';
    $clientSecret = '95eb4262-2a11-4a4c-888d-e1fc483605b5';
    $url = "https://www.blackballgw.co.tz/api/sms/send";

    $headers = [
        "Content-Type: application/json",
        "Accept: application/json"
    ];

    if (!is_array($phoneNumbers)) {
        $phoneNumbers = [$phoneNumbers];
    }

    $messages = [];
    foreach (array_slice($phoneNumbers, 0, 50) as $index => $phone) {
        $phone = trim($phone);
        // Remove the '+' if present to match the required format (e.g., +255710459064 -> 255710459064)
        $phone = ltrim($phone, '+');
        // Validate phone number: should be 12 to 15 digits (e.g., 255710459064)
        if (!preg_match('/^\d{12,15}$/', $phone)) {
            continue;
        }
        $reference = "MSG_" . time() . "_" . $index . "_" . uniqid();
        $reference = str_pad($reference, 20, "0");
        $messages[] = [
            "text" => $message,
            "msisdn" => $phone,
            "source" => $senderId,
            "reference" => $reference,
            "coding" => "GSM7"
        ];
    }

    if (empty($messages)) {
        return [
            'success' => false,
            'message' => 'No valid phone numbers provided'
        ];
    }

    $data = [
        "auth" => [
            "clientId" => $clientId,
            "clientSecret" => $clientSecret
        ],
        "messages" => $messages
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $responseData = json_decode($response, true);

    // Log the raw response for debugging
    file_put_contents('sms_log.txt', "Request: " . json_encode($data) . "\nResponse: " . print_r($responseData, true) . "\n\n", FILE_APPEND);

    if ($httpCode === 200 && isset($responseData['status']) && $responseData['status'] === true) {
        return [
            'success' => true,
            'message' => 'Message sent successfully!',
            'balance' => $responseData['balance'] ?? null
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Failed to send message: ' . ($responseData['message'] ?? 'Unknown error'),
            'errors' => $responseData['data'] ?? [],
            'balance' => $responseData['balance'] ?? null
        ];
    }
}

$result = sendMessageLeadPending(
    ['+255710459064'], 
    'Hello, this is a test message!',
    'KODASMS'
);
print_r($result);

?>