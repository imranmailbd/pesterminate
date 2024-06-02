<?php
class Home
{
	protected $db;
	public function __construct($db)
	{
		$this->db = $db;
	}

	public function index()
	{

		$users_id = $_SESSION["users_id"] ?? 0;
		$admin_id = $_SESSION["admin_id"] ?? 0;
		$returnHTML = "";
		$popup_message = '';
		$usersObj = $this->db->getObj("SELECT popup_message FROM users WHERE users_id = $users_id", array());
		if ($usersObj) {
			$popup_message = $usersObj->fetch(PDO::FETCH_OBJ)->popup_message;
		}

		if ($popup_message != '' && strlen(trim(strip_tags($popup_message))) > 10) {
			$returnHTML .= "<script language=\"JavaScript\" type=\"text/javascript\">
				j(document).ready(function(){
					var popupmsg = stripslashes('" . addslashes($popup_message) . "');
					alert_dialog('" . COMPANYNAME . " Update', popupmsg, 'Ok thanks');				
				});
			</script>";
			$this->db->update('users', array('popup_message' => '', 'last_updated' => date('Y-m-d H:i:s')), $users_id);
		}

		$sendemail = 1;
		$created_on = strtotime($_SESSION["created_on"]);
		$registeredDays = floor((strtotime(date('Y-m-d')) - $created_on) / 86400);

		$str = '';
		$l = 0;
		$iconnumber = 5;
		if (isset($_COOKIE['screenHeight'])) {
			$screenHeight = (int)$_COOKIE['screenHeight'];
			$iconnumber = floor($screenHeight / 100);
		}


		//return 'Ok';
		$Template = new Template($this->db);
		$modules = $Template->modules();
		$modulesicon = $Template->modulesicon();
		foreach ($modules as $title => $module) {
			if ((empty($_SESSION["allowed"]) || array_key_exists($module, $_SESSION["allowed"]))) {
				$l++;
				if ($l > $iconnumber) {
					$fonticon = $modulesicon[$module];
					$str .= "<li>
								<div class=\"homeiconmenu blue_bg boxshadow\">
									<a class=\"firstclild sidebarlink txtwhite\" href=\"/$module\" onClick=\"tracking_emails($sendemail, '$module');return true;\" title=\"$title\">
										<br>
										<i class=\"fa fa-$fonticon fa-2\"></i><br>
										$title
									</a>
								</div>
							</li>";
				}
			}
		}

		if ($str != '') {
			$returnHTML .= "<div class=\"row mtop15\">
				<div class=\"col-sm-12\">
					<div class=\"widget mbottom10\">
						<div class=\"widget-header\">
							<h3>Modules list</h3>
						</div> <!-- /widget-header -->
						<div class=\"widget-content module-center\">                
							<ul class=\"inventory txtcenter\">							
								$str     
							</ul>
						</div>
					</div>
				</div>
			</div>";
		}

		return $returnHTML;
	}

	public function help()
	{
		$prevuri = 'Home';
		if (isset($_SERVER['HTTP_REFERER'])) {
			$prevuri = str_replace($_SERVER['SERVER_NAME'], '', $_SERVER['HTTP_REFERER']);
		}
		$returnHTML = "";

		$returnHTML .= "<div class=\"row\">
				<div class=\"col-xs-6 col-sm-6\">
					<h1 class=\"singin2\">$GLOBALS[title] <i class=\"fa fa-info-circle txt16normal\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"This page displays the list of helping information\"></i></h1>
				</div>
				<div class=\"col-xs-6 col-sm-6 ptop15\">    	
					<div class=\"floatright mleft15 pbottom15\">
						<a class=\"btn hilightbutton\" href=\"javascript:void(0);\" title=\"Contact Us\" onClick=\"showHelpPopup();\">
							Contact Us
						</a>
					</div>
					<input type=\"hidden\" name=\"prevuri\" id=\"prevuri\" value=\"$prevuri\">
				</div>     
			</div>
			<div class=\"row\">
				<div class=\"width100per\">
					<div class=\"widget mbottom10\">           
						<div class=\"widget-header\">
							<h3>Training Videos</h3>
						</div> <!-- /widget-header -->
						<div class=\"widget-content\">
							<div class=\"row\">";
		$videosSql = "SELECT videos_url FROM videos WHERE videos_publish = 1 ORDER BY videos_id DESC";
		$videosObj = $this->db->getObj($videosSql, array());
		if ($videosObj) {
			while ($oneRow = $videosObj->fetch(PDO::FETCH_OBJ)) {
				$videos_url = trim(stripslashes($oneRow->videos_url));
				$returnHTML .= '<div class="col-sm-4">
											<iframe width="100%" height="315" src="' . $videos_url . '" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
										</div>';
			}
		}
		$returnHTML .= "</div>
						</div>
					</div>
				</div>		
			</div>
		</div>";

		return $returnHTML;
	}

