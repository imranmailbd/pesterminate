<?php
class Appointments {
	protected $db;
	private $page, $rowHeight, $totalRows, $services_id, $appointments_id, $notifications;
	private $sorting_type, $keyword_search, $history_type, $servicesIdOpt, $actFeeTitOpt;
	
	public function __construct($db){$this->db = $db;}
	
	public function lists($segment3){
		
		$list_filters = $_SESSION['list_filters']??array();		
		$ssorting_type = $list_filters['ssorting_type']??'appointments_date DESC, appointments_no DESC';
		$this->sorting_type = $ssorting_type;

		$snotifications = $list_filters['snotifications']??3;
		$this->notifications = $snotifications;
		
		$sservices_id = $list_filters['sservices_id']??0;
		$this->services_id = $sservices_id;
		
		$keyword_search = $list_filters['keyword_search']??'';
		$this->keyword_search = $keyword_search;
		
		$this->filterAndOptions();
		$servicesIdOpt = $this->servicesIdOpt;
		if(empty($servicesIdOpt)){$servicesIdOpt = "<option value=\"0\">All Services</option>";}
		
		$page = !empty($segment3) ? intval($segment3):1;
		if($page<=0){$page = 1;}
		if(!isset($_SESSION['limit'])){$_SESSION['limit'] = 'auto';}
		$limit = $_SESSION['limit'];
		
		$this->rowHeight = 34;
		$this->page = $page;
		$tableRows = $this->loadTableRows();

		$notifOpt = '';
		$notifOpts = array('3'=>"All Appointments", '1'=>'Pending Appointments', '0'=>'Approved Appointments', '2'=>'Canceled Appointments');
		foreach($notifOpts as $optValue=>$optLabel){
			$selected = '';
			if($snotifications==$optValue){$selected = ' selected';}
			$notifOpt .= "<option$selected value=\"$optValue\">$optLabel</option>";
		}

		$sorTypOpt = '';
		$sorTypOpts = array('appointments_date DESC, appointments_no DESC'=>"Date, Appointment No", 'appointments_date DESC'=>'Date', 'appointments_no DESC'=>'Appointment No');
		foreach($sorTypOpts as $optValue=>$optLabel){
			$selected = '';
			if($ssorting_type==$optValue){$selected = ' selected';}
			$sorTypOpt .= "<option$selected value=\"$optValue\">$optLabel</option>";
		}
		
		$limOpt = '';
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
			<div class=\"col-sm-12\">
				<h1 class=\"metatitle\">$GLOBALS[title] <i class=\"fa fa-info-circle txt16normal\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"$GLOBALS[title]\"></i></h1>
			</div>  
		</div>	
		<div class=\"row\">        
			<div class=\"col-xs-6 col-sm-4 col-md-3 pbottom10\">
				<select class=\"form-control\" name=\"notifications\" id=\"notifications\" onchange=\"checkAndLoadFilterData();\">
					$notifOpt
				</select>
			</div>
			<div class=\"col-xs-6 col-sm-4 col-md-3 pbottom10\">
				<select class=\"form-control\" name=\"sorting_type\" id=\"sorting_type\" onchange=\"checkAndLoadFilterData();\">
					$sorTypOpt
				</select>
			</div>
			<div class=\"col-xs-6 col-sm-4 col-md-3 pbottom10\">
				<select class=\"form-control\" name=\"sservices_id\" id=\"sservices_id\" onchange=\"checkAndLoadFilterData();\">
					$servicesIdOpt
				</select>
			</div>
			<div class=\"col-sm-12 col-sm-4 col-md-3 pbottom10\">
				<div class=\"input-group\">
					<input type=\"text\" placeholder=\"Search Customer\" value=\"$keyword_search\" id=\"keyword_search\" name=\"keyword_search\" class=\"form-control\" maxlength=\"50\" />
					<span class=\"input-group-addon cursor\" onClick=\"checkAndLoadFilterData();\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Search Customer\">
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
								<th class=\"txtleft\" width=\"8%\">Date</th>
								<th class=\"txtright\" width=\"7%\">Appointment No</th>
								<th class=\"txtcenter\" width=\"8%\">Services Name</th>
								<th align=\"left\" width=\"10%\">Customer Name</th>
								<th width=\"8%\">Phone</th>
								<th width=\"8%\">Email</th>
								<th width=\"8%\">Address</th>
								<th width=\"8%\">Services Type</th>
								<th>Description</th>
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
					$limOpt
				</select>
				<label id=\"fromtodata\"></label>
				<div class=\"floatright\" id=\"Pagination\"></div>
			</div>
		</div>";
		return $htmlStr;
	}
	
