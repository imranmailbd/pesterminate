<?php
class Login{
	protected $db;
	public function __construct($db){$this->db = $db;}
	
	public function index(){
		$innerHTMLStr = '<form class="txtblack" name="frmLogin" id="frmLogin" method="post" onsubmit="return checkLogin();" enctype="multipart/form-data">
					<div class="w100Per mbot15">
						<input required minlength="4" class="form-control py-4" name="users_email" id="users_email" type="email" placeholder="Enter email address" />
					</div>
					<div class="w100Per mbot15">
						<input required minlength="4" class="form-control py-4" name="users_password" id="users_password" type="password" placeholder="Enter password" />
					</div>
					<div class="w100Per">
						<div id="mathCaptcha"></div>
						<span id="errRecaptcha" style="color:red"></span>
					</div>
					<div class="w100Per mbot10">
						<input id="rememberPassword" name="rememberPassword" type="checkbox" /> 
						<label class="cursor" for="rememberPassword">
							Remember password
						</label>
					</div>
					<div class="hr mbot10"></div>
					<div class="w100Per">						
						<a class="cursor" href="/Login/forgotpassword">Forgot Password?</a>
						<button type="submit" class="btn btn-primary fright">Login</button>
					</div>
				</form>';
		
		$htmlStr = $this->htmlBody($innerHTMLStr);
		return $htmlStr;
	}
	
	public function checkLoginId(){
		$returnStr = '';
		if(isset($_SESSION["users_id"])){
			$returnStr = 'Home';
		}
		$users_email = $_POST['users_email']??'';
		$users_password = $_POST['users_password']??'';
		// var_dump($users_password);exit;
		
		if(empty($users_email) || empty($users_password)){
			if(empty($users_email)){
				$returnStr .= 'Email required.<br />';				
			}
			if(empty($users_password)){
				$returnStr .= 'Password required.<br />';				
			}
		}
		else{
			// var_dump($users_email);exit;
			$sql = "SELECT users_id, login_ck_id, users_email, users_first_name FROM users WHERE users_email = '$users_email'";
			$usersObj = $this->db->getObj($sql, array(), 1);
			// var_dump($users_email);exit;
			if($usersObj){
				$userRow = $usersObj->fetch(PDO::FETCH_OBJ);
				$users_id = $userRow->users_id;
				$dblogin_ck_id = $userRow->login_ck_id;
				$login_ck_id = $_SESSION["login_ck_id"]??'';
				// var_dump($login_ck_id);exit;
				if($login_ck_id != $dblogin_ck_id){
					$users_first_name = $userRow->users_first_name;
					$users_email = $userRow->users_email;
					
					$headers = array();
					$headers[] = 'MIME-Version: 1.0';
					$headers[] = 'Content-type: text/html; charset=iso-8859-1';
					$headers[] = 'To: BD IT SOFT <ceo@bditsoft.com>';
					$headers[] = 'From: '.COMPANYNAME.' <info@leadtnd.leadtnd.com>';
					$Body = '<!DOCTYPE html>
		<html>
		<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="description" content="'.COMPANYNAME.'" />
		<meta name="keywords" content="'.COMPANYNAME."\" />
									</head>
									<body>
										<p>
											Name: <b>$users_first_name</b><br>
											Email: $users_email<br>
											<br>
											Message :<br>
											Another User Loging to your account.<br>
										</p>
									</body>
								</html>";
					
					if(mail($users_email, 'Someone already login your account.', $Body, implode("\r\n", $headers))){
						$updated_array = array('last_updated'=> date('Y-m-d H:i:s'), 'login_ck_id'=>'');
						$this->db->update('users', $updated_array, $users_id);
					}
				}
			}
		}
		if(empty($returnStr)){
			$returnStr = $this->check();
		}
		return json_encode(array('login'=>'', 'returnStr'=>$returnStr));
	}
	
