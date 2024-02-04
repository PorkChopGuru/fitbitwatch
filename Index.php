<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// URL of the exchange rate API
$url = "https://v6.exchangerate-api.com/v6/43c2368b1bc541af101361c6/latest/CAD";

// Initialize cURL session
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

// Execute cURL session
$apiResponse = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    // Handle cURL error
    $response = ["status" => "error", "info" => "Curl error: " . curl_error($ch)];
    echo json_encode($response);
    exit;
}

// Close cURL session
curl_close($ch);

// Decode the API response
$obj = json_decode($apiResponse);
$response = new StdClass();

// Process the API response
if ($obj && $obj->result == 'success') {
    $response->status = 'ok';
    $cadzarValue = floatval($obj->conversion_rates->ZAR);
    $info = "CAD 1 = ZAR " . round($cadzarValue, 2, PHP_ROUND_HALF_UP) . "\n";

    // Get current date and time in Vancouver timezone
    $dateTime = new DateTime('now', new DateTimeZone('America/Vancouver'));
    $info .= "\n" . $dateTime->format('Y-m-d H:i:s');

    $response->info = $info;
} else {
    // Handle invalid or no JSON input or API error
    $response->status = 'error';
    $response->info = "Invalid or no JSON input or API error.";
}

// Set header and return the response
header("Content-Type: application/json;charset=utf-8");
echo json_encode($response);

?>