	private function filterAndOptions(){

		$snotifications = $this->notifications;
		$ssorting_type = $this->sorting_type;
		$sservices_id = $this->services_id;
		$keyword_search = $this->keyword_search;
		
		$_SESSION["current_module"] = "Appointments";
		$_SESSION["list_filters"] = array('ssorting_type'=>$ssorting_type, 'sservices_id'=>$sservices_id, 'keyword_search'=>$keyword_search);
		
		$filterSql = "";
		$bindData = array();
		if($snotifications<3){
			$filterSql .= " AND notifications = :notifications";
			$bindData['notifications'] = $snotifications;			
		}
		if($sservices_id >0){
			$filterSql .= " AND services_id = :services_id";
			$bindData['services_id'] = $sservices_id;
		}

		if($keyword_search !=''){
			$keyword_search = addslashes(trim($keyword_search));
			$filterSql .= " AND (appointments_no LIKE CONCAT('%', :appointments_no, '%') OR customers_id IN ( SELECT customers_id FROM customers WHERE customers_publish = 1";
			$bindData['appointments_no'] = str_replace('s', '', strtolower($keyword_search));
					
			if ( $keyword_search == "" ) { $keyword_search = " "; }
			$keyword_searches = explode (" ", $keyword_search);
			if ( strpos($keyword_search, " ") === false ) {$keyword_searches[0] = $keyword_search;}
			
			$num = 0;
			while($num < sizeof($keyword_searches)){
				$filterSql .= " AND CONCAT_WS(' ', name, phone, email, address) LIKE CONCAT('%', :keyword_search$num, '%')";
				$bindData['keyword_search'.$num] = trim($keyword_searches[$num]);
				$num++;
			}
			$filterSql .= "))";
		}
		
		$strextra ="SELECT services_id FROM appointments WHERE appointments_publish = 1 $filterSql";
		$query = $this->db->getData($strextra, $bindData);
		$totalRows = 0;
		$servicesIds = array();
		if($query){
			$totalRows = count($query);
			foreach($query as $getOneRow){
				$servicesIds[$getOneRow['services_id']] = '';
			}
		}

		$servicesIdOpt = "<option value=\"0\">All Services</option>";
		if(!empty($servicesIds)){
			$tableObj = $this->db->getObj("SELECT services_id, name FROM services WHERE services_id IN (".implode(', ', array_keys($servicesIds)).")", array());
			if($tableObj){
				while($tableOneRow = $tableObj->fetch(PDO::FETCH_OBJ)){							
					$optlabel = trim(stripslashes($tableOneRow->name));
					$selected = '';
					if(strcmp($tableOneRow->services_id, $sservices_id)==0){$selected = ' selected="selected"';}
					$servicesIdOpt .= "<option$selected value=\"$tableOneRow->services_id\">$optlabel</option>";
				}
			}
		}
		
		$this->totalRows = $totalRows;
		$this->servicesIdOpt = $servicesIdOpt;
	}
	
