<?php
class Activity_Feed{
	protected $db;
	private $page, $rowHeight, $totalRows, $pusers_id;
	private $activity_feed, $date_range, $actFeeTitOpt, $pUserOpt;
	
	public function __construct($db){$this->db = $db;}
	
	public function lists($segment4name){
		$dateformat = $_SESSION["dateformat"]??'m/d/Y';
		$list_filters = $_SESSION['list_filters']??array();
		$activity_feed = $list_filters['sactivity_feed']??'';
		$this->activity_feed = $activity_feed;
		$pusers_id = $list_filters['pusers_id']??0;
		$this->pusers_id = $pusers_id;
		$date_range = $list_filters['date_range']??date(str_replace('y', 'Y', $dateformat)).' - '.date(str_replace('y', 'Y', $dateformat));
		$this->date_range = $date_range;
		
		$this->filterAndOptions();
		$actFeeTitOpt = $this->actFeeTitOpt;
		$pUserOpt = $this->pUserOpt;
		
		$page = !empty($segment4name) ? intval($segment4name):1;
		if($page<=0){$page = 1;}
		if(!isset($_SESSION['limit'])){$_SESSION['limit'] = 'auto';}
		$limit = $_SESSION['limit'];
		
		$this->rowHeight = 45;
		$this->page = $page;	
		$tableRows = $this->loadTableRows();
		
		$limOpts = array(15, 20, 25, 50, 100, 500);
		$limOpt = '';
		foreach($limOpts as $oneOpt){
			$selected = '';
			if($limit==$oneOpt){$selected = ' selected';}
			$limOpt .= "<option$selected value=\"$oneOpt\">$oneOpt</option>";
		}
		
		$htmlStr = "<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"/assets/css/daterangepicker.css\" />
		<script type=\"text/javascript\" src=\"/assets/js/moment.min.js\"></script>
		<script type=\"text/javascript\" src=\"/assets/js/daterangepicker.js\"></script>
		<input type=\"hidden\" name=\"pageURI\" id=\"pageURI\" value=\"$GLOBALS[segment1]/$GLOBALS[segment2]\">
		<input type=\"hidden\" name=\"page\" id=\"page\" value=\"$this->page\">
		<input type=\"hidden\" name=\"rowHeight\" id=\"rowHeight\" value=\"$this->rowHeight\">
		<input type=\"hidden\" name=\"totalTableRows\" id=\"totalTableRows\" value=\"$this->totalRows\">
		<div class=\"row\">
			<div class=\"col-sm-12\">
				<h1 class=\"metatitle\">Activity Report <i class=\"fa fa-info-circle txt16normal\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Activity Report\"></i></h1>
			</div>
		</div>	
		<div class=\"row\">
			<div class=\"col-xs-6 col-sm-4 col-md-3 pbottom10\">&nbsp;</div>
			<div class=\"col-xs-6 col-sm-4 col-md-3 pbottom10\">
				<select class=\"form-control\" name=\"sactivity_feed\" id=\"sactivity_feed\" onchange=\"checkAndLoadFilterData();\">
					$actFeeTitOpt
				</select>
			</div>
			<div class=\"col-xs-6 col-sm-3 col-md-3 pbottom10\">
				<select class=\"form-control\" name=\"pusers_id\" id=\"pusers_id\" onchange=\"checkAndLoadFilterData();\">
					$pUserOpt
				</select>
			</div>
			<div class=\"col-xs-6 col-sm-3 col-md-3 pbottom10\">
				<div class=\"input-group\">
					<input type=\"text\" placeholder=\"Activity Feed Search\" value=\"$date_range\" id=\"date_range\" name=\"date_range\" class=\"form-control\" maxlength=\"23\" />
					<span class=\"input-group-addon cursor\" onClick=\"checkAndLoadFilterData();\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Activity Feed Search\">
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
								<th align=\"left\" width=\"8%\">Date</th>
								<th align=\"left\" width=\"8%\">Time</th>
								<th align=\"left\" width=\"20%\">Creator Name</th>
								<th align=\"left\" width=\"20%\">Activity</th>
								<th align=\"left\">Details</th>
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
		
		$sactivity_feed = $this->activity_feed;
		$pusers_id = $this->pusers_id;
		$date_range = $this->date_range;
		
		$_SESSION["current_module"] = "Activity_Feed";
		$_SESSION["list_filters"] = array('sactivity_feed'=>$sactivity_feed, 'pusers_id'=>$pusers_id, 'date_range'=>$date_range);
		
		$startdate = $enddate = '';
		if($date_range !=''){
			$activity_feeddatearray = explode(' - ', $date_range);
			if(is_array($activity_feeddatearray) && count($activity_feeddatearray)>1){
				$startdate = date('Y-m-d',strtotime($activity_feeddatearray[0])).' 00:00:00';
				$enddate = date('Y-m-d',strtotime($activity_feeddatearray[1])).' 23:59:59';
			}
		}
		
		$userIdNames = array();
		$userObj = $this->db->getObj("SELECT users_id, users_first_name, users_last_name FROM users", array());
		if($userObj){
			while($userOneRow = $userObj->fetch(PDO::FETCH_OBJ)){
				$userIdNames[$userOneRow->users_id] = trim("$userOneRow->users_first_name $userOneRow->users_last_name");
			}
		}
		
		$filterSqls = $bindData = array();
		if($pusers_id >0){
			$filterSqls[] = "users_id = :users_id";
			$bindData['users_id'] = $pusers_id;
		}

		$loginFilterSqls = $filterSqls;
		if($startdate !='' && $enddate !=''){
			$filterSqls[] = "created_on BETWEEN :startdate AND :enddate";
			$loginFilterSqls[] = "login_datetime BETWEEN :startdate AND :enddate";			
			$bindData['startdate']= $startdate;
			$bindData['enddate']= $enddate;
		}		
		$filterSql = $loginFilterSql = '';
		if(!empty($filterSqls)){
			$filterSql = ' WHERE '.implode(' AND ', $filterSqls);
			$loginFilterSql = ' WHERE '.implode(' AND ', $loginFilterSqls);
		}

		$tableNames = array('appointments', 'banners', 'branches', 'customers', 'customer_reviews', 'news_articles', 'pages', 'services', 'track_edits', 'users', 'videos', 'why_choose_us');			
		if($sactivity_feed !=''){
			$activity_feed_title = str_replace('_', ' ', $sactivity_feed);
			$activity_feed_title .= ' Created';
			if(in_array($sactivity_feed, $tableNames)){
				$sqlquery = "SELECT users_id, $activity_feed_title AS activity_feed_title, $sactivity_feed AS tableName FROM $sactivity_feed $filterSql";
			}
			else{
				if(!empty($filterSql)){
					$sqlquery = "SELECT users_id, activity_feed_title, 'activity_feed' AS tableName FROM activity_feed $filterSql AND activity_feed_title = '$sactivity_feed'";
				}
				else{
					$sqlquery = "SELECT users_id, activity_feed_title, 'activity_feed' AS tableName FROM activity_feed WHERE activity_feed_title = '$sactivity_feed'";
				}
			}
		}
		else{
			$sqlquery = "SELECT users_id, activity_feed_title, 'activity_feed' AS tableName FROM activity_feed $filterSql";
			foreach($tableNames as $oneTableName){
				$activity_feed_title = str_replace('_', ' ', $oneTableName);
				$activity_feed_title .= ' Created';
				$sqlquery .= " UNION ALL ";
				$sqlquery .= "SELECT users_id, '$activity_feed_title' AS activity_feed_title, '$oneTableName' AS tableName FROM $oneTableName $filterSql";
			}
		}
		
		$totalRows = 0;		
		$actFeeTitOpts = $pUserOpts = array();
		$query = $this->db->getData($sqlquery, $bindData);
		if($query){
			$totalRows = count($query);
			foreach($query as $getOneRow){
				$actFeeTitOpts[$getOneRow['tableName']] = ucfirst(str_replace('_', ' ', $getOneRow['activity_feed_title']));
				$pUserOpts[$getOneRow['users_id']] = $userIdNames[$getOneRow['users_id']]??'';
			}
		}
		
		ksort($actFeeTitOpts);
		$actFeeTitOpt = "<option value=\"\">All Activities</option>";
		if(!empty($actFeeTitOpts)){
			foreach($actFeeTitOpts as $tableName=>$optlabel){
				$optlabel = stripslashes(trim($optlabel));
				$selected = '';
				if(strcmp($tableName, $sactivity_feed)==0){$selected = ' selected="selected"';}
				$actFeeTitOpt .= "<option$selected value=\"$tableName\">".stripslashes($optlabel)."</option>";
			}
		}
		
		asort($pUserOpts);
		$pUserOpt = "<option value=\"0\">All Users</option>";
		if(!empty($pUserOpts)){
			foreach($pUserOpts as $optlabel=>$val){
				$optlabel = stripslashes(trim($optlabel));
				$selected = '';
				if(strcmp($optlabel, $pusers_id)==0){$selected = ' selected="selected"';}
				$pUserOpt .= "<option$selected value=\"$optlabel\">".stripslashes($val)."</option>";
			}
		}
		
		$this->totalRows = $totalRows;
		$this->actFeeTitOpt = $actFeeTitOpt;
		$this->pUserOpt = $pUserOpt;
	}
	
