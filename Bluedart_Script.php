<?php
date_default_timezone_set("Asia/Calcutta");
$log_file = '/tmp/Bluedart_Script.log';

file_put_contents($log_file, date("Y-m-d H:i:s") . '->' . 'Inside Bluedart Script'.  PHP_EOL, FILE_APPEND);

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


echo date("Y-m-d");
file_put_contents($log_file, date("Y-m-d H:i:s") .date("Y-m-d").  PHP_EOL, FILE_APPEND);




$credit_ref_no = $_GET['crn'];
$customer_name = $_GET['c_name'];
$customer_add1 = $_GET['c_add1'];
$customer_add2 = $_GET['c_add2'];
$customer_add3 = $_GET['city'].", ".$_GET['state'];
$customer_mobile = $_GET['c_mob'];
$customer_pincode = $_GET['pin'];
$order_no = $_GET['o'];
$product = $_GET['t'];
$cost = 2000;
$customer_mobile = substr($customer_mobile, -10);

file_put_contents($log_file, date("Y-m-d H:i:s") . 'Before get d if condition'.  PHP_EOL, FILE_APPEND);
if($_GET['d']=="")
{
        file_put_contents($log_file, date("Y-m-d H:i:s") . '->' . 'Inside get d if condition'.  PHP_EOL, FILE_APPEND);
        $query = pg_query($DBConn,"SELECT json from tsim_orders where order_no='$order_no'");
        file_put_contents($log_file, date("Y-m-d H:i:s") . '->'.'Query'.$query .  PHP_EOL, FILE_APPEND);
        $row=pg_fetch_assoc($query);
        $order_det = json_decode($row['json']);
        //print_r($order_det);
        foreach($order_det as $data=>$obj)
        {
                $count =$obj->total_line_items_quantity;
                $method = $obj->shipping_methods;
                $shipping_cost = $obj->total_shipping;
        }
}
else
{
        $method = $_GET['d'];
}

$query2 = pg_query($DBConn,"SELECT bluedart_flag,waybill_no from tsim_shipping_details where order_no='$order_no'");
$row2=pg_fetch_assoc($query2);
$waybill_no = $row2['waybill_no'];
$flag = $row2['bluedart_flag'];
if($_GET['email']=="")
{
        $email_query = pg_query($DBConn,"select * from tsim_simmis_map where order_no='$order_no'");
        $fetch_email_query = pg_fetch_assoc($email_query);
        $track_email = $fetch_email_query['email'];
}
else
{
        $track_email = $_GET['email'];
}


$LoginID = 'BOM91828';
$LicenseKey = 'fjqjrjmuplougttg1effihlkugiss3xi';




echo "<h1> Bluedart Waybill </h1>";
echo "<b>Order number : </b>".$order_no."<br>";
echo "<b>Credit reference number : </b>".$credit_ref_no."<br>";
echo "<b>Trip id : </b>".$product."<br>";
echo "<b>Customer name : </b>".$customer_name."<br>";
echo "<b>Customer add1 : </b>".$customer_add1."<br>";
echo "<b>Customer add2 : </b>".$customer_add2."<br>";
echo "<b>Customer add3 : </b>".$customer_add3."<br>";
echo "<b>Pincode : </b>".$customer_pincode."<br>";
echo "<b>Mobile number : </b>".$customer_mobile."<br>";
echo "<b>Total amount : </b>".$cost."<br>";
echo "<b>Shipping method : </b>".$method."<br>";
echo "<b>Total shipping cost : </b>".$shipping_cost."<br>";

