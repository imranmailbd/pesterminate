<?php
class Customers{
	protected $db;
	private $page, $rowHeight, $totalRows, $customers_id, $sorting_type, $keyword_search, $history_type, $actFeeTitOpt;
	
	public function __construct($db){$this->db = $db;}
	
	public function lists($segment3){
		$list_filters = $_SESSION['list_filters']??array();
		
		$sorting_type = $list_filters['sorting_type']??'name ASC, phone ASC';
		$this->sorting_type = $sorting_type;
		
		$keyword_search = $list_filters['keyword_search']??'';
		$this->keyword_search = $keyword_search;
		
		$this->filterAndOptions();
		
		$page = !empty($segment3) ? intval($segment3):1;
		if($page<=0){$page = 1;}
		if(!isset($_SESSION['limit'])){$_SESSION['limit'] = 'auto';}
		$limit = $_SESSION['limit'];
		
		$this->rowHeight = 34;
		$this->page = $page;
		$tableRows = $this->loadTableRows();
		
		$sorTypOpt = $limOpt = '';
		$sorTypOpts = array('name ASC, phone ASC'=>"Name and Phone", 'name ASC'=>'Name');
		foreach($sorTypOpts as $optValue=>$optLabel){
			$selected = '';
			if(strcmp($sorting_type, $optValue)==0){$selected = ' selected';}
			$sorTypOpt .= "<option$selected value=\"$optValue\">$optLabel</option>";
		}
		$limOpts = array(15, 20, 25, 50, 100, 500);
		foreach($limOpts as $oneOpt){
			$selected = '';
			if($limit==$oneOpt){$selected = ' selected';}
			$limOpt .= "<option$selected value=\"$oneOpt\">$oneOpt</option>";
		}
		
		$htmlStr = "<input type=\"hidden\" name=\"pageURI\" id=\"pageURI\" value=\"$GLOBALS[segment1]/$GLOBALS[segment2]\">
		<input type=\"hidden\" name=\"page\" id=\"page\" value=\"$this->page\">
		<input type=\"hidden\" name=\"rowHeight\" id=\"rowHeight\" value=\"$this->rowHeight\">
		<input type=\"hidden\" name=\"totalTableRows\" id=\"totalTableRows\" value=\"$this->totalRows\">
		<div class=\"row\">
			<div class=\"col-sm-12 col-md-6\">
				<h1 class=\"metatitle\">$GLOBALS[title] <i class=\"fa fa-info-circle txt16normal\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"This page displays the list of your customers\"></i></h1>
			</div>	
			<div class=\"col-sm-12 col-md-6 ptopbottom15\">
				<a href=\"javascript:void(0);\" title=\"Create Customer\" onClick=\"AJget_CustomersPopup(0);\">
					<button class=\"btn btn-default hilightbutton floatright\"><i class=\"fa fa-plus\"></i> <strong>Create Customer</strong></button>
				</a>
			</div>  
		</div>
		<div class=\"row\">
			<div class=\"col-sm-12 col-sm-4 col-md-6 pbottom10\">&nbsp;</div>
			<div class=\"col-xs-6 col-sm-4 col-md-3 pbottom10\">
				<select class=\"form-control\" name=\"sorting_type\" id=\"sorting_type\" onchange=\"checkAndLoadFilterData();\">
					$sorTypOpt
				</select>
			</div>
			<div class=\"col-sm-12 col-sm-4 col-md-3 pbottom10\">
				<div class=\"input-group\">
					<input type=\"text\" placeholder=\"Search Customers\" value=\"$keyword_search\" id=\"keyword_search\" name=\"keyword_search\" class=\"form-control\" maxlength=\"50\" />
					<span class=\"input-group-addon cursor\" onClick=\"checkAndLoadFilterData();\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Search Customers\">
						<i class=\"fa fa-search\"></i>
					</span>
				</div>
			</div>
		</div>	
		<div class=\"row\">
			<div class=\"col-sm-12\" style=\"position:relative;\">
				<div id=\"no-more-tables\">
					<table class=\"col-md-12 table-bordered table-striped table-condensed cf listing\">
						<thead class=\"cf\">
							<tr>
								<th align=\"left\">Name</th>
								<th align=\"left\" width=\"25%\">Email</th>
								<th align=\"left\" width=\"25%\">Contact No</th>
								<th align=\"left\" width=\"25%\">Address</th>
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
		</div>";
		
		return $htmlStr;
	}
   
   private function filterAndOptions(){
		$sorting_type = $this->sorting_type;
		$keyword_search = $this->keyword_search;
		
		$_SESSION["current_module"] = "Customers";
		$_SESSION["list_filters"] = array('sorting_type'=>$sorting_type, 'keyword_search'=>$keyword_search);
		
		$filterSql = "FROM customers WHERE customers_publish = 1";
		$bindData = array();
		
		if($keyword_search !=''){
			$keyword_search = addslashes(trim($keyword_search));
			if ( $keyword_search == "" ) { $keyword_search = " "; }
			$keyword_searches = explode (" ", $keyword_search);
			if ( strpos($keyword_search, " ") === false ) {$keyword_searches[0] = $keyword_search;}
			$num = 0;
			while ( $num < sizeof($keyword_searches) ) {
				$filterSql .= " AND CONCAT_WS(' ', name, phone, email) LIKE CONCAT('%', :keyword_search$num, '%')";
				$bindData['keyword_search'.$num] = trim($keyword_searches[$num]);
				$num++;
			}
		}
		$totalRows = 0;
		
		$strextra ="SELECT COUNT(customers_id) AS totalRows $filterSql";
		$query = $this->db->getObj($strextra, $bindData);
		if($query){
			$totalRows = $query->fetch(PDO::FETCH_OBJ)->totalRows;
		}
		$this->totalRows = $totalRows;
	}
	