   private function loadTableRows(){
		$dateformat = $_SESSION["dateformat"]??'m/d/Y';
		$timeformat = $_SESSION["timeformat"]??'12 hour';
		$currency = $_SESSION["currency"]??'$';            
		$limit = $_SESSION["limit"];
		
		$rowHeight = $this->rowHeight;
		$page = $this->page;
		$totalRows = $this->totalRows;
		$sactivity_feed = $this->activity_feed;
		$pusers_id = $this->pusers_id;			
		if($pusers_id==''){$pusers_id = 0;}
		$date_range = $this->date_range;
		
		if(in_array($limit, array('', 'auto'))){
			$screenHeight = $_COOKIE['screenHeight']??480;
			$headerHeight = $_COOKIE['headerHeight']??300;
			$bodyHeight = floor($screenHeight-$headerHeight);
			$limit = floor($bodyHeight/$rowHeight);
			if($limit<=0){$limit = 1;}
		}
		$starting_val = ($page-1)*$limit;
		if($starting_val>$totalRows){$starting_val = 0;}
		
		$startdate = $enddate = '';
		if($date_range !=''){
			$activity_feeddatearray = explode(' - ', $date_range);
			if(is_array($activity_feeddatearray) && count($activity_feeddatearray)>1){
				$startdate = date('Y-m-d',strtotime($activity_feeddatearray[0])).' 00:00:00';
				$enddate = date('Y-m-d',strtotime($activity_feeddatearray[1])).' 23:59:59';
			}
		}
		
		$userIdNames = array();
		$userObj = $this->db->getObj("SELECT users_id, users_first_name, users_last_name FROM users", array());
		if($userObj){
			while($userOneRow = $userObj->fetch(PDO::FETCH_OBJ)){
				$userIdNames[$userOneRow->users_id] = trim("$userOneRow->users_first_name $userOneRow->users_last_name");
			}
		}
		
		$filterSqls = $bindData = array();
		if($pusers_id >0){
			$filterSqls[] = "users_id = :users_id";
			$bindData['users_id'] = $pusers_id;
		}
		$loginFilterSqls = $filterSqls;
		if($startdate !='' && $enddate !=''){
			$filterSqls[] = "created_on BETWEEN :startdate AND :enddate";
			$loginFilterSqls[] = "login_datetime BETWEEN :startdate AND :enddate";			
			$bindData['startdate']= $startdate;
			$bindData['enddate']= $enddate;
		}		
		$filterSql = $loginFilterSql = '';
		if(!empty($filterSqls)){
			$filterSql = ' WHERE '.implode(' AND ', $filterSqls);
			$loginFilterSql = ' WHERE '.implode(' AND ', $loginFilterSqls);
		}
		
		$tableNames = array('appointments', 'banners', 'branches', 'customers', 'customer_reviews', 'news_articles', 'pages', 'services', 'track_edits', 'users', 'videos', 'why_choose_us');			
		if($sactivity_feed !=''){
			
			$activity_feed_title = str_replace('_', ' ', $sactivity_feed);
			$activity_feed_title .= ' Created';
			if(in_array($sactivity_feed, $tableNames)){		
				$tableIdName = $sactivity_feed.'_id';		
				$sqlquery = "SELECT $tableIdName AS tableId, '$sactivity_feed' AS tablename, users_id, created_on, '$sactivity_feed' AS activity_feed_title FROM $sactivity_feed $filterSql";
			}
			else{
				if(!empty($filterSql)){
					$sqlquery = "SELECT activity_feed_id AS tableId, 'activity_feed' AS tablename, users_id, created_on, activity_feed_title FROM activity_feed $filterSql AND activity_feed_title = '$sactivity_feed'";
				}
				else{
					$sqlquery = "SELECT activity_feed_id AS tableId, 'activity_feed' AS tablename, users_id, created_on, activity_feed_title FROM activity_feed WHERE activity_feed_title = '$sactivity_feed'";
				}
			}
		}
		else{
			$sqlquery = "SELECT activity_feed_id AS tableId, 'activity_feed' AS tablename, users_id, created_on, activity_feed_title FROM activity_feed $filterSql";
			foreach($tableNames as $oneTableName){
				$tableIdName = $oneTableName.'_id';
				$activity_feed_title = str_replace('_', ' ', $oneTableName);
				$activity_feed_title .= ' Created';
				$sqlquery .= " UNION ALL ";
				$sqlquery .= "SELECT $tableIdName AS tableId, '$oneTableName' AS tablename, users_id, created_on, '$activity_feed_title' AS activity_feed_title FROM $oneTableName $filterSql";
			}
		}
		$sqlquery .= "  ORDER BY created_on DESC";
		
		$str = '';
		$sqlquery .= " LIMIT $starting_val, $limit";
		$query = $this->db->getData($sqlquery, $bindData);
		$i = $starting_val+1;
		
		if($query){
			foreach($query as $onerow){
				$singletablename = $onerow['tablename'];
				$tableIdName = $singletablename.'_id';
				$activity_feed_title = $onerow['activity_feed_title'];
				$date = date($dateformat, strtotime($onerow['created_on']));
				if($timeformat=='24 hour'){$time =  date('H:i', strtotime($onerow['created_on']));}
				else{$time =  date('g:i a', strtotime($onerow['created_on']));}
				$userNameStr = $userIdNames[$onerow['users_id']]??'';
							
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
					if(strcmp($singletablename,'users')==0){
						$select = "CONCAT(users_first_name, ' ', users_last_name) AS name";
					}
					elseif(strcmp($singletablename,'appointments')==0){
						$select = "appointments_no AS name";
					}
					elseif(strcmp($singletablename,'notes')==0){
						$select = "note AS name";
					}
					elseif(strcmp($singletablename,'track_edits')==0){
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
							
							$activity_feed_link = '';
							if(strcmp($singletablename,'customers')==0){
								$activity_feed_link = "/Customers/view/$table_idval";
							}
							elseif(strcmp($singletablename,'appointments')==0){
								$activity_feed_link = "/Appointments/view/$activity_feed_name";
								$activity_feed_name = "Appointment No. $activity_feed_name";
							}
							
							if(!empty($activity_feed_link)){
								$activity_feed_name = "<a href=\"$activity_feed_link\" class=\"txtunderline txtblue\" title=\"View Details\">$activity_feed_name <i class=\"fa fa-link\"></i></a>";
							}
						}
					}
				}
				$str .= "<tr>";
				$str .= "<td valign=\"top\" data-title=\"Date\" align=\"left\">$date</td>";
				$str .= "<td valign=\"top\" data-title=\"Time\" align=\"right\">$time</td>";
				$str .= "<td valign=\"top\" data-title=\"Creator Name\" align=\"center\">$userNameStr</td>";
				$str .= "<td valign=\"top\" data-title=\"Activity\" align=\"left\">".ucfirst(str_replace('_', ' ', $activity_feed_title))."</td>";
				$str .= "<td valign=\"top\" data-title=\"Details\" align=\"left\">$activity_feed_name</td>";
				$str .= "</tr>";
			}
		}
		else{
			$str .= "<tr><td colspan=\"5\" class=\"red18bold\">No activity feed meet the criteria given</td></tr>";
		}

		return $str;
    }
		
	public function AJgetPage($segment4name){
	
		$sactivity_feed = $_POST['sactivity_feed']??'';
		$pusers_id = intval($_POST['pusers_id']??0);
		$date_range = $_POST['date_range']??'';
		$totalRows = $_POST['totalRows']??0;
		$rowHeight = $_POST['rowHeight']??45;
		$page = $_POST['page']??1;
		if($page<=0){$page = 1;}
		$_SESSION["limit"] = $_POST['limit']??'auto';
		
		$this->activity_feed = $sactivity_feed;
		$this->pusers_id = $pusers_id;
		$this->date_range = $date_range;
		
		$jsonResponse = array();
		$jsonResponse['login'] = '';
		//===If filter options changes===//	
		if($segment4name=='filter'){				
			$this->filterAndOptions();
			$jsonResponse['totalRows'] = $totalRows = $this->totalRows;
			$jsonResponse['actFeeTitOpt'] = $this->actFeeTitOpt;
			$jsonResponse['pUserOpt'] = $this->pUserOpt;
		}
		$this->page = $page;
		$this->totalRows = $totalRows;
		$this->rowHeight = $rowHeight;
		
		$jsonResponse['tableRows'] = $this->loadTableRows();
		
		return json_encode($jsonResponse);
	}
}
?>