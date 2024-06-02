<?php
class Stripe{
	public string $STRIPE_API_KEY, $STRIPE_PUBLISHABLE_KEY, $STRIPE_CURRENCY;	
	
	protected $db;
	public function __construct($db){
		$this->db = $db;
		$branches_id = 1;
		if(isset($_SESSION["branches_id"]) && intval($_SESSION["branches_id"])>0){
			$branches_id = $_SESSION["branches_id"];
		}

		$stripe_pk = $stripe_sk = '';
		if(isset($_SESSION["branches_id"]) && intval($_SESSION["branches_id"])>0){
			$branches_id = $_SESSION["branches_id"]??0;
			$tableObj = $this->db->getObj("SELECT stripe_pk, stripe_sk FROM branches WHERE branches_id = $branches_id", array());
			if($tableObj){
				$branchesRow = $tableObj->fetch(PDO::FETCH_OBJ);
				$stripe_pk = $branchesRow->stripe_pk;
				$stripe_sk = $branchesRow->stripe_sk;
			}
		}
		
		$this->STRIPE_API_KEY = $stripe_sk;
		$this->STRIPE_PUBLISHABLE_KEY = $stripe_pk;
		$this->STRIPE_CURRENCY = "cad";
	}
	
	public function getPaymentIntent($customers_id, $price){
		$branches_id = $_SESSION["branches_id"]??0;
		$first_name = $email = $contact_no = '';
        if($customers_id>0){
            $customersObj = $this->db->getObj("SELECT name, email, phone, branches_id FROM customers WHERE customers_id = $customers_id", array());
            if($customersObj){
                while($oneRow = $customersObj->fetch(PDO::FETCH_OBJ)){
                    $first_name = $oneRow->first_name;
                    $email = $oneRow->email;
                    $contact_no = $oneRow->contact_no;
                    $branches_id = $oneRow->branches_id;
                    if($branches_id==0){
                        $branches_id = $oneRow->branches_id;
                        $_SESSION["branches_id"] = $branches_id;
                    }
                }
            }
        }
		
		$stripeSecretKey = $this->STRIPE_API_KEY;
		$returnData = array();
		$returnData['login'] = '';
		$returnData['error'] = '';
		$returnData['stripe_client_secret'] = '';
		$returnData['paymentIntentId'] = '';

		$description = "$first_name, $email, $contact_no";
		$paymentData = [
			'description'=>$description,
			'amount' => round(floatval($price),2) * 100,
			'currency' => $this->STRIPE_CURRENCY
		];

		$paymentData = http_build_query($paymentData);

		$ch = curl_init();
		curl_setopt_array($ch, [
			CURLOPT_URL => 'https://api.stripe.com/v1/payment_intents',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => 1,
			CURLOPT_POSTFIELDS => $paymentData,
			CURLOPT_HTTPHEADER => [
				'Content-Type: application/x-www-form-urlencoded',
				'Authorization: Bearer ' . $stripeSecretKey,
			],
		]);

		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		$this->db->writeIntoLog("httpCode: $httpCode, response:".json_encode($response));

		if ($httpCode === 200) {
			$paymentIntent = json_decode($response);

			if (isset($paymentIntent->client_secret) && $paymentIntent->client_secret != '') {
				$returnData['stripe_client_secret'] = $paymentIntent->client_secret;
			}
			if (isset($paymentIntent->id) && $paymentIntent->id != '') {
				$returnData['paymentIntentId'] = $paymentIntent->id;     
			}
		}
		
		return $returnData;
	}
	