   private function loadTableRows(){
		$limit = $_SESSION["limit"];
		$rowHeight = $this->rowHeight;
		$page = $this->page;
		$totalRows = $this->totalRows;
		$sorting_type = $this->sorting_type;			
		if($sorting_type==''){$sorting_type = 'name ASC, phone ASC';}
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
		
		$filterSql = "FROM customers WHERE customers_publish = 1";
		$bindData = array();

		if($keyword_search !=''){
			$keyword_search = addslashes(trim($keyword_search));
			if ( $keyword_search == "" ) { $keyword_search = " "; }
			$keyword_searches = explode (" ", $keyword_search);
			if ( strpos($keyword_search, " ") === false ) {$keyword_searches[0] = $keyword_search;}
			$num = 0;
			while ( $num < sizeof($keyword_searches) ) {
				$filterSql .= " AND CONCAT_WS(' ', name, phone, email) LIKE CONCAT('%', :keyword_search$num, '%')";
				$bindData['keyword_search'.$num] = trim($keyword_searches[$num]);
				$num++;
			}
		}
		
		$sqlquery = "SELECT customers_id, created_on, name, email, phone, address $filterSql ORDER BY $sorting_type LIMIT $starting_val, $limit";
		$query = $this->db->getData($sqlquery, $bindData);
		$i = $starting_val+1;

		$str = '';		
		if($query){
			foreach($query as $oneRow){
				$customers_id = $oneRow['customers_id'];
				
				$name = stripslashes($oneRow['name']);	
				$phone = $oneRow['phone'];
				if($phone==''){$phone = '&nbsp;';}			
				$email = $oneRow['email'];
				if($email==''){$email = '&nbsp;';}
				$address = $oneRow['address'];
				if($address==''){$address = '&nbsp;';}
				
				$str .= "<tr>";
				$str .= "<td data-title=\"Name\" align=\"justify\"><a class=\"anchorfulllink\" href=\"/Customers/view/$customers_id\" title=\"View Information\">$name</a></td>";
				$str .= "<td data-title=\"Phone No.\" align=\"justify\"><a class=\"anchorfulllink\" href=\"/Customers/view/$customers_id\" title=\"View Information\">$phone</a></td>";
				$str .= "<td data-title=\"Email\" align=\"justify\"><a class=\"anchorfulllink\" href=\"/Customers/view/$customers_id\" title=\"View Information\">$email</a></td>";
				$str .= "<td data-title=\"Address\" align=\"justify\"><a class=\"anchorfulllink\" href=\"/Customers/view/$customers_id\" title=\"View Information\">$address</a></td>";
				$str .= "</tr>";
			}
		}
		else{
			$str .= "<tr><td colspan=\"4\" class=\"red18bold\">No customers meet the criteria given</td></tr>";
		}
		return $str;
   }
	
	public function aJgetPage(){
	
		$segment3 = $GLOBALS['segment3'];
		$sorting_type = $_POST['sorting_type']??'name ASC, phone ASC';
		$keyword_search = $_POST['keyword_search']??'';
		$totalRows = $_POST['totalRows']??0;
		$rowHeight = $_POST['rowHeight']??34;
		$page = $_POST['page']??1;
		if($page<=0){$page = 1;}
		$_SESSION["limit"] = $_POST['limit']??'auto';
		
		$this->sorting_type = $sorting_type;
		$this->keyword_search = $keyword_search;
		
		$jsonResponse = array();
		$jsonResponse['login'] = '';
		//===If filter options changes===//	
		if($segment3=='filter'){
			$this->filterAndOptions();
			$jsonResponse['totalRows'] = $totalRows = $this->totalRows;
		}
		$this->page = $page;
		$this->totalRows = $totalRows;
		$this->rowHeight = $rowHeight;
		
		$jsonResponse['tableRows'] = $this->loadTableRows();
		
		return json_encode($jsonResponse);
	}
	