   private function loadTableRows(){
		
		$dateformat = $_SESSION["dateformat"]??'m/d/y';
		$timeformat = $_SESSION["timeformat"]??'12 hour';
		$currency = $_SESSION["currency"]??'$';
		$limit = $_SESSION["limit"]??'auto';
		$Common = new Common($this->db);
		
		$rowHeight = $this->rowHeight;
		$page = $this->page;
		$totalRows = $this->totalRows;
		$snotifications = $this->notifications;
		$ssorting_type = $this->sorting_type;
		$sservices_id = $this->services_id;
		$keyword_search = $this->keyword_search;
		$limit = 15;
		
		$starting_val = ($page-1)*$limit;
		if($starting_val>$totalRows){$starting_val = 0;}
		
		$filterSql = "";
		$bindData = array();
		if($snotifications<3){
			$filterSql .= " AND notifications = :notifications";
			$bindData['notifications'] = $snotifications;			
		}
		if($sservices_id >0){
			$filterSql .= " AND services_id = :services_id";
			$bindData['services_id'] = $sservices_id;
		}

		if($keyword_search !=''){
			$keyword_search = addslashes(trim($keyword_search));
			$filterSql .= " AND (appointments_no LIKE CONCAT('%', :appointments_no, '%') OR customers_id in ( SELECT customers_id FROM customers WHERE customers_publish = 1";
			$bindData['appointments_no'] = str_replace('s', '', strtolower($keyword_search));
					
			if ( $keyword_search == "" ) { $keyword_search = " "; }
			$keyword_searches = explode (" ", $keyword_search);
			if ( strpos($keyword_search, " ") === false ) {$keyword_searches[0] = $keyword_search;}
			$num = 0;
			while ( $num < sizeof($keyword_searches) ) {
				$filterSql .= " AND CONCAT_WS(' ', name, phone, email, address) LIKE CONCAT('%', :keyword_search$num, '%')";
				$bindData['keyword_search'.$num] = trim($keyword_searches[$num]);
				$num++;
			}
			$filterSql .= "))";
		}
		
		$sqlquery = "SELECT * FROM appointments WHERE appointments_publish = 1 $filterSql GROUP BY appointments_id ORDER BY $ssorting_type LIMIT $starting_val, $limit";
		$query = $this->db->getData($sqlquery, $bindData);
		$str = '';
		if($query){
			$customersId = $servicesIds = array();
			foreach($query as $oneRow){
				$customersId[$oneRow['customers_id']] = '';
				$servicesIds[$oneRow['services_id']] = '';
			}					
			
			if(!empty($customersId)){
				$tableObj = $this->db->getObj("SELECT customers_id, name, phone, email, address FROM customers WHERE customers_id IN (".implode(', ', array_keys($customersId)).")", array());
				if($tableObj){
					while($tableOneRow = $tableObj->fetch(PDO::FETCH_OBJ)){							
						$customersId[$tableOneRow->customers_id] = array(trim(stripslashes($tableOneRow->name)), $tableOneRow->phone, $tableOneRow->email, $tableOneRow->address);
					}
				}
			}					
			
			if(!empty($servicesIds)){
				$tableObj = $this->db->getObj("SELECT services_id, name FROM services WHERE services_id IN (".implode(', ', array_keys($servicesIds)).")", array());
				if($tableObj){
					while($oneTableRow = $tableObj->fetch(PDO::FETCH_OBJ)){							
						$servicesIds[$oneTableRow->services_id] = trim(stripslashes($oneTableRow->name));
					}
				}
			}
			
			foreach($query as $oneRow){
			
				$appointments_id = $oneRow['appointments_id'];
				$appointments_no = $oneRow['appointments_no'];
				if($appointments_no ==0){
					$appointments_no = $oneRow['appointments_id'];
				}
				$customers_id = $oneRow['customers_id'];
				$customerInfo = $customersId[$customers_id]??array();
				$cname = $cphone = $cemail = $caddress = '';
				if(!empty($customerInfo)){
					$cname = $customerInfo[0];
					$cphone = $customerInfo[1];
					$cemail = $customerInfo[2];
					$caddress = $customerInfo[3];
				}

				$date =  date($dateformat, strtotime($oneRow['appointments_date']));
				if($timeformat=='24 hour'){$time =  date('H:i', strtotime($oneRow['appointments_date']));}
				else{$time =  date('g:i a', strtotime($oneRow['appointments_date']));}
				
				$services_id = $oneRow['services_id'];
				$serviceName = $servicesIds[$services_id]??'&nbsp;';
				$services_type = $oneRow['services_type'];
				$description = trim(stripslashes($oneRow['description']));
				$notifications = $oneRow['notifications'];
				
				$boldClass = '';
				if($notifications==1){
					$boldClass = '  class="txtbold"';
				}
				$str .= "<tr$boldClass>
							<td nowrap data-title=\"Date\">$date $time</td>
							<td align=\"center\" nowrap data-title=\"Appointment No\"><a title=\"View Details\" href=\"/Appointments/view/$appointments_no\">$appointments_no <i class=\"fa fa-link\"></i></a></td>
							<td align=\"left\" data-title=\"Service Name\">$serviceName</td>
							<td align=\"left\" data-title=\"Customer Name\">$cname</td>
							<td align=\"left\" data-title=\"Phone\">$cphone</td>
							<td align=\"left\" data-title=\"Email\">$cemail</td>
							<td align=\"left\" data-title=\"Address\">$caddress</td>
							<td data-title=\"Service Type\">$services_type</td>
							<td data-title=\"Description\">$description</td>
						</tr>";
			}
		}
		else{
			$str .= "<tr><td colspan=\"9\" class=\"red18bold\">No appointment meet the criteria given</td></tr>";
		}
		
		return $str;
   }
	
