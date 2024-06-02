<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Request-Method: GET, POST");
header('Access-Control-Allow-Headers: accept, origin, content-type');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

$db = new Db();

/**
 * API for Generate and Get Apps Token
 * ask = app secret key
 * mauth = member authentication ID 
*/

$apiResponse = array();
$apiResponse['statusCode'] = 401;
$apiResponse['message'] = 'Invalid Request';
$apiResponse['resposeData'] = array();
$POST = json_decode(file_get_contents('php://input'), true);

if (isset($segment2) && is_array($POST)) {	
	if($segment2 == "login"){

		$apiResponse['statusCode'] = 301;
		$apiResponse['message'] = 'Authorization Request';

		$users_email = $POST['email'];
		$users_password = $POST['password'];
		$userType = $POST['userType'];
		
		$db->writeIntoLog("userType: $userType, POST:".json_encode($POST));
		if(empty($users_email) || empty($users_password)){
			if(empty($users_email)){
				$apiResponse['message'] = 'Email required';				
			}
			if(empty($users_password)){
				$apiResponse['message'] = 'Password required';				
			}
		}
		elseif($userType=='users'){
			
			$sql = "SELECT * FROM users WHERE users_email = '$users_email'";
			$tableObj = $db->getObj($sql, array(), 1);
			if($tableObj){
				$tableRow = $tableObj->fetch(PDO::FETCH_OBJ);

				$users_id = $tableRow->users_id;
				$branches_id = $tableRow->branches_id;
				$first_name = $tableRow->users_first_name;
				$last_name = $tableRow->users_last_name;
				$email = $tableRow->users_email;
				$password_hash = $tableRow->password_hash;

				$password_hash_vrf = password_verify($users_password, $password_hash);
				if(!$password_hash_vrf){
					$apiResponse['message'] = 'Your account is invalid.';
				}
				else{
				
					$current_time = date('Y-m-d');
					$one_day_later = date('Y-m-d', strtotime($current_time . ' +1 day'));
					$salt_key = base64_encode($one_day_later);
					$passwrd = $users_password;
					$enc_passwrd = base64_encode($passwrd);
					
					$apps_token = base64_encode($password_hash."||".$salt_key);

					$account_data = array(  'last_updated' => date('Y-m-d H:i:s'),
											'token_valid_till'=>$salt_key
											);
					
					$db->update('users', $account_data, $users_id);
					
					$apiResponse['message'] = '';
					$resposeData = array();
					$resposeData['users_id'] = $users_id;
					$resposeData['branches_id'] = $branches_id;
					$branchesName = $branchesAddress = '';
					$branchesObj = $db->getObj("SELECT name, address FROM branches WHERE branches_id = $branches_id", array());
					if($branchesObj){
						$branchesRow = $branchesObj->fetch(PDO::FETCH_OBJ);	
						$branchesName = $branchesRow->name;
						$branchesAddress = $branchesRow->address;
					}
                    
					$resposeData['branchesName'] = $branchesName;
					$resposeData['branchesAddress'] = $branchesAddress;

					$resposeData['first_name'] = $first_name;
					$resposeData['last_name'] = $last_name;
					$resposeData['email'] = $email;
					$resposeData['apps_token'] = $apps_token;
					
					$apiResponse['resposeData'] = $resposeData;
					$apiResponse['statusCode'] = 200;
				}
			}
			else{		
				$apiResponse['message'] = 'Your account is invalid.';
			}
		}
		elseif($userType=='customers'){
			
			$sql = "SELECT * FROM customers WHERE email = '$users_email'";
			$tableObj = $db->getObj($sql, array(), 1);
			if($tableObj){
				$tableRow = $tableObj->fetch(PDO::FETCH_OBJ);
				$customers_id = $tableRow->customers_id;
				$branches_id = $tableRow->branches_id;
				$password_hash = $tableRow->password_hash;
				$name = $tableRow->name;
				$email = $tableRow->email;
				$phone = $tableRow->phone;
				$address = $tableRow->address;

				$password_hash_vrf = password_verify($users_password, $password_hash);
				if(!$password_hash_vrf){
					$apiResponse['message'] = 'Your password did not match. Try again with correct password.';
				}
				else{
				
					$current_time = date('Y-m-d');
					$one_day_later = date('Y-m-d', strtotime($current_time . ' +1 day'));
					$salt_key = base64_encode($one_day_later);
					
					$apps_token = base64_encode($password_hash."||".$salt_key);

					$updateData = array('last_updated' => date('Y-m-d H:i:s'));
					
					$db->update('customers', $updateData, $customers_id);
					
					$apiResponse['message'] = '';
					$resposeData = array();
					$resposeData['customers_id'] = $customers_id;
					$resposeData['branches_id'] = $branches_id;
					$resposeData['name'] = $name;
					$resposeData['email'] = $email;
					$resposeData['phone'] = $phone;
					$resposeData['address'] = $address;
					$resposeData['apps_token'] = $apps_token;
					$resposeData['userType'] = $userType;
					
					$apiResponse['resposeData'] = $resposeData;
					$apiResponse['statusCode'] = 200;
				}
			}
			else{		
				$apiResponse['message'] = 'Your account is invalid.';
			}
		}
	}
	elseif($segment2 == "saveCustomer"){
		$savemsg = 'error';
		$returnStr = '';
		$customers_id = intval($POST['customers_id']??0);
		$name = trim(addslashes($POST['name']??''));
		$phone = trim(addslashes($POST['phone']??''));
		$email = trim(addslashes($POST['email']??''));
		$address = trim(addslashes($POST['address']??''));
		$password = trim(addslashes($POST['password']??''));

		$conditionarray = array();
		$conditionarray['last_updated'] = date('Y-m-d H:i:s');

		if(empty($name)){
			$returnStr = "Name is blank.";
		}
		elseif(empty($phone)){
			$returnStr = "Phone is blank.";
		}
		elseif(empty($email)){
			$returnStr = "Email is blank.";
		}
		elseif(empty($address)){
			$returnStr = "Address is blank.";
		}
		elseif(empty($password) && $customers_id==0){
			$returnStr = "Password is blank.";
		}
		elseif(strlen($password)<4 && $customers_id==0){
			$returnStr = "Password should be minimum 4 characters.";
		}
		
		if(empty($returnStr)){
			$duplSql = "SELECT customers_publish, customers_id FROM customers WHERE name = :name AND phone = :phone AND email = :email";
			$bindData = array('name'=>$name, 'phone'=>$phone, 'email'=>$email);
			if($customers_id>0){
				$duplSql .= " AND customers_id != :customers_id";
				$bindData['customers_id'] = $customers_id;
			}
			$duplSql .= " LIMIT 0, 1";
			$customersObj = $db->getData($duplSql, $bindData);
			if($customersObj){
				foreach($customersObj as $onerow){
					$customers_publish = $onerow['customers_publish'];
					$customers_id = $onerow['customers_id'];
					$conditionarray['customers_publish'] = 1;
					$db->update('customers', $conditionarray, $customers_id);

					$savemsg = 'Duplicate';
					$returnStr = "Your Name, Email & Phone already exists.";
				}
			}
		}

		if(empty($returnStr)){
			$conditionarray['name'] = $name;
			$conditionarray['phone'] = $phone;
			$conditionarray['email'] = $email;
			$conditionarray['address'] = $address;
			if($customers_id==0){
				$password_hash = password_hash($password, PASSWORD_DEFAULT);
				$conditionarray['password_hash'] = $password_hash;
			}
			$conditionarray['users_id'] = 0;
			
			if($customers_id==0){
				$conditionarray['created_on'] = date('Y-m-d H:i:s');
				$customers_id = $db->insert('customers', $conditionarray);
				if($customers_id){						
					$savemsg = 'Add';
				}
				else{
					$returnStr = 'Opps! Error occured which adding new Pest service';
				}
			}
			else{

				$update = $db->update('customers', $conditionarray, $customers_id);
				if($update){
					$savemsg = 'Update';
					$returnStr = "Customer information updated successfully.";
					$activity_feed_title = $returnStr;
					$activity_feed_link = "/Customers/view/$customers_id";
					
					$afData = array('created_on' => date('Y-m-d H:i:s'),
									'users_id' => 0,
									'activity_feed_title' =>  $activity_feed_title,
									'activity_feed_name' => $name,
									'activity_feed_link' => $activity_feed_link,
									'uri_table_name' => "customers",
									'uri_table_field_name' =>"customers_publish",
									'field_value' => 1);
					$db->insert('activity_feed', $afData);
				}
				else{
					$returnStr = 'No changes / Error occurred while updating data! Please try again.';
				}
			}
		}
		
		$resposeData = array('returnStr'=>$returnStr, 'savemsg'=>$savemsg);

		$apiResponse['message'] = '';
		$apiResponse['resposeData'] = $resposeData;
		$apiResponse['statusCode'] = 200;
	}
	elseif($segment2 == "getAllCustomers"){
		$keyword_search = $POST['keyword_search']??'';
		$page = intval($POST['page']);
		$limit = intval($POST['limit']);

		$resposeData = array();

		$dateformat = 'm/d/y';
		$timeformat = '12 hour';
		$currency = '$';
		
		$starting_val = ($page-1)*$limit;
		if($starting_val<0){$starting_val = 0;}
		
		$filterSql = "FROM customers WHERE customers_publish = 1";
		$bindData = array();
		if($keyword_search !=''){
			$keyword_search = addslashes(trim($keyword_search));
			if ( $keyword_search == "" ) { $keyword_search = " "; }
			$keyword_searches = explode (" ", $keyword_search);
			if ( strpos($keyword_search, " ") === false ) {$keyword_searches[0] = $keyword_search;}
			$num = 0;
			while ( $num < sizeof($keyword_searches) ) {
				$filterSql .= " AND CONCAT_WS(' ', name, phone, email, address) LIKE CONCAT('%', :keyword_search$num, '%')";
				$bindData['keyword_search'.$num] = trim($keyword_searches[$num]);
				$num++;
			}
		}
		
		$sqlquery = "SELECT * $filterSql ORDER BY name ASC, phone ASC, email ASC LIMIT $starting_val, $limit";
		$query = $db->getData($sqlquery, $bindData);
		if($query){			
			foreach($query as $oneRow){
			
				$customers_id = $oneRow['customers_id'];
				$name = stripslashes(trim($oneRow['name']));
				$phone = stripslashes(trim($oneRow['phone']));
				$email = stripslashes(trim($oneRow['email']));
				$address = stripslashes(trim($oneRow['address']));
				$resposeData[] = array('customers_id'=>$customers_id, 'name'=> $name, 'phone'=>$phone, 'email'=>$email, 'address'=>$address);
			}
		}

		$apiResponse['message'] = '';
		$apiResponse['resposeData'] = $resposeData;
		$apiResponse['statusCode'] = 200;
	}
	elseif($segment2 == "sendVarificationCode"){

		$email = $POST['email']??'';
		
		$resposeData = array();
		$resposeData['saveMsg'] = 'Error';
		$resposeData['message'] = 'Could not found email. Please register.';
		$resposeData['varification_code'] = '';
		if(!empty($email)){
			$sql = "SELECT * FROM customers WHERE email = '$email'";
			$tableObj = $db->getObj($sql, array(), 1);
			if($tableObj){
				$tableRow = $tableObj->fetch(PDO::FETCH_OBJ);
				$customers_id = $tableRow->customers_id;
				$branches_id = $tableRow->branches_id;
				$name = $tableRow->name;
				$email = $tableRow->email;
				$phone = $tableRow->phone;
				$address = $tableRow->address;

				$resposeData['message'] = 'Sorry! Could not send Varification Code to your email. Please contact with us.';

				$info = $db->supportEmail('info');
				$headersAdmin = array();
				$headersAdmin[] = "From: ".$info;
				$headersAdmin[] = "Reply-To: ".$info;
				$headersAdmin[] = "Organization: ".COMPANYNAME;
				$headersAdmin[] = "MIME-Version: 1.0";
				$headersAdmin[] = "Content-type: text/html; charset=iso-8859-1";
				$headersAdmin[] = "X-Priority: 3";
				$headersAdmin[] = "X-Mailer: PHP".phpversion();   

				$varificationCode = substr(time(), -6, 6);

				$subject = 'Forgot Password Varification Code of '.LIVE_DOMAIN;
				$message = "<html>";
				$message .= "<head>";
				$message .= "<title>$subject</title>";
				$message .= "</head>";
				$message .= "<body>";
				$message .= "<p>";
				$message .= "Dear <i><strong>$name</strong></i>,<br />";
				$message .= "Email: $email<br>";
				$message .= "Phone: $phone<br>";
				$message .= "Address: $address<br>";
				$message .= "Your Varification Code is:<br /><br />";
				$message .= "<strong style=\"font-size:40px\">$varificationCode</strong>";
				$message .= "</p>";
				$message .= "<p>";
				$message .= "<br />";
				$message .= "Please type this varification code then change your password.";
				$message .= "</p>";
				$message .= "</body>";
				$message .= "</html>";
				
				if(mail($email, $subject, $message, implode("\r\n", $headersAdmin))){
					$update = $db->update('customers', array('varification_code'=>$varificationCode, 'last_updated'=>date('Y-m-d H:i:s')), $customers_id);
					if($update){
						$resposeData['saveMsg'] = 'Sent';
						$resposeData['varification_code'] = $varificationCode;
					}
					else{
						$resposeData['message'] = 'Sorry! Could not update varification code. Please contact with us.';
					}
				}
				else{
					$resposeData['message'] = 'Sorry! Could not send varification code to your email. Please contact with us.';
				}				
			}
		}
		
		$apiResponse['resposeData'] = $resposeData;
		$apiResponse['statusCode'] = 200;
		
	}
	elseif($segment2 == "changeCustomerPassword"){

		$email = $POST['email']??'';
		$varification_code = $POST['varification_code']??'';
		$password = $POST['password']??'';
		
		$resposeData = array();
		$resposeData['saveMsg'] = 'Error';
		$resposeData['message'] = 'Could not found email. Please register.';
		if(empty($email)){
			$resposeData['message'] = 'Your email is missing.';
		}
		elseif(empty($password)){
			$resposeData['message'] = 'Your password is missing.';
		}
		elseif(empty($varification_code)){
			$resposeData['message'] = 'Your varification code is missing.';
		}
		else{
			$sql = "SELECT * FROM customers WHERE email = '$email' AND varification_code = '$varification_code'";
			$tableObj = $db->getObj($sql, array(), 1);
			if($tableObj){
				$tableRow = $tableObj->fetch(PDO::FETCH_OBJ);
				$customers_id = $tableRow->customers_id;
				$branches_id = $tableRow->branches_id;
				$name = $tableRow->name;
				$email = $tableRow->email;
				$phone = $tableRow->phone;
				$address = $tableRow->address;

				$resposeData['message'] = 'Sorry! Could not change password. Please contact with us.';

				$info = $db->supportEmail('info');
				$headersAdmin = array();
				$headersAdmin[] = "From: ".$info;
				$headersAdmin[] = "Reply-To: ".$info;
				$headersAdmin[] = "Organization: ".COMPANYNAME;
				$headersAdmin[] = "MIME-Version: 1.0";
				$headersAdmin[] = "Content-type: text/html; charset=iso-8859-1";
				$headersAdmin[] = "X-Priority: 3";
				$headersAdmin[] = "X-Mailer: PHP".phpversion();  

				$subject = 'You changed your password of '.LIVE_DOMAIN;
				$message = "<html>";
				$message .= "<head>";
				$message .= "<title>$subject</title>";
				$message .= "</head>";
				$message .= "<body>";
				$message .= "<p>";
				$message .= "Dear <i><strong>$name</strong></i>,<br />";
				$message .= "Email: $email<br>";
				$message .= "Phone: $phone<br>";
				$message .= "Address: $address<br>";
				$message .= "Your password has been changed successfully.<br>";
				$message .= "</p>";
				$message .= "<p>";
				$message .= "<br />";
				$message .= "Please try to login using email and new password.";
				$message .= "</p>";
				$message .= "</body>";
				$message .= "</html>";
				
				if(mail($email, $subject, $message, implode("\r\n", $headersAdmin))){
					$password_hash = password_hash($password, PASSWORD_DEFAULT);
					$update = $db->update('customers', array('password_hash'=>$password_hash, 'last_updated'=>date('Y-m-d H:i:s')), $customers_id);
					if($update){
						$resposeData['saveMsg'] = 'Changed';
						$resposeData['message'] = '';
					}
				}			
			}
			else{
				$resposeData['message'] = 'Your email and varification code could not match. Please try again with correct information.';
			}
		}
		
		$apiResponse['resposeData'] = $resposeData;
		$apiResponse['statusCode'] = 200;
		
	}
	elseif($segment2 == "changeCustomerPassword2"){

		$customers_id = intval($POST['customers_id']??0);
		$oldPassword = $POST['oldPassword']??'';
		$password = $POST['password']??'';
		
		$resposeData = array();
		$resposeData['saveMsg'] = 'Error';
		$resposeData['message'] = 'Could not found email. Please register.';
		if($customers_id==0){
			$resposeData['message'] = 'Customer ID is missing.';
		}
		elseif(empty($password)){
			$resposeData['message'] = 'Your password is missing.';
		}
		elseif(empty($oldPassword)){
			$resposeData['message'] = 'Old Password is missing.';
		}
		else{
			$sql = "SELECT * FROM customers WHERE customers_id = $customers_id";
			$tableObj = $db->getObj($sql, array(), 1);
			if($tableObj){
				$tableRow = $tableObj->fetch(PDO::FETCH_OBJ);
				$customers_id = $tableRow->customers_id;
				$branches_id = $tableRow->branches_id;
				$name = $tableRow->name;
				$email = $tableRow->email;
				$phone = $tableRow->phone;
				$address = $tableRow->address;

				$password_hash = $tableRow->password_hash;

				$password_hash_vrf = password_verify($oldPassword, $password_hash);
				if(!$password_hash_vrf){
					$apiResponse['message'] = 'Your password did not match. Try again with correct password.';
				}
				else{

					$resposeData['message'] = 'Sorry! Could not change password. Please contact with us.';

					$info = $db->supportEmail('info');
					$headersAdmin = array();
					$headersAdmin[] = "From: ".$info;
					$headersAdmin[] = "Reply-To: ".$info;
					$headersAdmin[] = "Organization: ".COMPANYNAME;
					$headersAdmin[] = "MIME-Version: 1.0";
					$headersAdmin[] = "Content-type: text/html; charset=iso-8859-1";
					$headersAdmin[] = "X-Priority: 3";
					$headersAdmin[] = "X-Mailer: PHP".phpversion();  

					$subject = 'You changed your password of '.LIVE_DOMAIN;
					$message = "<html>";
					$message .= "<head>";
					$message .= "<title>$subject</title>";
					$message .= "</head>";
					$message .= "<body>";
					$message .= "<p>";
					$message .= "Dear <i><strong>$name</strong></i>,<br />";
					$message .= "Email: $email<br>";
					$message .= "Phone: $phone<br>";
					$message .= "Address: $address<br>";
					$message .= "Your password has been changed successfully.<br>";
					$message .= "</p>";
					$message .= "<p>";
					$message .= "<br />";
					$message .= "Please try to login using email and new password.";
					$message .= "</p>";
					$message .= "</body>";
					$message .= "</html>";
					
					if(mail($email, $subject, $message, implode("\r\n", $headersAdmin))){
						$password_hash = password_hash($password, PASSWORD_DEFAULT);
						$update = $db->update('customers', array('password_hash'=>$password_hash, 'last_updated'=>date('Y-m-d H:i:s')), $customers_id);
						if($update){
							$resposeData['saveMsg'] = 'Changed';
							$resposeData['message'] = 'Your password has been changed successfully.';
						}
					}			
				}
			}
		}
		
		$apiResponse['resposeData'] = $resposeData;
		$apiResponse['statusCode'] = 200;
		
	}
	elseif($segment2 == "getInvoiceDetails"){
		$pos_id = intval($POST['pos_id']);
		$resposeData = array();
		$posObj = $db->getObj("SELECT * FROM pos WHERE pos_id = :pos_id", array('pos_id'=>$pos_id),1);
		if($posObj){
			$pos_onerow = $posObj->fetch(PDO::FETCH_OBJ);
			
			$pos_id = $pos_onerow->pos_id;
			$resposeData['pos_id'] = $pos_id;
			$resposeData['invoice_no'] = $pos_onerow->invoice_no;
			$resposeData['branches_id'] = $branches_id = $pos_onerow->branches_id;
			$resposeData['customers_id'] = $customers_id = $pos_onerow->customers_id;
			$resposeData['service_fee'] = $pos_onerow->service_fee;
			$resposeData['order_status'] = $pos_onerow->order_status;
			$pos_publish = $pos_onerow->pos_publish;
			$resposeData['is_due'] = $pos_onerow->is_due;
			$resposeData['sales_datetime'] = $pos_onerow->sales_datetime;
			$resposeData['service_datetime'] = $pos_onerow->service_datetime;
			$resposeData['paymentIntentId'] = $pos_onerow->paymentIntentId;
			$resposeData['paymentMethodId'] = $pos_onerow->paymentMethodId;
			
			$customername = $customeremail = $customerphone = $customeraddress = '';
			$customerObj = $db->getObj("SELECT * FROM customers WHERE customers_id = $customers_id", array());
			if($customerObj){
				$customerrow = $customerObj->fetch(PDO::FETCH_OBJ);	
				$name = $customerrow->name;
				$company = $customerrow->company;			
				$customername = $company;
				if($customername !=''){$customername .= ', ';}
				$customername .= $name;
				
				$customeremail = $customerrow->email;
				$customerphone = $customerrow->phone;
				$customeraddress = $customerrow->address;
			}

			$resposeData['customername'] = $customername;
			$resposeData['customeremail'] = $customeremail;
			$resposeData['customerphone'] = $customerphone;
			$resposeData['customeraddress'] = $customeraddress;

			$branchesName = $branchesAddress = '';
			$branchesObj = $db->getObj("SELECT name, address FROM branches WHERE branches_id = $branches_id", array());
			if($branchesObj){
				$branchesRow = $branchesObj->fetch(PDO::FETCH_OBJ);	
				$branchesName = $branchesRow->name;
				$branchesAddress = $branchesRow->address;
			}

			$resposeData['branchesName'] = $branchesName;
			$resposeData['branchesAddress'] = $branchesAddress;
			$tax_inclusive1 = $pos_onerow->tax_inclusive1;

			$taxable_total = $nontaxable_total = 0.00;
			$cartData = array();
			$sqlquery = "SELECT * FROM pos_cart WHERE pos_id = $pos_id";
			$query = $db->getObj($sqlquery, array());
			if($query){
				$i=0;
				while($row = $query->fetch(PDO::FETCH_OBJ)){
					$i++;
					$pos_cart_id = $row->pos_cart_id;
					$sales_price = $row->sales_price;
					$qty = $row->qty;									
					$total = round($sales_price * $qty,2);
					$taxable_total = $taxable_total+$total;
					
					$description = stripslashes(trim($row->description));
					$item_id = $row->item_id;

					$servicesName = '';
					$servicesObj = $db->getObj("SELECT name FROM pest_services WHERE pest_services_id = $item_id", array());
					if($servicesObj){
						$servicesName = $servicesObj->fetch(PDO::FETCH_OBJ)->name;	
					}					

					$cartData[] = array('sl'=>$i, 'pos_cart_id'=>$pos_cart_id, 'item_id'=>$item_id, 'servicesName'=>$servicesName, 'description'=>$description, 'qty'=>$qty, 'sales_price'=>$sales_price);
				}
			}

			$resposeData['cartData'] = $cartData;
			$resposeData['taxable_total'] = $taxable_total;
			$resposeData['nontaxable_total'] = $nontaxable_total;
			$resposeData['taxable_total'] = $taxable_total;
			$tiStr = '';
			if($pos_onerow->tax_inclusive1>0){$tiStr = ' Inclusive';}
			$resposeData['taxes_name1'] = "$pos_onerow->taxes_name1 ($pos_onerow->taxes_percentage1%$tiStr)";
			$taxes_total1 = calculateTax($taxable_total, $pos_onerow->taxes_percentage1, $pos_onerow->tax_inclusive1);
			$resposeData['tax_inclusive1'] = intval($pos_onerow->tax_inclusive1);
			$resposeData['taxes_total1'] = $taxes_total1;

			$totalpayment = 0;
			$paymentData = array();
			$ppSql = "SELECT payment_method, payment_amount, payment_datetime FROM pos_payment WHERE pos_id = $pos_id AND payment_method != 'Change'";
			$ppQueryObj = $db->getObj($ppSql, array());
			if($ppQueryObj){
				while($onerow = $ppQueryObj->fetch(PDO::FETCH_OBJ)){
					$payment_amount = $onerow->payment_amount;
					$payment_datetime =  date('m/d/Y g:i a', strtotime($onerow->payment_datetime));
					
					$totalpayment = $totalpayment+$payment_amount;
					
					$paymentData[] = array('payment_datetime'=>$payment_datetime, 'payment_method'=>$onerow->payment_method, 'payment_amount'=>$payment_amount);
				}
			}
			$db->writeIntoLog("pos_id: $pos_id, ppSql: $ppSql, paymentData:".json_encode($paymentData));
		
			$resposeData['paymentData'] = $paymentData;
			$resposeData['totalpayment'] = $totalpayment;
			if($pos_onerow->tax_inclusive1>0){
				$taxes_total1 = 0;
			}
			$resposeData['grand_total'] = $taxable_total+$taxes_total1+$nontaxable_total+$pos_onerow->service_fee;

		}

		$apiResponse['message'] = '';
		$apiResponse['resposeData'] = $resposeData;
		$apiResponse['statusCode'] = 200;
	}
	elseif($segment2 == "getAllInvoices"){
		
		$scustomers_id = intval($POST['customers_id']??0);
		$sbranches_id = intval($POST['branches_id']);
		$sorder_status = intval($POST['order_status']??0);
		$page = intval($POST['page']);
		$limit = intval($POST['limit']);

		$resposeData = array();

		$dateformat = 'm/d/y';
		$timeformat = '12 hour';
		$currency = '$';
		
		$starting_val = ($page-1)*$limit;
		if($starting_val<0){$starting_val = 0;}
		
		$filterSql = "";
		$bindData = array();
		if($sorder_status<3){
			$filterSql .= " AND pos.order_status = :order_status";
			$bindData['order_status'] = $sorder_status;			
		}
		if($sbranches_id >0){
			$filterSql .= " AND pos.branches_id = :branches_id";
			$bindData['branches_id'] = $sbranches_id;
		}
		if($scustomers_id >0){
			$filterSql .= " AND pos.customers_id = :customers_id";
			$bindData['customers_id'] = $scustomers_id;
		}
		
		$sqlquery = "SELECT pos.*, SUM(pos_cart.sales_price*pos_cart.qty) AS taxableTotal FROM pos, pos_cart WHERE pos.pos_id = pos_cart.pos_id AND pos.pos_publish = 1 $filterSql GROUP BY pos.pos_id ORDER BY pos.sales_datetime DESC, pos.invoice_no DESC LIMIT $starting_val, $limit";
		$query = $db->getData($sqlquery, $bindData);
		// var_dump($limit);exit;
		$str = '';
		if($query){
			$customersId = $brancIds = array();
			foreach($query as $oneRow){
				$customersId[$oneRow['customers_id']] = '';
				$brancIds[$oneRow['branches_id']] = '';
			}					
			
			if(!empty($customersId)){
				$tableObj = $db->getObj("SELECT customers_id, name, phone, email, address FROM customers WHERE customers_id IN (".implode(', ', array_keys($customersId)).")", array());
				if($tableObj){
					while($tableOneRow = $tableObj->fetch(PDO::FETCH_OBJ)){							
						$customersId[$tableOneRow->customers_id] = array(trim(stripslashes($tableOneRow->name)), $tableOneRow->phone, $tableOneRow->email, $tableOneRow->address);
					}
				}
			}					
			
			if(!empty($brancIds)){
				$tableObj = $db->getObj("SELECT branches_id, name FROM branches WHERE branches_id IN (".implode(', ', array_keys($brancIds)).")", array());
				if($tableObj){
					while($oneTableRow = $tableObj->fetch(PDO::FETCH_OBJ)){							
						$brancIds[$oneTableRow->branches_id] = trim(stripslashes($oneTableRow->name));
					}
				}
			}
			
			foreach($query as $oneRow){
			
				$pos_id = $oneRow['pos_id'];
				$invoice_no = $oneRow['invoice_no'];
				$customers_id = $oneRow['customers_id'];
				$customername = $customersId[$customers_id][0];
				$customerphone = $customersId[$customers_id][1];
				$customeremail = $customersId[$customers_id][2];
				$customeraddress= $customersId[$customers_id][3];
				
				$date =  date($dateformat, strtotime($oneRow['sales_datetime']));
				if($timeformat=='24 hour'){$time =  date('H:i', strtotime($oneRow['sales_datetime']));}
				else{$time =  date('g:i a', strtotime($oneRow['sales_datetime']));}
				
				$branches_id = $oneRow['branches_id'];
				$branchName = $brancIds[$branches_id]??'&nbsp;';
				
				$taxable_total = $oneRow['taxableTotal'];
								
				$taxes_total1 = calculateTax($taxable_total, $oneRow['taxes_percentage1'], $oneRow['tax_inclusive1']);
				
				$tax_inclusive1 = $oneRow['tax_inclusive1'];
				$order_status = intval($oneRow['order_status']);
				$service_fee = floatval($oneRow['service_fee']);
				
				$taxestotal = $taxes_total1;
				
				$grand_total = $taxable_total+$taxestotal+$service_fee;
				if($tax_inclusive1>0){$grand_total -= $taxes_total1;}

				$resposeData[] = array('pos_id'=>$pos_id, 'order_status'=>$order_status, 'dateTime'=> "$date $time", 'invoice_no'=>"s$invoice_no", 'customername'=>$customername, 'customerphone'=>$customerphone, 'customeremail'=>$customeremail, 'customeraddress'=>$customeraddress, 'branchName'=>$branchName, 'grand_total'=>$grand_total);

			}
		}

		$apiResponse['message'] = '';
		$apiResponse['resposeData'] = $resposeData;
		$apiResponse['statusCode'] = 200;
	}
	elseif($segment2 == "saveInvoices"){
		$savemsg = 'error';
		$returnStr = '';
		$pos_id = intval($POST['pos_id']??0);
		$users_id = intval($POST['users_id']??0);
		$customers_id = intval($POST['customers_id']??0);
		$service_fee = floatval($POST['service_fee']??0);
		$taxes_percentage = floatval($POST['taxes_percentage']??0);
		$tax_inclusive = intval($POST['tax_inclusive']??0);
		$branches_id = intval($POST['branches_id']??0);
		$service_datetime = $POST['service_datetime']??'';
		
		if($customers_id==0){
			$returnStr = "Customer Name is blank.";
		}
		elseif($branches_id ==0){
			$returnStr = "Branch Name is blank.";
		}
		elseif($service_datetime ==''){
			$returnStr = "Service Date & time is blank.";
		}

		$conditionarray = array();
		if(empty($returnStr)){
			$conditionarray['customers_id'] = $customers_id;
			$conditionarray['service_fee'] = $service_fee;
			$conditionarray['taxes_name1'] = 'Tax';
			$conditionarray['taxes_percentage1'] = $taxes_percentage;
			$conditionarray['tax_inclusive1'] = $tax_inclusive;
			$conditionarray['branches_id'] = $branches_id;
			$conditionarray['last_updated'] = date('Y-m-d H:i:s');
			$conditionarray['service_datetime'] = date('Y-m-d H:i:s', strtotime($service_datetime));
			
			if($pos_id==0){
				$invoice_no = 1;
				$queryObj = $db->getObj("SELECT invoice_no FROM pos ORDER BY invoice_no DESC LIMIT 0, 1", array());
				if($queryObj){
					$invoice_no = intval($queryObj->fetch(PDO::FETCH_OBJ)->invoice_no)+1;
				}
				
				$conditionarray['invoice_no'] = $invoice_no;
				$conditionarray['users_id'] = $users_id;
				$conditionarray['invoice_no'] = $invoice_no;
				$conditionarray['sales_datetime'] = date('Y-m-d H:i:s');
				$conditionarray['order_status'] = 1;
				$conditionarray['created_on'] = date('Y-m-d H:i:s');
				$conditionarray['is_due'] = 1;
				$pos_id = $db->insert('pos', $conditionarray);
				if($pos_id){					
					$savemsg = 'Add';
				}
				else{
					$returnStr = 'Opps! Error occured which adding new Invoice';
				}
			}
			else{

				$update = $db->update('pos', $conditionarray, $pos_id);
				if($update){
					$savemsg = 'Update';
					$returnStr = "Invoice information updated successfully.";
					$activity_feed_title = $returnStr;
					$activity_feed_link = "/InvoiceDetails/$pos_id";
					
					$afData = array('created_on' => date('Y-m-d H:i:s'),
									'users_id' => $users_id,
									'activity_feed_title' =>  $activity_feed_title,
									'activity_feed_name' => $name,
									'activity_feed_link' => $activity_feed_link,
									'uri_table_name' => "pos",
									'uri_table_field_name' =>"pos_publish",
									'field_value' => 1);
					$db->insert('activity_feed', $afData);
				}
				else{
					$returnStr = 'No changes / Error occurred while updating data! Please try again.';
				}
			}
		}
		
		$resposeData = array('returnStr'=>$returnStr, 'savemsg'=>$savemsg, 'pos_id'=>$pos_id);

		$apiResponse['message'] = '';
		$apiResponse['resposeData'] = $resposeData;
		$apiResponse['statusCode'] = 200;
	}
	elseif($segment2 == "saveCart"){
		$pos_id = $postpos_id = intval($POST['pos_id']??0);
		$cartData = $POST['cartData']??array();
		$returnStr = "";
		$savemsg = 'Error';
		if($pos_id==0){
			$returnStr = "Invoice is blank.";
		}
		elseif(empty($cartData)){
			$returnStr = "Cart is blank.";
		}

		$db->writeIntoLog("pos_id: $pos_id, returnStr: $returnStr, POST:".json_encode($POST));
		$updateCount = $insertCount = 0;
		$conditionarray = array();
		if(empty($returnStr)){
			foreach($cartData as $oneCart){
				$pos_cart_id = intval($oneCart['pos_cart_id']??0);
				$services_id = intval($oneCart['services_id']);
				$description = $oneCart['description'];
				$sales_price = $oneCart['sales_price'];				
				$qty = $oneCart['qty'];

				$conditionarray['pos_id'] = $pos_id;
				$conditionarray['item_id'] = $services_id;
				$conditionarray['description'] = $description;
				$conditionarray['sales_price'] = $sales_price;
				$conditionarray['qty'] = $qty;
				$conditionarray['shipping_qty'] = 0;

				if($pos_cart_id>0){
					$update = $db->update('pos_cart', $conditionarray, $pos_cart_id);
					if($update){						
						$savemsg = 'Update';
						$updateCount++;
					}
				}
				else{
					$pos_cart_id = $db->insert('pos_cart', $conditionarray);
					if($pos_cart_id){						
						$savemsg = 'Add';						
						$insertCount++;
					}
				}
			}
		}

		if($postpos_id == 0 && $insertCount == 0){
			$returnStr = 'Opps! Error occured which adding new cart into Invoice';
		}
		elseif($postpos_id>0 && $updateCount == 0){
			$returnStr = 'Opps! There is no changes into Invoice Cart';
		}
		else{
			$returnStr = "$insertCount cart data inserted & $updateCount cart data updated";
		}
		
		$resposeData = array('returnStr'=>$returnStr, 'savemsg'=>$savemsg);

		$apiResponse['message'] = '';
		$apiResponse['resposeData'] = $resposeData;
		$apiResponse['statusCode'] = 200;
	}
	elseif($segment2 == "getAllCart"){
		$pos_id = intval($POST['pos_id']??0);
		$resposeData = array();
		if($pos_id >0){
			$tableObj = $db->getObj("SELECT * FROM pos_cart WHERE pos_id = $pos_id", array());
			if($tableObj){
				while($tableOneRow = $tableObj->fetch(PDO::FETCH_OBJ)){
					$servicesName = '';
					$servicesObj = $db->getObj("SELECT name FROM pest_services WHERE pest_services_id = $tableOneRow->item_id", array());
					if($servicesObj){
						$servicesName = $servicesObj->fetch(PDO::FETCH_OBJ)->name;	
					}
					$resposeData[] = array('pos_cart_id'=>intval($tableOneRow->pos_cart_id), 'services_id'=>intval($tableOneRow->item_id), 'servicesName'=>$servicesName, 'description'=>trim(stripslashes($tableOneRow->description)), 'sales_price'=>floatval($tableOneRow->sales_price), 'qty'=>floatval($tableOneRow->qty), 'shipping_qty'=>floatval($tableOneRow->shipping_qty));
				}
			}
		}

		$apiResponse['message'] = '';
		$apiResponse['resposeData'] = $resposeData;
		$apiResponse['statusCode'] = 200;
	}
	elseif($segment2 == "invoiecNotifications"){
		$pos_id = intval(array_key_exists('pos_id', $POST) ? $POST['pos_id'] : 0);
		$users_id = intval(array_key_exists('users_id', $POST) ? $POST['users_id'] : 0);
		$order_status = intval(array_key_exists('order_status', $POST) ? $POST['order_status'] : 0);
		
		$db->writeIntoLog("pos_id: $pos_id, order_status: $order_status, POST:".json_encode($POST));

		$resposeData = array();
		$posObj = $db->getObj("SELECT pos_id, customers_id, order_status, service_datetime, paymentIntentId, paymentMethodId, is_due FROM pos WHERE pos_id = :pos_id", array('pos_id'=>$pos_id),1);
		if($posObj){
			$posRow = $posObj->fetch(PDO::FETCH_OBJ);
			$pos_id = $posRow->pos_id;
			$customers_id = $posRow->customers_id;
			$oldorder_status = $posRow->order_status;
			$paymentIntentId = $posRow->paymentIntentId;
			$paymentMethodId = $posRow->paymentMethodId;
			$is_due = intval($posRow->is_due);

			if($is_due==1 && in_array($order_status, array(0, 2)) && $paymentMethodId !=''){
				$Stripe = new Stripe($db);
				if($order_status==0){
					$resposeData = $takePaymentData = $Stripe->takePayment($pos_id);
					if($takePaymentData['actionStatus']=='Success'){

						$updateData = array('order_status'=>$order_status, 'is_due' => 0, 'sales_datetime'=> date('Y-m-d H:i:s'), 'last_updated'=> date('Y-m-d H:i:s'));
						$db->update('pos', $updateData, $pos_id);
						$resposeData = array('status'=>'paymentCompleted');
					}
					$db->writeIntoLog("takePaymentData:".json_encode($takePaymentData));
				}
				elseif($order_status==2){
					$resposeData = $cancelPaymentData = $Stripe->cancelPayment($pos_id);
					if($cancelPaymentData['actionStatus']=='Success'){
						$updateData = array('order_status'=>$order_status, 'last_updated'=> date('Y-m-d H:i:s'));
						$db->update('pos', $updateData, $pos_id);
					}
					$db->writeIntoLog("cancelPaymentData:".json_encode($cancelPaymentData));
				}
			}
			else{
				$updateData = array('order_status'=>$order_status, 'last_updated'=> date('Y-m-d H:i:s'));
				$db->update('pos', $updateData, $pos_id);
				$resposeData = array('status'=>'Success');
				$jsonResponse['message'] = 'Order status updated successful.';
			}

			$apiResponse['message'] = '';
			$apiResponse['resposeData'] = $resposeData;
			$apiResponse['statusCode'] = 200;
		}
	}	
	elseif($segment2 == "checkNotifications"){
		
		$order_status = $pos_id = 0;
		$branches_id = intval($POST['branches_id']??0);
		$sql = "SELECT pos_id FROM pos WHERE order_status = 1";
		if($branches_id>0){
			$sql .= " AND branches_id = $branches_id";
		}
		$sql .= " ORDER BY pos_id DESC";
		$tableObj = $db->getObj($sql, array());
		$posIds = array();
		if($tableObj){
			while($tableOneRow = $tableObj->fetch(PDO::FETCH_OBJ)){
				$pos_id = $tableOneRow->pos_id;
				$posIds[$pos_id] = '';
			}
			$order_status = intval(count($posIds));
		}
		$resposeData = array();
		$resposeData['order_status'] = $order_status;
		$resposeData['pos_id'] = $pos_id;

		$apiResponse['message'] = '';
		$apiResponse['resposeData'] = $resposeData;
		$apiResponse['statusCode'] = 200;
		
	}
	elseif($segment2 == "paymentStatusUpdate"){

		$pos_id = intval(array_key_exists('pos_id', $POST) ? $POST['pos_id'] : 0);
		$users_id = intval(array_key_exists('users_id', $POST) ? $POST['users_id'] : 0);
		$payment_amount = floatval(array_key_exists('payment_amount', $POST) ? $POST['payment_amount'] : 0.00);
		$payment_method = array_key_exists('payment_method', $POST) ? $POST['payment_method'] :'';
		$paymentIntentId = array_key_exists('paymentIntentId', $POST) ? $POST['paymentIntentId'] :'';
		$paymentMethodId = array_key_exists('paymentMethodId', $POST) ? $POST['paymentMethodId'] :'';

		$db->writeIntoLog("pos_id: $pos_id, paymentIntentId: $paymentIntentId, paymentMethodId: $paymentMethodId, POST:".json_encode($POST));
		$saveMsg = 'error';
		$sql = "SELECT pos_id FROM pos WHERE pos_id = $pos_id AND order_status = 1 ORDER BY pos_id DESC";
		$tableObj = $db->getObj($sql, array());
		if($tableObj){
			while($tableOneRow = $tableObj->fetch(PDO::FETCH_OBJ)){
				$pos_id = $tableOneRow->pos_id;
				$updateData = array('paymentIntentId'=>$paymentIntentId, 'paymentMethodId'=>$paymentMethodId, 'is_due' => 0, 'sales_datetime'=> date('Y-m-d H:i:s'), 'last_updated'=> date('Y-m-d H:i:s'));
				$update = $db->update('pos', $updateData, $pos_id);
				if($update){
					$saveMsg = 'success';

					$ppData = array('pos_id' => $pos_id,
								'payment_method' => $payment_method,
								'payment_amount' => round($payment_amount,2),	
								'payment_datetime' => date('Y-m-d H:i:s'),
								'drawer' => ''
								);
					$db->insert('pos_payment', $ppData);
				}
			}
		}
		
		$resposeData = array();
		$resposeData['saveMsg'] = $saveMsg;
		$resposeData['pos_id'] = $pos_id;

		$apiResponse['message'] = '';
		$apiResponse['resposeData'] = $resposeData;
		$apiResponse['statusCode'] = 200;
		
	}
	elseif($segment2 == "getAllServices"){
		$keyword_search = $POST['keyword_search']??'';
		$page = intval($POST['page']);
		$limit = intval($POST['limit']);

		$resposeData = array();		
		$starting_val = ($page-1)*$limit;
		if($starting_val<0){$starting_val = 0;}
		
		$filterSql = "FROM pest_services WHERE pest_services_publish = 1";
		$bindData = array();
		if($keyword_search !=''){
			$keyword_search = addslashes(trim($keyword_search));
			if ( $keyword_search == "" ) { $keyword_search = " "; }
			$keyword_searches = explode (" ", $keyword_search);
			if ( strpos($keyword_search, " ") === false ) {$keyword_searches[0] = $keyword_search;}
			$num = 0;
			while ( $num < sizeof($keyword_searches) ) {
				$filterSql .= " AND CONCAT_WS(' ', name, description) LIKE CONCAT('%', :keyword_search$num, '%')";
				$bindData['keyword_search'.$num] = trim($keyword_searches[$num]);
				$num++;
			}
		}
		
		$sqlquery = "SELECT * $filterSql ORDER BY name ASC LIMIT $starting_val, $limit";
		$query = $db->getData($sqlquery, $bindData);
		if($query){			
			foreach($query as $oneRow){
			
				$pest_services_id = $oneRow['pest_services_id'];
				$name = stripslashes(trim($oneRow['name']));
				$description = stripslashes(trim((string) $oneRow['description']));
				$price = $oneRow['price'];
				$resposeData[] = array('pest_services_id'=>$pest_services_id, 'name'=> $name, 'description'=> $description, 'price'=>$price);
			}
		}

		$apiResponse['message'] = '';
		$apiResponse['resposeData'] = $resposeData;
		$apiResponse['statusCode'] = 200;
	}
	elseif($segment2 == "getAllWebServices"){
		$keyword_search = $POST['keyword_search']??'';
		$page = intval($POST['page']??1);
		$limit = intval($POST['limit']??20);

		$resposeData = array();		
		$starting_val = ($page-1)*$limit;
		if($starting_val<0){$starting_val = 0;}
		
		$filterSql = "FROM services WHERE services_publish = 1";
		$bindData = array();
		if($keyword_search !=''){
			$keyword_search = addslashes(trim($keyword_search));
			if ( $keyword_search == "" ) { $keyword_search = " "; }
			$keyword_searches = explode (" ", $keyword_search);
			if ( strpos($keyword_search, " ") === false ) {$keyword_searches[0] = $keyword_search;}
			$num = 0;
			while ( $num < sizeof($keyword_searches) ) {
				$filterSql .= " AND CONCAT_WS(' ', name, short_description, uri_value, description) LIKE CONCAT('%', :keyword_search$num, '%')";
				$bindData['keyword_search'.$num] = trim($keyword_searches[$num]);
				$num++;
			}
		}
		
		$sqlquery = "SELECT * $filterSql ORDER BY name ASC LIMIT $starting_val, $limit";
		$query = $db->getData($sqlquery, $bindData);
		if($query){			
			foreach($query as $oneRow){
			
				$services_id = $oneRow['services_id'];
				$name = stripslashes(trim($oneRow['name']));
				$uri_value = stripslashes(trim($oneRow['uri_value']));
				$font_awesome = stripslashes(trim($oneRow['font_awesome']));
				$short_description = stripslashes(trim((string) $oneRow['short_description']));
				$description = stripslashes(trim((string) $oneRow['description']));
				$serviceImg = '';
				$filePath = "./assets/accounts/serv_$services_id".'_';
				$pics = glob($filePath."*.jpg");
				if(!$pics){
					$pics = glob($filePath."*.png");
				}
				if($pics){
					foreach($pics as $onePicture){
						$serviceImg = baseURL.str_replace('./', '/', $onePicture);
					}
				}
				if(empty($serviceImg)){
					$serviceImg = baseURL."/assets/images/event/1.jpg";
				}

				$serviceIcon = baseURL."/assets/images/icons/$font_awesome.png";

				$resposeData[] = array('services_id'=>$services_id, 'name'=> $name, 'uri_value'=> $uri_value, 'font_awesome'=> $font_awesome, 'short_description'=> $short_description, 'description'=> $description, 'serviceIcon'=>$serviceIcon, 'serviceImg'=>$serviceImg);
			}
		}

		$apiResponse['message'] = '';
		$apiResponse['resposeData'] = $resposeData;
		$apiResponse['statusCode'] = 200;
	}
	elseif($segment2 == "saveServices"){
		$savemsg = 'error';
		$returnStr = '';
		$users_id = intval($POST['users_id']??0);
		$pest_services_id = intval($POST['pest_services_id']??0);
		$name = trim(addslashes($POST['name']??''));
		$description = trim(addslashes($POST['description']??''));
		$price = trim(addslashes($POST['price']??''));
		
		$conditionarray = array();
		$conditionarray['name'] = $name;
		$conditionarray['description'] = $description;
		$conditionarray['price'] = $price;
		$conditionarray['last_updated'] = date('Y-m-d H:i:s');
		$conditionarray['users_id'] = $users_id;
		
		$duplSql = "SELECT pest_services_publish, pest_services_id FROM pest_services WHERE name = :name";
		$bindData = array('name'=>$name);
		if($pest_services_id>0){
			$duplSql .= " AND pest_services_id != :pest_services_id";
			$bindData['pest_services_id'] = $pest_services_id;
		}
		$duplSql .= " LIMIT 0, 1";
		$duplRows = 0;
		$pest_servicesObj = $db->getData($duplSql, $bindData);
		if($pest_servicesObj){
			foreach($pest_servicesObj as $onerow){
				$duplRows = 1;
				$pest_services_publish = $onerow['pest_services_publish'];
				if($pest_services_id==0 && $pest_services_publish==0){
					$pest_services_id = $onerow['pest_services_id'];
					$db->update('pest_services', array('pest_services_publish'=>1), $pest_services_id);
					$duplRows = 0;
					$returnStr = 'Update';
				}
			}
		}
		
		if($duplRows>0 && !empty($name)){
			$returnStr = "This name is already exist! Please try again with different name.";
		}
		elseif(empty($name)){
			$returnStr = "Name is blank.";
		}
		else{			
			if($pest_services_id==0){
				$conditionarray['created_on'] = date('Y-m-d H:i:s');
				$pest_services_id = $db->insert('pest_services', $conditionarray);
				if($pest_services_id){						
					$savemsg = 'Add';
				}
				else{
					$returnStr = 'Opps! Error occured which adding new Pest service';
				}
			}
			else{

				$update = $db->update('pest_services', $conditionarray, $pest_services_id);
				if($update){
					$activity_feed_title = 'Services was edited';
					$activity_feed_link = "/Manage_Data/pest_services/view/$pest_services_id";
					
					$afData = array('created_on' => date('Y-m-d H:i:s'),
									'users_id' => $users_id,
									'activity_feed_title' =>  $activity_feed_title,
									'activity_feed_name' => $name,
									'activity_feed_link' => $activity_feed_link,
									'uri_table_name' => "pest_services",
									'uri_table_field_name' =>"pest_services_publish",
									'field_value' => 1);
					$db->insert('activity_feed', $afData);
					
					$savemsg = 'Update';
				}
				else{
					$returnStr = 'No changes / Error occurred while updating data! Please try again.';
				}
			}
		}
		
		$resposeData = array('returnStr'=>$returnStr, 'savemsg'=>$savemsg);

		$apiResponse['message'] = '';
		$apiResponse['resposeData'] = $resposeData;
		$apiResponse['statusCode'] = 200;
	}
	elseif($segment2 == "getAllWebPages"){
		$keyword_search = $POST['keyword_search']??'';
		$page = intval($POST['page']??1);
		$limit = intval($POST['limit']??20);

		$resposeData = array();		
		$starting_val = ($page-1)*$limit;
		if($starting_val<0){$starting_val = 0;}
		
		$filterSql = "FROM pages WHERE pages_publish = 1";
		$bindData = array();
		if($keyword_search !=''){
			$keyword_search = addslashes(trim($keyword_search));
			if ( $keyword_search == "" ) { $keyword_search = " "; }
			$keyword_searches = explode (" ", $keyword_search);
			if ( strpos($keyword_search, " ") === false ) {$keyword_searches[0] = $keyword_search;}
			$num = 0;
			while ( $num < sizeof($keyword_searches) ) {
				$filterSql .= " AND CONCAT_WS(' ', name, uri_value, short_description, description) LIKE CONCAT('%', :keyword_search$num, '%')";
				$bindData['keyword_search'.$num] = trim($keyword_searches[$num]);
				$num++;
			}
		}
		
		$sqlquery = "SELECT * $filterSql ORDER BY name ASC LIMIT $starting_val, $limit";
		$query = $db->getData($sqlquery, $bindData);
		if($query){			
			foreach($query as $oneRow){
			
				$pages_id = $oneRow['pages_id'];
				$name = stripslashes(trim($oneRow['name']));
				$uri_value = stripslashes(trim($oneRow['uri_value']));
				$short_description = stripslashes(trim((string) $oneRow['short_description']));
				$description = stripslashes(trim((string) $oneRow['description']));
				$pagesImg = '';
				$filePath = "./assets/accounts/page_$pages_id".'_';
				$pics = glob($filePath."*.jpg");
				if(!$pics){
					$pics = glob($filePath."*.png");
				}
				if($pics){
					foreach($pics as $onePicture){
						$pagesImg = baseURL.str_replace('./', '/', $onePicture);
					}
				}
				if(empty($pagesImg)){
					$pagesImg = baseURL."/assets/images/event/1.jpg";
				}

				$resposeData[] = array('pages_id'=>$pages_id, 'name'=> $name, 'uri_value'=> $uri_value, 'short_description'=> $short_description, 'description'=> $description, 'pagesImg'=>$pagesImg);
			}
		}

		$apiResponse['message'] = '';
		$apiResponse['resposeData'] = $resposeData;
		$apiResponse['statusCode'] = 200;
	}
	elseif($segment2 == "getAllWebBanners"){
		$keyword_search = $POST['keyword_search']??'';
		$page = intval($POST['page']??1);
		$limit = intval($POST['limit']??20);

		$resposeData = array();		
		$starting_val = ($page-1)*$limit;
		if($starting_val<0){$starting_val = 0;}
		
		$filterSql = "FROM banners WHERE banners_publish = 1";
		$bindData = array();
		if($keyword_search !=''){
			$keyword_search = addslashes(trim($keyword_search));
			if ( $keyword_search == "" ) { $keyword_search = " "; }
			$keyword_searches = explode (" ", $keyword_search);
			if ( strpos($keyword_search, " ") === false ) {$keyword_searches[0] = $keyword_search;}
			$num = 0;
			while ( $num < sizeof($keyword_searches) ) {
				$filterSql .= " AND CONCAT_WS(' ', name, description) LIKE CONCAT('%', :keyword_search$num, '%')";
				$bindData['keyword_search'.$num] = trim($keyword_searches[$num]);
				$num++;
			}
		}
		
		$sqlquery = "SELECT * $filterSql ORDER BY name ASC LIMIT $starting_val, $limit";
		$query = $db->getData($sqlquery, $bindData);
		if($query){			
			foreach($query as $oneRow){
			
				$banners_id = $oneRow['banners_id'];
				$name = stripslashes(trim($oneRow['name']));
				$description = stripslashes(trim((string) $oneRow['description']));
				$resposeData[] = array('banners_id'=>$banners_id, 'name'=> $name,'description'=> $description);
			}
		}

		$apiResponse['message'] = '';
		$apiResponse['resposeData'] = $resposeData;
		$apiResponse['statusCode'] = 200;
	}
	elseif($segment2 == "getAllWebNewsArticles"){
		$keyword_search = $POST['keyword_search']??'';
		$page = intval($POST['page']??1);
		$limit = intval($POST['limit']??20);

		$resposeData = array();		
		$starting_val = ($page-1)*$limit;
		if($starting_val<0){$starting_val = 0;}
		
		$filterSql = "FROM news_articles WHERE news_articles_publish = 1";
		$bindData = array();
		if($keyword_search !=''){
			$keyword_search = addslashes(trim($keyword_search));
			if ( $keyword_search == "" ) { $keyword_search = " "; }
			$keyword_searches = explode (" ", $keyword_search);
			if ( strpos($keyword_search, " ") === false ) {$keyword_searches[0] = $keyword_search;}
			$num = 0;
			while ( $num < sizeof($keyword_searches) ) {
				$filterSql .= " AND CONCAT_WS(' ', name, short_description, uri_value, description) LIKE CONCAT('%', :keyword_search$num, '%')";
				$bindData['keyword_search'.$num] = trim($keyword_searches[$num]);
				$num++;
			}
		}
		
		$sqlquery = "SELECT * $filterSql ORDER BY name ASC LIMIT $starting_val, $limit";
		$query = $db->getData($sqlquery, $bindData);
		if($query){			
			foreach($query as $oneRow){
			
				$news_articles_id = $oneRow['news_articles_id'];
				$name = stripslashes(trim($oneRow['name']));
				$uri_value = stripslashes(trim($oneRow['uri_value']));
				$short_description = stripslashes(trim((string) $oneRow['short_description']));
				$description = stripslashes(trim((string) $oneRow['description']));
				$news_articles_date = $oneRow['news_articles_date'];
				
				$news_articlesImg = '';
				$filePath = "./assets/accounts/news_$news_articles_id".'_';
				$pics = glob($filePath."*.jpg");
				if(!$pics){
					$pics = glob($filePath."*.png");
				}
				if($pics){
					foreach($pics as $onePicture){
						$news_articlesImg = baseURL.str_replace('./', '/', $onePicture);
					}
				}
				if(empty($news_articlesImg)){
					$news_articlesImg = baseURL."/assets/images/default.png";
				}

				$resposeData[] = array('news_articles_id'=>$news_articles_id, 'name'=> $name, 'uri_value'=> $uri_value, 'short_description'=> $short_description, 'description'=> $description, 'news_articles_date'=>$news_articles_date, 'news_articlesImg'=>$news_articlesImg);
			}
		}

		$apiResponse['message'] = '';
		$apiResponse['resposeData'] = $resposeData;
		$apiResponse['statusCode'] = 200;
	}
	elseif($segment2 == "getAllWebVideos"){
		$keyword_search = $POST['keyword_search']??'';
		$page = intval($POST['page']??1);
		$limit = intval($POST['limit']??20);

		$resposeData = array();		
		$starting_val = ($page-1)*$limit;
		if($starting_val<0){$starting_val = 0;}
		
		$filterSql = "FROM videos WHERE videos_publish = 1";
		$bindData = array();
		if($keyword_search !=''){
			$keyword_search = addslashes(trim($keyword_search));
			if ( $keyword_search == "" ) { $keyword_search = " "; }
			$keyword_searches = explode (" ", $keyword_search);
			if ( strpos($keyword_search, " ") === false ) {$keyword_searches[0] = $keyword_search;}
			$num = 0;
			while ( $num < sizeof($keyword_searches) ) {
				$filterSql .= " AND CONCAT_WS(' ', name, youtube_url) LIKE CONCAT('%', :keyword_search$num, '%')";
				$bindData['keyword_search'.$num] = trim($keyword_searches[$num]);
				$num++;
			}
		}
		
		$sqlquery = "SELECT * $filterSql ORDER BY name ASC LIMIT $starting_val, $limit";
		$query = $db->getData($sqlquery, $bindData);
		if($query){
			foreach($query as $oneRow){			
				$videos_id = $oneRow['videos_id'];
				$name = stripslashes(trim($oneRow['name']));
				$youtube_url = stripslashes(trim((string) $oneRow['youtube_url']));
				$urlExplode = explode('embed/', $youtube_url);
				$imgLink = "http://img.youtube.com/vi/$urlExplode[1]/sddefault.jpg";
				$resposeData[] = array('videos_id'=>$videos_id, 'name'=> $name,'youtube_url'=> $youtube_url, 'imgLink'=>$imgLink);
			}
		}

		$apiResponse['message'] = '';
		$apiResponse['resposeData'] = $resposeData;
		$apiResponse['statusCode'] = 200;
	}
	elseif($segment2 == "getAllWebWhyChooseUs"){
		$keyword_search = $POST['keyword_search']??'';
		$page = intval($POST['page']??1);
		$limit = intval($POST['limit']??20);

		$resposeData = array();		
		$starting_val = ($page-1)*$limit;
		if($starting_val<0){$starting_val = 0;}
		
		$filterSql = "FROM why_choose_us WHERE why_choose_us_publish = 1";
		$bindData = array();
		if($keyword_search !=''){
			$keyword_search = addslashes(trim($keyword_search));
			if ( $keyword_search == "" ) { $keyword_search = " "; }
			$keyword_searches = explode (" ", $keyword_search);
			if ( strpos($keyword_search, " ") === false ) {$keyword_searches[0] = $keyword_search;}
			$num = 0;
			while ( $num < sizeof($keyword_searches) ) {
				$filterSql .= " AND CONCAT_WS(' ', name, description) LIKE CONCAT('%', :keyword_search$num, '%')";
				$bindData['keyword_search'.$num] = trim($keyword_searches[$num]);
				$num++;
			}
		}
		
		$sqlquery = "SELECT * $filterSql ORDER BY name ASC LIMIT $starting_val, $limit";
		$query = $db->getData($sqlquery, $bindData);
		if($query){			
			foreach($query as $oneRow){
			
				$why_choose_us_id = $oneRow['why_choose_us_id'];
				$name = stripslashes(trim($oneRow['name']));
				$description = stripslashes(trim((string) $oneRow['description']));
				$resposeData[] = array('why_choose_us_id'=>$why_choose_us_id, 'name'=> $name,'description'=> $description);
			}
		}

		$apiResponse['message'] = '';
		$apiResponse['resposeData'] = $resposeData;
		$apiResponse['statusCode'] = 200;
	}
	elseif($segment2 == "sendAppointments"){
		$services_id = intval($POST['services_id']??0);
		$name = addslashes(trim($POST['name']??''));
		$phone = addslashes(trim($POST['phone']??''));
		$email = addslashes(trim($POST['email']??''));	
		$description = addslashes(trim($POST['description']??''));	
		$address = addslashes(trim($POST['address']??''));

		$returnStr = "";
		$savemsg = 'Error';
		if($services_id==0){
			$returnStr = "Service is blank.";
		}
		elseif(empty($name)){
			$returnStr = "Name is blank.";
		}
		elseif(empty($email)){
			$returnStr = "Emaiil is blank.";
		}

		$insertCount = 0;
		if(empty($returnStr)){

			$customerData = array();
			$customerData['name'] = $name;
			$customerData['phone'] = $phone;
			$customerData['email'] = $email;
			$customerData['address'] = $address;

			$customers_id = 0;
			$queryManuObj = $db->getObj("SELECT customers_id FROM customers WHERE name = :name AND phone = :phone", array('name'=>$name, 'phone'=>$phone));
			if($queryManuObj){
				$customers_id = $queryManuObj->fetch(PDO::FETCH_OBJ)->customers_id;						
			}

			if($customers_id==0){
				$customerData['offers_email'] = 1;
				$customerData['customers_publish'] = 1;
				$customerData['users_id'] = 0;
				$customerData['last_updated'] = date('Y-m-d H:i:s');
				$customerData['created_on'] = date('Y-m-d H:i:s');
				$customers_id = $db->insert('customers', $customerData);
			}

			if($customers_id>0 && $services_id>0){
            
				$appointmentsData = array();
				$appointmentsData['created_on'] = date('Y-m-d H:i:s');
				$appointmentsData['last_updated'] = date('Y-m-d H:i:s');
				$appointmentsData['users_id'] = 1;
				$appointmentsData['appointments_publish'] = 1;
				$appointmentsData['notifications'] = 1;
				$appointments_no = 1;
				$queryObj = $db->getObj("SELECT appointments_no FROM appointments ORDER BY appointments_no DESC LIMIT 0, 1", array());
				if($queryObj){
					$appointments_no = intval($queryObj->fetch(PDO::FETCH_OBJ)->appointments_no)+1;
				}
	
				$appointmentsData['appointments_no'] = $appointments_no;
				$appointmentsData['services_id'] = $services_id;
				$appointmentsData['customers_id'] = $customers_id;
				$appointmentsData['services_type'] = '';
				$appointmentsData['description'] = $description;
				$appointmentsData['appointments_date'] = date('Y-m-d');
				$appointments_id = $db->insert('appointments', $appointmentsData);
				if($appointments_id){	
					$insertCount++;		
					$savemsg = 'Add';				
					$subject = '[New message] From '.LIVE_DOMAIN." Appointment Form";								
					
					$message = "<html>";
					$message .= "<head>";
					$message .= "<title>$subject</title>";
					$message .= "</head>";
					$message .= "<body>";
					$message .= "<p>";
					$message .= "Dear <i><strong>$name</strong></i>,<br />";
					$message .= "We received your request for appointment.<br /><br />";
					$message .= "You wrote:<br />";
					$message .= "Phone: $phone<br>";
					$message .= "Email: $email<br>";
					$message .= "Address: $address<br>";
					$message .= "Message: $description";
					$message .= "</p>";
					$message .= "<p>";
					$message .= "<br />";
					$message .= "Thank you for appointing us.";
					$message .= "<br />";
					$message .= "We will reply as soon as possible.";
					$message .= "</p>";
					$message .= "</body>";
					$message .= "</html>";            

					$info = $db->supportEmail('info');
					$headers = array();				
					$headers[] = "From: ".COMPANYNAME;
					$headers[] = "Reply-To: ".$info;
					$headers[] = "Organization: ".COMPANYNAME;
					$headers[] = "MIME-Version: 1.0";
					$headers[] = "Content-type: text/html; charset=iso-8859-1";
					$headers[] = "X-Priority: 3";
					$headers[] = "X-Mailer: PHP".phpversion();

					
					if(mail($email, $subject, $message, implode("\r\n", $headers))){
						$savemsg = 'sent';
						
						$headersAdmin = array();
						$headersAdmin[] = "From: ".$email;
						$headersAdmin[] = "Reply-To: ".$info;
						$headersAdmin[] = "Organization: ".COMPANYNAME;
						$headersAdmin[] = "MIME-Version: 1.0";
						$headersAdmin[] = "Content-type: text/html; charset=iso-8859-1";
						$headersAdmin[] = "X-Priority: 3";
						$headersAdmin[] = "X-Mailer: PHP".phpversion();                
						
						$message = "<html>";
						$message .= "<head>";
						$message .= "<title>$subject</title>";
						$message .= "</head>";
						$message .= "<body>";
						$message .= "<p>";
						$message .= "Dear Admin of <i><strong>".COMPANYNAME."</strong></i>,<br />";
						$message .= "We received a Contact request from $name.<br /><br />";
						$message .= "He / She wrotes:<br />";
						$message .= "Phone: $phone<br>";
						$message .= "Email: $email<br>";
						$message .= "Address: $address<br>";
						$message .= "Message: $description";
						$message .= "</p>";
						$message .= "<p>";
						$message .= "<br />";
						$message .= "Please reply him/her as soon as possible.";
						$message .= "</p>";
						$message .= "</body>";
						$message .= "</html>";
						
						mail($info, $subject, $message, implode("\r\n", $headersAdmin));
						
					}
					else{
						$savemsg = "Sorry! Could not send mail. Try again later.";
					}
					
					$savemsg = 'Sent';
				}
			}
		}

		if($insertCount>0){
			$returnStr = "Appointment sent successfully.";
		}
		
		$resposeData = array('returnStr'=>$returnStr, 'savemsg'=>$savemsg);

		$apiResponse['message'] = '';
		$apiResponse['resposeData'] = $resposeData;
		$apiResponse['statusCode'] = 200;
	}
	elseif($segment2 == "getAllBranches"){
		$keyword_search = $POST['keyword_search']??'';
		$page = intval($POST['page']);
		$limit = intval($POST['limit']);

		$resposeData = array();		
		$starting_val = ($page-1)*$limit;
		if($starting_val<0){$starting_val = 0;}
		
		$filterSql = "FROM branches WHERE branches_publish = 1";
		$bindData = array();
		if($keyword_search !=''){
			$keyword_search = addslashes(trim($keyword_search));
			if ( $keyword_search == "" ) { $keyword_search = " "; }
			$keyword_searches = explode (" ", $keyword_search);
			if ( strpos($keyword_search, " ") === false ) {$keyword_searches[0] = $keyword_search;}
			$num = 0;
			while ( $num < sizeof($keyword_searches) ) {
				$filterSql .= " AND CONCAT_WS(' ', name, address) LIKE CONCAT('%', :keyword_search$num, '%')";
				$bindData['keyword_search'.$num] = trim($keyword_searches[$num]);
				$num++;
			}
		}
		
		$sqlquery = "SELECT * $filterSql ORDER BY name ASC LIMIT $starting_val, $limit";
		$query = $db->getData($sqlquery, $bindData);
		if($query){			
			foreach($query as $oneRow){
			
				$branches_id = $oneRow['branches_id'];
				$name = stripslashes(trim($oneRow['name']));
				$address = stripslashes(trim($oneRow['address']));
				$resposeData[] = array('branches_id'=>$branches_id, 'name'=> $name, 'address'=>$address);
			}
		}

		$apiResponse['message'] = '';
		$apiResponse['resposeData'] = $resposeData;
		$apiResponse['statusCode'] = 200;
	}
	elseif($segment2 == "archiveTable"){
		$tableName = trim(addslashes($POST['tableName']??''));
		$idValue = intval($POST['idValue']??0);

		$savemsg = 'error';
		$returnStr = '';
		if($idValue==0){
			$returnStr = "Id value is invalid.";
		}
		
		$idField = $tableName.'_id';
		$publishField = $tableName.'_publish';
		$sql = "SELECT $publishField FROM $tableName WHERE $idField = $idValue AND $publishField = 1 ORDER BY $idField DESC LIMIT 0, 1";
		$tableObj = $db->getData($sql, array());
		if($tableObj){
			foreach($tableObj as $onerow){
				$publishValue = $onerow[$publishField];	

				if($publishValue>0){
					$conditionarray = array();
					$conditionarray['last_updated'] = date('Y-m-d H:i:s');
					$conditionarray[$publishField] = 0;
					$db->update($tableName, $conditionarray, $idValue);

					$savemsg = 'Update';
					$returnStr = "Archived successfully.";
				}
				else{
					$returnStr = "You already archived.";
				}
			}
		}
		else{
			$returnStr = "Table data could not found.";
		}

		$resposeData = array();
		$resposeData['savemsg'] = $savemsg;
		$resposeData['returnStr'] = $returnStr;

		$apiResponse['message'] = '';
		$apiResponse['resposeData'] = $resposeData;
		$apiResponse['statusCode'] = 200;
		
	}
	elseif($segment2 == "deleteTableRow"){

		$fromUserIdValue = intval($POST['fromUserIdValue']??0);
		$fromUser = trim(addslashes($POST['fromUser']??''));
		$apps_token = trim(addslashes($POST['apps_token']??''));
		$idValue = intval($POST['idValue']??0);
		$tableName = trim(addslashes($POST['tableName']??''));
		
		$savemsg = 'error';
		$returnStr = '';
		$checkToken = 0;
		if($fromUserIdValue>0 && !empty($fromUser) && !empty($apps_token)){
			
			$apps_tokenExp = explode('||', base64_decode($apps_token));
			$spassword_hash = $apps_tokenExp[0];
		
			$idField = $fromUser.'_id';
			$sql = "SELECT $idField, password_hash FROM $fromUser WHERE $idField = $fromUserIdValue ORDER BY $idField DESC LIMIT 0, 1";
			$tableObj = $db->getData($sql, array());
			if($tableObj){
				foreach($tableObj as $onerow){
					$fromUserIdValue = $onerow[$idField];	
					$password_hash = $onerow['password_hash'];	
					
					if($fromUserIdValue>0){
						if($spassword_hash==$password_hash){
							$checkToken = 1;
						}
					}
					else{
						$returnStr = "You already archived.";
					}
				}
			}
		}
		if($checkToken==0){
			$returnStr = "Invalid login credencial.";
		}
		
		if($idValue==0){
			$returnStr = "Id value is invalid.";
		}		
		
		if(in_array($tableName, array('pos_cart')) && empty($returnStr)){
			$idField = $tableName.'_id';
		
			$sql = "SELECT $idField FROM $tableName WHERE $idField = $idValue ORDER BY $idField DESC LIMIT 0, 1";
			$tableObj = $db->getData($sql, array());
			if($tableObj){
				foreach($tableObj as $onerow){
					$idValue = $onerow[$idField];	

					if($idValue>0){
						$db->delete($tableName, $idField, $idValue);

						$savemsg = 'Deleted';
						$returnStr = "Deleted successfully.";
					}
					else{
						$returnStr = "You already Deleted.";
					}
				}
			}
			else{
				$returnStr = "Table data could not found.";
			}
		}

		$resposeData = array();
		$resposeData['savemsg'] = $savemsg;
		$resposeData['returnStr'] = $returnStr;

		$apiResponse['message'] = '';
		$apiResponse['resposeData'] = $resposeData;
		$apiResponse['statusCode'] = 200;
		
	}
}
elseif($segment2 == "sendInvoiceMail"){
	$currency = '$';
	$pos_id = intval($_POST['pos_id']);
	$emailAppsLinks = $_POST['emailAppsLinks']??'';
	$resposeData = array();
	$posObj = $db->getObj("SELECT * FROM pos WHERE pos_id = :pos_id", array('pos_id'=>$pos_id),1);
	if($posObj){
		$pos_onerow = $posObj->fetch(PDO::FETCH_OBJ);
		
		$pos_id = $pos_onerow->pos_id;
		$pos_id = $pos_id;
		$invoice_no = $pos_onerow->invoice_no;
		$branches_id = $branches_id = $pos_onerow->branches_id;
		$customers_id = $customers_id = $pos_onerow->customers_id;
		$service_fee = $pos_onerow->service_fee;
		$order_status = $pos_onerow->order_status;
		$pos_publish = $pos_onerow->pos_publish;
		$is_due = $pos_onerow->is_due;
		$sales_datetime = $pos_onerow->sales_datetime;
		$service_datetime = $pos_onerow->service_datetime;
		$paymentIntentId = $pos_onerow->paymentIntentId;
		$paymentMethodId = $pos_onerow->paymentMethodId;
		
		$customername = $customeremail = $customerphone = $customeraddress = '';
		$customerObj = $db->getObj("SELECT * FROM customers WHERE customers_id = $customers_id", array());
		if($customerObj){
			$customerrow = $customerObj->fetch(PDO::FETCH_OBJ);	
			$name = $customerrow->name;
			$company = $customerrow->company;			
			$customername = $company;
			if($customername !=''){$customername .= ', ';}
			$customername .= $name;
			
			$customeremail = $customerrow->email;
			$customerphone = $customerrow->phone;
		}

		
		$branchesName = $branchesAddress = '';
		$branchesObj = $db->getObj("SELECT name, address FROM branches WHERE branches_id = $branches_id", array());
		if($branchesObj){
			$branchesRow = $branchesObj->fetch(PDO::FETCH_OBJ);	
			$branchesName = $branchesRow->name;
			$branchesAddress = $branchesRow->address;
		}

		$tax_inclusive1 = $pos_onerow->tax_inclusive1;

		$taxable_total = $nontaxable_total = 0.00;
		$cartData = array();
		$sqlquery = "SELECT * FROM pos_cart WHERE pos_id = $pos_id";
		$query = $db->getObj($sqlquery, array());
		if($query){
			$i=0;
			while($row = $query->fetch(PDO::FETCH_OBJ)){
				$i++;
				$sales_price = $row->sales_price;
				$qty = $row->qty;									
				$total = round($sales_price * $qty,2);
				$taxable_total = $taxable_total+$total;
			}
		}

		$tiStr = '';
		if($pos_onerow->tax_inclusive1>0){$tiStr = ' Inclusive';}
		$taxes_name1 = "$pos_onerow->taxes_name1 ($pos_onerow->taxes_percentage1%$tiStr)";
		$taxes_total1 = calculateTax($taxable_total, $pos_onerow->taxes_percentage1, $pos_onerow->tax_inclusive1);
		if($pos_onerow->tax_inclusive1>0){
			$taxes_total1 = 0;
		}
		$totalpayment = 0;
		$paymentData = array();
		$ppSql = "SELECT payment_method, payment_amount, payment_datetime FROM pos_payment WHERE pos_id = $pos_id AND payment_method != 'Change'";
		$ppQueryObj = $db->getObj($ppSql, array());
		if($ppQueryObj){
			while($onerow = $ppQueryObj->fetch(PDO::FETCH_OBJ)){
				$payment_amount = $onerow->payment_amount;
				$payment_datetime =  date('m/d/Y g:i a', strtotime($onerow->payment_datetime));
				
				$totalpayment = $totalpayment+$payment_amount;
				$paymentData[] = "$payment_datetime, $onerow->payment_method, $payment_amount";
			}
		}
		
		$grand_total = $taxable_total+$taxes_total1+$nontaxable_total+$pos_onerow->service_fee;

		if(!empty($emailAppsLinks)){$emailAppsLinks = "<br>$emailAppsLinks<br>";}
		$mailBody = "<p>
			Dear <strong>$customername</strong>,<br />
			<br />
			<strong>You ordered Invoice #$invoice_no. You will get service from $branchesName, $branchesAddress. </strong><br />
			<br />
			Your Invoice Total Price: $currency$grand_total
			<br />
			You payment to us  ".implode('<br>', $paymentData)."
			<br />
			$emailAppsLinks
			<br />
			Sincerely,<br />
			The ".COMPANYNAME." Team
		</p>";

		$mail = new PHPMailer;
		$mail->isSMTP();
		$mail->Host = $db->supportEmail('Host');
		$mail->Port = 587;
		$mail->SMTPAuth = true;
		$mail->Username = $db->supportEmail('Username');
		$mail->Password = $db->supportEmail('Password');

		$file_name = $_FILES['attached']['name'];
		$file_tmp = $_FILES['attached']['tmp_name'];

		move_uploaded_file($file_tmp, "assets/uploads/$file_name");

		$mail->addReplyTo($db->supportEmail('info'), COMPANYNAME);
		$mail->setFrom($db->supportEmail('info'), COMPANYNAME);
		$mail->clearAddresses();
		$mail->addAddress($customeremail, $customername);
		$mail->addCC('mdshobhancse@gmail.com', $customername);
		$mail->addAttachment("assets/uploads/$file_name", "$file_name");
		$mail->Subject = "Invoice for $customername, Invoice #$invoice_no";
		$mail->isHTML(true);
		$mail->CharSet = 'UTF-8';
		$mail->Body = $mailBody;
		
		$returnStr = '';
		if($customeremail =='' || is_null($customeremail)){
			$returnStr = 'Your email is blank. Please contact with site admin.';
		}
		else{
			if (!$mail->send()) {
				$returnStr = 'Mail could not sent.';
			}
			else{
				$returnStr = 'Please check your email for a message from us';
				unlink("assets/uploads/$file_name");
			}
		}
		$resposeData['returnStr'] = $returnStr;
	}

	$apiResponse['message'] = '';
	$apiResponse['resposeData'] = $resposeData;
	$apiResponse['statusCode'] = 200;
}

function calculateTax($taxable_total, $taxes_rate, $tax_inclusive){
	$returntax = 0.00;
	$taxes_percentage = $taxes_rate*0.01;
	if($tax_inclusive>0){
		$returntax = $taxable_total-round($taxable_total/($taxes_percentage+1),2);
	}
	else{
		$returntax = round($taxable_total*$taxes_percentage,2);
	}
	return $returntax;
}

echo json_encode($apiResponse);
exit;