	public function view($segment3, $segment4){
		$htmlStr = "";
		$customersObj = $this->db->getObj("SELECT * FROM customers WHERE customers_id = :customers_id", array('customers_id'=>$segment3),1);
		if($customersObj){
			$customersarray = $customersObj->fetch(PDO::FETCH_OBJ);
			$list_filters = $_SESSION["list_filters"]??array();
			$shistory_type = $list_filters['shistory_type']??'';
		
			$customers_id = $customersarray->customers_id;
			$name = $customersarray->name;
			$phone = $customersarray->phone;
			$email = $customersarray->email;
			$address = $customersarray->address;

			$customers_publish = $customersarray->customers_publish;
			$htmlStr = "<div class=\"row\">
				<div class=\"col-sm-8\">
					<h1 class=\"metatitle\">$GLOBALS[title] <i class=\"fa fa-info-circle txt16normal\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"This page displays the information of customer\"></i></h1>
				</div>
				<div class=\"col-sm-4 ptopbottom15\">
					<a href=\"/Customers/lists\" title=\"Customers List\">
						<button class=\"btn btn-default hilightbutton floatright\"><i class=\"fa fa-list\"></i> <strong>Customers List</strong></button>
					</a>
				</div>    
			</div>";
			
			$htmlStr .= "<div class=\"row supplierheader margin0 mbot15 ptopbottom15\">
						<div class=\"col-sm-3\">
							<div class=\"image\">
								<img class=\"img-responsive\" alt=\"My Profile\" src=\"/assets/images/man.jpg\">
							</div>
						</div>
						<div class=\"col-sm-9\">
							<div class=\"image_content txtleft\">
								<h2>$name</h2>
								<p class=\"prelative pleft25\">
									<i class=\"fa fa-envelope-o txt16normal pabsolute0x0\"></i>
									<span>
										$email";
							if($email !=''){
								$htmlStr .= " <a href=\"javascript:void(0);\" title=\"Send Email\" onClick=\"sentSMS('$name', '$email', 'Email');\"><i class=\"txt20bold fa fa-envelope\"></i></a>";
							}
							$htmlStr .= "</span>
								</p>
								<p class=\"prelative pleft25\">
									<i class=\"fa fa-phone txt16normal pabsolute0x0\"></i> 
									<span>$phone</span>
								</p>
								<p class=\"prelative pleft25\">
									<i class=\"fa fa-map-marker txt16normal pabsolute0x0\"></i> 
									<span>$address</span>
								</p>
								<p>";						
								if($customers_publish>0){
									$htmlStr .= "<input type=\"button\" class=\"btn edit mbottom10 marginright15\" title=\"Edit\" onclick=\"AJget_CustomersPopup($customers_id);\" value=\"Edit\">
									<input type=\"button\" class=\"btn btn-default mbottom10 marginright15\" title=\"Merge Customers\" onclick=\"AJmergeCustomersPopup($customers_id);\" value=\"Merge Customers\">";
								}
								$htmlStr .= "</p>
							</div>
						</div>
			</div>";
			
			$htmlStr .= "<div class=\"row\">
				<div class=\"col-sm-12\">
					<div class=\"widget mbottom10\">";
					
			$list_filters = $_SESSION["list_filters"]??array();
			$shistory_type = $list_filters['shistory_type']??'';
			
			$this->customers_id = $customers_id;
			$this->history_type = $shistory_type;
			$this->filterHAndOptions();
			$actFeeTitOpt = $this->actFeeTitOpt;
			
			$page = !empty($GLOBALS['segment4']) ? intval($GLOBALS['segment4']):1;
			if($page<=0){$page = 1;}
			if(!isset($_SESSION["limit"])){$_SESSION["limit"] = 'auto';}
			$limit = $_SESSION["limit"];
			
			$this->page = $page;
			$this->rowHeight = 34;
			
			$tableRows = $this->loadHTableRows();
			
			$limitOpt = '';
			$limitOpts = array(15, 20, 25, 50, 100, 500);
			foreach($limitOpts as $oneOpt){
				$selected = '';
				if($limit==$oneOpt){$selected = ' selected';}
				$limitOpt .= "<option$selected value=\"$oneOpt\">$oneOpt</option>";
			}
			
			$htmlStr .= "<input type=\"hidden\" name=\"pageURI\" id=\"pageURI\" value=\"$GLOBALS[segment1]/$GLOBALS[segment2]/$customers_id\">
						<input type=\"hidden\" name=\"page\" id=\"page\" value=\"$this->page\">
						<input type=\"hidden\" name=\"rowHeight\" id=\"rowHeight\" value=\"$this->rowHeight\">
						<input type=\"hidden\" name=\"totalTableRows\" id=\"totalTableRows\" value=\"$this->totalRows\">
						<input type=\"hidden\" name=\"note_forTable\" id=\"note_forTable\" value=\"customers\">
						<input type=\"hidden\" name=\"publicsShow\" id=\"table_idValue\" value=\"$customers_id\">
						<input type=\"hidden\" name=\"publicsShow\" id=\"publicsShow\" value=\"0\">
						<div class=\"widget-header\">
							<div class=\"row\">
								<div class=\"col-sm-4\" style=\"position:relative;\">
									<h3>Customer History</h3>
								</div>
								<div class=\"col-sm-4\" style=\"position:relative;\">
									<select class=\"form-control mtop2\" name=\"shistory_type\" id=\"shistory_type\" onchange=\"checkAndLoadFilterData();\">
										$actFeeTitOpt
									</select>
								</div>
								<div class=\"col-sm-4\" style=\"position:relative;\">
									<div class=\"floatright mtop-1 pright10\"><a href=\"javascript:void(0);\" onclick=\"AJget_notesPopup(0);\" class=\"btn btn-default\">Add New Note</a></div>
								</div>
							</div>
						</div>
						<div class=\"widget-content padding0\">						
							<div class=\"row\">
								<div class=\"col-sm-12\" style=\"position:relative;\">
									<div id=\"no-more-tables\">
										<table class=\"col-md-12 table-bordered table-striped table-condensed cf listing\">
											<thead class=\"cf\">
												<tr>
													<td class=\"titlerow width80\">Date</td>
													<td class=\"titlerow width80\">Time</td>
													<td class=\"titlerow\" align=\"left\" width=\"20%\">Creator Name</td>
													<td class=\"titlerow\" align=\"left\" width=\"20%\">Activity</td>
													<td class=\"titlerow\" align=\"left\">Details</td>
												</tr>
											</thead>
											<tbody id=\"tableRows\">$tableRows</tbody>
										</table>
									</div>
								</div>
							</div>
							<div class=\"row mtop10\">
								<div class=\"col-sm-12\">
									<select class=\"form-control width100 floatleft\" name=\"limit\" id=\"limit\" onChange=\"checkloadTableRows();\">
										<option value=\"auto\">Auto</option>
										$limitOpt
									</select>
									<label id=\"fromtodata\"></label>
									<div class=\"floatright\" id=\"Pagination\"></div>
								</div>
							</div>
						</div>";
					$htmlStr .= "</div>
				</div>
			</div>";
			$customersData = array();
			$customerssql = "SELECT name, email, phone, address, customers_id, offers_email FROM customers WHERE customers_id != $customers_id AND customers_publish = 1";
			$customersquery = $this->db->getObj($customerssql, array());
			if($customersquery){
				while($onerow = $customersquery->fetch(PDO::FETCH_OBJ)){
					$customers_id = $onerow->customers_id;
					
					$email = trim(stripslashes($onerow->email));
					$phone = trim(stripslashes($onerow->phone));
					$name = trim(stripslashes($onerow->name));					
					$offers_email = trim(stripslashes($onerow->offers_email));					
					if($email !=''){
						$name .= " ($email)";
					}
					
					$customersData[] =array('id' => $customers_id,
											'email' => $email,
											'phone' => $phone,
											'am' => $offers_email,
											'label' => $name
											);
				}
			}

			$htmlStr .= '
			<script type="text/javascript">
				var customersData = '.json_encode($customersData).';
			</script>';
			
		}
		
		return $htmlStr;
	}
   