	public function aJgetPage($segment3){
		
		$ssorting_type = $_POST['ssorting_type']??'appointments_date DESC, appointments_no DESC';
		$snotifications = $_POST['snotifications']??3;
		$sservices_id = $_POST['sservices_id']??0;

		$keyword_search = $_POST['keyword_search']??'';
		$totalRows = $_POST['totalRows']??0;
		$rowHeight = $_POST['rowHeight']??34;
		$page = $_POST['page']??1;
		if($page<=0){$page = 1;}
		$_SESSION["limit"] = $_POST['limit']??'auto';		
		
		$this->notifications = $snotifications;
		$this->sorting_type = $ssorting_type;
		$this->services_id = $sservices_id;
		$this->keyword_search = $keyword_search;
		
		$jsonResponse = array();
		$jsonResponse['login'] = '';
		//===If filter options changes===//	
		if($segment3=='filter'){				
			$this->filterAndOptions();
			$jsonResponse['totalRows'] = $totalRows = $this->totalRows;
			$jsonResponse['servicesIdOpt'] = $this->servicesIdOpt;
		}
		$this->page = $page;
		$this->totalRows = $totalRows;
		$this->rowHeight = $rowHeight;
		
		$jsonResponse['tableRows'] = $this->loadTableRows();
		
		return json_encode($jsonResponse);
	}
	