	public function takePayment($pos_id){

		$posObj = $this->db->getObj("SELECT pos_id, branches_id, paymentIntentId, paymentMethodId FROM pos WHERE pos_id = :pos_id", array('pos_id'=>$pos_id),1);
		if($posObj){
			$posRow = $posObj->fetch(PDO::FETCH_OBJ);
			$pos_id = $posRow->pos_id;
			$branches_id = $posRow->branches_id;
			$paymentIntentId = $posRow->paymentIntentId;
			$paymentMethodId = $posRow->paymentMethodId;

			$tableObj = $this->db->getObj("SELECT branches_id, stripe_sk FROM branches WHERE branches_id = $branches_id", array());
			if($tableObj){
				while($oneRow = $tableObj->fetch(PDO::FETCH_OBJ)){
					$this->STRIPE_API_KEY = $oneRow->stripe_sk;
				}
			}

			$jsonResponse = array();
			$jsonResponse['actionStatus'] = '';
			$jsonResponse['message'] = '';

			$stripeSecretKey = $this->STRIPE_API_KEY;

			$stripeChargeUrl = "https://api.stripe.com/v1/payment_intents/$paymentIntentId/confirm";
			
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $stripeChargeUrl);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
				'payment_method' => $paymentMethodId
			)));

			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Authorization: Bearer ' . $stripeSecretKey,
			));

			$response = curl_exec($ch);
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			$this->db->writeIntoLog("httpCode: $httpCode, response:".json_encode($response));
			if ($httpCode === 200) {
				$chargeData = json_decode($response, true);
				if (isset($chargeData['id'])) {
					$jsonResponse['actionStatus'] = 'Success';
					$jsonResponse['message'] = 'Payment successful! Charge ID: ' . $chargeData['id'];
				} else {
					$jsonResponse['actionStatus'] = 'Error';
					$jsonResponse['message'] = 'Payment failed: ' . $chargeData['error']['message'];
				}
			}
			else {
				$jsonResponse['actionStatus'] = 'Error';
				$jsonResponse['message'] = 'No response received from the server.';
			}
		}
		else {
			$jsonResponse['actionStatus'] = 'Error';
			$jsonResponse['message'] = 'No POS found.';
		}
		
		return $jsonResponse;
	}

	public function cancelPayment($pos_id){

		$posObj = $this->db->getObj("SELECT pos_id, branches_id, paymentIntentId, paymentMethodId FROM pos WHERE pos_id = :pos_id", array('pos_id'=>$pos_id),1);
		if($posObj){
			$posRow = $posObj->fetch(PDO::FETCH_OBJ);
			$pos_id = $posRow->pos_id;
			$branches_id = $posRow->branches_id;
			$paymentIntentId = $posRow->paymentIntentId;
			$paymentMethodId = $posRow->paymentMethodId;

			$tableObj = $this->db->getObj("SELECT branches_id, stripe_sk FROM branches WHERE branches_id = $branches_id", array());
			if($tableObj){
				while($oneRow = $tableObj->fetch(PDO::FETCH_OBJ)){
					$this->STRIPE_API_KEY = $oneRow->stripe_sk;
				}
			}

			$jsonResponse = array();
			$jsonResponse['actionStatus'] = '';
			$jsonResponse['message'] = '';

			$stripeSecretKey = $this->STRIPE_API_KEY;

			$stripeChargeUrl = "https://api.stripe.com/v1/payment_intents/$paymentIntentId/cancel";
			
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $stripeChargeUrl);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array()));

			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Authorization: Bearer ' . $stripeSecretKey,
			));

			$response = curl_exec($ch);
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			$this->db->writeIntoLog("httpCode: $httpCode, response:".json_encode($response));
			if ($httpCode === 200) {
				$chargeData = json_decode($response, true);
				if (isset($chargeData['id'])) {
					$jsonResponse['actionStatus'] = 'Success';
					$jsonResponse['message'] = 'Payment successfuly Cancelled';
				} else {
					$jsonResponse['actionStatus'] = 'Error';
					$jsonResponse['message'] = 'Payment failed: ' . $chargeData['error']['message'];
				}
			}
			else {
				$jsonResponse['actionStatus'] = 'Error';
				$jsonResponse['message'] = 'No response received from the server.';
			}
		}
		else {
			$jsonResponse['actionStatus'] = 'Error';
			$jsonResponse['message'] = 'No POS found.';
		}
		
		return $jsonResponse;
	}

}
?>