	private function filterHAndOptions(){
		$users_id = $_SESSION["users_id"]??0;
		
		$scustomers_id = $this->customers_id;
		$shistory_type = $this->history_type;
		
		$filterSql = '';
		$bindData = array();
		$bindData['customers_id'] = $scustomers_id;

		if($shistory_type !=''){
			if(strcmp($shistory_type, 'customers')==0){
				$filterSql = "SELECT 'Customer Created' AS afTitle, 'customers' AS tableName FROM customers WHERE customers_id = :customers_id";
			}
			elseif(strcmp($shistory_type, 'appointments')==0){
				$filterSql = "SELECT 'Appointments Created' AS afTitle, 'appointments' AS tableName FROM appointments WHERE customers_id = :customers_id";
			}
			elseif(strcmp($shistory_type, 'notes')==0){
				$filterSql = "SELECT 'Notes Created' AS afTitle, 'notes' AS tableName FROM notes WHERE note_for = 'customers' AND table_id = :customers_id";
			}
			elseif(strcmp($shistory_type, 'track_edits')==0){
				$filterSql = "SELECT 'Track Edits' AS afTitle, 'track_edits' AS tableName FROM track_edits WHERE record_for = 'customers' AND record_id = :customers_id";
			}
			else{
				$filterSql = "SELECT activity_feed_title AS afTitle, 'activity_feed' AS tableName FROM activity_feed WHERE uri_table_name = 'customers' AND activity_feed_link LIKE CONCAT('/Customers/view/', :customers_id)";
			}
		}
		else{
			$filterSql = "SELECT activity_feed_title AS afTitle, 'activity_feed' AS tableName FROM activity_feed WHERE uri_table_name = 'customers' AND activity_feed_link = '/Customers/view/$scustomers_id' 
			UNION ALL SELECT 'Customer Created' AS afTitle, 'customers' AS tableName FROM customers WHERE customers_id = :customers_id 
			UNION ALL SELECT 'Appointment Created' AS afTitle, 'appointments' AS tableName FROM appointments WHERE customers_id = :customers_id 
			UNION ALL SELECT 'Notes Created' AS afTitle, 'notes' AS tableName FROM notes WHERE note_for = 'customers' AND table_id = :customers_id 
			UNION ALL SELECT 'Track Edits' AS afTitle, 'track_edits' AS tableName FROM track_edits WHERE record_for = 'customers' AND record_id = :customers_id";
		}
		
		$totalRows = 0;		
		$actFeeTitOpts = array();
		$query = $this->db->getData($filterSql, $bindData);
		if($query){
			$totalRows = count($query);
			foreach($query as $getOneRow){
				$actFeeTitOpts[$getOneRow['tableName']] = ucfirst(str_replace('_', ' ', $getOneRow['afTitle']));
			}
		}
		
		$this->totalRows = $totalRows;
		ksort($actFeeTitOpts);
		$actFeeTitOpt = "<option value=\"\">All Activities</option>";
		if(!empty($actFeeTitOpts)){
			foreach($actFeeTitOpts as $tableName=>$optlabel){
				$optlabel = stripslashes(trim($optlabel));
				$selected = '';
				if(strcmp($tableName, $shistory_type)==0){$selected = ' selected="selected"';}
				$actFeeTitOpt .= "<option$selected value=\"$tableName\">".stripslashes($optlabel)."</option>";
			}
		}
		
		$this->actFeeTitOpt = $actFeeTitOpt;
	}
	