	public function view($segment3, $segment4){
		$dateformat = $_SESSION["dateformat"]??'m/d/y';
		$timeformat = $_SESSION["timeformat"]??'12 hour';
		$currency = $_SESSION["currency"]??'$';
		$Common = new Common($this->db);
		
		$htmlStr = "";
		$appointmentsObj = $this->db->getObj("SELECT * FROM appointments WHERE appointments_no = :appointments_no", array('appointments_no'=>$segment3),1);
		if($appointmentsObj){
			$appointments_onerow = $appointmentsObj->fetch(PDO::FETCH_OBJ);
			$list_filters = array();
			if(isset($_SESSION["list_filters"])){
				$list_filters = $_SESSION["list_filters"];
			}
			$shistory_type = $list_filters['shistory_type']??'';
		
			$appointments_id = $appointments_onerow->appointments_id;
			$appointments_no = $appointments_onerow->appointments_no;
			$appointments_publish = $appointments_onerow->appointments_publish;
			$services_type = $appointments_onerow->services_type;
			$notifications = intval($appointments_onerow->notifications);
			
			$appointments_date = $appointments_onerow->appointments_date;
			$date = '';
			if(!in_array($appointments_date, array('0000-00-00 00:00:00', '1000-01-01 00:00:00'))){
				$date = date($dateformat, strtotime($appointments_date));
				if($timeformat=='24 hour'){$date .= ' '.date('H:i', strtotime($appointments_date));}
				else{$date .= ' '.date('g:i a', strtotime($appointments_date));}
			}
			
			$services_id = $appointments_onerow->services_id;
			$serviceName = $servicesIds[$services_id]??'&nbsp;';
			
			$customers_id = $appointments_onerow->customers_id;
			$cname = $cphone = $cemail = $caddress = '';
			$offers_email = 0;
			$customerObj = $this->db->getObj("SELECT * FROM customers WHERE customers_id = $customers_id", array());
			if($customerObj){
				$customerrow = $customerObj->fetch(PDO::FETCH_OBJ);	
				$cname = stripslashes(trim($customerrow->name));
				$cphone = $customerrow->phone;
				$cemail = $customerrow->email;				  
				$caddress = $customerrow->address;
				$offers_email = $customerrow->offers_email;
			}
			$description = trim(stripslashes((string) $appointments_onerow->description));
			$notifOpts = array('3'=>"All Appointments", '1'=>'Pending Appointment', '0'=>'Approved Appointment', '2'=>'Canceled Appointment');
		
			$htmlStr = "<div class=\"row\">
				<div class=\"col-sm-6\">
					<h1 class=\"metatitle\">$GLOBALS[title] <i class=\"fa fa-info-circle txt16normal\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"$GLOBALS[title]\"></i></h1>
				</div>
				<div class=\"col-sm-6\">					
					<input type=\"hidden\" id=\"appointments_id\" name=\"appointments_id\" value=\"$appointments_id\">
					<a class=\"btn btn-default floatright mtop10 mbottom10\" href=\"/Appointments/lists\" title=\"Appointments\">
						<i class=\"fa fa-list\"></i> Appointments
					</a>
				</div>
			</div>
			<div class=\"row\">		
			  <div class=\"col-sm-12 col-md-5\">
				<div class=\"widget\">
					<div class=\"widget-header\">
						<i class=\"fa fa-user\"></i>
						<h3>Customer info</h3>
					</div>
					<div class=\"widget-content\" id=\"customer_information\">
						<ul class=\"list-unstyled labelfixedlist label100\">
							<li>
								<label>Customer: </label>
								<a class=\"anchorfulllink txtunderline txtblue\" href=\"/Customers/view/$customers_id\" title=\"View Customer Details\">".trim(stripslashes($cname))." <i class=\"fa fa-link\"></i></a>
							</li>
							<li>
								<label>Email: </label>$cemail
							</li>
							<li>
								<label>Phone No.: </label>$cphone
							</li>
							<li>
								<label>Address: </label>$caddress
							</li>
						</ul>
					</div>
				</div>
			  </div>		  
			  <div class=\"col-sm-12 col-md-7\">
				 <div class=\"widget\">
					<div class=\"widget-header\">
						<i class=\"fa fa-mobile\"></i>
						<h3>Appointment Information</h3>
					</div>
					<div class=\"widget-content\" id=\"order_info\">
						<ul class=\"list-unstyled labelfixedlist label100\">
							<li>
								<label>Services Name: </label>
								<span id=\"servicesstr\">";
									$tableObj = $this->db->getObj("SELECT name FROM services WHERE services_id = $appointments_onerow->services_id", array());
									if($tableObj){
										$htmlStr .= stripslashes(trim($tableObj->fetch(PDO::FETCH_OBJ)->name));
									}
								$htmlStr .= "</span>&emsp;&emsp;Service Type: $services_type
							</li>
							<li>
								<label>Date: </label>".date($dateformat,strtotime($appointments_onerow->appointments_date))."
							</li>
							<li>
								<label>Description: </label>".stripslashes(trim(nl2br($appointments_onerow->description)))."
							</li>
							<li><label>Status: </label>$notifOpts[$notifications]";								
								if($notifications !=2){
									$htmlStr .= "<input type=\"button\" class=\"mleft15 btn btn-danger floatright\" onclick=\"changeNotification(2);\" value=\"Cancel Appointment\" />";
								}
								if($notifications !=1){
									$htmlStr .= "<input type=\"button\" class=\"mleft15 btn btn-warning floatright\" onclick=\"changeNotification(1);\" value=\"Pending Appointment\" />";
								}
								if($notifications !=0){
									$htmlStr .= "<input type=\"button\" class=\"btn btn-success floatright\" onclick=\"changeNotification(0);\" value=\"Approve Appointment\" />";
								}
							$htmlStr .= "</li>
						</ul>
				  </div>
				</div>
			  </div>
			</div>
			<div class=\"row\">
				<div class=\"col-sm-12\">
					<div class=\"widget mbottom10\">";
					
			$list_filters = array();
			if(isset($_SESSION["list_filters"])){$list_filters = $_SESSION["list_filters"];}
			
			$sappointments_id = $list_filters['sappointments_id']??$appointments_id;
			$history_type = $list_filters['shistory_type']??'';
			$this->appointments_id = $sappointments_id;
			$this->history_type = $history_type;
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
			
			$htmlStr .= "<input type=\"hidden\" name=\"pageURI\" id=\"pageURI\" value=\"$GLOBALS[segment1]/$GLOBALS[segment2]/$appointments_no\">
						<input type=\"hidden\" name=\"page\" id=\"page\" value=\"$this->page\">
						<input type=\"hidden\" name=\"rowHeight\" id=\"rowHeight\" value=\"$this->rowHeight\">
						<input type=\"hidden\" name=\"totalTableRows\" id=\"totalTableRows\" value=\"$this->totalRows\">
						<input type=\"hidden\" name=\"note_forTable\" id=\"note_forTable\" value=\"appointments\">
						<input type=\"hidden\" name=\"sappointments_id\" id=\"sappointments_id\" value=\"$appointments_id\">
						<input type=\"hidden\" name=\"appointments_date\" id=\"appointments_date\" value=\"$appointments_onerow->appointments_date\">
						<input type=\"hidden\" name=\"publicsShow\" id=\"table_idValue\" value=\"$appointments_id\">
						<input type=\"hidden\" name=\"publicsShow\" id=\"publicsShow\" value=\"1\">
						<div class=\"widget-header\">
							<div class=\"row\">
								<div class=\"col-sm-4\" style=\"position:relative;\">
									<h3>Appointment History</h3>
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
													<td class=\"titlerow\" align=\"left\" width=\"10%\">Date</td>
													<td class=\"titlerow\" align=\"left\" width=\"10%\">Time</td>
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
		}
		return $htmlStr;
	}
	