	public function helpForm()
	{

		$returnData = array();
		$returnData['login'] = '';
		$users_id = $_SESSION["users_id"] ?? 0;
		$users_email = '';
		$usersObj = $this->db->getObj("SELECT users_email FROM users WHERE users_id = $users_id", array());
		if ($usersObj) {
			$users_email = $usersObj->fetch(PDO::FETCH_OBJ)->users_email;
		}
		$returnData['helpemail'] = $users_email;
		$returnData['helpname'] = $_SESSION["users_first_name"];
		return json_encode($returnData);
	}

	public function sendHelpMail()
	{

		$returnStr = '';
		$subdomain = $GLOBALS['subdomain'];

		$name = $_POST['helpname'] ?? '';
		$email = $_POST['helpemail'] ?? '';
		$helpbrowser = $_POST['helpbrowser'] ?? '';
		$helpurl = $_POST['helpurl'] ?? '';
		$subject = $_POST['helpsubject'] ?? '';
		$subject = $subdomain . " SUPPORT " . $subject;
		$description = $_POST['helpdescription'] ?? '';

		$headers = array();
		$headers[] = 'MIME-Version: 1.0';
		$headers[] = 'Content-type: text/html; charset=iso-8859-1';
		$headers[] = 'To: BD IT SOFT <ceo@bditsoft.com>';
		$headers[] = 'From: ' . COMPANYNAME . ' <info@leadtnd.leadtnd.com>';

		$Body = '<!DOCTYPE html>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="description" content="' . COMPANYNAME . '" />
<meta name="keywords" content="' . COMPANYNAME . "\" />
							</head>
							<body>
								<p>
									Name: <b>$name</b><br>
									Email: $email<br>
									<br>
									Message :<br>
									" . nl2br($description) . "<br>
									<br />
									Page URL: $helpurl<br />
									Browser Info: $helpbrowser
									<br />
									Status: $status
								</p>
							</body>
						</html>";
		if ($email == '' || is_null($email)) {
			$returnStr = 'Your email is blank. Please contact with site admin.';
		} else {
			if (!mail('ceo@bditsoft.com', $subject, $Body, implode("\r\n", $headers))) {
				$returnStr = 'Sorry! Could not send mail. Try again later.';
			} else {
				$returnStr = 'sent';
			}
		}

		return json_encode(array('login' => '', 'returnStr' => $returnStr));
	}

	public function notpermitted()
	{
		$returnHTML = "<div class=\"row\">
			<div class=\"col-sm-12\">
				<div class=\"bs-callout bs-callout-info well\">
					<h4 class=\"borderbottom\">$GLOBALS[title] </h4>
					<p>Sorry, you do not have permission to view this page</p>
				</div>
			</div>    
		</div>";
		return $returnHTML;
	}

	public function checkNotification()
	{
		$notifications = 0;
		$tableObj = $this->db->getObj("SELECT COUNT(appointments_id) AS totalNotification FROM appointments WHERE notifications = 1", array(), 1);
		if ($tableObj) {
			$notifications = intval($tableObj->fetch(PDO::FETCH_OBJ)->totalNotification);
		}
		return json_encode(array('login' => '', 'notifications' => $notifications));
	}

	function handleErr()
	{
		$POST = json_decode(file_get_contents('php://input'), true);

		$name = $POST['name'] ?? '';
		if (is_array($name)) {
			$name = implode(', ', $name);
		}
		$message = $POST['message'] ?? '';
		if (is_array($message)) {
			$message = implode(', ', $message);
		}
		$url = $POST['url'] ?? '';
		if (is_array($url)) {
			$url = implode(', ', $url);
		}

		$this->db->writeIntoLog($name . ', Message: ' . $message . ', Page Url: ' . $url);
		return json_encode(array('returnMsg' => 'Data Saved'));
	}
}