   private function loadHTableRows(){
		
		$limit = intval($_SESSION["limit"]);
		$rowHeight = $this->rowHeight;
		$page = intval($this->page);
		$totalRows = $this->totalRows;
		$scustomers_id = intval($this->customers_id);
		$shistory_type = $this->history_type;
		
		$starting_val = ($page-1)*$limit;
		if($starting_val>$totalRows){$starting_val = 0;}
		
		$users_id = $_SESSION["users_id"]??0;
		$currency = $_SESSION["currency"]??'$';
		$dateformat = $_SESSION["dateformat"]??'m/d/Y';
		$timeformat = $_SESSION["timeformat"]??'12 hour';
		
		$bindData = array();
		$bindData['customers_id'] = $scustomers_id;            
		if($shistory_type !=''){
			if(strcmp($shistory_type, 'customers')==0){
				$filterSql = "SELECT users_id, created_on, 'Customer Created' AS afTitle, 'customers' AS tableName, customers_id AS tableId FROM customers WHERE customers_id = :customers_id";
			}
			elseif(strcmp($shistory_type, 'appointments')==0){
				$filterSql = "SELECT users_id, created_on, 'Appointment Created' AS afTitle, 'appointments' AS tableName, appointments_id AS tableId FROM appointments WHERE customers_id = :customers_id";
			}
			elseif(strcmp($shistory_type, 'notes')==0){
				$filterSql = "SELECT users_id, created_on, 'Notes Created' AS afTitle, 'notes' AS tableName, notes_id AS tableId FROM notes WHERE note_for = 'customers' AND table_id = :customers_id";
			}
			elseif(strcmp($shistory_type, 'track_edits')==0){
				$filterSql = "SELECT users_id, created_on, 'Track Edits' AS afTitle, 'track_edits' AS tableName, track_edits_id AS tableId FROM track_edits WHERE record_for = 'customers' AND record_id = :customers_id";
			}
			else{
				$filterSql = "SELECT users_id, created_on, activity_feed_title AS afTitle, 'activity_feed' AS tableName, activity_feed_id AS tableId FROM activity_feed WHERE uri_table_name = 'customers' AND activity_feed_link LIKE CONCAT('/Customers/view/', :customers_id)";
			}
		}
		else{
			$filterSql = "SELECT users_id, created_on, activity_feed_title AS afTitle, 'activity_feed' AS tableName, activity_feed_id AS tableId FROM activity_feed WHERE uri_table_name = 'customers' AND activity_feed_link = '/Customers/view/$scustomers_id' 
			UNION ALL SELECT users_id, created_on, 'Customer Created' AS afTitle, 'customers' AS tableName, customers_id AS tableId FROM customers WHERE customers_id = :customers_id 
			UNION ALL SELECT users_id, created_on, 'Appointment Created' AS afTitle, 'appointments' AS tableName, appointments_id AS tableId FROM appointments WHERE customers_id = :customers_id 
			UNION ALL SELECT users_id, created_on, 'Notes Created' AS afTitle, 'notes' AS tableName, notes_id AS tableId FROM notes WHERE note_for = 'customers' AND table_id = :customers_id 
			UNION ALL SELECT users_id, created_on, 'Track Edits' AS afTitle, 'track_edits' AS tableName, track_edits_id AS tableId FROM track_edits WHERE record_for = 'customers' AND record_id = :customers_id";
		}
		$str = '';
		
		$query = $this->db->getData($filterSql, $bindData);
		if($query){
			$userIdNames = array();
			$userObj = $this->db->getObj("SELECT users_id, users_first_name, users_last_name FROM users", array());
			if($userObj){
				while($userOneRow = $userObj->fetch(PDO::FETCH_OBJ)){
					$userIdNames[$userOneRow->users_id] = trim("$userOneRow->users_first_name $userOneRow->users_last_name");
				}
			}
			
			foreach($query as $onerow){
				$singletablename = $onerow['tableName'];
				$tableIdName = $singletablename.'_id';
				$activity_feed_title = $onerow['afTitle'];
				
				$date = date($dateformat, strtotime($onerow['created_on']));
				if($timeformat=='24 hour'){$time =  date('H:i', strtotime($onerow['created_on']));}
				else{$time =  date('g:i a', strtotime($onerow['created_on']));}
				$userNameStr = $userIdNames[$onerow['users_id']]??'Website';
						
				$table_idval = $onerow['tableId'];
				
				if(strcmp($singletablename,'activity_feed')==0){
					$sql2nd = "SELECT activity_feed_name, activity_feed_link FROM activity_feed WHERE $tableIdName = $table_idval";
					$query2nd = $this->db->getObj($sql2nd, array());
					if($query2nd){
						while($oneRow = $query2nd->fetch(PDO::FETCH_OBJ)){
							$activity_feed_name = stripslashes(trim(strip_tags($oneRow->activity_feed_name)));
							$activity_feed_link = $oneRow->activity_feed_link;
							if(!empty($activity_feed_link)){
								$activity_feed_name = "<a href=\"$activity_feed_link\" class=\"txtunderline txtblue\" title=\"View Details\">$activity_feed_name <i class=\"fa fa-link\"></i></a>";
							}
						}
					}
				}
				else{
					$select = "name";
					if(strcmp($singletablename,'appointments')==0){
						$select = "appointments_no AS name";
					}
					else if(strcmp($singletablename,'notes')==0){
						$select = "note AS name";
					}
					else if(strcmp($singletablename,'track_edits')==0){
						$select = "details AS name";
					}
					$sql2nd = "SELECT $select FROM $singletablename WHERE $tableIdName = $table_idval";
					
					$query2nd = $this->db->getObj($sql2nd, array());
					if($query2nd){
						while($oneRow = $query2nd->fetch(PDO::FETCH_OBJ)){
							$activity_feed_name = stripslashes(trim(strip_tags($oneRow->name)));
							if(strcmp($singletablename,'track_edits')==0){
								$activityFeedNames = array();
								$details = json_decode($activity_feed_name);
								$moreInfo = (array)$details->moreInfo;
								$changed = $details->changed;
								if(array_key_exists('description', $moreInfo)){
									$description = $moreInfo['description'];									
									$activityFeedNames[] = $description;
								}
								if(!empty($changed)){
									$changed = (array)$changed;
									$changeStr = 'Edited: ';
									$c=0;
									foreach($changed as $key=>$changedData){
										$c++;
										if($c>1){$changeStr .= ', ';}
										$changeStr .= ucfirst(str_replace('_', ' ', $key));
										if(!is_array($changedData)){$changeStr .= ' '.$changedData;}
										elseif(is_array($changedData) && count($changedData)==2){												
											$changeStr .= ' "'.$changedData[0].'" to "'.$changedData[1].'"';
										}										
									}
									$activityFeedNames[] = $changeStr;
								}
								$activity_feed_name = implode('<br>', $activityFeedNames);
							}
							elseif(strcmp($singletablename,'appointments')==0){
								$activity_feed_name = "Appointment No. $activity_feed_name";
							}
						}
					}					
				}
				$str .= "<tr>";
				$str .= "<td valign=\"top\" data-title=\"Date\" align=\"left\">$date</td>";
				$str .= "<td valign=\"top\" data-title=\"Time\" align=\"right\">$time</td>";
				$str .= "<td valign=\"top\" data-title=\"Creator Name\" align=\"center\">$userNameStr</td>";
				$str .= "<td valign=\"top\" data-title=\"Activity\" align=\"left\">$activity_feed_title</td>";
				$str .= "<td valign=\"top\" data-title=\"Details\" align=\"left\">$activity_feed_name</td>";
				$str .= "</tr>";
				
			}
		}
		else{
			$str .= "<tr><td colspan=\"5\" style=\"color:red\">No note history meets the criteria given.</td></tr>";
		}
		
		return $str;

   }
		