	public function AJgetHPage($segment3){
	
		$sappointments_id = $_POST['sappointments_id']??0;
		$shistory_type = $_POST['shistory_type']??'';
		$totalRows = $_POST['totalRows']??0;
		$rowHeight = $_POST['rowHeight']??34;
		$page = $_POST['page']??1;
		if($page<=0){$page = 1;}
		$_SESSION["limit"] = $_POST['limit']??'auto';
		
		$this->appointments_id = $sappointments_id;
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
	
	private function filterHAndOptions(){
		$users_id = $_SESSION["users_id"]??0;
		
		$sappointments_id = $this->appointments_id;
		$shistory_type = $this->history_type;
		$sappointments_no = 0;
		$appointmentsObj = $this->db->getObj("SELECT appointments_no FROM appointments WHERE appointments_id = $sappointments_id", array());
		if($appointmentsObj){
			$sappointments_no = $appointmentsObj->fetch(PDO::FETCH_OBJ)->appointments_no;
		}

		$filterSql = '';
		$bindData = array();
		$bindData['appointments_id'] = $sappointments_id;

		if($shistory_type !=''){
			if(strcmp($shistory_type, 'appointments')==0){
				$filterSql = "SELECT 'Appointment Created' AS afTitle, 'appointments' AS tableName FROM appointments WHERE appointments_id = :appointments_id";
			}
			elseif(strcmp($shistory_type, 'notes')==0){
				$filterSql = "SELECT 'Notes Created' AS afTitle, 'notes' AS tableName FROM notes WHERE note_for = 'appointments' AND table_id = :appointments_id";
			}
			elseif(strcmp($shistory_type, 'track_edits')==0){
				$filterSql = "SELECT 'Track Edits' AS afTitle, 'track_edits' AS tableName FROM track_edits WHERE record_for = 'appointments' AND record_id = :appointments_id";
			}
			else{
				$filterSql = "SELECT activity_feed_title AS afTitle, 'activity_feed' AS tableName FROM activity_feed WHERE uri_table_name = 'appointments' AND activity_feed_link LIKE CONCAT('/Appointments/view/', :appointments_id)";
			}
		}
		else{
			$filterSql = "SELECT activity_feed_title AS afTitle, 'activity_feed' AS tableName FROM activity_feed WHERE uri_table_name = 'appointments' AND activity_feed_link = '/Appointments/view/$sappointments_no' 
			UNION ALL SELECT 'Appointment Created' AS afTitle, 'appointments' AS tableName FROM appointments WHERE appointments_id = :appointments_id 
			UNION ALL SELECT 'Notes Created' AS afTitle, 'notes' AS tableName FROM notes WHERE note_for = 'appointments' AND table_id = :appointments_id 
			UNION ALL SELECT 'Track Edits' AS afTitle, 'track_edits' AS tableName FROM track_edits WHERE record_for = 'appointments' AND record_id = :appointments_id";
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
		$sappointments_id = intval($this->appointments_id);
		$shistory_type = $this->history_type;
		$sappointments_no = 0;
		$appointmentsObj = $this->db->getObj("SELECT appointments_no FROM appointments WHERE appointments_id = $sappointments_id", array());
		if($appointmentsObj){
			$sappointments_no = $appointmentsObj->fetch(PDO::FETCH_OBJ)->appointments_no;
		}
		
		$starting_val = ($page-1)*$limit;
		if($starting_val>$totalRows){$starting_val = 0;}
		
		$users_id = $_SESSION["users_id"]??0;
		$currency = $_SESSION["currency"]??'$';
		$dateformat = $_SESSION["dateformat"]??'m/d/Y';
		$timeformat = $_SESSION["timeformat"]??'12 hour';
		
		$bindData = array();
		$bindData['appointments_id'] = $sappointments_id;            
		if($shistory_type !=''){
			if(strcmp($shistory_type, 'appointments')==0){
				$filterSql = "SELECT users_id, created_on, 'Appointment Created' AS afTitle, 'appointments' AS tableName, appointments_id AS tableId FROM appointments WHERE appointments_id = :appointments_id";
			}
			elseif(strcmp($shistory_type, 'notes')==0){
				$filterSql = "SELECT users_id, created_on, 'Notes Created' AS afTitle, 'notes' AS tableName, notes_id AS tableId FROM notes WHERE note_for = 'appointments' AND table_id = :appointments_id";
			}
			elseif(strcmp($shistory_type, 'track_edits')==0){
				$filterSql = "SELECT users_id, created_on, 'Track Edits' AS afTitle, 'track_edits' AS tableName, track_edits_id AS tableId FROM track_edits WHERE record_for = 'appointments' AND record_id = :appointments_id";
			}
			else{
				$filterSql = "SELECT users_id, created_on, activity_feed_title AS afTitle, 'activity_feed' AS tableName, activity_feed_id AS tableId FROM activity_feed WHERE uri_table_name = 'appointments' AND activity_feed_link LIKE CONCAT('/Appointments/view/', :appointments_id)";
			}
		}
		else{
			$filterSql = "SELECT users_id, created_on, activity_feed_title AS afTitle, 'activity_feed' AS tableName, activity_feed_id AS tableId FROM activity_feed WHERE uri_table_name = 'appointments' AND activity_feed_link = '/Appointments/view/$sappointments_no' 
			UNION ALL SELECT users_id, created_on, 'Appointment Created' AS afTitle, 'appointments' AS tableName, appointments_id AS tableId FROM appointments WHERE appointments_id = :appointments_id 
			UNION ALL SELECT users_id, created_on, 'Notes Created' AS afTitle, 'notes' AS tableName, notes_id AS tableId FROM notes WHERE note_for = 'appointments' AND table_id = :appointments_id 
			UNION ALL SELECT users_id, created_on, 'Track Edits' AS afTitle, 'track_edits' AS tableName, track_edits_id AS tableId FROM track_edits WHERE record_for = 'appointments' AND record_id = :appointments_id";
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
							if(strcmp($singletablename,'appointments')==0){
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
	
	public function changeNotification(){
		$POST = json_decode(file_get_contents('php://input'), true);
		$users_id = $_SESSION["users_id"]??0;
		
		$update = 0;
		$appointments_id = intval(array_key_exists('appointments_id', $POST) ? $POST['appointments_id'] : 0);
		$notifications = intval(array_key_exists('notifications', $POST) ? $POST['notifications'] : 1);
		if($appointments_id>0){
			$appointmentsObj = $this->db->getObj("SELECT appointments_id, customers_id, notifications FROM appointments WHERE appointments_id = :appointments_id", array('appointments_id'=>$appointments_id),1);
			if($appointmentsObj){
			    
				$appointmentsRow = $appointmentsObj->fetch(PDO::FETCH_OBJ);
				// var_dump($POST);exit;
				$appointments_id = $appointmentsRow->appointments_id;
				$customers_id = $appointmentsRow->customers_id;
				$oldnotifications = $appointmentsRow->notifications;
				if($oldnotifications != $notifications){
					
					$description = "Approved this Appointment";
					if($oldnotifications==1){
						if($notifications==2)
							$description = "Cancel this Appointment";
							$this->sendAppointments($appointments_id, $customers_id, $notifications);
					}
					else{
						if($oldnotifications==2){
							if($notifications==1){
								$description = "Changed this Appointment from Cancel to Approved";
								$this->sendAppointments($appointments_id, $customers_id, $notifications);
							} else {
								$description = "Changed this Appointment from Approved to Cancel";	
								$this->sendAppointments($appointments_id, $customers_id, $notifications);	
							}					
						}
					}

					$moreInfo = array();
					$teData = array();
					$teData['created_on'] = date('Y-m-d H:i:s');
					$teData['users_id'] = $_SESSION["users_id"];
					$teData['record_for'] = 'appointments';
					$teData['record_id'] = $appointments_id;
					$teData['details'] = json_encode(array('changed'=>array(''=>$description), 'moreInfo'=>$moreInfo));
					$this->db->insert('track_edits', $teData);

					$this->db->update('appointments', array('notifications'=>$notifications), $appointments_id);
					$update = 1;

					
				}
			}
		}
		
		return json_encode(array('login'=>'', 'update'=>$update));
	}


	function sendAppointments($appointments_id,$customers_id,$notifications){
		/**
		 * 
		 */
		$cust_id = $customers_id;

        $queryCustObj = $this->db->getObj("SELECT * FROM customers WHERE customers_id = :customers_id", array('customers_id'=>$cust_id));
        if($queryCustObj){
            $customers_id = $queryCustObj->fetch(PDO::FETCH_OBJ)->customers_id;						
            $customers_email = $queryCustObj->fetch(PDO::FETCH_OBJ)->email;						
            $customers_name = $queryCustObj->fetch(PDO::FETCH_OBJ)->name;				
            $customers_address = $queryCustObj->fetch(PDO::FETCH_OBJ)->address;				
        }

		if($customers_email =='' || is_null($customers_email)){
			$returnStr = 'Could not send mail because of missing customer email address.';
		}
		else{
			
			$fromName = trim(stripslashes((string) $customers_name?$customers_name:''));			
			$address = trim(stripslashes((string) $customers_address?$customers_address:''));			
			$subject = 'Customer appoinment'.$notifications.' on '.LIVE_DOMAIN;						
			$message = "<html>";
			$message .= "<head>";
			$message .= "<title>$subject</title>";
			$message .= "</head>";
			$message .= "<body>";
			$message .= "<p>";
			$message .= "Dear <i><strong>$fromName</strong></i>,<br />";
			$message .= "We ".$notifications." your appointment request.<br /><br />";
			$message .= "You Appointment Details:<br />";
			$message .= "Address: $address<br>";
			$message .= "Additional Notes: ..";
			$message .= "</p>";
			$message .= "<p>";
			$message .= "<br />";
			$message .= "Thank you for being with us.";
			$message .= "<br />";
			$message .= "</p>";
			$message .= "</body>";
			$message .= "</html>";

			$do_not_reply = $this->db->supportEmail('do_not_reply');
			
			$headers = array();
			$headers[] = 'MIME-Version: 1.0';
			$headers[] = 'Content-type: text/html; charset=iso-8859-1';
			$headers[] = 'To: '.$fromName.' <'.$customers_email.'>';
			$headers[] = 'From: '.COMPANYNAME.' <'.$do_not_reply.'>';
			
			//$headers .= 'Cc: shobhancse@gmail.com' . "\r\n";
			if(mail($customers_email, $subject, $message, implode("\r\n", $headers))){
				$returnStr = 'sent';
				
				$info = $this->db->supportEmail('info');
				$headers = array();
				$headers[] = 'MIME-Version: 1.0';
				$headers[] = 'Content-type: text/html; charset=iso-8859-1';
				$headers[] = 'To: '.COMPANYNAME.' <'.$info.'>';
				$headers[] = 'From: '.$fromName.' <'.$customers_email.'>';
				
				$message = "<html>";
				$message .= "<head>";
				$message .= "<title>$subject</title>";
				$message .= "</head>";
				$message .= "<body>";
				$message .= "<p>";
				$message .= "Dear Admin of <i><strong>".COMPANYNAME."</strong></i>,<br />";
				$message .= "We received a Catering request from $fromName.<br /><br />";
				$message .= "He / She wrotes:<br />";
				$message .= "Address: $address<br>";
				$message .= "Additional Notes: ..";
				$message .= "</p>";
				$message .= "<p>";
				$message .= "<br />";
				$message .= "Customer appointment status change to ".$notifications." .";
				$message .= "</p>";
				$message .= "</body>";
				$message .= "</html>";

				mail($info, $subject, $message, implode("\r\n", $headers));

				return true;

			} else {
				$returnStr = "Sorry! Could not send mail. Try again later.";
			}
		}
		//##########        
      
        if($customers_id>0 && $appointments_id>0){ 
            $appointments_no = $appointments_id;
            $queryObj = $this->db->getObj("SELECT * FROM appointments WHERE appointments_no = $appointments_no", array());
            if($queryObj){
                $appointments_no = $queryObj->fetch(PDO::FETCH_OBJ)->appointments_no;
                $services_id = $queryObj->fetch(PDO::FETCH_OBJ)->services_id;
                $customers_id = $queryObj->fetch(PDO::FETCH_OBJ)->customers_id;
                $description = $queryObj->fetch(PDO::FETCH_OBJ)->description;
                $appointments_date = $queryObj->fetch(PDO::FETCH_OBJ)->appointments_date;
            }			
        }
        
        return false;

    }

	
}
?>