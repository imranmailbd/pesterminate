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
					
					$db_token = hash_hmac('sha256', $enc_passwrd, $salt_key, false);
					$apps_token = base64_encode($enc_passwrd.".".$salt_key); 

					$account_data = array(  'last_updated' => date('Y-m-d H:i:s'),
											'token_valid_till'=>$salt_key,
											'db_token'=>$db_token
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
				$first_name = $tableRow->first_name;
				$last_name = $tableRow->last_name;
				$email = $tableRow->email;
				$contact_no = $tableRow->contact_no;

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
					
					$db_token = hash_hmac('sha256', $enc_passwrd, $salt_key, false);
					$apps_token = base64_encode($enc_passwrd.".".$salt_key);

					$updateData = array('last_updated' => date('Y-m-d H:i:s'));
					
					$db->update('customers', $updateData, $customers_id);
					
					$apiResponse['message'] = '';
					$resposeData = array();
					$resposeData['customers_id'] = $customers_id;
					$resposeData['branches_id'] = $branches_id;
					$resposeData['first_name'] = $first_name;
					$resposeData['last_name'] = $last_name;
					$resposeData['email'] = $email;
					$resposeData['contact_no'] = $contact_no;
					$resposeData['apps_token'] = $apps_token;
					
					$apiResponse['resposeData'] = $resposeData;
					$apiResponse['statusCode'] = 200;
				}
			}
			else{		
				$apiResponse['message'] = 'Your account is invalid.';
			}
		}
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
			$resposeData['pickup_minutes'] = intval($pos_onerow->pickup_minutes);
			$resposeData['pos_type'] = $pos_onerow->pos_type;
			$resposeData['notifications'] = intval($pos_onerow->notifications);
			
			$customername = $customeremail = $customerphone = '';
			$customerObj = $db->getObj("SELECT * FROM customers WHERE customers_id = $customers_id", array());
			if($customerObj){
				$customerrow = $customerObj->fetch(PDO::FETCH_OBJ);	
				$first_name = $customerrow->first_name;
				$last_name = $customerrow->last_name;
				$company = $customerrow->company;			
				$customername = $company;
				if($customername !=''){$customername .= ', ';}
				$customername .= $first_name;
				if($customername !=''){$customername .= ' ';}
				$customername .= $last_name;
				  
				$customeremail = $customerrow->email;
				$customerphone = $customerrow->contact_no;
			}
			$resposeData['customername'] = $customername;
			$resposeData['customeremail'] = $customeremail;
			$resposeData['customerphone'] = $customerphone;

			$branchesName = $branchesAddress = '';
			$branchesObj = $db->getObj("SELECT name, address FROM branches WHERE branches_id = $branches_id", array());
			if($branchesObj){
				$branchesRow = $branchesObj->fetch(PDO::FETCH_OBJ);	
				$branchesName = $branchesRow->name;
				$branchesAddress = $branchesRow->address;
			}
			$resposeData['branchesName'] = $branchesName;
			$resposeData['branchesAddress'] = $branchesAddress;

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
					$discount_is_percent = $row->discount_is_percent;
					$discount = $row->discount;
					if($discount_is_percent>0){
						$discount_value = round($total*0.01*$discount,2);
					}
					else{ 
						$discount_value = round($discount,2);
					}					
					$taxable = $row->taxable;
					if($taxable>0){
						$taxable_total = $taxable_total+$total-$discount_value;
					}
					else{
						$nontaxable_total = $nontaxable_total+$total-$discount_value;
					}
					$description = stripslashes(trim($row->description));
					$item_id = $row->item_id;
					$item_type = $row->item_type;
					$add_description = stripslashes(trim($row->add_description));
					if($add_description !=''){
						$description .= nl2br($add_description);
					}
					$newchoseMore = array();
					$choice_more = $row->choice_more;
					if($choice_more>0){
						$cartCMOObj = $db->getObj("SELECT pcc.choice_more_options FROM pos_cart_cmo pcc, choice_more cm WHERE pcc.pos_cart_id  = :pos_cart_id AND pcc.choice_more_id = cm.choice_more_id ORDER BY cm.sorting_value ASC", array('pos_cart_id'=>$row->pos_cart_id));
						if($cartCMOObj){
							while($cartCMORow = $cartCMOObj->fetch(PDO::FETCH_OBJ)){
								$choseMore = array();
								$choiceMoreOptions = json_decode($cartCMORow->choice_more_options);
								$CMname = str_replace('&amp;', '&', stripslashes(trim($choiceMoreOptions->CMname)));
								$CMOData = $choiceMoreOptions->CMOData;
								if(!empty($CMOData)){
									foreach($CMOData as $oneCMORow){
										$optionName = str_replace('&amp;', '&', $oneCMORow->name);
										$price = $oneCMORow->price;
										if(!in_array($price, array('0', '')))
											$optionName .= " [$price]";

										$choseMore[] = $optionName;
									}
								}

								if(!empty($choseMore)){
									$newchoseMore[] = array('name'=>$CMname, 'sublist'=>$choseMore);
								}
							}
						}
					}

					$cartData[] = array('sl'=>$i, 'description'=>$description, 'choseMore'=>$newchoseMore, 'qty'=>$qty, 'sales_price'=>$sales_price, 'discount_value'=>$discount_value);
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
			$resposeData['taxes_total1'] = $taxes_total1;

			$totalpayment = 0;
			$paymentData = array();
			$ppSql = "SELECT payment_method, payment_amount, payment_datetime FROM pos_payment WHERE pos_id = $pos_id AND payment_method != 'Change'";
			$ppQueryObj = $db->getObj($ppSql, array());
			if($ppQueryObj){
				$p=0;
				$rowspan = $ppQueryObj->rowCount();
				while($onerow = $ppQueryObj->fetch(PDO::FETCH_OBJ)){
					
					$p++;												
					$payment_amount = $onerow->payment_amount;
					$payment_datetime =  date('m/d/Y g:i a', strtotime($onerow->payment_datetime));
					
					$totalpayment = $totalpayment+$payment_amount;
					
					$paymentData[] = array('payment_datetime'=>$payment_datetime, 'payment_method'=>$onerow->payment_method, 'payment_amount'=>$payment_amount);
				}
			}
			$resposeData['paymentData'] = $paymentData;
			$resposeData['totalpayment'] = $totalpayment;
			
			$resposeData['grand_total'] = $taxable_total+$taxes_total1+$nontaxable_total+$pos_onerow->service_fee;

		}

		$apiResponse['message'] = '';
		$apiResponse['resposeData'] = $resposeData;
		$apiResponse['statusCode'] = 200;
	}
	elseif($segment2 == "getAllInvoices"){
		$sbranches_id = intval($POST['branches_id']);
		$snotifications = intval($POST['notifications']);
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
		if($snotifications<3){
			$filterSql .= " AND pos.notifications = :notifications";
			$bindData['notifications'] = $snotifications;			
		}
		if($sbranches_id >0){
			$filterSql .= " AND pos.branches_id = :branches_id";
			$bindData['branches_id'] = $sbranches_id;
		}
		
		$sqlquery = "SELECT pos.*, SUM(CASE WHEN pos_cart.taxable>0 AND pos_cart.discount_is_percent>0 THEN (pos_cart.sales_price*pos_cart.qty)-(pos_cart.sales_price*pos_cart.qty*pos_cart.discount/100) WHEN pos_cart.taxable>0 AND pos_cart.discount_is_percent=0 THEN (pos_cart.sales_price*pos_cart.qty)-(pos_cart.discount) ELSE 0 END) AS taxableTotal, 
		SUM(CASE WHEN pos_cart.taxable=0 AND pos_cart.discount_is_percent>0 THEN (pos_cart.sales_price*pos_cart.qty)-(pos_cart.sales_price*pos_cart.qty*pos_cart.discount/100) WHEN pos_cart.taxable=0 AND pos_cart.discount_is_percent=0 THEN (pos_cart.sales_price*pos_cart.qty)-(pos_cart.discount) ELSE 0 END) AS nonTaxableTotal 
		FROM pos, pos_cart WHERE pos.pos_id = pos_cart.pos_id AND pos.pos_publish = 1 $filterSql GROUP BY pos.pos_id ORDER BY pos.sales_datetime DESC, pos.invoice_no DESC LIMIT $starting_val, $limit";
		$query = $db->getData($sqlquery, $bindData);
		$str = '';
		if($query){
			$customersId = $brancIds = array();
			foreach($query as $oneRow){
				$customersId[$oneRow['customers_id']] = '';
				$brancIds[$oneRow['branches_id']] = '';
			}					
			
			if(!empty($customersId)){
				$tableObj = $db->getObj("SELECT customers_id, first_name, last_name, contact_no FROM customers WHERE customers_id IN (".implode(', ', array_keys($customersId)).")", array());
				if($tableObj){
					while($tableOneRow = $tableObj->fetch(PDO::FETCH_OBJ)){							
						$customersId[$tableOneRow->customers_id] = trim(stripslashes("$tableOneRow->first_name $tableOneRow->last_name $tableOneRow->contact_no"));
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
				if($invoice_no ==0){
					$invoice_no = $oneRow['pos_id'];
				}
				$customers_id = $oneRow['customers_id'];
				$customername = $customersId[$customers_id]??'';
				
				$date =  date($dateformat, strtotime($oneRow['sales_datetime']));
				if($timeformat=='24 hour'){$time =  date('H:i', strtotime($oneRow['sales_datetime']));}
				else{$time =  date('g:i a', strtotime($oneRow['sales_datetime']));}
				
				$branches_id = $oneRow['branches_id'];
				$branchName = $brancIds[$branches_id]??'&nbsp;';
				
				$taxable_total = $oneRow['taxableTotal'];
				$totalnontaxable = $oneRow['nonTaxableTotal'];
								
				$taxes_total1 = calculateTax($taxable_total, $oneRow['taxes_percentage1'], $oneRow['tax_inclusive1']);
				
				$tax_inclusive1 = $oneRow['tax_inclusive1'];
				$notifications = intval($oneRow['notifications']);
				$service_fee = floatval($oneRow['service_fee']);
				
				$taxestotal = $taxes_total1;
				
				$grand_total = $taxable_total+$taxestotal+$totalnontaxable+$service_fee;
				if($tax_inclusive1>0){$grand_total -= $taxes_total1;}

				$bold = 0;
				if($notifications==1){
					$bold = 1;
				}

				$resposeData[] = array('bold'=>$bold, 'pos_id'=>$pos_id, 'dateTime'=> "$date $time", 'invoice_no'=>"s$invoice_no", 'customername'=>$customername, 'branchName'=>$branchName, 'grand_total'=>$grand_total);

			}
		}

		$apiResponse['message'] = '';
		$apiResponse['resposeData'] = $resposeData;
		$apiResponse['statusCode'] = 200;
	}
	elseif($segment2 == "invoiecNotifications"){
		$pos_id = intval(array_key_exists('pos_id', $POST) ? $POST['pos_id'] : 0);
		$users_id = intval(array_key_exists('users_id', $POST) ? $POST['users_id'] : 0);
		$notifications = intval(array_key_exists('notifications', $POST) ? $POST['notifications'] : 0);
		$db->writeIntoLog("pos_id: $pos_id, notifications: $notifications, POST:".json_encode($POST));

		$resposeData = array();
		$posObj = $db->getObj("SELECT pos_id, customers_id, notifications, pickup_minutes, paymentIntentId, paymentMethodId, is_due FROM pos WHERE pos_id = :pos_id", array('pos_id'=>$pos_id),1);
		if($posObj){
			$posRow = $posObj->fetch(PDO::FETCH_OBJ);
			$pos_id = $posRow->pos_id;
			$customers_id = $posRow->customers_id;
			$oldnotifications = $posRow->notifications;
			$paymentIntentId = $posRow->paymentIntentId;
			$paymentMethodId = $posRow->paymentMethodId;
			$is_due = intval($posRow->is_due);

			$db->writeIntoLog("pos_id: $pos_id, is_due: $is_due, POST:".json_encode($posRow));
			if($is_due==1 && in_array($notifications, array(0, 2))){
				$Stripe = new Stripe($db);
				if($notifications==0){
					$resposeData = $takePaymentData = $Stripe->takePayment($pos_id);
					if($takePaymentData['actionStatus']=='Success'){
						$updateData = array('notifications'=>$notifications, 'is_due' => 0, 'sales_datetime'=> date('Y-m-d H:i:s'), 'last_updated'=> date('Y-m-d H:i:s'));
						$db->update('pos', $updateData, $pos_id);
						$resposeData = array('status'=>'paymentCompleted');
					}
					$db->writeIntoLog("takePaymentData:".json_encode($takePaymentData));
				}
				elseif($notifications==2){
					$resposeData = $cancelPaymentData = $Stripe->cancelPayment($pos_id);
					if($cancelPaymentData['actionStatus']=='Success'){
						$updateData = array('notifications'=>$notifications, 'order_status' => 3, 'last_updated'=> date('Y-m-d H:i:s'));
						$db->update('pos', $updateData, $pos_id);
					}
					$db->writeIntoLog("cancelPaymentData:".json_encode($cancelPaymentData));
				}
			}
			elseif($notifications==5){
				$oldpickup_minutes = $posRow->pickup_minutes;
				if($oldnotifications != $notifications){
					
					$description = "Approved this Invoice";
					if($notifications<=2){
						if($oldnotifications==1){
							if($notifications==2)
								$description = "Cancel this Invoice";
						}
						else{
							if($oldnotifications==2){
								if($notifications==1)
									$description = "Changed this Invoice from Cancel to Approved";
								else
									$description = "Changed this Invoice from Approved to Cancel";							
							}
						}
					}
					else{
						$description = "Add more 5 minutes with pickup time.";
					}
					$resposeData = array('status'=>$description);

					$moreInfo = array();
					$teData = array();
					$teData['created_on'] = date('Y-m-d H:i:s');
					$teData['users_id'] = $users_id;
					$teData['record_for'] = 'pos';
					$teData['record_id'] = $pos_id;
					$teData['details'] = json_encode(array('changed'=>array(''=>$description), 'moreInfo'=>$moreInfo));
					$db->insert('track_edits', $teData);

					$updateData = array('notifications'=>$notifications);
					if($notifications>2){
						$updateData = array('pickup_minutes'=>floor($oldpickup_minutes+5));
					}
					$db->update('pos', $updateData, $pos_id);
					$update = 1;
				}
			}

			$apiResponse['message'] = '';
			$apiResponse['resposeData'] = $resposeData;
			$apiResponse['statusCode'] = 200;
		}
	}	
	elseif($segment2 == "checkNotifications"){
		
		$notifications = $pos_id = 0;
		$branches_id = intval($POST['branches_id']??0);
		$sql = "SELECT pos_id FROM pos WHERE notifications = 1";
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
			$notifications = intval(count($posIds));
		}
		$resposeData = array();
		$resposeData['notifications'] = $notifications;
		$resposeData['pos_id'] = $pos_id;

		$apiResponse['message'] = '';
		$apiResponse['resposeData'] = $resposeData;
		$apiResponse['statusCode'] = 200;
		
	}
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