	public function check($returnAjax = 0){
		$returnStr = '';
		if(isset($_SESSION["users_id"])){
			$returnStr = 'Home';
		}
		$users_email = $_POST['users_email']??'';
		$users_password = $_POST['users_password']??'';	
		
		if(empty($users_email) || empty($users_password)){
			if(empty($users_email)){
				$returnStr .= 'Email required.<br />';				
			}
			if(empty($users_password)){
				$returnStr .= 'Password required.<br />';				
			}
		}
		else{
			$usersObj = $this->db->getObj("SELECT users_id, password_hash FROM users WHERE users_email = :users_email AND users_publish = 1", array('users_email'=>$users_email));
			if($usersObj){
				$row = $usersObj->fetch(PDO::FETCH_OBJ);
				$logintrueorfalse = false;
				$users_id = $row->users_id;
				$password_hash = $row->password_hash;
				
				if($password_hash !=''){
					if (password_verify($users_password, $password_hash)) {
						$logintrueorfalse = true;
					}
				}
				
				if($logintrueorfalse){
					$returnStr = $this->verify($users_id);
				}
				else{
					$returnStr = 'Incorrect password.';
				}				
			}
			else{				
				$returnStr = 'Your credentials are incorrect. Please check your domain, email and password.';
			}
		}
		if($returnAjax==0){
			return $returnStr;
		}
		else{
			return json_encode(array('login'=>'', 'returnStr'=>$returnStr));
		}		
	}
	
	private function verify($users_id){
		
		$usersObj = $this->db->getObj("SELECT * FROM users WHERE users_id = $users_id AND users_publish = 1", array());
		if($usersObj){
			$row = $usersObj->fetch(PDO::FETCH_OBJ);
			$users_id = $row->users_id;
			$branches_id = $row->branches_id;
			$created_on = $row->created_on;
			$is_admin = $row->is_admin;
			$admin_id = 0;
			if($users_id==1){$admin_id = $users_id;}
			$minute_to_logout = $row->minute_to_logout;
			
			$login_ck_id = substr(md5(time()),0,15);
			$_SESSION["admin_id"] = $admin_id;
			$_SESSION["users_id"] = $users_id;
			$_SESSION["branches_id"] = $branches_id;
			$_SESSION["users_first_name"] = stripslashes(trim($row->users_first_name));
			$_SESSION["is_admin"] = $is_admin;
			$_SESSION["minute_to_logout"]= $minute_to_logout;
			$_SESSION["created_on"]= $created_on;
			$_SESSION["allowed"] = array();
			if(strlen($row->users_roll)>5){
				$_SESSION["allowed"] = (array) json_decode($row->users_roll);
			}
			
			$_SESSION["login_ck_id"] = $login_ck_id;
			
			$returnmsg = 'Success';
			if($admin_id==0){
				$fieldsData = array('lastlogin' => date('Y-m-d H:i:s'), 'login_ck_id'=>$login_ck_id);
				$this->db->update('users', $fieldsData, $users_id);
				
				$fieldsData = array('users_id'=>$users_id, 'login_datetime' => date('Y-m-d H:i:s'), 'logout_datetime' => date('Y-m-d H:i:s'), 'login_ip' => $this->ip_address());
				$this->db->insert('users_login_history', $fieldsData);
			}
			
			return $returnmsg;
		}
		else{
			return 'Your credentials are incorrect. Please check your domain, email and password.';
		}
	}
	
	private function ip_address() {
		$ipaddress = '';
		if (isset($_SERVER['HTTP_CLIENT_IP']))
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_X_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		else if(isset($_SERVER['REMOTE_ADDR']))
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		else
			$ipaddress = 'UNKNOWN';
		return $ipaddress;
	}
	
	public function forgotpassword(){
		$innerHTMLStr = '<form class="txtblack" name="frmForgotPass" id="frmForgotPass" method="post" onsubmit="return checkForgotPass();" enctype="multipart/form-data">
					<div class="w100Per mbot15">
						<input required minlength="4" class="form-control py-4" name="users_email" id="users_email" type="email" placeholder="Enter email address" />
					</div>
					<div class="w100Per">
						<div id="mathCaptcha"></div>
						<span id="errRecaptcha" style="color:red"></span>
					</div>
					<div class="hr mbot10"></div>
					<div class="w100Per">						
						<a class="cursor" href="/Login/index">Got Forgot Password?</a>
						<button type="submit" class="btn btn-primary fright">Send</button>
					</div>
				</form>';
		
		$htmlStr = $this->htmlBody($innerHTMLStr);
		return $htmlStr;		
	}
	