	public function aJgetHPage(){
	
		$segment3 = $GLOBALS['segment3'];
		$customers_id = $_POST['customers_id']??0;
		$shistory_type = $_POST['shistory_type']??'';
		$totalRows = $_POST['totalRows']??0;
		$rowHeight = $_POST['rowHeight']??34;
		$page = $_POST['page']??1;
		if($page<=0){$page = 1;}
		$_SESSION["limit"] = $_POST['limit']??'auto';
		
		$this->customers_id = $customers_id;
		$this->history_type = $shistory_type;
		
		$jsonResponse = array();
		$jsonResponse['login'] = '';
		//===If filter options changes===//	
		if($segment3=='filter'){
			$this->filterHAndOptions();
			$jsonResponse['totalRows'] = $totalRows = $this->totalRows;
			$jsonResponse['actFeeTitOpt'] = $this->actFeeTitOpt;
			
		}
		$this->page = $page;
		$this->totalRows = $totalRows;
		$this->rowHeight = $rowHeight;
		$jsonResponse['tableRows'] = $this->loadHTableRows();
		
		return json_encode($jsonResponse);
	}
	
	public function aJget_CustomersPopup(){
	
		$customers_id = $_POST['customers_id']??0;
		$customersData = array();
		$customersData['login'] = '';
		$customersData['customers_id'] = 0;				
		$customersData['name'] = '';
		$customersData['phone'] = '';
		$customersData['email'] = '';
		$customersData['address'] = '';
		$customersData['offers_email'] = 0;
		if($customers_id>0){
			$customersObj = $this->db->getObj("SELECT * FROM customers WHERE customers_id = :customers_id", array('customers_id'=>$customers_id),1);
			if($customersObj){
				$customersRow = $customersObj->fetch(PDO::FETCH_OBJ);	

				$customersData['customers_id'] = $customers_id;
				$customersData['name'] = stripslashes(trim($customersRow->name));
				$customersData['phone'] = trim($customersRow->phone);
				$customersData['email'] = trim($customersRow->email);
				$customersData['address'] = stripslashes(trim($customersRow->address));
				$customersData['offers_email'] = intval($customersRow->offers_email);				
				$customersData['created_on'] = $customersRow->created_on;
				$customersData['last_updated'] = $customersRow->last_updated;				
			}
		}
				
		return json_encode($customersData);
	}
	
