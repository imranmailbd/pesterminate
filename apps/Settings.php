<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class Settings{

   protected $db;
   private $page, $rowHeight, $totalRows;
	public $pageTitle;
	private $keyword_search;
	
	public function __construct($db){$this->db = $db;}
	
	public function myInfo(){
		$users_id = $_SESSION['users_id']??0;
		
		$this->pageTitle = $GLOBALS['title'];
		$innerHTMLStr = "";
		
		$usersObj = $this->db->getObj("SELECT users_email, users_first_name, users_last_name, is_admin, minute_to_logout FROM users WHERE users_id = $users_id", array());
		if($usersObj){
			$usersOneRow = $usersObj->fetch(PDO::FETCH_OBJ);
			$users_email =  $usersOneRow->users_email;
			$users_first_name = $usersOneRow->users_first_name;
			$users_last_name = $usersOneRow->users_last_name;
			$is_admin = $usersOneRow->is_admin;
			$minute_to_logout = $usersOneRow->minute_to_logout;
			
			$minToLogOutOpt = "";
			$mtl = 10;
			while($mtl<=120){
				$selected = '';
				if($mtl==$minute_to_logout){$selected = ' selected="selected"';}
				$minToLogOutOpt .= "<option$selected value=\"$mtl\">$mtl Minutes</option>";
				if($mtl<30){$mtl=$mtl+10;}
				else{$mtl=$mtl+15;}
			}
			
			$innerHTMLStr = "<form name=\"frmmyInfo\" id=\"frmmyInfo\" action=\"#\" onsubmit=\"return check_myInfo();\" enctype=\"multipart/form-data\" method=\"post\" accept-charset=\"utf-8\">
				<h4 class=\"borderbottom\">Login Information</h4>
				<div class=\"form-group row\">
					<div class=\"col-sm-12 col-sm-4 col-md-2 txtright\">
						<label for=\"users_email\">Email<span class=\"required\">*</span> :</label>
					</div>
					<div class=\"col-sm-12 col-sm-8 col-md-6\">
						<input maxlength=\"50\" type=\"email\" required value=\"$users_email\" id=\"users_email\" name=\"users_email\" class=\"form-control\" />
						<span id=\"errmsg_users_email\" class=\"errormsg\"></span>
					</div>
				</div>
				<div class=\"form-group row\">
					<div class=\"col-sm-12 col-sm-4 col-md-2 txtright\">
						<label for=\"users_password\">Password :</label>
					</div>
					<div class=\"col-sm-12 col-sm-8 col-md-6\">
						<input maxlength=\"32\" autocomplete=\"off\" type=\"password\" value=\"\" id=\"users_password\" name=\"users_password\" class=\"form-control\">
						<span id=\"errmsg_users_password\" class=\"errormsg\"></span>
					</div>
				</div>
				<h4 class=\"borderbottom\">Basic Information</h4>
				<div class=\"form-group row\">
					<div class=\"col-sm-12 col-sm-4 col-md-2 txtright\">
						<label for=\"users_first_name\">First Name<span class=\"required\">*</span> :</label>
					</div>
					<div class=\"col-sm-12 col-sm-8 col-md-6\">
						<input type=\"text\" required value=\"$users_first_name\" id=\"users_first_name\" name=\"users_first_name\" class=\"form-control\" size=\"12\" maxlength=\"12\" />
					</div>
				</div>
				<div class=\"form-group row\">
					<div class=\"col-sm-12 col-sm-4 col-md-2 txtright\">
						<label for=\"users_last_name\">Last Name<span class=\"required\">*</span> :</label>
					</div>
					<div class=\"col-sm-12 col-sm-8 col-md-6\">
						<input type=\"text\" required value=\"$users_last_name\" id=\"users_last_name\" name=\"users_last_name\" class=\"form-control\" size=\"17\" maxlength=\"17\">
					</div>
				</div>
				<div class=\"form-group row\">
					<div class=\"col-sm-12 col-sm-4 col-md-2 txtright\">
						<label for=\"minute_to_logout\">Log out after :</label>
					</div>
					<div class=\"col-sm-12 col-sm-8 col-md-6\">
						<select id=\"minute_to_logout\" name=\"minute_to_logout\" class=\"form-control\">
							$minToLogOutOpt
						</select>
					</div>
				</div>  
				<div class=\"form-group row\">
					<div class=\"col-sm-12 col-sm-4 col-md-2 txtright\">&nbsp;</div>
					<div class=\"col-sm-12 col-sm-8 col-md-6\">
						<input type=\"hidden\" name=\"users_id\" id=\"users_id\" value=\"$users_id\">
						<input type=\"hidden\" name=\"is_admin\" id=\"is_admin\" value=\"$is_admin\">
						<input class=\"btn btn-success\" name=\"submit\" id=\"submit\" type=\"submit\" value=\"Update\">
					</div>
				</div>                                  
			</form>";
		}
		
		$htmlStr = $this->htmlBody($innerHTMLStr);
		return $htmlStr;
	}
	
   public function AJsave_myInfo(){
	
		$savemsg = $message = '';

		$users_id = $_SESSION["users_id"]??0;
		$users_email = $_POST['users_email']??'';
		$users_first_name = $_POST['users_first_name']??'';
		$users_last_name = $_POST['users_last_name']??'';
		$minute_to_logout = $_POST['minute_to_logout']??120;
		$totalrows = 0;
		$dupSql = "SELECT COUNT(users_id) AS totalrows FROM users WHERE users_email = :users_email AND users_id != $users_id AND users_publish = 1";
		$queryObj = $this->db->getObj($dupSql, array('users_email'=>$users_email));
		if($queryObj){
			$totalrows = $queryObj->fetch(PDO::FETCH_OBJ)->totalrows;
		}
		if($totalrows>0){
			$savemsg = 'error';
			$message .= "<p>Same Email Address already used.</p>";
		}
		else{
			$conditionarray = array();
			$conditionarray['users_email'] = $users_email;
			$users_passwordstr = '';
			if(!empty($_POST['users_password']) && strlen($_POST['users_password'])>4){
				$password_hash = password_hash($_POST['users_password'], PASSWORD_DEFAULT);
				$conditionarray['password_hash'] = $password_hash;
			}
			$conditionarray['users_first_name'] = $users_first_name;
			$conditionarray['users_last_name'] = $users_last_name;
			$conditionarray['last_updated'] = date('Y-m-d H:i:s');
			$conditionarray['lastlogin'] = date('Y-m-d H:i:s');
			$conditionarray['minute_to_logout'] = $minute_to_logout;
			
			$updateusers = $this->db->update('users', $conditionarray, $users_id);
			if($updateusers){
				$_SESSION["users_first_name"]=$users_first_name;
				$_SESSION["minute_to_logout"]=$minute_to_logout;
				$savemsg = 'update-success';
				$message = 'Updated successfully';
			}
			else{
				$savemsg = 'error';
				$message = 'Could not updated';
			}
		}

		$array = array( 'login'=>'', 'id'=>$users_id,
			'savemsg'=>$savemsg,
			'message'=>$message);
		return json_encode($array);
	}
	
	//========================For branches module=======================//    		
	public function branches($segment3){
		
		$this->pageTitle = $GLOBALS['title'];
		$list_filters = $_SESSION['list_filters']??array();
		
		$keyword_search = $list_filters['keyword_search']??'';
		$this->keyword_search = $keyword_search;
		
		$this->filterAndOptions_branches();
		
		$page = !empty($segment3) ? intval($segment3):1;
		if($page<=0){$page = 1;}
		if(!isset($_SESSION['limit'])){$_SESSION['limit'] = 'auto';}
		$limit = $_SESSION['limit'];
		
		$this->rowHeight = 34;
		$this->page = $page;
		$tableRows = $this->loadTableRows_branches();
		
		$limOpt = '';
		$limOpts = array(15, 20, 25, 50, 100, 500);
		foreach($limOpts as $oneOpt){
			$selected = '';
			if($limit==$oneOpt){$selected = ' selected';}
			$limOpt .= "<option$selected value=\"$oneOpt\">$oneOpt</option>";
		}
		
		$innerHTMLStr = "<input type=\"hidden\" name=\"pageURI\" id=\"pageURI\" value=\"$GLOBALS[segment1]/$GLOBALS[segment2]\">
		<input type=\"hidden\" name=\"page\" id=\"page\" value=\"$this->page\">
		<input type=\"hidden\" name=\"rowHeight\" id=\"rowHeight\" value=\"$this->rowHeight\">
		<input type=\"hidden\" name=\"totalTableRows\" id=\"totalTableRows\" value=\"$this->totalRows\">		
		<div class=\"row\">
			<div class=\"col-sm-12 col-sm-12 col-md-7\">
				<div class=\"row\">
					<div class=\"col-sm-12 col-md-6\">
						<h1 class=\"metatitle\">branches List <i class=\"fa fa-info-circle txt16normal\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"branches List\"></i></h1>
					</div>				
					<div class=\"col-sm-12 col-md-6\">
						<div class=\"input-group\">
							<input type=\"text\" placeholder=\"Search branches\" value=\"$keyword_search\" id=\"keyword_search\" name=\"keyword_search\" class=\"form-control\" maxlength=\"50\" />
							<span class=\"input-group-addon cursor\" onClick=\"checkAndLoadFilterData();\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Search branches\">
								<i class=\"fa fa-search\"></i>
							</span>
						</div>
					</div>
				</div>				
				<div class=\"row\">
					<div class=\"col-sm-12\" style=\"position:relative;\">
						<div id=\"no-more-tables\">
							<table class=\"col-sm-12 table-bordered table-striped table-condensed cf listing\">
								<thead class=\"cf\">
									<tr>
										<th class=\"txtcenter\">ID</th>
										<th class=\"txtcenter\">Name</th>
										<th class=\"txtcenter\">Picture</th>
									</tr>
								</thead>
								<tbody id=\"tableRows\">
									$tableRows
								</tbody>
							</table>
						</div>
					</div>    
				</div>
				<div class=\"row mtop10\">
					<div class=\"col-sm-12\">
						<select class=\"form-control width100 floatleft\" name=\"limit\" id=\"limit\" onChange=\"checkloadTableRows();\">
							<option value=\"auto\">Auto</option>
							$limOpt
						</select>
						<label id=\"fromtodata\"></label>
						<div class=\"floatright\" id=\"Pagination\"></div>
					</div>
				</div>
			</div>
			<div class=\"col-sm-12 col-sm-12 col-md-5\">            
				<h4 class=\"borderbottom\" id=\"formtitle\">Add New branches</h4>
				<form action=\"#\" name=\"frmbranches\" id=\"frmbranches\" onsubmit=\"return AJsave_branches();\" enctype=\"multipart/form-data\" method=\"post\" accept-charset=\"utf-8\">
					<div class=\"form-group\">
						<label for=\"name\">Name<span class=\"required\">*</span></label>
						<input type=\"text\" required class=\"form-control\" name=\"name\" id=\"name\" value=\"\" size=\"255\" maxlength=\"255\"/>
						<span id=\"error_name\" class=\"errormsg\"></span>
					</div>
					<div class=\"form-group\"> 
						<label for=\"address\">Address<span class=\"required\">*</span></label>
						<textarea rows=\"5\" cols=\"50\" required class=\"form-control\" name=\"address\" id=\"address\"></textarea>
					</div>
					<div class=\"form-group\"> 
						<label for=\"google_map\">Google Maps<span class=\"required\">*</span></label>
						<textarea rows=\"5\" cols=\"50\" required class=\"form-control\" name=\"google_map\" id=\"google_map\"></textarea>
					</div>
					<div class=\"form-group\"> 
						<label for=\"working_hours\">Working Hours<span class=\"required\">*</span></label>
						<textarea rows=\"5\" cols=\"50\" required class=\"form-control\" name=\"working_hours\" id=\"working_hours\"></textarea>
					</div>
					<div class=\"form-group\">
						<input type=\"hidden\" name=\"branches_id\" id=\"branches_id\" value=\"0\">
						<input type=\"submit\" id=\"submit\" class=\"btn btn-primary backlink\" value=\" Save \" />
						<input type=\"button\" name=\"reset\" id=\"reset\" onClick=\"resetForm_branches();\" value=\"Cancel\" class=\"hidediv btn btn-warning floatleft mleft10\" />
						<input type=\"button\" name=\"archive\" id=\"archive\" value=\"Archive\" class=\"hidediv btn btn-danger floatright\" />
					</div>
				</form>
				<link href=\"/assets/css/jqueryimageupload.css\" rel=\"stylesheet\" type=\"text/css\">
				<script type=\"text/javascript\" src=\"/assets/js/jquery.form.min.js\"></script>
			</div>
		</div>";
		
		$htmlStr = $this->htmlBody($innerHTMLStr);
		return $htmlStr;
	}
	
	public function AJsave_branches(){
	
		
		$users_id = $_SESSION["users_id"]??0;
		$returnStr = 'Ok';		
		$savemsg = '';
		$branches_id = $_POST['branches_id']??0;
		$name = trim(addslashes($_POST['name']??''));
		$address = trim(addslashes($_POST['address']??''));
		$google_map = trim(addslashes($_POST['google_map']??''));
		$working_hours = trim(addslashes($_POST['working_hours']??''));
		
		$conditionarray = array();
		$conditionarray['name'] = $name;
		$conditionarray['address'] = $address;
		$conditionarray['google_map'] = $google_map;
		$conditionarray['working_hours'] = $working_hours;
		$conditionarray['last_updated'] = date('Y-m-d H:i:s');
		$conditionarray['users_id'] = $users_id;
		
		$duplSql = "SELECT branches_publish, branches_id FROM branches WHERE name = :name";
		$bindData = array('name'=>$name);
		if($branches_id>0){
			$duplSql .= " AND branches_id != :branches_id";
			$bindData['branches_id'] = $branches_id;
		}
		$duplSql .= " LIMIT 0, 1";
		$duplRows = 0;
		$branchesObj = $this->db->getData($duplSql, $bindData);
		if($branchesObj){
			foreach($branchesObj as $onerow){
				$duplRows = 1;
				$branches_publish = $onerow['branches_publish'];
				if($branches_id==0 && $branches_publish==0){
					$branches_id = $onerow['branches_id'];
					$this->db->update('branches', array('branches_publish'=>1), $branches_id);
					$duplRows = 0;
					$returnStr = 'Update';
				}
			}
		}
		
		if($duplRows>0 || empty($name)){
			$savemsg = 'error';
			$returnStr = "<p>This name is already exist! Please try again with different name.</p>";
		}
		else{			
			if($branches_id==0){
				$conditionarray['created_on'] = date('Y-m-d H:i:s');
				$branches_id = $this->db->insert('branches', $conditionarray);
				if($branches_id){						
					$returnStr = 'Add';
				}
				else{
					$returnStr = 'Adding new branches';
				}
			}
			else{
				$update = $this->db->update('branches', $conditionarray, $branches_id);
				if($update){
					$activity_feed_title = 'branches was edited';
					$activity_feed_link = "/Settings/branches/view/$branches_id";
					
					$afData = array('created_on' => date('Y-m-d H:i:s'),
									'users_id' => $_SESSION["users_id"],
									'activity_feed_title' =>  $activity_feed_title,
									'activity_feed_name' => $name,
									'activity_feed_link' => $activity_feed_link,
									'uri_table_name' => "branches",
									'uri_table_field_name' =>"branches_publish",
									'field_value' => 1);
					$this->db->insert('activity_feed', $afData);
					
					$returnStr = 'Update';
				}
				elseif($returnStr == 'Ok'){
					$returnStr = 'No changes / Error occurred while updating data! Please try again.';
				}
			}
		}
		
		return json_encode(array('login'=>'', 'returnStr'=>$returnStr, 'savemsg'=>$savemsg));
	}
	
	public function aJgetPage_branches($segment3){
	
		$keyword_search = $_POST['keyword_search']??'';
		$totalRows = $_POST['totalRows']??0;
		$rowHeight = $_POST['rowHeight']??34;
		$page = $_POST['page']??1;
		if($page<=0){$page = 1;}
		$_SESSION["limit"] = $_POST['limit']??'auto';
		
		$this->keyword_search = $keyword_search;
		
		$jsonResponse = array();
		$jsonResponse['login'] = '';
		//===If filter options changes===//	
		if($segment3=='filter'){
			$this->filterAndOptions_branches();
			$jsonResponse['totalRows'] = $totalRows = $this->totalRows;
		}
		$this->page = $page;
		$this->totalRows = $totalRows;
		$this->rowHeight = $rowHeight;
		
		$jsonResponse['tableRows'] = $this->loadTableRows_branches();
		
		return json_encode($jsonResponse);
	}
	
	private function filterAndOptions_branches(){
		
		$keyword_search = $this->keyword_search;
		
		$_SESSION["current_module"] = "Settings";
		$_SESSION["list_filters"] = array('keyword_search'=>$keyword_search);
		
		$filterSql = "FROM branches WHERE branches_publish = 1";
		$bindData = array();
		if($keyword_search !=''){
			$keyword_search = addslashes(trim($keyword_search));
			if ( $keyword_search == "" ) { $keyword_search = " "; }
			$keyword_searches = explode (" ", $keyword_search);
			if ( strpos($keyword_search, " ") === false ) {$keyword_searches[0] = $keyword_search;}
			$num = 0;
			while ( $num < sizeof($keyword_searches) ) {
				$filterSql .= " AND CONCAT_WS(' ', name, address, working_hours) LIKE CONCAT('%', :keyword_search$num, '%')";
				$bindData['keyword_search'.$num] = trim($keyword_searches[$num]);
				$num++;
			}
		}
		
		$totalRows = 0;
		$strextra ="SELECT COUNT(branches_id) AS totalrows $filterSql";
		$query = $this->db->getObj($strextra, $bindData);
		if($query){
			$totalRows = $query->fetch(PDO::FETCH_OBJ)->totalrows;
		}
		$this->totalRows = $totalRows;		
	}
	
   private function loadTableRows_branches(){
		
		$limit = $_SESSION["limit"];
		$rowHeight = $this->rowHeight;
		$page = $this->page;
		$totalRows = $this->totalRows;
		$keyword_search = $this->keyword_search;
		
		if(in_array($limit, array('', 'auto'))){
			$screenHeight = $_COOKIE['screenHeight']??480;
			$headerHeight = $_COOKIE['headerHeight']??300;
			$bodyHeight = floor($screenHeight-$headerHeight);
			$limit = floor($bodyHeight/$rowHeight);
			if($limit<=0){$limit = 1;}
		}
		$starting_val = ($page-1)*$limit;
		if($starting_val>$totalRows){$starting_val = 0;}
		
		$filterSql = "FROM branches WHERE branches_publish = 1";
		$bindData = array();
		if($keyword_search !=''){
			$keyword_search = addslashes(trim($keyword_search));
			if ( $keyword_search == "" ) { $keyword_search = " "; }
			$keyword_searches = explode (" ", $keyword_search);
			if ( strpos($keyword_search, " ") === false ) {$keyword_searches[0] = $keyword_search;}
			$num = 0;
			while ( $num < sizeof($keyword_searches) ) {
				$filterSql .= " AND CONCAT_WS(' ', name, address, working_hours) LIKE CONCAT('%', :keyword_search$num, '%')";
				$bindData['keyword_search'.$num] = trim($keyword_searches[$num]);
				$num++;
			}
		}
		
		$sqlquery = "SELECT * $filterSql ORDER BY name ASC LIMIT $starting_val, $limit";
		$query = $this->db->getData($sqlquery, $bindData);
		$str = '';
		if($query){
			foreach($query as $onerow){

				$branches_id = $onerow['branches_id'];
				$name = stripslashes($onerow['name']);

				$BranchImgUrl = '';
                $filePath = "./assets/accounts/bran_$branches_id".'_';
                $pics = glob($filePath."*.jpg");
				if(!$pics){
					$pics = glob($filePath."*.png");
				}
                if($pics){
                    foreach($pics as $onePicture){
                        $prodImg = str_replace("./assets/accounts/", '', $onePicture);
                        $BranchImgUrl = str_replace('./', '/', $onePicture);
                    }
                }
                if(!empty($BranchImgUrl)){
                    $BranchImg = "<div class=\"currentPicture\"><img alt=\"".addslashes($name)."\" class=\"img-responsive maxheight250\" src=\"$BranchImgUrl\"></div>";
                }
                else{
                    $BranchImg = "<button class=\"btn btn-default\" onClick=\"upload_dialog('Upload Branch Picture', 'branches', 'bran_$branches_id"."_');\">Upload<button>";
                }
				$str .= "<tr class=\"cursor\">
							<td data-title=\"ID\" onClick=\"getOneRowInfo('branches', $branches_id);\" align=\"right\">$branches_id</td>
							<td data-title=\"Name\" onClick=\"getOneRowInfo('branches', $branches_id);\" align=\"left\">$name</td>
							<td data-title=\"Branch Picture\" align=\"left\" id=\"Branch$branches_id\" class=\"positionrelative\">$BranchImg</td>
						</tr>";
			}
		}
		else{
			$str .= "<tr><td class=\"errormsg\" colspan=\"3\">There is no branches meet this criteria</td></tr>";
		}
		return $str;
   }

	//===============User Module==============//
	public function users($segment3){
		$this->pageTitle = $GLOBALS['title'];
		$list_filters = $_SESSION['list_filters']??array();
		
		$keyword_search = $list_filters['keyword_search']??'';
		$this->keyword_search = $keyword_search;
		
		$this->filterAndOptions_users();
		
		$page = !empty($segment3) ? intval($segment3):1;
		if($page<=0){$page = 1;}
		if(!isset($_SESSION['limit'])){$_SESSION['limit'] = 'auto';}
		$limit = $_SESSION['limit'];
		
		$this->rowHeight = 34;
		$this->page = $page;
		$tableRows = $this->loadTableRows_users();
		
		$limOpt = '';
		$limOpts = array(15, 20, 25, 50, 100, 500);
		foreach($limOpts as $oneOpt){
			$selected = '';
			if($limit==$oneOpt){$selected = ' selected';}
			$limOpt .= "<option$selected value=\"$oneOpt\">$oneOpt</option>";
		}
		
		$innerHTMLStr = "<input type=\"hidden\" name=\"pageURI\" id=\"pageURI\" value=\"$GLOBALS[segment1]/$GLOBALS[segment2]\">
		<input type=\"hidden\" name=\"page\" id=\"page\" value=\"$this->page\">
		<input type=\"hidden\" name=\"rowHeight\" id=\"rowHeight\" value=\"$this->rowHeight\">
		<input type=\"hidden\" name=\"totalTableRows\" id=\"totalTableRows\" value=\"$this->totalRows\">
		<div class=\"row\">
			<div class=\"col-sm-12 col-sm-12 col-md-6\">
				<div class=\"row\">
					<div class=\"col-sm-12 col-md-6\">
						<h1 class=\"metatitle\">Users List <i class=\"fa fa-info-circle txt16normal\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Users List\"></i></h1>
					</div>				
					<div class=\"col-sm-12 col-md-6\">
						<div class=\"input-group\">
							<input type=\"text\" placeholder=\"Search Users\" value=\"$keyword_search\" id=\"keyword_search\" name=\"keyword_search\" class=\"form-control\" maxlength=\"50\" />
							<span class=\"input-group-addon cursor\" onClick=\"checkAndLoadFilterData();\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Search Users\">
								<i class=\"fa fa-search\"></i>
							</span>
						</div>
					</div>
				</div>				
				<div class=\"row\">
					<div class=\"col-sm-12\" style=\"position:relative;\">
						<div id=\"no-more-tables\">
							<table class=\"col-sm-12 table-bordered table-striped table-condensed cf listing\">
								<thead class=\"cf\">
									<tr>
										<th class=\"left\">Users Name</th>
										<th class=\"left\">Email</th>
										<th class=\"left\">Modules Permission Name</th>
									</tr>
								</thead>
								<tbody id=\"tableRows\">
									$tableRows
								</tbody>
							</table>
						</div>
					</div>    
				</div>
				<div class=\"row mtop10\">
					<div class=\"col-sm-12\">
						<select class=\"form-control width100 floatleft\" name=\"limit\" id=\"limit\" onChange=\"checkloadTableRows();\">
							<option value=\"auto\">Auto</option>
							$limOpt
						</select>
						<label id=\"fromtodata\"></label>
						<div class=\"floatright\" id=\"Pagination\"></div>
					</div>
				</div>
			</div>
			<div class=\"col-sm-12 col-sm-12 col-md-6\">            
				<h4 class=\"borderbottom\" id=\"formtitle\">Add New User</h4>
				<form action=\"#\" name=\"frmusers\" id=\"frmusers\" onsubmit=\"return AJsave_users();\" enctype=\"multipart/form-data\" method=\"post\" accept-charset=\"utf-8\">
					<div class=\"form-group\">
						<div class=\"col-sm-3 pleft0 txtright\">
							<label for=\"users_roll[0]\">User Roles<span class=\"required\">*</span> :</label>
						</div>
						<div class=\"col-sm-9\">
							<table cellspacing=\"1\" cellpadding=\"0\" border=\"0\" align=\"left\">
								<tr>
									<td valign=\"top\" width=\"200\" class=\"black12normal\" align=\"left\">
										<label style=\"font-weight:normal\">
											<input type=\"checkbox\" class=\"full_access\" name=\"users_roll[]\" onClick=\"checkUserRolls();\" value=\"Full-Access\"/> Full Access
										</label>
									</td>
								</tr>
								<tr>";
		$Template = new Template($this->db);
		$modules = $Template->modules();
		if($modules){
			$d = 0;							 
			foreach($modules as $label=>$value){
				if($d>0 && $d%2==0){
					$innerHTMLStr .= "</tr><tr>";
				}
				$d++;
				$innerHTMLStr .= "<td valign=\"top\" width=\"200\" class=\"black12normal\" align=\"left\"><label style=\"font-weight:normal\"><input type=\"checkbox\" class=\"users_roll\" name=\"users_roll[]\" onClick=\"checkUserRolls();\" value=\"$value\"/> $label</label></td>";
			}	
		}
		else{
			$innerHTMLStr .= "<td class=\"black12normal\">No users meet the criteria given</td>";
		}
		
		$innerHTMLStr .= "</tr>
							</table>
							<span class=\"error_msg\" id=\"errmsg_users_roll\"></span>
						</div>
					</div>
					<div class=\"form-group\">
						<div class=\"col-sm-3 pleft0 txtright\">
							<label for=\"branches_id\">Branch Name<span class=\"required\">*</span> :</label>
						</div>
						<div class=\"col-sm-9\">
							<select class=\"form-control\" name=\"branches_id\" id=\"branches_id\">
								<option value=\"0\">Select Branch Name</option>";
								$branchesObj = $this->db->getObj("SELECT branches_id, name FROM branches WHERE branches_publish = 1", array());
								if($branchesObj){
									while($branchesRow = $branchesObj->fetch(PDO::FETCH_OBJ)){
										$oneOpt = stripslashes(trim($branchesRow->name));
										$innerHTMLStr .= "<option value=\"$branchesRow->branches_id\">$oneOpt</option>";
									}
								}
							$innerHTMLStr .= "</select>
						</div>
					</div>
					<div class=\"form-group\">
						<div class=\"col-sm-3 pleft0 txtright\">
							<label for=\"users_first_name\">First Name<span class=\"required\">*</span> :</label>
						</div>
						<div class=\"col-sm-9\">
							<input type=\"text\" name=\"users_first_name\" id=\"users_first_name\" value=\"\" class=\"form-control\" maxlength=\"12\" size=\"12\" required>
						</div>
					</div>
					<div class=\"form-group\">
						<div class=\"col-sm-3 pleft0 txtright\">
							<label for=\"users_last_name\">Last Name</label>
						</div>
						<div class=\"col-sm-9\">
							<input type=\"text\" name=\"users_last_name\" id=\"users_last_name\" value=\"\" class=\"form-control\" maxlength=\"17\" size=\"17\">
						</div>
					</div>
					<div class=\"form-group\">
						<div class=\"col-sm-12\">
							<p>Users email login</p>
						</div>
						<div class=\"col-sm-3 txtright\">
							<label for=\"users_email\">Email<span class=\"required\">*</span> :</label>
						</div>
						<div class=\"col-sm-9\">
							<input maxlength=\"50\" type=\"email\" required name=\"users_email\" id=\"users_email\" class=\"form-control\" value=\" \" autocomplete=\"off\" onFocus=\"if(this.value==' '){this.value=''}\" onBlur=\"if(this.value==''){this.value=' '}\">
						</div>
					</div>
					<div class=\"form-group\">
						<div class=\"col-sm-3 txtright\">&nbsp;</div>
						<div class=\"col-sm-9\">
							<input type=\"hidden\" name=\"is_admin\" id=\"is_admin\" value=\"0\">
							<input type=\"hidden\" name=\"users_id\" id=\"users_id\" value=\"0\">
							<input type=\"submit\" id=\"submit\" class=\"btn btn-primary backlink\" value=\" Save \" />
							<input type=\"button\" name=\"reset\" id=\"reset\" onClick=\"resetForm_users();\" value=\"Cancel\" class=\"hidediv btn btn-warning floatleft mleft10\" />
							<input type=\"button\" name=\"archive\" id=\"archive\" value=\"Archive\" class=\"hidediv btn btn-danger floatright\" />
						</div>
					</div>
				</form>
			</div>
		</div>";
		
		$htmlStr = $this->htmlBody($innerHTMLStr);
		return $htmlStr;
	}
	
	public function aJgetPage_users($segment3){
	
		$keyword_search = $_POST['keyword_search']??'';
		$totalRows = $_POST['totalRows']??0;
		$rowHeight = $_POST['rowHeight']??34;
		$page = $_POST['page']??1;
		if($page<=0){$page = 1;}
		$_SESSION["limit"] = $_POST['limit']??'auto';
		
		$this->keyword_search = $keyword_search;
		
		$jsonResponse = array();
		$jsonResponse['login'] = '';
		//===If filter options changes===//	
		if($segment3=='filter'){
			$this->filterAndOptions_users();
			$jsonResponse['totalRows'] = $totalRows = $this->totalRows;
		}
		$this->page = $page;
		$this->totalRows = $totalRows;
		$this->rowHeight = $rowHeight;
		
		$jsonResponse['tableRows'] = $this->loadTableRows_users();
		
		return json_encode($jsonResponse);
	}
	
	private function filterAndOptions_users(){
		$keyword_search = $this->keyword_search;
		
		$_SESSION["current_module"] = "Settings";
		$_SESSION["list_filters"] = array('keyword_search'=>$keyword_search);
		
		$filterSql = "FROM users WHERE users_publish = 1 AND users_email != ''";
		$bindData = array();
		if($keyword_search !=''){
			$keyword_search = addslashes(trim($keyword_search));
			if ( $keyword_search == "" ) { $keyword_search = " "; }
			$keyword_searches = explode (" ", $keyword_search);
			if ( strpos($keyword_search, " ") === false ) {$keyword_searches[0] = $keyword_search;}
			$num = 0;
			while ( $num < sizeof($keyword_searches) ) {
				$filterSql .= " AND CONCAT_WS(' ', users_first_name, users_last_name, users_email, users_roll) LIKE CONCAT('%', :keyword_search$num, '%')";
				$bindData['keyword_search'.$num] = trim($keyword_searches[$num]);
				$num++;
			}
		}
		
		$totalRows = 0;
		$strextra ="SELECT COUNT(users_id) AS totalrows $filterSql";
		$query = $this->db->getObj($strextra, $bindData);
		if($query){
			$totalRows = $query->fetch(PDO::FETCH_OBJ)->totalrows;
		}
		$this->totalRows = $totalRows;		
	}
	
   private function loadTableRows_users(){
		$limit = $_SESSION["limit"];
		$rowHeight = $this->rowHeight;
		$page = $this->page;
		$totalRows = $this->totalRows;
		$keyword_search = $this->keyword_search;
		
		if(in_array($limit, array('', 'auto'))){
			$screenHeight = $_COOKIE['screenHeight']??480;
			$headerHeight = $_COOKIE['headerHeight']??300;
			$bodyHeight = floor($screenHeight-$headerHeight);
			$limit = floor($bodyHeight/$rowHeight);
			if($limit<=0){$limit = 1;}
		}
		$starting_val = ($page-1)*$limit;
		if($starting_val>$totalRows){$starting_val = 0;}
		
		$filterSql = "FROM users WHERE users_publish = 1 AND users_email != ''";
		$bindData = array();
		if($keyword_search !=''){
			$keyword_search = addslashes(trim($keyword_search));
			if ( $keyword_search == "" ) { $keyword_search = " "; }
			$keyword_searches = explode (" ", $keyword_search);
			if ( strpos($keyword_search, " ") === false ) {$keyword_searches[0] = $keyword_search;}
			$num = 0;
			while ( $num < sizeof($keyword_searches) ) {
				$filterSql .= " AND CONCAT_WS(' ', users_first_name, users_last_name, users_email, users_roll) LIKE CONCAT('%', :keyword_search$num, '%')";
				$bindData['keyword_search'.$num] = trim($keyword_searches[$num]);
				$num++;
			}
		}
		
		$sqlquery = "SELECT * $filterSql ORDER BY users_first_name ASC LIMIT $starting_val, $limit";
		$query = $this->db->getData($sqlquery, $bindData);
		$str = '';
		if($query){
			foreach($query as $onerow){

				$users_id = $onerow['users_id'];
				$name = stripslashes(trim("$onerow[users_first_name] $onerow[users_last_name]"));
				$users_email = stripslashes($onerow['users_email']);
				if($onerow['is_admin']>0){
					$name .= " (Admin)";
				}
				$modulesName = 'Full Access';
				if(strlen($onerow['users_roll'])>5){
					$modulesName = str_replace('_', ' ', implode(', ', array_keys((array) json_decode($onerow['users_roll']))));
				}
				$str .= "<tr class=\"cursor\" onClick=\"getOneRowInfo('users', $users_id);\">
		<td data-title=\"Users Name\" align=\"left\">$name</td>
		<td data-title=\"Email\" align=\"left\">$users_email</td>
		<td data-title=\"Projects Name\" align=\"left\">$modulesName</td>
		</tr>";
			}
		}
		else{
			$str .= "<tr><td colspan=\"3\" class=\"errormsg\">No users meet the criteria given</td></tr>";
		}
		return $str;
   }
	
   public function AJsave_users(){
	
		$returnStr = 'Ok';		
		$savemsg = '';
		
		$serverexp = explode('.', $_SERVER['SERVER_NAME']); //creates the various parts
		$domainname = implode('.', array_slice($serverexp, -2, 2));

		$users_id = $_POST['users_id']??0;
		$users_rollarray = $_POST['users_roll']??array();
		$Common = new Common($this->db);
		
		$usersRolls = array();
		if(!empty($users_rollarray)){
			foreach($users_rollarray as $oneModule){
				if(strcmp('Full-Access', $oneModule) !=0){
					$usersRolls[$oneModule] = array();					
				}
			}
		}
		$users_roll = json_encode($usersRolls);	
		
		$conditionarray = array();
		$conditionarray['branches_id'] = $branches_id = addslashes(trim($_POST['branches_id']??0));
		$conditionarray['users_first_name'] = $users_first_name = addslashes(trim($_POST['users_first_name']??''));
		$conditionarray['users_last_name'] = $users_last_name = addslashes(trim($_POST['users_last_name']??''));
		$conditionarray['users_roll'] = $users_roll;
		$conditionarray['users_email'] = $users_email = addslashes(trim($_POST['users_email']??''));
		$conditionarray['last_updated'] = date('Y-m-d H:i:s');
	
		if($users_id==0){
			$totalrows = 0;
			$queryObj = $this->db->getObj("SELECT users_id, users_publish FROM users WHERE users_email = :users_email", array('users_email'=>$users_email));
			if($queryObj){
				$totalrows = $queryObj->rowCount();
			}
			if($totalrows>0){
				$returnStr = 'Update';
				$conditionarray['users_publish'] = 1;
				$conditionarray['created_on'] = date('Y-m-d H:i:s');
				$users_password = $Common->randomPassword();
				$users_password = trim($users_password);
				$password_hash = password_hash($users_password, PASSWORD_DEFAULT);
				
				$conditionarray['password_hash'] = $password_hash;

				$oneusersrow = $queryObj->fetch(PDO::FETCH_OBJ);
				$users_id = $oneusersrow->users_id;
				$users_publish = $oneusersrow->users_publish;

				if($users_publish>0){
					$returnStr = 'This email address already exists.';
					$savemsg = 'error';
				}
				else{
					$update = $this->db->update('users', $conditionarray, $users_id);
					if($update){
						$activity_feed_title = 'User was edited';
						$activity_feed_link = "/Settings/users_setup/view/$users_id";
						
						$afData = array('created_on' => date('Y-m-d H:i:s'),
										'users_id' => $_SESSION["users_id"],
										'activity_feed_title' => $activity_feed_title,
										'activity_feed_name' => "$users_first_name $users_last_name",
										'activity_feed_link' => $activity_feed_link,
										'uri_table_name' => "users",
										'uri_table_field_name' =>"users_id",
										'field_value' => $users_id);
						$this->db->insert('activity_feed', $afData);
						
					}
					
					$headers = array();
					$headers[] = 'MIME-Version: 1.0';
					$headers[] = 'Content-type: text/html; charset=iso-8859-1';
					$headers[] = 'To: '.$users_first_name.' <'.$users_email.'>';
					$headers[] = 'From: '.COMPANYNAME.' <info@leadtnd.leadtnd.com>';
					
					$subject = "Your access to ".$_SESSION["company_name"];
					
					$Body = "<html><head><title>$subject</title></head>
		<body>
		<p>
		Hi $users_first_name,<br />
		<br />
		<br />
		Welcome to ".$_SESSION["company_name"]."'s user, you have just been granted access.
		<br />
		<br />
		BD IT Software is used to manage ERP software activities like Projects, Inventory and Staff Management, and more.<br />
		<br />
		<br />
		Login Details Below::<br />
		Login Address: $GLOBALS[subdomain].$domainname<br />
		Username: $users_email<br />
		Password: $users_password<br />
		<br />
		<br />
		<a href=\"http://$GLOBALS[subdomain].$domainname/Login/index\" target=\"_blank\" title=\"Login\">Login</a> and check things out. If you have any questions feel free to Contact Us</a><br />
		<br />
		<br />
		Thanks again,<br />
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
							$returnStr = 'Please check your email for login information.';
						}
					}
				}
			}
			else{

				$conditionarray['is_admin'] = 0;

				$users_password = $Common->randomPassword();
				$password_hash = password_hash($users_password, PASSWORD_DEFAULT);

				$conditionarray['password_hash'] = $password_hash;
				$conditionarray['changepass_link'] = '';
				$conditionarray['employee_number'] = '';
				$conditionarray['pin'] = '';
				$conditionarray['created_on'] = date('Y-m-d H:i:s');
				$conditionarray['lastlogin'] = '1000-01-01 00:00:00';
				$conditionarray['popup_message'] = '';
				$conditionarray['login_message'] = '';
				$conditionarray['login_ck_id'] = '';
				$users_id = $this->db->insert('users', $conditionarray);
				if($users_id){
					
					$headers = array();
					$headers[] = 'MIME-Version: 1.0';
					$headers[] = 'Content-type: text/html; charset=iso-8859-1';
					$headers[] = 'To: '.$users_first_name.' <'.$users_email.'>';
					$headers[] = 'From: '.COMPANYNAME.' <info@leadtnd.leadtnd.com>';
					
					$subject = "Your access to ".$_SESSION["company_name"];
					
					$Body = "<html><head><title>$subject</title></head>
		<body>
		<p>
		Hi $users_first_name,<br />
		<br />
		<br />
		Welcome to ".$_SESSION["company_name"]."'s user, you have just been granted access.
		<br />
		<br />
		BD IT Software is used to manage ERP software activities like Projects, Inventory and Staff Management, and more.<br />
		<br />
		<br />
		Login Details Below::<br />
		Login Address: $GLOBALS[subdomain].$domainname<br />
		Username: $users_email<br />
		Password: $users_password<br />
		<br />
		<br />
		<a href=\"http://$GLOBALS[subdomain].$domainname/Login/index\" target=\"_blank\" title=\"Login\">Login</a> and check things out. If you have any questions feel free to Contact Us</a><br />
		<br />
		<br />
		Thanks again,<br />
		BD IT SOFT TEAM
		</p>
		</body>
		</html>";			
					$returnStr = 'Add';
					if($users_email =='' || is_null($users_email)){
						$returnStr = 'Your email is blank. Please contact with site admin.';
					}
					else{
						if (!mail($users_email, $subject, $Body, implode("\r\n", $headers))){
							$returnStr = 'Mail could not sent.';
						}
						else{
							$returnStr = 'Please check your email for login information.';
						}
					}
				}
				else{
					$returnStr = 'Error occured while adding new user! Please try again.';
				}
			}
		}
		else{
			$totalrows = 0;
			$queryObj = $this->db->getObj("SELECT COUNT(users_id) AS totalrows FROM users WHERE users_email = :users_email AND users_id != :users_id", array('users_email'=>$users_email, 'users_id'=>$users_id));
			if($queryObj){
				$totalrows = $queryObj->fetch(PDO::FETCH_OBJ)->totalrows;
			}
			if($totalrows>0){
				$returnStr = 'This user is already exist! Please try again with different user.';
				$savemsg = 'error';
			}
			else{

				$update = $this->db->update('users', $conditionarray, $users_id);
				if($update){
					$activity_feed_title = 'User was edited';
					$activity_feed_link = "/Settings/users_setup/view/$users_id";
					
					 $afData = array('created_on' => date('Y-m-d H:i:s'),
									'users_id' => $_SESSION["users_id"],
									'activity_feed_title' => $activity_feed_title,
									'activity_feed_name' => "$users_first_name $users_last_name",
									'activity_feed_link' => $activity_feed_link,
									'uri_table_name' => "users",
									'uri_table_field_name' =>"users_id",
									'field_value' => $users_id);
					$this->db->insert('activity_feed', $afData);
					
				}

				$returnStr = 'Update';
			}
		}
		
		return json_encode(array('login'=>'', 'returnStr'=>$returnStr, 'savemsg'=>$savemsg));
   }
	
	public function htmlBody($pageMiddle){
		
		$returnHTML = "<div class=\"row\">
			<div class=\"col-sm-12\">
				<h1 class=\"singin2\">$this->pageTitle <i class=\"fa fa-info-circle txt16normal\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"This page captures the company setup.\"></i></h1>
			</div>   
		</div>
		<div class=\"row\">
			<div class=\"col-md-2 col-sm-3 pright0\">
				<div style=\"margin-top:0;\" class=\"bs-callout well bs-callout-info\">					
					<a href=\"javascript:void(0);\" id=\"settingsleftsidemenu\">
						<i class=\"fa fa-align-justify fa-2\"></i>                                        
					</a>
					<ul class=\"leftsidemenu settingslefthide\">";
						$menuLinks = array();
						$menuLinks['Settings'] = array(stripslashes('Accounts Setup'), array('myInfo'=>stripslashes('My Information'), 'branches'=>stripslashes('Setup Branches'), 'users'=>stripslashes('Setup Users')));
						
						foreach($menuLinks as $clasName=>$moduleInfo){
							$classNameLabel = $moduleInfo[0];
							$moduleList = $moduleInfo[1];
							$returnHTML .= "<li><h4><span>$classNameLabel</span></h4></li>";
							foreach($moduleList as $module=>$moduletitle){
								$linkstr = "<a class=\"mleft10\" href=\"/$clasName/$module\" title=\"$moduletitle\"><span>$moduletitle</span></a>";
								$activeclass = '';
								if(strcmp($GLOBALS['segment1'], $clasName)==0 && strcmp($GLOBALS['segment2'], $module)==0){
									$activeclass = ' class="activeclass"';
								}
								$returnHTML .= "<li$activeclass>$linkstr</li>";
							}
						}
												
					$returnHTML .= "</ul>
				</div>
			</div>			
			<div class=\"col-md-10 col-sm-9\">
				<div class=\"bs-callout well bs-callout-info\" style=\"margin-top:0; border-left:1px solid #EEEEEE; background:#FFF;\">
					$pageMiddle
				</div>
			</div>
		</div>";
		
		return $returnHTML;
	}
	
}
?>