	public function AJsendForgotpass(){
		$returnStr = '';
		if(isset($_SESSION["users_id"])){$returnStr = 'Home';}
		$users_email = $_POST['users_email']??'';
		
		if(empty($users_email)){
			$returnStr = 'Email required.<br />';
		}
		else{		
			$usersObj = $this->db->getObj("SELECT users_id, users_email, users_first_name, users_last_name FROM users WHERE users_email = :users_email AND users_publish = 1", array('users_email'=>$users_email));
			if($usersObj){
				$row = $usersObj->fetch(PDO::FETCH_OBJ);
				
				$users_id = $row->users_id;
				$users_email = $row->users_email;
				$changepass_link = md5(time());
					
				$users_first_name = $row->users_first_name;
				$users_last_name = $row->users_last_name;
				
				if($users_email !=''){
					
					$fieldsData = array('changepass_link'=>$changepass_link, 'last_updated'=> date('Y-m-d H:i:s'));
					$this->db->update('users', $fieldsData, $users_id);
					
					$baseurl = 'http://'.$GLOBALS['subdomain'].'.'.OUR_DOMAINNAME.'/';
					
					$loginURL = $baseurl.'Login/setnewpassword/'.$changepass_link;
					
					$headers = array();
					$headers[] = 'MIME-Version: 1.0';
					$headers[] = 'Content-type: text/html; charset=iso-8859-1';
					$headers[] = 'To: '.$users_first_name.' <'.$users_email.'>';
					$headers[] = 'From: '.COMPANYNAME.' <info@leadtnd.com>';
					
					$subject = "Forgot password mail for $users_first_name $users_last_name";
					
					$Body = "<html><head><title>$subject</title></head>
<body>
<p>
Dear <strong>$users_first_name $users_last_name</strong>,<br />
<br />
<strong>Welcome to the ".COMPANYNAME." forgot password information</strong><br />
<br />
Click or copy the link below to change your password
<br />
<br />
<a href=\"$loginURL\" title=\"Click Here\">Click here</a><br />
Or
<br />
Copy link: $loginURL
<br /><br />
Sincerely,<br />
BD IT SOFT TEAM
</p>
</body>
</html>";
				
					if($users_email =='' || is_null($users_email)){
						$returnStr = 'Your email is blank. Please contact with site admin.';
					}
					else{
						if (!mail($users_email, $subject, $Body, implode("\r\n", $headers))){
							$returnStr = 'Mail could not sent.';
						}
						else{
							$returnStr = 'Please check your email for a message from us';
						}
					}					
				}
				else{
					$returnStr = 'Your email is blank. Please contact with site admin.';				
				}
			}				
			else{						
				$returnStr = 'Your sub-domain and email was not found in our system. Please check email and sub-domain then try to reset password.';
			}
		}
		return json_encode(array('login'=>'', 'returnStr'=>$returnStr));
	}
	
	public function setnewpassword(){
		$changepass_link = $GLOBALS['segment3'];
		
		$users_email = '';
		$usersObj = $this->db->getObj("SELECT users_id, users_email, changepass_link FROM users WHERE changepass_link = :changepass_link AND users_publish = 1", array('changepass_link'=>$changepass_link));
		if($usersObj){
			$row = $usersObj->fetch(PDO::FETCH_OBJ);
			$users_email = $row->users_email;
			$changepass_link = $row->changepass_link;
		}
		else{
			return "<meta http-equiv = \"refresh\" content = \"0; url = /Login/index/\" />";
		}
		
		$innerHTMLStr = '<form class="txtblack" name="frmNewPass" id="frmNewPass" method="post" onsubmit="return checkNewPass();" enctype="multipart/form-data">
					<div class="w100Per mbot15">
						<input readonly required minlength="4" class="form-control py-4" name="users_email" id="users_email" value="'.$users_email.'" type="email" placeholder="Enter email address" />
					</div>
					<div class="w100Per mbot15">
						<input type="password" name="users_password" id="users_password" required minlength="4" maxlength="32" class="form-control" placeholder="Password" autocomplete="off" value=""/>
					</div>
					<div class="w100Per mbot15">
						<input type="password" name="reusers_password" id="reusers_password" required minlength="4" maxlength="32" class="form-control" placeholder="Retype Password" autocomplete="off" value=""/>
					</div>					
					<div class="w100Per">
						<div id="mathCaptcha"></div>
						<span id="errRecaptcha" style="color:red"></span>
					</div>
					<div class="hr mbot10"></div>
					<div class="w100Per">						
						<input type="hidden" name="changepass_link" id="changepass_link" value="'.$changepass_link.'"/>
						<a class="cursor" href="/Login/index">Got Forgot Password?</a>
						<button type="submit" class="btn btn-primary fright">Save & Login</button>
					</div>
				</form>';
		
		$htmlStr = $this->htmlBody($innerHTMLStr);
		return $htmlStr;
	}
	