	public function aJsave_Customers(){
		
		$savemsg = $message = $str = '';
		
		$users_id = $_SESSION["users_id"]??0;
		$customers_id = $_POST['customers_id']??0;
		
		$name = addslashes(trim($_POST['name']??''));
		
		$bindData = $conditionarray = array();
		$bindData['name'] = $name;
		$conditionarray['name'] = $name;
		
		$phone = addslashes(trim($_POST['phone']??''));
		$conditionarray['phone'] = $phone;

		$email = addslashes(trim($_POST['email']??''));		
		$conditionarray['email'] = $email;

		$offers_email = trim($_POST['offers_email']??0);
		$conditionarray['offers_email'] = $offers_email;

		$address = addslashes(trim($_POST['address']??''));		
		$conditionarray['address'] = $address;
		
		$dupsql = "email = :email";
		$bindData['email'] = $email;
		if($email==''){
			$dupsql = "phone = :email";
			$bindData['email'] = $phone;
		}
		$conditionarray['users_id'] = $users_id;
		$conditionarray['last_updated'] = date('Y-m-d H:i:s');
		
		if($customers_id==0){
			$totalrows = 0;
			$queryManuObj = $this->db->getObj("SELECT COUNT(customers_id) AS totalrows FROM customers WHERE name = :name AND $dupsql AND customers_publish = 1", $bindData);
			if($queryManuObj){
				$totalrows = $queryManuObj->fetch(PDO::FETCH_OBJ)->totalrows;						
			}
			if($totalrows>0){
				$savemsg = 'error';
				$message .= "<p>This name and email already exists. Try again with different name/email.</p>";
			}
			else{										
				$conditionarray['created_on'] = date('Y-m-d H:i:s');
				
				$customers_id = $this->db->insert('customers', $conditionarray);
				if($customers_id){
					$str = $name;
					if($email !=''){
						$str .= " ($email)";
					}
					elseif($phone !=''){
						$str .= " ($phone)";
					}
				}
				else{
					$savemsg = 'error';
					$message .= "<p>'Error occured while adding new customer! Please try again.'</p>";
				}
			}
		}
		else{
			$totalrows = 0;
			$bindData['customers_id'] = $customers_id;
			$queryManuObj = $this->db->getObj("SELECT COUNT(customers_id) AS totalrows FROM customers WHERE name = :name AND $dupsql AND customers_id != :customers_id AND customers_publish = 1", $bindData);
			if($queryManuObj){
				$totalrows = $queryManuObj->fetch(PDO::FETCH_OBJ)->totalrows;						
			}
			if($totalrows>0){
				$savemsg = 'error';
				$message .= "<p>This name and email already exists. Try again with different name/email.</p>";
			}
			else{
				$custObj = $this->db->getData("SELECT * FROM customers WHERE customers_id = $customers_id", array());
				
				$update = $this->db->update('customers', $conditionarray, $customers_id);
				if($update){
					$str = $name;
					if($email !=''){$str .= " ($email)";}
					elseif($phone !=''){$str .= " ($phone)";}
					
					if($custObj){
						$changed = array();
						unset($conditionarray['last_updated']);
						foreach($conditionarray as $fieldName=>$fieldValue){
							$prevFieldVal = $custObj[0][$fieldName];
							if($prevFieldVal != $fieldValue){
								if($prevFieldVal=='1000-01-01'){$prevFieldVal = '';}
								if($fieldValue=='1000-01-01'){$fieldValue = '';}
								$changed[$fieldName] = array($prevFieldVal, $fieldValue);
							}
						}
						
						if(!empty($changed)){
							$moreInfo = array();
							$teData = array();
							$teData['created_on'] = date('Y-m-d H:i:s');
							$teData['users_id'] = $_SESSION["users_id"];
							$teData['record_for'] = 'customers';
							$teData['record_id'] = $customers_id;
							$teData['details'] = json_encode(array('changed'=>$changed, 'moreInfo'=>$moreInfo));
							$this->db->insert('track_edits', $teData);							
						}
					}
				}
				
				$savemsg = 'update-success';					
			}
		}
		
		$array = array( 'login'=>'',
						'customers_id'=>$customers_id,
						'email'=>$email,
						'phone'=>$phone,
						'savemsg'=>$savemsg,
						'message'=>$message,
						'resulthtml'=>$str);
		return json_encode($array);
	}
	
