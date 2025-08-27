<?php
date_default_timezone_set("Asia/Calcutta");

file_put_contents('/tmp/Shiprocket_script.log', date("Y-m-d H:i:s") . '-------->' . 'Inside SHIPROCKET_API_SCRIPT'.  PHP_EOL, FILE_APPEND);


// Replace YOUR_API_KEY with your actual Shiprocket API key
$apiKey = "YOUR_API_KEY";



//Making DB Connection
$DBConnection = pg_connect("dbname='e2fax' user='domains'");
file_put_contents('/tmp/Shiprocket_script.log', date("Y-m-d H:i:s") . '------>' . 'DB Response ' . $DBConnection .  PHP_EOL, FILE_APPEND);



// Set up the data for the new order
$data = [
    "order_id" => "12345",
    "pickup_code" => "PICKUP123",
    "order_date" => "2022-01-01",
    "pickup_date" => "2022-01-02",
    "sender_name" => "Husain",
    "sender_email" => "husain@staff.ownmail.com",
    "sender_phone" => "7208309120",
    "sender_address" => "Kurla Mumbai",
    "recipient_name" => "Mohd Husain",
    "recipient_email" => "husain@staff.ownmail.com",
    "recipient_phone" => "07208309120",
    "recipient_address" => "kurla east mumbai 400024",
    "weight" => "5",
    "length" => "10",
    "width" => "10",
    "height" => "10",
    "insurance" => "0",
    "cod" => "0",
    "items" => [
        [
            "name" => "item 1",
            "sku" => "SKU1",
            "qty" => "1",
            "price" => "10"
        ],
        [
            "name" => "item 2",
            "sku" => "SKU2",
            "qty" => "2",
            "price" => "20"
        ]
    ]
];

file_put_contents('/tmp/Shiprocket_script.log', date("Y-m-d H:i:s") . '--------->' . $data . PHP_EOL, FILE_APPEND);

// Use cURL to make a POST request to the Shiprocket API
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://apiv2.shiprocket.in/v1/external/orders/create",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => json_encode($data),
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "Authorization: Bearer $apiKey"
    ],
]);


// Get the response from the API
$response = curl_exec($curl);
$err = curl_error($curl);


// Close the cURL session
curl_close($curl);

// Check if there was an error and handle it
if ($err) {
    echo "cURL Error #:" . $err;
} else {
    echo $response;
}




//Track order deliveries
$order_id = "12345"; //replace it with the actual order id

// Use cURL to make a GET request to the Shiprocket API
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://apiv2.shiprocket.in/v1/external/orders/track/$order_id",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $apiKey"
    ],
]);
$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);
if ($err) {
    echo "cURL Error #:" . $err;
} else {
    echo $response;
}





//Cancel Order deliveries
$order_id = "12345"; //replace it with the actual order id

// Use cURL to make a PUT request to the Shiprocket API
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://apiv2.shiprocket.in/v1/external/orders/cancel/$order_id",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => "PUT",
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $apiKey"
    ],
]);
$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);
if ($err) {
    echo "cURL Error #:" . $err;
} else {
    echo $response;
}


?>