	public function AJsaveNewPass(){
		$returnStr = '';
		if(isset($_SESSION["users_id"])){$returnStr = 'home';}
		
		$changepass_link = $_POST['changepass_link']??'';
		$users_email = $_POST['users_email']??'';
		
		if($changepass_link == '' || $users_email == ''){
			$returnStr = 'Form fields data is missing.';
		}
		else{
			$baseurl = "http://$GLOBALS[subdomain].".OUR_DOMAINNAME.'/';
			
			$usersObj = $this->db->getObj("SELECT users_id, users_first_name, users_last_name FROM users WHERE users_email = :users_email AND changepass_link = :changepass_link AND users_publish = 1", array('users_email'=>$users_email, 'changepass_link'=>$changepass_link));
			if($usersObj){
				$row = $usersObj->fetch(PDO::FETCH_OBJ);			
					
				$users_id = $row->users_id;
				$users_password = trim($_POST['users_password']??'');
				$password_hash = password_hash($users_password, PASSWORD_DEFAULT);
					
				$users_first_name = $row->users_first_name;
				$users_last_name = $row->users_last_name;
				
				if($users_email !=''){
					
					$fieldsData = array('password_hash'=>$password_hash, 'changepass_link'=>'', 'last_updated'=> date('Y-m-d H:i:s'));
					$this->db->update('users', $fieldsData, $users_id);
									
					$loginURL = $baseurl.'login/';
					
					$headers = array();
					$headers[] = 'MIME-Version: 1.0';
					$headers[] = 'Content-type: text/html; charset=iso-8859-1';
					$headers[] = 'To: '.$users_first_name.' <'.$users_email.'>';
					$headers[] = 'From: '.COMPANYNAME.' <info@leadtnd.leadtnd.com>';
					
					$subject = "Set new password mail for $users_first_name $users_last_name";
					
					$Body = "<html><head><title>$subject</title></head>
<body>
<p>
Dear <strong>$users_first_name $users_last_name</strong>,<br />
<br />
<strong>Welcome to the ".COMPANYNAME." forgot password information</strong><br />
<br />
Please try to login with the following users name and password.<br />
<br />
Email: $users_email<br />
Password: $users_password<br />
<br />
Please click on the link below to login.<br />
<br />
<a href=\"$loginURL\" title=\"Click Here\">Click Here</a><br />
<br />
<br />
Sincerely,<br />
BD IT SOFT TEAM
</p>
</body>
</html>";
				
					if($users_email =='' || is_null($users_email)){
						$returnStr = 'Your email is blank. Please contact with site admin.';
					}
					else{
						if (!mail($users_email, $subject, $Body, implode("\r\n", $headers))){
							$returnStr = 'Mail could not sent. '.$mail->ErrorInfo();
						}
						else{
							$returnStr = 'Password changed successfully';
						}
					}
				}				
				else{						
					$returnStr = 'Your sub-domain and email was not found in our system. Please check email and sub-domain then try to reset password.';
				}
			}
			else{
				$returnStr = 'Your link is not found in our system. Please check email then try to reset password.';
			}
		}
		return json_encode(array('login'=>'', 'returnStr'=>$returnStr));
	
	}
		