if($method == 'Next Business Day (if ordered before 8PM)' || $method=='Same Business Day (if ordered before 6PM)' || $method=='Next Business Day 11AM (if ordered before 6PM)' || $method=='Next Business Day Noon (for orders placed before 6PM)')
{
        file_put_contents($log_file,date("Y-m-d H:i:s").'Inside shipping method if condition'.  PHP_EOL, FILE_APPEND);

        $check_tdd ="select tdd_1030,tdd_1230 from bluedart_tdd_pincodes where pincode='$customer_pincode'";
        file_put_contents($log_file,date("Y-m-d H:i:s").$check_tdd .PHP_EOL, FILE_APPEND);
        $check_tdd_query = pg_query($DBConn,$check_tdd);
        $row3=pg_fetch_assoc($check_tdd_query);
        $tdd_1030 = $row3['tdd_1030'];
        $tdd_1040 = $row3['tdd_1230'];
        if($tdd_1030=='Y')
        {
                $pack_type = "T";
                $instruction = "TDD";
        }
        elseif($tdd_1040=='Y')
        {
                $pack_type = "N";
                $instruction = "TDD";
        }
        else
        {
                $pack_type = "";
                $instruction = "";
        }
}
$time = date("H:m");
$date = date("Y-m-d");
file_put_contents($log_file, date("Y-m-d H:i:s") . '->' . 'Time and date '. $time. $date. PHP_EOL, FILE_APPEND);
if($time<date("19:30"))
{
        $time = 1930;
        $date = date("Y-m-d");
        echo  $date;
}
else
{
        echo "In else after 7 30";
        $date = date('Y-m-d', strtotime('+1 day', strtotime($d)));
        echo  $date;
        $time = 1930;
}


$currentDate = time(); // Get the current Unix timestamp in seconds
$milliseconds = $currentDate * 1000; // Convert to milliseconds

file_put_contents($log_file, date("Y-m-d H:i:s") .'EPOCH TIME - >'. $milliseconds . PHP_EOL, FILE_APPEND);


file_put_contents($log_file, date("Y-m-d H:i:s") . '->' . 'Just above before starting curl'. PHP_EOL, FILE_APPEND);

$Query = pg_query($DBConn,"select value from config_values where key='Bluedart_API_Token'");
$gettoken = pg_fetch_assoc($Query);
$JWT_Token = $gettoken['value'];

file_put_contents($log_file, date("Y-m-d H:i:s") .'Got the Token ---> '.$JWT_Token. PHP_EOL, FILE_APPEND);

$url = "https://apigateway.bluedart.com/in/transportation/waybill/v1/GenerateWayBill";


$headers = array(
                'content-type: application/json',
                'JWTToken: ' .$JWT_Token
        );


$params = '{
  "Request": {
    "Consignee": {
      "ConsigneeAddress1": "'.$customer_add1.'",
      "ConsigneeAddress2": "'.$customer_add2.'",
      "ConsigneeAddress3": "'.$customer_add3.'",
      "ConsigneeMobile": "'.$customer_mobile.'",
      "ConsigneeName": "'.$customer_name.'",
      "ConsigneePincode": "'.$customer_pincode.'"
    },
    "Services": {
      "ActualWeight": "0.5",
      "CreditReferenceNo": "'.$credit_ref_no.'",
      "Dimensions": [
        {
          "Breadth": 1,
          "Count": 1,
          "Height": 1,
          "Length": 1
        }
      ],
      "PickupDate": "/Date('.$milliseconds.')/",
      "PickupTime": "'.$time.'",
      "PieceCount": "1",
      "ProductCode": "D",
      "PackType": "'.$pack_type.'",
      "SpecialInstruction":"'.$instruction.'",
      "DeclaredValue":"'. $cost.'"

    },
    "Shipper": {
      "CustomerAddress1": "Trikon Electronics, Dadar (West)",
      "CustomerCode": "348876",
      "CustomerEmailID": "services@tsim.in",
      "CustomerMobile": "022 24216344",
      "CustomerName": "TSIM",
      "CustomerPincode": "400028",
      "CustomerTelephone": "022 24216344",
      "OriginArea": "BOM",
      "Sender": "TSIM"
    }
  },
  "Profile": {
    "LoginID": "'.$LoginID.'",
    "LicenceKey": "'.$LicenseKey.'",
    "Api_type": "S"
  }
}';


file_put_contents($log_file, date("Y-m-d H:i:s") .'Parameters data' .$params. PHP_EOL, FILE_APPEND);


// Initialize cURL session
$curl = curl_init($url);


//FOR POST REQUEST
curl_setopt($curl, CURLOPT_POST, true);

// Set the cURL options
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

//Post parameter fields
curl_setopt($curl, CURLOPT_POSTFIELDS, $params);


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
        $decodedResponse = json_decode($response, true);
