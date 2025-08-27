<?php
date_default_timezone_set("Asia/Calcutta");

file_put_contents('/tmp/Shiprocket_Shipping_Service.log', date("Y-m-d H:i:s") . '***********************>>>>>>>>>' . 'Inside Shiprocket Shipping Service'.  PHP_EOL, FILE_APPEND);




//DB CONNECTION 
$DBConn = pg_connect("dbname='e2fax' user='domains'");



//Getting Shiprocket API key/Token
$GetAPITokenQuery = pg_query($DBConn,"select value from config_values where name='simmis' and key='Shiprocket_API_Token'");
$GetToken = pg_fetch_assoc($GetAPITokenQuery);
$token = $GetToken['value'];

file_put_contents('/tmp/Shiprocket_Shipping_Service.log', date("Y-m-d H:i:s") . '************TOKEN**********' . $token.  PHP_EOL, FILE_APPEND);

	//GETTING DATA FROM URL
	$credit_ref_no = $_GET['crn'];
	$customer_name = $_GET['c_name'];
	$customer_add1 = $_GET['c_add1'];
	$customer_add2 = $_GET['c_add2'];
	$customer_add3 = $_GET['city'].", ".$_GET['state'];
	$customer_mobile = $_GET['c_mob'];
	$customer_pincode = $_GET['pin'];
	$order_no = $_GET['o'];
	$product = $_GET['t'];
	$customer_mobile = substr($customer_mobile, -10);
	$cost = 1000;
	file_put_contents('/tmp/Shiprocket_Shipping_Service.log', date("Y-m-d H:i:s") . '->' . 'Before get d if condition'.  PHP_EOL, FILE_APPEND);
		if($_GET['d']=="")
		{
	        file_put_contents('/tmp/Shiprocket_Shipping_Service.log', date("Y-m-d H:i:s") . '->' . 'Inside get d if condition'.  PHP_EOL, FILE_APPEND);
        	$query = pg_query($DBConn,"SELECT json from tsim_orders where order_no='$order_no'");
	        file_put_contents('/tmp/Shiprocket_Shipping_Service.log', date("Y-m-d H:i:s") . '->' . $query.  PHP_EOL, FILE_APPEND);
        	$row=pg_fetch_assoc($query);
	        $order_det = json_decode($row,true);
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


		//GET EMAIL
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


		//GETTING TODAYS DATE
		$date = date("Y-m-d");


		//GETTING SHIPPING DATA FOR SHIPROCKET API ORDER CREATION
		$getOrderQuery = pg_query($DBConn,"Select json from tsim_orders where order_no='$order_no'");
		$row = pg_fetch_assoc($getOrderQuery);
		$jsonString = json_decode($row['json'],true);

		foreach($jsonString as $Order => $value)
		{
		        foreach($value as $Order_Obj => $Order_Data)
		        {
		                $Shipping_address_Array=$value['shipping_address'];
		                foreach($Shipping_address_Array as $key => $value2)
		                {
		                        $Shipping_city = $Shipping_address_Array['city'];
		                        $Shipping_State = $Shipping_address_Array['state'];
		                        $Shipping_country = $Shipping_address_Array['country'];
		                }
		        }
		}


		//CHECKING THE DATA BY DISPLAYING 
		echo "<div id='hidekaro'>";
		echo "<b><h2>Shiprocket Waybill </h2></b> <br>";	
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
		echo "<b>Email : </b>".$track_email."<br>";
		echo "<b>Shipping City:</b>".$Shipping_city."<br>";
		echo "<b>Shipping State:</b>".$Shipping_State."<br>";
		echo "<b>Shipping country:</b>".$Shipping_country."<br>";
		echo "<b> date is:</b>".$date."<br>";
		echo "</div>";
		echo "<div id='apiResponse'></div>";

		// Set up the data for the new order
		$data = [
		  "order_id" => $order_no,
		  "order_date" => $date,
		  "pickup_location" => "primary",
		  "comment" => "",
		  "billing_customer_name" => $customer_name,
		  "billing_last_name" => "",
		  "billing_address" => $customer_add1,
		  "billing_address_2" => $customer_add2,
		//"billing_address_3"=> $customer_add3,
		  "billing_city" => $Shipping_city,
		  "billing_pincode" => $customer_pincode,
		  "billing_state" => $Shipping_State,
		  "billing_country" => $Shipping_country,
		  "billing_email" => $track_email,
		  "billing_phone" => $customer_mobile,
		  "shipping_is_billing" => true,
		  "shipping_customer_name" => "",
		  "shipping_last_name" => "",
		  "shipping_address" => "",
		  "shipping_address_2" => "",
		  "shipping_city" => "",
		  "shipping_pincode" => "",
		  "shipping_country" => "",
		  "shipping_state" => "",
		  "shipping_email" => "",
		  "shipping_phone" => "",
		  "order_items" => [
			    [
		      "name" => "Deactivated Sim Card",
		      "sku" => "Sim1234",
		      "units" => "1",
		      "selling_price" => $cost,
		      "discount" => "",
		      "tax" => "",
		      "hsn"=> "",
			    ]
			  ],
			  "payment_method" => "Prepaid",
			  "sub_total" => $cost,
			  "length" => 21,
			  "breadth"=> 26,
			  "height"=> 1,
			  "weight"=> 0.5
			];

	//ORDER CREATION POST REQUEST
	// Use cURL to make a POST request to the Shiprocket API
	$curl = curl_init();
	curl_setopt_array($curl, [
	    CURLOPT_URL => "https://apiv2.shiprocket.in/v1/external/orders/create/adhoc",
	    CURLOPT_RETURNTRANSFER => true,
	    CURLOPT_ENCODING => "",
	    CURLOPT_MAXREDIRS => 10,
	    CURLOPT_TIMEOUT => 30,
	    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	    CURLOPT_CUSTOMREQUEST => "POST",
	    CURLOPT_POSTFIELDS => json_encode($data),
	    CURLOPT_HTTPHEADER => [
	        "Content-Type: application/json",
	        "Authorization: Bearer $token"
	    ],
	]);


	// Get the response from the API
	$response = curl_exec($curl);
	$err = curl_error($curl);
	

	// Close the cURL session
	curl_close($curl);

	// Check if there was an error and handle it
	if ($err) 
	{
	    echo "cURL Error #:" . $err;
	} 
	else 
	{
	   // echo $response;
	}
	//Order Creation in SHIPROCKET DONE HERE


	//GETTING LIST OF COURIER IN A DROPDOWNLIST AND PASSING TO FOR SCHEDULE
	
	$JsonResponse = json_decode($response);

	$Shiprocket_OrderID = $JsonResponse->order_id;
	//echo $Shiprocket_OrderID ;

	 file_put_contents('/tmp/Shiprocket_Shipping_Service.log', date("Y-m-d H:i:s") . 'Shiprocket order id' .$Shiprocket_OrderID.  PHP_EOL, FILE_APPEND);



	$ShipmentID = $JsonResponse->shipment_id;
	//echo $ShipmentID ;

	$Data_for_courier_list_API = [
	    "pickup_postcode"=>"400028",
	    "delivery_postcode"=>$customer_pincode,
	    "order_id"=>$Shiprocket_OrderID
	];

	$curl = curl_init();
	curl_setopt_array($curl, [
	    CURLOPT_URL => "https://apiv2.shiprocket.in/v1/external/courier/serviceability/",
	    CURLOPT_RETURNTRANSFER => true,
	    CURLOPT_ENCODING => "",
	    CURLOPT_MAXREDIRS => 10,
	    CURLOPT_TIMEOUT => 30,
	    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	    CURLOPT_CUSTOMREQUEST => "GET",
	    CURLOPT_POSTFIELDS => json_encode($Data_for_courier_list_API),
	    CURLOPT_HTTPHEADER => [
	        "Content-Type: application/json",
	        "Authorization: Bearer $token"
	    ],
	]);


	// Get the response from the API
	$CourierListresponse = curl_exec($curl);
	$err = curl_error($curl);


	// Close the cURL session
	curl_close($curl);

	// Check if there was an error and handle it
	if ($err)
	{
	    echo "cURL Error #:" . $err;
	}
	else
	{
	 //  echo $CourierListresponse;
	}

	$data = json_decode($CourierListresponse,true);

	 	
if(!isset($_GET['schedulepickup']))
{
	
		
		echo '<div id="remaining_code">';	
		echo '<form method="get" id="myform">';
	 	echo '<br> <select id="courier"  name="courier">';
	 	echo '<option value=""  selected > Select Courier Partner </option>';

	foreach ($data['data']['available_courier_companies'] as $company) 
	{
	    	$courier_name = $company['courier_name'];
	    	$freight_charge = $company['freight_charge'];
	    	$courier_company_id = $company['courier_company_id'];
    
		echo '<option value="'.$courier_company_id.'">'.$courier_name. ' - ' .$freight_charge.'</option>';
	}

    	echo '</select><br>';
	echo "<p id='msg' style='display:none'> PLEASE SELECT COURIER PARTNER </p>";
    	echo '<br><input type="submit" id="mybtn" value="Submit" onclick="return labelcreation()"  ><br>';
    	echo '</form>';

	//GETTING COURIER PARTNER LIST ENDS HERE


	file_put_contents('/tmp/Shiprocket_Shipping_Service.log', date("Y-m-d H:i:s") . '*****BEFORE SUBMITTING IF CONDITION*******COURIER ID***** & ACTION*****' . $courier_id.$action.  PHP_EOL, FILE_APPEND);
}
else
{
$action = $_GET['schedulepickup'];
$courier_id = $_GET['courier'];
$ShipmentID =  $_GET['shipmentID'];
$order_no = $_GET['o'];

file_put_contents('/tmp/Shiprocket_Shipping_Service.log', date("Y-m-d H:i:s") . '*****AFTER SUBMITTING IF CONDITION*******COURIER ID***** & ACTION*****' . $courier_id.$action.  PHP_EOL, FILE_APPEND);


//Selecting Courier Parthner using shipment id from order creation response

// Get the selected value from the dropdown list
file_put_contents('/tmp/Shiprocket_Shipping_Service.log', date("Y-m-d H:i:s") . '*****BEFORE IF CONDITION*******COURIER ID**********' . $courier_id.  PHP_EOL, FILE_APPEND);
	 
if($courier_id != "")
{
	
			  
//	echo $courier_id ;
	file_put_contents('/tmp/Shiprocket_Shipping_Service.log', date("Y-m-d H:i:s") . '************COURIER ID**********' . $courier_id.  PHP_EOL, FILE_APPEND);
	file_put_contents('/tmp/Shiprocket_Shipping_Service.log', date("Y-m-d H:i:s") . '************SHIPMENT ID**********' . $ShipmentID.  PHP_EOL, FILE_APPEND);


	$Data_for_selecting_courier_API = [
	"shipment_id"=> $ShipmentID,
	"courier_id"=> $courier_id
	];


	//SELECTING COURIER POST REQUEST
	$curl = curl_init();
	curl_setopt_array($curl, [
	    CURLOPT_URL => "https://apiv2.shiprocket.in/v1/external/courier/assign/awb" ,
	    CURLOPT_RETURNTRANSFER => true,
	    CURLOPT_ENCODING => "",
	    CURLOPT_MAXREDIRS => 10,
	    CURLOPT_TIMEOUT => 30,
	    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	    CURLOPT_CUSTOMREQUEST => "POST",
	    CURLOPT_POSTFIELDS => json_encode($Data_for_selecting_courier_API),
	    CURLOPT_HTTPHEADER => [
	        "Content-Type: application/json",
	        "Authorization: Bearer $token"
	    ],
	]);


	// Get the response from the API
	$responseAWB = curl_exec($curl);
	$err = curl_error($curl);


	// Close the cURL session
	curl_close($curl);

	// Check if there was an error and handle it
	if ($err)
	{
	    echo "cURL Error #:" . $err;
	}
	else
	{
	   //echo $responseAWB;
	}
	//SELECTING COURIER  DONE HERE

	$AWB = json_decode($responseAWB,true);
	$AWBcode = $AWB['response']['data']['awb_code'];
	//echo $AWBcode;

	echo "<b> AWB Number : </b>" . $AWBcode . "<br>";


	//SCHEDULING PICKUP
	$Data_for_scheduling_pickup=[
	  "shipment_id"=> $ShipmentID
	];
 
	$curl = curl_init();
	curl_setopt_array($curl, [
	    CURLOPT_URL => "https://apiv2.shiprocket.in/v1/external/courier/generate/pickup" ,
	    CURLOPT_RETURNTRANSFER => true,
	    CURLOPT_ENCODING => "",
	    CURLOPT_MAXREDIRS => 10,
	    CURLOPT_TIMEOUT => 30,
	    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	    CURLOPT_CUSTOMREQUEST => "POST",
	    CURLOPT_POSTFIELDS => json_encode($Data_for_scheduling_pickup),
	    CURLOPT_HTTPHEADER => [
	        "Content-Type: application/json",
	        "Authorization: Bearer $token"
	    ],
	]);



	// Get the response from the API
	$responseforSchedulingPickup = curl_exec($curl);
	$err = curl_error($curl);


	// Close the cURL session
	curl_close($curl);
	
	// Check if there was an error and handle it
	if ($err)
	{
	    echo "cURL Error #:" . $err;
	}
	else
	{
	//    echo $responseforSchedulingPickup;
	}
	//SCHEDULING PICKUP DONE HERE



	//GENERATING LABEL
	$Data_for_label=[
	  "shipment_id"=> [$ShipmentID]
	];



	$curl = curl_init();
	curl_setopt_array($curl, [
	    CURLOPT_URL => "https://apiv2.shiprocket.in/v1/external/courier/generate/label" ,
	    CURLOPT_RETURNTRANSFER => true,
	    CURLOPT_ENCODING => "",
	    CURLOPT_MAXREDIRS => 10,
	    CURLOPT_TIMEOUT => 30,
	    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	    CURLOPT_CUSTOMREQUEST => "POST",
	    CURLOPT_POSTFIELDS => json_encode($Data_for_label),
	    CURLOPT_HTTPHEADER => [
	        "Content-Type: application/json",
	        "Authorization: Bearer $token"
	    ],
	]);



		
	// Get the response from the API
	$responseforlabel = curl_exec($curl);
	$err = curl_error($curl);
	

	// Close the cURL session
	curl_close($curl);

	// Check if there was an error and handle it
	if ($err)
	{
	    echo "cURL Error #:" . $err;
	}
	else
	{
	  //  echo $responseforlabel;
	}
	//GENERATING LABEL DONE HERE 

	$responseforlabelobj = json_decode($responseforlabel);
	$DownloadLabelURL = $responseforlabelobj->label_url;


	//GENERATE MANIFEST
	$Data_for_manifest=[
	  "shipment_id"=> [$ShipmentID]
	];



	$curl = curl_init();
	curl_setopt_array($curl, [
	    CURLOPT_URL => "https://apiv2.shiprocket.in/v1/external/manifests/generate" ,
	    CURLOPT_RETURNTRANSFER => true,
	    CURLOPT_ENCODING => "",
	    CURLOPT_MAXREDIRS => 10,
	    CURLOPT_TIMEOUT => 30,
	    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	    CURLOPT_CUSTOMREQUEST => "POST",
	    CURLOPT_POSTFIELDS => json_encode($Data_for_manifest),
	    CURLOPT_HTTPHEADER => [
	        "Content-Type: application/json",
	        "Authorization: Bearer $token"
	    ],
	]);




	// Get the response from the API
	$manifestresponse = curl_exec($curl);
	$err = curl_error($curl);


	// Close the cURL session
	curl_close($curl);

	// Check if there was an error and handle it
	if ($err)
	{
	    echo "cURL Error #:" . $err;
	}
	else
	{
	//   echo $manifestresponse;
	}
	//GENERATING MANIFEST DONE HERE

	$ManifestJsonObj = json_decode($manifestresponse);
	$DownloadManifestURL = $ManifestJsonObj->manifest_url;



	//CREATING FILENAME
	$Filename ="Waybill_".$AWBcode."_".$order_no;
	file_put_contents('/tmp/Shiprocket_Shipping_Service.log',"*********************************".$Filename .  PHP_EOL, FILE_APPEND);
	

	echo "<b> Filename :</b> ".$Filename.".pdf"."<br>";
	echo "<b> Download Label : </b>" .  '<a href="'. $DownloadLabelURL .'">Click Here</a>'. " <br>";
	echo "<b> Download Manifest : </b>" . '<a href="'.$DownloadManifestURL .'">Click Here</a>'."<br>";

	//STORING FILE IN BREIFCASE
	// Download the file contents
	$file_contents = file_get_contents($DownloadLabelURL);
		
	// Save the contents to a local file
	file_put_contents('/home/briefcase/in/tsim/.briefcase/s/services/shipments/'.$Filename.'.pdf', $file_contents);


	//UPDATING DB
	$insert = pg_query($DBConn,"update tsim_Shipping_details set final_flag='t',label_created_at=now(),tracking_no='$AWBcode',shipped_through='via shiprocket' where order_no='$order_no'");



	}
}




?>

<script>

	var shipmentID = "<?php echo $ShipmentID; ?>";
	var order_no ="<?php echo $order_no; ?>";
	var creditRefNo = "<?php echo $credit_ref_no; ?>";
	var customerName = "<?php echo $customer_name; ?>";
	var customerAdd1 = "<?php echo $customer_add1; ?>";
	var customerAdd2 = "<?php echo $customer_add2; ?>";
	var customercity = "<?php echo $Shipping_city; ?>";
	var customerstate = "<?php echo $Shipping_State; ?>";
	var customerMobile = "<?php echo $customer_mobile; ?>";
	var customerPincode = "<?php echo $customer_pincode; ?>";
	var orderNo = "<?php echo $order_no; ?>";
	var product = "<?php echo $product; ?>";
	var method = "<?php echo $method; ?>";
	var cost = 1000;	

	// Get the form and submit button elements
	var myForm = document.getElementById("myform");
	var submitBtn = document.getElementById("mybtn");

	// Add an event listener to the submit button
	submitBtn.addEventListener("click", function(event) 
	{
  	event.preventDefault(); // prevent the default form submission
  	var schedulepickup = "a";

  	// Get the selected value of the dropdown list
  	var courier = document.getElementById("courier").value;

  	// Make an AJAX call to the PHP script
  /*	var xhr = new XMLHttpRequest();
	xhr.open("GET", "https://mail.tsim.in/tsim/Shiprocket_shipping_service.php?courier=" + courier + "&shipmentID=" + shipmentID + "&order_no=" + order_no + "&schedulepickup=" + schedulepickup , true);
    	xhr.send();*/
	
	var xhr = new XMLHttpRequest();


	xhr.open("GET", "https://mail.tsim.in/tsim/Shiprocket_shipping_service.php?courier=" + courier + "&shipmentID=" + shipmentID + "&o=" + orderNo + "&schedulepickup=" + schedulepickup + "&crn=" + creditRefNo + "&c_name=" + customerName + "&c_add1=" + customerAdd1 + "&c_add2=" + customerAdd2 + "&city=" + customercity + "&state=" + customerstate +  "&c_mob=" + customerMobile + "&pin=" + customerPincode + "&t=" + product + "&cost=" + cost + "&d=" + method, true);




	
	document.getElementById("hidekaro").style.display = "none";
	// Add an event listener to the xhr object
	xhr.addEventListener("readystatechange", function() {
	  // Check if the operation is complete and the response is ready
	  if (this.readyState === 4 && this.status === 200) {
	    // Get the API response
	    var apiResponse = this.responseText;

	    // Update the HTML page with the API response
	    document.getElementById("apiResponse").innerHTML = apiResponse;
	
      	// Hide the dropdown list and submit button
	// Get the dropdown list element
	  }
	});

	xhr.send();

	});

	function labelcreation()
	{
		var courierDropdown = document.getElementById("courier");
		if (courierDropdown.value == "") {
	          // Show the dropdown list and submit button
        	  courierDropdown.style.display = "block";
        	  submitBtn.style.display = "block";
		document.getElementById("msg").style.display = "block";

		   return false;
       		  } else {
	          // Hide the dropdown list and submit button
		document.getElementById("msg").style.display = "none";
          	  courierDropdown.style.display = "none";
	          submitBtn.style.display = "none";
		  return true;
        	}



	}

</script>





