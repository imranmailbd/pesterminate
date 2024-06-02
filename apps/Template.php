<?php
class Template{
	protected $db;
	public function __construct($db){$this->db = $db;}
	
	public function headerHTML(){
		$segment1 = $GLOBALS['segment1'];
		$segment2 = $GLOBALS['segment2'];
		$segment3 = $GLOBALS['segment3'];
		if(in_array($segment3, array('duplicated_users', 'Logout', 'autologout', 'session_ended'))){
			if(isset($_SESSION["users_id"]) && $segment3 !='duplicated_users'){
				$users_id = $_SESSION["users_id"]??0;
				$updated_array = array('last_updated'=> date('Y-m-d H:i:s'), 'login_ck_id'=>'');
				$this->db->update('users', $updated_array, $users_id);
				
				$sql = "SELECT users_login_history_id FROM users_login_history WHERE users_id = :users_id ORDER BY users_login_history_id DESC LIMIT 0, 1";
				$ulhObj = $this->db->getObj($sql, array("users_id"=>$users_id),1);
				if($ulhObj){
					$users_login_history_id = $ulhObj->fetch(PDO::FETCH_OBJ)->users_login_history_id;
					$updated_array = array('logout_datetime' => date('Y-m-d H:i:s'));
					$this->db->update('users_login_history', $updated_array, $users_login_history_id);
				}
			}
			session_unset();session_destroy();
		}

		$users_first_name = $_SESSION["users_first_name"]??'';
		$is_admin = $_SESSION["is_admin"]??0;
		$admin_id = $_SESSION["admin_id"]??0;
		
		if(!isset($title)){$title = 'Home - '.COMPANYNAME;}
		
		if(isset($_COOKIE['failcall'])){
			$failcall = $_COOKIE['failcall'];
			setcookie('failcall', '', time() - 3600, '/');
		}

		$prevuri = 'Home';
		if(isset($_SERVER['HTTP_REFERER'])){$prevuri = str_replace($_SERVER['SERVER_NAME'], '', $_SERVER['HTTP_REFERER']);}

		//===============Check Session and Unset================//
		if(isset($_SESSION["current_module"]) && isset($_SESSION["list_filters"]) && strcmp($_SESSION["current_module"],$segment1) !==0){
			unset($_SESSION["list_filters"]);
		}
		$htmlStr = "<!DOCTYPE html>
			<html>
			<head>
				<meta charset=\"utf-8\">
				<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
				<title>$GLOBALS[title]</title>
				<link rel=\"shortcut icon\" href=\"/assets/images/icons/favicon.ico\">
				
				<link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css\">
			<link rel=\"stylesheet\" href=\"/assets/css/font-awesome.min.css\">    
				<link rel=\"stylesheet\" href=\"/assets/css/jquery-ui.css\">
				<link href=\"/assets/css/adminStyles.css\" rel=\"stylesheet\" />	
				
				<script src=\"/assets/js/jquery.min.js\"></script>
				<script language=\"JavaScript\" type=\"text/javascript\">
					var j = jQuery.noConflict();
					var COMPANYNAME = '".COMPANYNAME."';
					var currency = 'TK';
					var calenderDate = 'dd-mm-yyyy';
				</script>
			</head>  
			<body>
			<input type=\"hidden\" name=\"segment1\" id=\"segment1\" value=\"$segment1\">
			<div class=\"width100per\" id=\"topheaderbar\">
    	<header class=\"dashboard_header\">
		<h3 class=\"h3\">
			<a class=\"txt26bold\" href=\"javascript:void(0);\" title=\"".COMPANYNAME."\" id=\"settingsleftsidemenubar\"><i class=\"fa fa-navicon hidden\" id=\"fanav\"></i></a>
			<a class=\"txt26bold\" href=\"/Home\" title=\"".COMPANYNAME."\"><i class=\"fa\">&#xf015;</i> ".COMPANYNAME."</a>
		</h3>
		<div class=\"width200 floatright headerright mtop5\">            
			<ul class=\"navBarNav\">
				<li class=\"dropdown minwidth120 txtleft\" id=\"mydropdown\">
				<a href=\"#\" class=\"dropdownToggle\" role=\"button\" aria-haspopup=\"true\" aria-expanded=\"true\">
					$users_first_name <span class=\"caret\"></span>
				</a>
				<ul class=\"dropDownMenu\">
					<li><a href=\"/Settings/myInfo\" title=\"My Information\">My Information</a></li>
					<li><a href=\"/Login/index/Logout\" id=\"lnkLogOut\" title=\"Logout\">Logout</a></li>
				</ul>
			  </li>
			</ul>
		</div>
		<div class=\"width80 floatright headerright ptop20 txtcenter\">
			<a class=\"txt16normal\" href=\"/Home/help\" title=\"Help\">Help</a>
		</div>";
		$s = '';
		if(isset($_GET['s'])){$s=$_GET['s'];}
		
		$htmlStr .= "<div class=\"width280 floatright\">
			<div class=\"header_search\">
				<form action=\"#\" name=\"frmsearchitem_number\" role=\"search\" method=\"get\" onsubmit=\"return check_frmsearchitem_number();\" enctype=\"multipart/form-data\" accept-charset=\"utf-8\">
					<label for=\"s\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Enter Project Name\"><i class=\"fa fa-search\"></i></label>
					<input maxlength=\"50\" autocomplete=\"off\" name=\"s\" id=\"s\" class=\"dashboard_search\" type=\"text\" value=\"$s\" placeholder=\"Search Project\" />
				</form>
				<style type=\"text/css\">
				.ui-autocomplete { height:auto !important; max-height:300px; overflow:auto; background:#FFF; border:1px solid #CCC; border-radius-bottom-left:4px;
					width:368px;}
				.search_row{text-align:left;}
				.ui-autocomplete li{ min-height:30px; border-bottom:1px dotted #CCC; padding:5px 10px; text-align:left;}
				</style>";
				if(in_array($segment1, array('Home'))){
					$htmlStr .= "<script type=\"text/javascript\" language=\"javascript\">
						j(document).ready(function(){
							setTimeout(function() {	document.getElementById(\"s\").focus();}, 500);
						});                            
					</script>";
				}
				
				$subdomain = '';
				if(OUR_DOMAINNAME=='bditsoft.com'){
					$subdomain = $GLOBALS['subdomain'].'.';
				}

				$htmlStr .= "</div>
		</div>
		<div class=\"width280 floatright ptop10\">
			<a href=\"http://$subdomain".OUR_DOMAINNAME."\" target=\"_blank\" title=\"Visit Website\" class=\"btn btn-default bggreen\">Visit Website</a>
		</div>
			</header>
		</div>
		<script type=\"text/javascript\">
			document.cookie = \"headerHeight=\"+Math.floor(j(\"#topheaderbar\").height()+240)+\"; path=/\";
		</script>
		<div class=\"width100per\">
			<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
			<tr id=\"pageBody\">
			<td width=\"100\" class=\"bgdeepblue\" valign=\"top\" id=\"sideBar\">
				<div class=\"sidebar-wrapper\" id=\"sidebarWrapper\">
					<ul class=\"sidebar-nav settingslefthide\" id=\"sideNav\">";					
						$users_id = $_SESSION["users_id"]??0;
						if($users_id>0){
							$modules = $this->modules();
							$modulesicon = $this->modulesicon();
							$l=0;						
							$iconnumber = 5;
							if(isset($_COOKIE['screenHeight'])){
								$iconnumber = floor(intval($_COOKIE['screenHeight'])/100);
							}
							
							foreach($modules as $mtitle=>$module){
								if(isset($_SESSION["allowed"]) && (empty($_SESSION["allowed"]) || array_key_exists($module, $_SESSION["allowed"])) && $l<$iconnumber){
									$l++;
									$active = '';
									if(strcmp($segment1, $module)==0){$active = ' class="active"';}
									$fonticon = $modulesicon[$module];
									$href = "";								
									$htmlStr .= "<li$active>
												<a class=\"firstclild sidebarlink\" href=\"/$module\">
													<i class=\"fa fa-$fonticon\"></i> $mtitle
												</a>
											</li>";
								}
								else{}
							}
						}
						
			$htmlStr .= "</ul>
				</div>
			</td>
			<td valign=\"top\">
				<div id=\"page-content-wrapper\">
					<div class=\"container-fluid\">
						<div class=\"col-lg-12 pleft0 pright0\">
							<div class=\"dashboard_contant pleft20 pright20\">";
			
		return $htmlStr;
	}
	
	public function footerHTML(){
		
		$htmlStr = "</div>
						</div>
					</div>
				</div>
			</td>
		</tr>
    	</table>
		</div>
		<div style=\"position:fixed; left:50%; top:0"."px; z-index:999; height:1px; margin-left:-250px; width:500px;\">			
			<div class=\"row\" id=\"showmessagehere\"></div>
		</div>
		<div id=\"dialog-confirm\" class=\"hidediv\" title=\"Information\" style=\"width:100%;\"></div>
		<div id=\"form-dialog\" class=\"hidediv\" title=\"Information\" style=\"width:100%;\"></div>
		<div id=\"popup600\" style=\"display:none;width: 100%;max-width:600px;background:#FFFFFF;border: 1px solid #CCCCCC;overflow:hidden; padding:15px;\"></div>
		<div id=\"popup1000\" style=\"display:none;width: 100%;max-width:1000px;background:#FFFFFF;border: 1px solid #CCCCCC;overflow:hidden; padding:15px;\"></div>
		<audio id=\"notifier\"  src=\"/assets/notifications_sound/notification.wav\"></audio>
		<script src=\"/assets/js/common.js\"></script>
		<script src=\"/assets/js/jquery-ui.min.js\"></script>	
		<script src=\"/assets/js/bootstrap.min.js\"></script>
		</body>
		</html>";
		return $htmlStr;
	}
		
	public function twoDateDifference($date1, $date2=''){
		$date1 = new DateTime(date('Y-m-d', strtotime($date1)));
		if($date2==''){
			$date2 = new DateTime("now");
		}
		else{
			$date2 = new DateTime(date('Y-m-d', strtotime($date2)));
		}
		$interval = $date1->diff($date2);
		$returnval = 0;
		if($date1 < $date2){
			$returnval = $interval->format('%a');
		}
		
		return $returnval;
	}
	
	public function modules(){
		$returnarray = array();
		$returnarray['Appointments'] = 'Appointments';
		$returnarray['Customers'] = 'Customers';
		$returnarray['Orders'] = 'Orders';
		$returnarray['News/Articles'] = 'News_articles';
		$returnarray['Services'] = 'Services';
		$returnarray['Activity Feed'] = 'Activity_Feed';
		$returnarray['Manage Data'] = 'Manage_Data';
		$returnarray['Setup'] = 'Settings';
		
		return $returnarray;
	}
	
	public function modulesicon(){
		$returnarray = array();
		$returnarray['Appointments'] = 'folder-open';
		$returnarray['Customers'] = 'user'; 
		$returnarray['Orders'] = 'exchange'; 
		$returnarray['News_articles'] = 'fa-bullhorn'; 
		$returnarray['Services'] = 'fa-wrench'; 
		$returnarray['Activity_Feed'] = 'exchange';
		$returnarray['Manage_Data'] = 'database'; 
		$returnarray['Settings'] = 'cog';
		
		return $returnarray;
	}
	
}
?>