//      print_r($decodedResponse);  // Display the response data
        file_put_contents($log_file, date("Y-m-d H:i:s") . '***Curl Requests OUTPUT :  ' . $decodedResponse . PHP_EOL, FILE_APPEND);
//        file_put_contents($log_file, date("Y-m-d H:i:s") . ' Requests OUTPUT :  ' . $response . PHP_EOL, FILE_APPEND);

}

// Close cURL session
curl_close($curl);


$responseArray = json_decode($response, true);

// Get the AWB number
$awb = $responseArray['GenerateWayBillResult']['AWBNo'];
echo "AWB Number: $awbNo\n";

file_put_contents($log_file, date("Y-m-d H:i:s") .'AWB NO : '.$awb . PHP_EOL, FILE_APPEND);



// Get and print AWBPrintContent
$awbPrintContent = $responseArray['GenerateWayBillResult']['AWBPrintContent'];
//echo "AWB Print Content: " . implode(",", $awbPrintContent) . "\n";

$awbPrintContent = call_user_func_array('pack', array_merge(array('C*'), $awbPrintContent));


echo "<br>";
echo "Pickup date : ".$date."<br>";
echo "Pickup time : 19:30";
echo "<br>Awb number : ".$awb."<br>";
if ( $awb == '')
{
        echo "AWB creation failed";
        exit;
}
$filename = "Waybill_".$awb."_".$order_no;
echo "Filename : ".$filename.".pdf";
echo "<br>";
echo "Download Waybill -> <a href='https://mail.tsim.in/bc.e?i=Waybill_".$awb."_".$order_no.".pdf&d=shipments'>Click here</a>";


file_put_contents($log_file, date("Y-m-d H:i:s") .$filename. PHP_EOL, FILE_APPEND);


//Create Way bill pdf!
file_put_contents('/home/briefcase/in/tsim/.briefcase/s/services/shipments/'.$filename.'.pdf', $awbPrintContent);
$insert = pg_query($DBConn,"update tsim_shipping_details set bluedart_flag='t',bluedart_label_created_at=now(),waybill_no='$awb' where order_no='$order_no'");


$track_url = "https://api.aftership.com/v4/trackings/fedex/$awb";
$track_url_insert = "https://api.aftership.com/v4/trackings";
$vars = array (
        "tracking" =>
                array (
                        "slug" => "bluedart",
                        "tracking_number" => "$awb",
                        "title" => "$awb",
                        "smses" =>
                        array (
                                0 => "+91$customer_mobile",
                        ),
                        "emails" =>
                        array (
                                0 => "$track_email",
                        ),
                        "order_id" => "$order_no",
                        "language" => "en",
                ),
        );


        $insert_body = json_encode($vars);
        $check = curl_track($track_url,"","");
        if($check == 200){
                echo "<br><b>Tracking number $awb exists in Aftership! </b><br>";
        }
        else{
                $post = "post";
                insert_tracking($track_url_insert,$insert_body,$post);
        }



function curl_track($url,$json_body,$post)
{
        $log_file = "/tmp/aftershipcurlapi.log";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'aftership-api-key: 62097b41-8455-4fde-96f4-016442f80ba4',
                'Content-Type: application/json'
        ));
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
        if($json_body==""){
                $curl_result = curl_exec($ch);
        }
        elseif($post=="post"){
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch,CURLOPT_POSTFIELDS, $json_body);
                $curl_result = curl_exec($ch);
        }
        file_put_contents($log_file, "Output = ".$curl_result.PHP_EOL, FILE_APPEND);
        $result_decode = json_decode($curl_result,true);
        curl_close($ch);
        return $result_decode['meta']['code'];
}



function insert_tracking($url_insert,$body,$post)
{
        $insert = curl_track($url_insert,$body,$post);
        if($insert == 201)
        {
                echo "<br><b>Tracking number inserted in Aftership.</b><br>";
                file_put_contents($log_file, date("Y-m-d H:i:s").'Tracking number inserted in Aftership.'.PHP_EOL, FILE_APPEND);


        }
        else
        {
                echo "<br><b>Tracking number not inserted in Aftership. </b><br>";
                file_put_contents($log_file,date("Y-m-d H:i:s").'Tracking number not inserted in Aftership.'.PHP_EOL, FILE_APPEND);

        }
}





?>