	private function htmlBody($pageMiddle){
		$segment3 = $GLOBALS['segment3'];
		$successMsg = '';
		$lmObj = $this->db->getObj("SELECT login_message FROM users WHERE users_id = 1", array(),1);
		if($lmObj){
			$login_message = stripslashes(trim($lmObj->fetch(PDO::FETCH_OBJ)->login_message));
			if(strlen($login_message)>50){
				$successMsg = "<div class=\"col-sm-12 col-sm-12 col-sm-12\">
                        <div class=\"bs-callout bs-callout-info well error_msg\">
                            $login_message
                        </div>
                    </div>";
			}
		}
		
		$msg = $_GET['msg']??'';
		if($segment3=='signup-success'){
			$successMsg = "<div class=\"col-sm-12 col-sm-12 col-sm-12\">
					<div class=\"bs-callout bs-callout-info well success_msg\">
						Congratulations! You are successfully registered.<br />
						Please login below to begin.
					</div>
				</div>";
		}
		elseif($segment3=='sent-success'){
			$successMsg = "<div class=\"col-sm-12 col-sm-12 col-sm-12\">
					<div class=\"bs-callout bs-callout-info well success_msg\">
						You have been sent a email to confirm this request.<br />
						Check your email and click the enclosed link to change your password.
					</div>
				</div>";
		}
		elseif($segment3=='password-saved'){
			$successMsg = "<div class=\"col-sm-12 col-sm-12 col-sm-12\">
					<div class=\"bs-callout bs-callout-info well success_msg\">
						Your new password saved successfully.<br />
						Check your new password.
					</div>
				</div>";
		}
		elseif($segment3=='duplicated_users'){
			$successMsg = "<div class=\"col-sm-12 col-sm-12 col-sm-12\">
                        <div class=\"bs-callout bs-callout-info well error_msg\">
                            Another users has logged in with your email address and has logged you out.
                        </div>
                    </div>";
		}
		elseif(!empty($msg)){
			if($msg=='Please check your email for a message from us'){
				$successMsg = "<div class=\"col-sm-12 col-sm-12 col-sm-12\">
                        <div class=\"bs-callout bs-callout-info well success_msg\">
                            $msg
                        </div>
                    </div>";
			}
			else{
				$successMsg = "<div class=\"col-sm-12 col-sm-12 col-sm-12\">
                        <div class=\"bs-callout bs-callout-info well error_msg\">
                            $msg
                        </div>
                    </div>";
			}
		}
		
		$returnHTML = $successMsg.$pageMiddle;
		
		return $returnHTML;
	}
	
	public function headerHTML(){
		$segment1 = $GLOBALS['segment1'];
		$segment2 = $GLOBALS['segment2'];
		$segment3 = $GLOBALS['segment3'];
		if(in_array($segment3, array('duplicated_users', 'Logout', 'autologout', 'session_ended'))){
			if(isset($_SESSION["users_id"]) && $segment3 !='duplicated_users'){
				$users_id = $_SESSION["users_id"]??0;
				$updated_array = array('last_updated'=> date('Y-m-d H:i:s'), 'login_ck_id'=>'');
				$this->db->update('users', $updated_array, $users_id);
				
				$sql = "SELECT users_login_history_id FROM users_login_history WHERE users_id = :users_id ORDER BY users_login_history_id DESC";
				$ulhObj = $this->db->getObj($sql, array("users_id"=>$users_id),1);
				if($ulhObj){
					$users_login_history_id = $ulhObj->fetch(PDO::FETCH_OBJ)->users_login_history_id;
					$updated_array = array('logout_datetime' => date('Y-m-d H:i:s'));
					$this->db->update('users_login_history', $updated_array, $users_login_history_id);
				}
			}
			session_unset();session_destroy();
		}
		
		$htmlStr = '<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
	<meta name="description" content="'.COMPANYNAME.'" />
	<meta name="author" content="'.COMPANYNAME.'" />
	<link rel="shortcut icon" href="/assets/images/icons/favicon.ico">
	<title>'.$GLOBALS['title'].'</title>
	
	<link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/font-awesome.min.css">    
    <link rel="stylesheet" href="/assets/css/jquery-ui.css">
	<link href="/assets/css/adminStyles.css" rel="stylesheet" />	
	
	<script src="/assets/js/jquery.min.js"></script>
    <script src="/assets/js/common.js"></script>
	<script src="/assets/js/jquery-ui.min.js"></script>	
    <script src="/assets/js/bootstrap.min.js"></script>
</head>
<body class="bg-primary">
	<div id="layoutAuthentication">
		<div id="layoutAuthentication_content">
			<main>
				<div class="container">
					<div class="row justify-content-center">
						<div class="col-lg-5">
							<div class="card shadow-lg border-0 rounded-lg mt-5">
								<div class="card-header">
									<h1 class="txtblack text-center font-weight-light my-4">'.$GLOBALS['title'].'</h1>
								</div>
								<div class="card-body">	
									<div class="col-sm-12" style="color:red" id="errMsg"></div>';
		
		return $htmlStr;
	}
	
	public function footerHTML(){
		$htmlStr = '<script src="/assets/js/mathCaptcha.js"></script>
								</div>
								<div class="card-footer text-center">
									<div class="small"><a target="_blank" href="http://bditsoft.com">@rights BDITSOFT.COM</a></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</main>
		</div>
	</div>
</body>
</html>';
		
		return $htmlStr;
	}
	
}
?>