	public function sendEmail(){
	
		$returnStr = '';			
		$email_address = addslashes($_POST['smstophone']??'');
		if($email_address =='' || is_null($email_address)){
			$returnStr = 'Sorry! Could not send mail. Try again later.';
		}
		else{
			$fromName = stripslashes($_POST['smsfromname']??'');
			
			$headers = array();
			$headers[] = 'MIME-Version: 1.0';
			$headers[] = 'Content-type: text/html; charset=iso-8859-1';
			$headers[] = 'To: '.$fromName.' <'.$email_address.'>';
			$headers[] = 'From: '.COMPANYNAME.' <info@leadtnd.com>';
			
			$subject = stripslashes($_POST['subject']??'');
			if($subject ==''){$subject = "Email from $fromName";}
			$description = nl2br(stripslashes($_POST['smsmessage']??''));
		
			$Body = "<html><head><title>$subject</title></head>";
			$Body .= "<body>";
			$Body .= "<p>$description</p>";
			$Body .= "</body>";
			$Body .= "</html>";
						
			if($email_address =='' || is_null($email_address)){
				$returnStr = 'Your email is blank.';
			}
			else{
				if (!mail($email_address, $subject, $Body, implode("\r\n", $headers))){
					$returnStr = "Sorry! Could not send mail. Try again later.";
				}
				else{
					$returnStr = 'sent';
				}
			}
		}
		return json_encode(array('login'=>'', 'returnStr'=>$returnStr));
	}

	public function aJmergeCustomers(){
		$returnStr = $savemsg = '';
		$id = 0;
		$customers_id = $_POST['fromcustomers_id']??0;
		$tocustomers_id = $_POST['tocustomers_id']??0;
		$fromCustObj = $this->db->getObj("SELECT * FROM customers WHERE customers_id = :customers_id", array('customers_id'=>$customers_id), 1);
		if($fromCustObj){
			$fromCustRow = $fromCustObj->fetch(PDO::FETCH_OBJ);
			$toCustObj = $this->db->getObj("SELECT * FROM customers WHERE customers_id = :customers_id", array('customers_id'=>$tocustomers_id), 1);
			if($toCustObj){
				$toCustRow = $toCustObj->fetch(PDO::FETCH_OBJ);
				
				$updateData = array();	
				if(!empty($fromCustRow->phone)){
					if(empty($toCustRow->phone)){
						$updateData['phone'] = $fromCustRow->phone;
					}
					elseif(empty($toCustRow->secondary_phone)){
						$updateData['phone'] = $fromCustRow->phone;
					}
				}			
				if(!empty($fromCustRow->email) && empty($toCustRow->email)){
					$updateData['email'] = $fromCustRow->email;
				}
				if(!empty($fromCustRow->address) && empty($toCustRow->address)){
					$updateData['address'] = $fromCustRow->address;
				}
				if(!empty($updateData)){
					$this->db->update('customers', $updateData, $tocustomers_id);
				}
				$update = $this->db->update('customers', array('customers_publish'=>0), $customers_id);
				if($update){
					$id = $customers_id;
					$savemsg = 'Success';
					$filterSql = "SELECT activity_feed_id FROM activity_feed WHERE uri_table_name = 'customers' AND activity_feed_link LIKE CONCAT('/Customers/view/', :customers_id)";
					$tableObj = $this->db->getObj($filterSql, array('customers_id'=>$customers_id));
					if($tableObj){
						while($oneRow = $tableObj->fetch(PDO::FETCH_OBJ)){
							$activity_feed_link = '/Customers/view/'.$tocustomers_id;
							$this->db->update('activity_feed', array('activity_feed_link'=>$activity_feed_link), $oneRow->activity_feed_id);
						}
					}					
					
					$filterSql = "SELECT track_edits_id FROM track_edits WHERE record_for = 'customers' AND record_id = :customers_id ";
					$tableObj = $this->db->getObj($filterSql, array('customers_id'=>$customers_id));
					if($tableObj){
						while($oneRow = $tableObj->fetch(PDO::FETCH_OBJ)){
							$this->db->update('track_edits', array('record_id'=>$tocustomers_id), $oneRow->track_edits_id);
						}
					}
					
					$filterSql = "SELECT notes_id FROM notes WHERE note_for = 'customers' AND table_id = :customers_id";
					$tableObj = $this->db->getObj($filterSql, array('customers_id'=>$customers_id));
					if($tableObj){
						while($oneRow = $tableObj->fetch(PDO::FETCH_OBJ)){
							$this->db->update('notes', array('table_id'=>$tocustomers_id), $oneRow->notes_id);
						}
					}
					
					$note_for = 'customers';
					$noteData=array('table_id'=> $customers_id,
									'note_for'=> $note_for,
									'created_on'=> date('Y-m-d H:i:s'),
									'last_updated'=> date('Y-m-d H:i:s'),
									'users_id'=> $_SESSION["users_id"],
									'note'=> "This customer's all information has been merged to $toCustRow->name",
									'publics'=>0);
					$notes_id = $this->db->insert('notes', $noteData);
					
				}
			}			
		}
		return json_encode(array('login'=>'', 'savemsg'=>$savemsg, 'id'=>$id));
	}
	
}
?>