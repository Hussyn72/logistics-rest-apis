<?php
date_default_timezone_set("Asia/Calcutta");
$log_file = '/tmp/Bluedart_JWT_token_generator_script.log';
file_put_contents($log_file, date("Y-m-d H:i:s") .'Inside Bluedart Token Generator script'.  PHP_EOL, FILE_APPEND);

//DB CONNECTION
global $DBConn;
$DBConn = pg_connect("dbname='e2fax' user='domains'");

if(!$DBConn)
{
        echo "Error : Unable to open database\n";
        file_put_contents($log_file, date("Y-m-d H:i:s") . ' Error : Unable to open database'.  PHP_EOL, FILE_APPEND);
}
else
{
        //  echo "Opened database successfully\n";
        file_put_contents($log_file, date("Y-m-d H:i:s") . ' DB Connection Established'.  PHP_EOL, FILE_APPEND);
}


file_put_contents($log_file, date("Y-m-d H:i:s") . '*********************' . 'START'.'********************** '.  PHP_EOL, FILE_APPEND);


$ClientID = 'QUH0qOgoJWQKergT';

$URL = 'https://apigateway.bluedart.com/in/transportation/token/v1/login';


$headers = array(
	'ClientID: '.$ClientID	
);

// Initialize cURL session
$curl = curl_init($URL);

// Set the cURL options
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);


// Set the cURL options
$response = curl_exec($curl);

// Check for errors
if ($response === false) 
{
	echo 'Error: ' . curl_error($curl);
        file_put_contents($log_file, date("Y-m-d H:i:s") . '***Curl Request Fail ERROR :  ' . curl_error($curl) . PHP_EOL, FILE_APPEND);
       
} 
else 
{
	// Process the response
	$Json_object = json_decode($response, true);
	$JWT_Token = $Json_object['JWTToken'];
        file_put_contents($log_file, date("Y-m-d H:i:s") . ' Requests OUTPUT :  ' . $JWT_Token . PHP_EOL, FILE_APPEND);

}

// Close cURL session
curl_close($curl);



$Query = "update config_values set value='$JWT_Token' where name='simmis' and key='Bluedart_API_Token'";
file_put_contents($log_file, date("Y-m-d H:i:s") .$Query. PHP_EOL, FILE_APPEND);
$Execute = pg_query($DBConn,$Query);
if($Execute)
	file_put_contents($log_file, date("Y-m-d H:i:s") .'Token Update sucessful'. PHP_EOL, FILE_APPEND);
else
	file_put_contents($log_file, date("Y-m-d H:i:s") .'Token Update Failed'. PHP_EOL, FILE_APPEND);


file_put_contents($log_file, date("Y-m-d H:i:s") .'-------Script Ends -----------------'. PHP_EOL, FILE_APPEND);




?>
