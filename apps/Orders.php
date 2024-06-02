<?php
class Orders{
	
	protected $db;
	private $page, $rowHeight, $totalRows, $employee_id, $pos_id;
	private $sorting_type, $keyword_search, $history_type, $empIdOpt, $actFeeTitOpt;
	
	public function __construct($db){$this->db = $db;}
	
	public function lists($segment3){

		$list_filters = $_SESSION['list_filters']??array();
		
		$sorting_type = $list_filters['sorting_type']??'pos.sales_datetime DESC, pos.invoice_no DESC';
		$this->sorting_type = $sorting_type;
		
		$keyword_search = $list_filters['keyword_search']??'';
		$this->keyword_search = $keyword_search;
		
		$this->filterAndOptions();
		$empIdOpt = $this->empIdOpt;
		if(empty($empIdOpt)){$empIdOpt = "<option value=\"0\">All Sales People</option>";}
		
		$page = !empty($segment3) ? intval($segment3):1;
		if($page<=0){$page = 1;}
		if(!isset($_SESSION['limit'])){$_SESSION['limit'] = 'auto';}
		$limit = $_SESSION['limit'];
		
		$this->rowHeight = 34;
		$this->page = $page;
		$tableRows = $this->loadTableRows();
		
		$sorTypOpt = $limOpt = '';
		// $sorTypOpts = array('name ASC, phone ASC'=>"Name and Phone", 'name ASC'=>'Name');
		$sorTypOpts = array('pos.sales_datetime DESC, pos.invoice_no DESC'=>"Date, Invoice No.", 'pos.sales_datetime DESC'=>'Date', 'pos.invoice_no DESC'=>'Invoice No.');
		foreach($sorTypOpts as $optValue=>$optLabel){
			$selected = '';
			if($sorting_type==$optValue){$selected = ' selected';}
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
				<h1 class=\"metatitle\">Order List <i class=\"fa fa-info-circle txt16normal\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"This page displays the list of your orders\"></i></h1>
			</div>	
			<div class=\"col-sm-12 col-md-6 ptopbottom15\">
				<a href=\"/Orders/add\" title=\"Create Order\" class=\"btn btn-default hilightbutton floatright\">
				<i class=\"fa fa-plus\"></i> Create Order
				</a>
			</div>  
		</div>
		<div class=\"row\">
			<div class=\"col-sm-6 col-sm-4 col-md-3 pbottom10\">&nbsp;</div>
			<div class=\"col-xs-6 col-sm-4 col-md-3 pbottom10\">
				<select class=\"form-control\" name=\"sorting_type\" id=\"sorting_type\" onchange=\"checkAndLoadFilterData();\">
					$sorTypOpt
				</select>
			</div>
			<div class=\"col-xs-6 col-sm-4 col-md-3 pbottom10\">
				<select class=\"form-control\" name=\"semployee_id\" id=\"semployee_id\" onchange=\"checkAndLoadFilterData();\">
					$empIdOpt
				</select>
			</div>
			<div class=\"col-sm-12 col-sm-4 col-md-3 pbottom10\">
				<div class=\"input-group\">
					<input type=\"text\" placeholder=\"Search Customer or Invoice\" value=\"$keyword_search\" id=\"keyword_search\" name=\"keyword_search\" class=\"form-control\" maxlength=\"50\" />
					<span class=\"input-group-addon cursor\" onClick=\"checkAndLoadFilterData();\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Search Customer or Invoice\">
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
								<th class=\"txtcenter\" width=\"15%\">Order Date</th>
								<th class=\"txtcenter\" width=\"15%\">InvoiceNo.</th>
								<th class=\"txtcenter\" width=\"24%\">Customer Name</th>
								<th class=\"txtcenter\" width=\"23%\">Service Fee</th>
								<th class=\"txtcenter\" width=\"23%\">Service Date Time</th>
								<!--th align=\"left\">Customer Company</th>
								<th class=\"txtright\" width=\"10%\">Total Amt.</th-->
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
		
		$_SESSION["current_module"] = "Orders";
		$_SESSION["list_filters"] = array('sorting_type'=>$sorting_type, 'keyword_search'=>$keyword_search);
		
		$filterSql = "FROM pos WHERE pos_publish = 1";
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
		
		$strextra ="SELECT COUNT(pos_id) AS totalRows $filterSql";
		$query = $this->db->getObj($strextra, $bindData);
		if($query){
			$totalRows = $query->fetch(PDO::FETCH_OBJ)->totalRows;
		}
		$this->totalRows = $totalRows;
	}
	
    private function loadTableRows(){

		if(!in_array($GLOBALS['segment2'], array('lists', 'AJgetPage', 'edit'))){
			$this->db->writeIntoLog("Call from: $GLOBALS[segment1]/$GLOBALS[segment2]");
		}
		
		$dateformat = $_SESSION["dateformat"]??'m/d/y';
		$timeformat = $_SESSION["timeformat"]??'24 hour';
		$currency = $_SESSION["currency"]??'$';
		$limit = $_SESSION["limit"]??'auto';
		$Common = new Common($this->db);
		$rowHeight = $this->rowHeight;
		$page = $this->page;
		$totalRows = $this->totalRows;
		$sorting_type = $this->sorting_type;	
		$semployee_id = $this->employee_id;	
		$keyword_search = $this->keyword_search;

		$sum_total = 0;
		$paid_total = 0;
		$due_total = 0;
		$sumTotal = 0;
		$paidTotal = 0;
		$dueTotal = 0;
		
		if(in_array($limit, array('', 'auto'))){
			$screenHeight = $_COOKIE['screenHeight']??480;
			$headerHeight = $_COOKIE['headerHeight']??300;
			$bodyHeight = floor($screenHeight-$headerHeight);
			$limit = floor($bodyHeight/$rowHeight);
			if($limit<=0){$limit = 1;}
		}
		$starting_val = ($page-1)*$limit;
		if($starting_val>$totalRows){$starting_val = 0;}
		
		$filterSql = "";
		$bindData = array();
		if($semployee_id >0){
			$filterSql .= " AND pos.employee_id = :employee_id";
			$bindData['employee_id'] = $semployee_id;
		}

		if($keyword_search !=''){
			$keyword_search = addslashes(trim($keyword_search));
			$filterSql .= " AND (pos.invoice_no LIKE CONCAT('%', :invoice_no, '%') OR pos.customers_id in ( SELECT customers_id FROM customers WHERE accounts_id = $prod_cat_man";
			$bindData['invoice_no'] = str_replace('s', '', strtolower($keyword_search));
					
			if ( $keyword_search == "" ) { $keyword_search = " "; }
			$keyword_searches = explode (" ", $keyword_search);
			if ( strpos($keyword_search, " ") === false ) {$keyword_searches[0] = $keyword_search;}
			$num = 0;
			while ( $num < sizeof($keyword_searches) ) {
				$filterSql .= " AND CONCAT_WS(' ', first_name, last_name, company, email, contact_no, secondary_phone, fax) LIKE CONCAT('%', :keyword_search$num, '%')";
				$bindData['keyword_search'.$num] = trim($keyword_searches[$num]);
				$num++;
			}
		}
		
		$sqlquery = "SELECT pos.*, SUM(pos_cart.sales_price*pos_cart.qty) AS Total FROM pos LEFT JOIN pos_cart ON (pos.pos_id = pos_cart.pos_id) WHERE pos.pos_publish = 1 AND (pos.order_status=1  OR pos.order_status=2)
		$filterSql GROUP BY pos.pos_id ORDER BY $sorting_type LIMIT $starting_val, $limit";
		$query = $this->db->querypagination($sqlquery, $bindData);
		$str = '';
		// echo($sqlquery);exit;

		if($query){
			$customersId = $salesmanId = $customersData = $customersCompData = $salesmanData = array();
			foreach($query as $oneRow){				
				if(!in_array($oneRow['customers_id'], $customersId)){
					array_push($customersId,$oneRow['customers_id']);
				}
				if(!in_array($oneRow['employee_id'], $salesmanId)){
					array_push($salesmanId, $oneRow['employee_id']);
				}
			}	

			// var_dump($customersId);exit;

			if(!empty($customersId)){
				$customersObj = $this->db->query("SELECT customers_id, name, company FROM customers WHERE customers_id IN (".implode(', ', $customersId).")", array());
				if($customersObj){
					while($customersrow = $customersObj->fetch(PDO::FETCH_OBJ)){							
						$customersData[$customersrow->customers_id] = trim(stripslashes("$customersrow->name"));
						$customersCompData[$customersrow->customers_id] = trim(stripslashes("$customersrow->company"));
					}
				}
			}	
				
			if(!empty($salesmanId)){
				$salesmanObj = $this->db->query("SELECT users_id, users_first_name, users_last_name FROM users WHERE users_id IN (".implode(', ', $salesmanId).")", array());
				if($salesmanObj){
					while($salesmanRow = $salesmanObj->fetch(PDO::FETCH_OBJ)){							
						$salesmanData[$salesmanRow->users_id] = trim(stripslashes("$salesmanRow->users_first_name $salesmanRow->users_last_name"));
					}
				}
			}


			foreach($query as $oneRow){
		
				$pos_id = $oneRow['pos_id'];
				$invoice_no = $oneRow['invoice_no'];
				$service_fee = $oneRow['service_fee'];
				$service_datetime = $oneRow['service_datetime'];
				
				if($invoice_no ==0){
					$invoice_no = $oneRow['pos_id'];
				}
				$customers_id = $oneRow['customers_id'];
				$customername = $customersData[$customers_id]??'&nbsp;';
				$amount_due = $oneRow['amt_due'];
				$amountDue = $currency.number_format($oneRow['amt_due']);
				$due_total += $amount_due;

				
				$customercompany = $customersCompData[$customers_id]??'&nbsp;';
				// echo $customercompany;exit;
				$date =  date($dateformat, strtotime($oneRow['sales_datetime']));
				$date_s =  date($dateformat, strtotime($oneRow['service_datetime']));
				
				$employee_id = $oneRow['employee_id'];
				$salesname = $salesmanData[$employee_id]??'&nbsp;';
				
				$taxable_total = $oneRow['taxableTotal'];
				$totalnontaxable = $oneRow['nonTaxableTotal'];
				$sum_total += $taxable_total;
				
				
				// var_dump($totalnontaxable);exit;

				
				$taxable_totalstr = $currency.number_format($taxable_total,2);
				if($taxable_total <0 ){
					$taxable_totalstr = '-'.$currency.number_format($taxable_total*(-1),2);
				}
				
				$totalnontaxablestr = $currency.number_format($totalnontaxable,2);
				if($totalnontaxable <0 ){
					$totalnontaxablestr = '-'.$currency.number_format($totalnontaxable*(-1),2);
				}
				
				$taxes_total1 = $Common->calculateTax($taxable_total, $oneRow['taxes_percentage1'], $oneRow['tax_inclusive1']);
				$taxes_total2 = $Common->calculateTax($taxable_total, $oneRow['taxes_percentage2'], $oneRow['tax_inclusive2']);

				$tax_inclusive1 = $oneRow['tax_inclusive1'];
				$tax_inclusive2 = $oneRow['tax_inclusive2'];
					
				$taxestotal = $taxes_total1+$taxes_total2;
				$taxestotalstr = $currency.number_format($taxestotal,2);
				if($taxestotal <0 ){
					$taxestotalstr = '-'.$currency.number_format($taxestotal*(-1),2);
				}
				
				$grand_total = $taxable_total+$taxestotal+$totalnontaxable;
				if($tax_inclusive1>0){
					$grand_total -= $taxes_total1;
				}
				if($tax_inclusive2>0){
					$grand_total -= $taxes_total2;
				}
				$grand_totalstr = $currency.number_format($grand_total,2);
				if($grand_total <0 ){
					$grand_totalstr = '-'.$currency.number_format($grand_total*(-1),2);
				}		
				
				$amt_paid = $taxable_total - $amount_due;
				$amtPaid = $currency.number_format($taxable_total - $amount_due);
				$paid_total += $amt_paid;
				// var_dump($amtPaid);exit;
				
				$editlinkstart = "<a class=\"anchorfulllink\" href=\"/Orders/edit/$invoice_no\" title=\"View Order Details\">";
				$editlinkclose = '</a>';
							
				$str .= "<tr>
							<td nowrap class=\"txtcenter\" data-title=\"Date\">$editlinkstart$date$editlinkclose</td>
							<td nowrap class=\"txtcenter\" data-title=\"Invoice No.\">$editlinkstart o$invoice_no$editlinkclose</td>
							<td class=\"txtcenter\" data-title=\"Customer Name\">$editlinkstart$customername$editlinkclose</td>
							<td class=\"txtcenter\" data-title=\"Service Fee\">$editlinkstart$service_fee$editlinkclose</td>
							<td class=\"txtcenter\" data-title=\"Service Date Time\">$editlinkstart$$date_s$editlinkclose</td>
							<!--td data-title=\"Company\" class=\"txtcenter\">$editlinkstart$customercompany$editlinkclose</td>
							<td align=\"left\" data-title=\"Sales Person\">$editlinkstart$salesname$editlinkclose</td-->							
							<!--td data-title=\"Total Amt.\" class=\"txtright\">$editlinkstart$taxable_totalstr$editlinkclose</td>
							<td data-title=\"Paid\" class=\"txtright\">$editlinkstart$amtPaid$editlinkclose</td>
							<td data-title=\"Due\" class=\"txtright\">$editlinkstart$amountDue$editlinkclose</td-->
						</tr>";

						

			}

			$sumTotal = $currency.number_format($sum_total);
			$paidTotal = $currency.number_format($paid_total);
			$dueTotal = $currency.number_format($due_total);

			$str .= "<!--tr>						
						<td colspan=\"0\" align=\"right\" data-title=\"Sales Person\"><b>Total: </b></td>
						<td data-title=\"Total Amt.\" class=\"txtright\"><b>$sumTotal</b></td>
					</tr-->";


		}
		else{
			$str .= "<tr><td colspan=\"4\" class=\"red18bold\">No invoices meet the criteria given</td></tr>";
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

	/**
	 * new
	 */
	public function add(){

		$prod_cat_man = '4484'; //$_SESSION['prod_cat_man']??0;
		$accounts_id = '4484';  //$_SESSION['accounts_id']??0;
		$customFields = 0;
		$queryObj = $this->db->query("SELECT COUNT(custom_fields_id) AS totalrows FROM custom_fields WHERE accounts_id = $prod_cat_man AND field_for = 'customers'", array());
		if($queryObj){
			$customFields = $queryObj->fetch(PDO::FETCH_OBJ)->totalrows;
		}
		$salesman_id = $_SESSION["user_id"]??0;
		

		$salManOpt = "<option value=\"\">Select Sales Person</option>";
		$sqlquery = "SELECT users_id, users_first_name, users_last_name FROM users WHERE users_publish =1 ORDER BY users_first_name asc, users_last_name asc";
		// echo $sqlquery;exit;
		$query = $this->db->query($sqlquery, array());
		if($query){
			while($useronerow = $query->fetch(PDO::FETCH_OBJ)){
				$user_id = $useronerow->users_id;
				$optLabel = stripslashes(trim("$useronerow->users_first_name $useronerow->users_last_name"));
				$select = '';
				if($user_id==$salesman_id){$select = ' selected="selected"';}
				$salManOpt .= "<option$select value=\"$user_id\">$optLabel</option>";
			}
		}
		
				
		$RefOpt = "<option value=\"0\">Select Referrer</option>";
		$sqlquery = "SELECT referrer_id, name FROM referrer WHERE accounts_id = $prod_cat_man AND referrer_publish =1 ORDER BY name ASC";
		$query = $this->db->query($sqlquery, array());
		if($query){
			while($useronerow = $query->fetch(PDO::FETCH_OBJ)){
				$optval = $useronerow->referrer_id;
				$optlable = trim(stripslashes("$optval - $useronerow->name"));
				$RefOpt .= "<option value=\"$optval\">$optlable</option>";
			}
		}
		

		$DeaOpt = "<option value=\"0\">Select PM</option>";
		$sqlquery = "SELECT users_id, users_first_name, users_last_name FROM users WHERE users_publish =1 ORDER BY users_first_name asc, users_last_name asc";
		$query = $this->db->query($sqlquery, array());
		if($query){
			while($useronerow = $query->fetch(PDO::FETCH_OBJ)){
				$optval = $useronerow->users_id;
				$optlable = trim(stripslashes("$useronerow->users_first_name $useronerow->users_last_name"));
				$DeaOpt .= "<option value=\"$optval\">$optlable</option>";
			}
		}


		$PayOpts = "<option value=\"0\">Select Payment Type</option>";
		$payArray = array("onetime"=>"One Time","installment"=>"Installment", "subscription"=>"Subscription");
		foreach($payArray as $key => $oneOpt){
			$selected = '';
			//if($limit==$key){$selected = ' selected';}
			$PayOpts .= "<option$selected value=\"$key\">$oneOpt</option>";
		}
		


		// $DeaOpt = "<option value=\"0\">Select Dealers</option>";
		// $sqlquery = "SELECT dealers_id, name FROM dealers WHERE accounts_id = $prod_cat_man AND dealers_publish =1 ORDER BY name ASC";
		// $query = $this->db->query($sqlquery, array());
		// if($query){
		// 	while($useronerow = $query->fetch(PDO::FETCH_OBJ)){
		// 		$optval = $useronerow->dealers_id;
		// 		$optlable = trim(stripslashes("$optval - $useronerow->name"));
		// 		$DeaOpt .= "<option value=\"$optval\">$optlable</option>";
		// 	}
		// }

		// $vehicle_driver_id = $_SESSION["vehicle_driver_id"]??0;
        // $VehDriOpt = "<option value=\"0\">Select Vehicle / Driver</option>";
		// $sqlquery = "SELECT vehicle_driver_id, vehicle, driver, carrier FROM vehicle_driver WHERE accounts_id = $accounts_id AND vehicle_driver_publish =1 ORDER BY vehicle ASC, driver ASC, carrier ASC";
		// $query = $this->db->query($sqlquery, array());
		// if($query){
		// 	while($useronerow = $query->fetch(PDO::FETCH_OBJ)){
		// 		$optval = $useronerow->vehicle_driver_id;
		// 		$optlable = trim(stripslashes("$useronerow->vehicle, $useronerow->driver, $useronerow->carrier"));
		// 		$select = '';
		// 		if($optval==$vehicle_driver_id){$select = ' selected="selected"';}
		// 		$VehDriOpt .= "<option$select value=\"$optval\">$optlable</option>";
		// 	}
		// }
		
		$DisOpt = "<option value=\"0\">Select Distributors</option>";
		$sqlquery = "SELECT distributors_id, name FROM distributors WHERE accounts_id = $prod_cat_man AND distributors_publish =1 ORDER BY name ASC";
		$query = $this->db->query($sqlquery, array());
		if($query){
			while($useronerow = $query->fetch(PDO::FETCH_OBJ)){
				$optval = $useronerow->distributors_id;
				$optlable = trim(stripslashes("$optval - $useronerow->name"));
				$DisOpt .= "<option value=\"$optval\">$optlable</option>";
			}
		}
		
		$htmlStr = "<div class=\"row\">
			<div class=\"col-sm-12\">
				<h1 class=\"singin2\">Title <i class=\"fa fa-info-circle txt16normal\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"This page captures the basic details required to create orders taken from telephone or online sales.\"></i></h1>
			</div>   
		</div>    
		<div class=\"row\">
			<div class=\"col-sm-12\">
				<div class=\"bs-callout well bs-callout-info\" style=\"margin-top:0; border-left:1px solid #EEEEEE;\">
					<form id=\"frmAddOrders\" action=\"#\" name=\"frmAddOrders\" onsubmit=\"return AJsave_Orders();\" enctype=\"multipart/form-data\" method=\"post\" accept-charset=\"utf-8\">
						<div class=\"form-group row\">
							<div class=\"col-xs-6 col-sm-4 col-md-2\">
								<label for=\"customer_name\" data-placement=\"bottom\">Customer Name<span class=\"required\">*</span></label>
							</div>
							<div class=\"col-xs-6 col-sm-8 col-md-4\">
								<div class=\"input-group\" id=\"customerNameField\">
									<input maxlength=\"50\" type=\"text\" value=\"\" required name=\"customer_name\" id=\"customer_name\" class=\"form-control\" placeholder=\"Search Customers\">
									<span data-toggle=\"tooltip\" title=\"Add New Customer\" class=\"input-group-addon cursor\" onClick=\"AJget_CustomersPopup(0, $customFields);\">
										<i class=\"fa fa-plus\"></i> New
									</span>
								</div>
								<input type=\"hidden\" name=\"customers_id\" id=\"customers_id\" value=\"0\">
								<span class=\"error_msg\" id=\"errmsg_customers_id\"></span>
							</div>
							<div class=\"col-sm-12 col-md-6\"><span class=\"error_msg\" id=\"errmsg_customer_name\"></span></div>
						</div>
						<div class=\"form-group row\">
							<div class=\"col-xs-6 col-sm-4 col-md-2\">
								<label for=\"salesman_id\" data-placement=\"bottom\">Sales Person<span class=\"required\">*</span></label>
							</div>
							<div class=\"col-xs-6 col-sm-8 col-md-4\">
								<select required name=\"salesman_id\" id=\"salesman_id\" class=\"form-control\">
									$salManOpt
								</select>
							</div>
						</div>
						<div class=\"form-group row\">
							<div class=\"col-xs-6 col-sm-4 col-md-2\">
								<label for=\"referrer_id\" data-placement=\"bottom\">Referrer</label>
							</div>
							<div class=\"col-xs-6 col-sm-8 col-md-4\">
								<select name=\"referrer_id\" id=\"referrer_id\" class=\"form-control\">
									$RefOpt
								</select>
							</div>
						</div>
						<div class=\"form-group row\">
							<div class=\"col-xs-6 col-sm-4 col-md-2\">
								<label for=\"dealers_id\" data-placement=\"bottom\">Project Manager<span class=\"required\">*</span></label>
							</div>
							<div class=\"col-xs-6 col-sm-8 col-md-4\">
								<select required name=\"dealers_id\" id=\"dealers_id\" class=\"form-control\">
									$DeaOpt
								</select>
							</div>
						</div>
						<div class=\"form-group row\">
							<div class=\"col-xs-6 col-sm-4 col-md-2\">
								<label for=\"payment_type\" data-placement=\"bottom\">Payment Type<span class=\"required\">*</span></label>
							</div>
							<div class=\"col-xs-6 col-sm-8 col-md-4\">
								<select name=\"payment_type\" id=\"payment_type\" class=\"form-control\">
									$PayOpts
								</select>
							</div>
						</div>
						<!--div class=\"form-group row\">
							<div class=\"col-xs-6 col-sm-4 col-md-2\">
								<label for=\"distributors_id\" data-placement=\"bottom\">Distributors<span class=\"required\">*</span></label>
							</div>
							<div class=\"col-xs-6 col-sm-8 col-md-4\">
								<select name=\"distributors_id\" id=\"distributors_id\" class=\"form-control\">
								$DisOpt
								</select>
							</div>
						</div-->
						<div class=\"form-group\">
							<div class=\"col-xs-12\" align=\"center\">
								<input type=\"hidden\" name=\"pos_id\" id=\"pos_id\" value=\"0\">
								<input type=\"button\" class=\"btn btn-default\" id=\"cancelbutton\" onClick=\"gotoprevpage('/Orders/lists');\" value=\"Cancel\" />
								<input class=\"btn btn-success mleft10\" name=\"submit\" id=\"submit\" type=\"submit\" value=\"Add\">
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<script src=\"/assets/js/a_m12045.js\"></script>
		<script src=\"/assets/js/n_z0324.js\"></script>
		";
		
		return $htmlStr;
	}
	
	/**
	 * new
	 */
	public function edit($segment3, $segment4){
		
		$prod_cat_man = $_SESSION['prod_cat_man']??0;
		$accounts_id = '4484'; //$_SESSION['accounts_id']??0;
		$user_id = $_SESSION['user_id']??0;
		$currency = $_SESSION["currency"]??'$';
		$dateformat = $_SESSION["dateformat"]??'m/d/Y';
		$timeformat = $_SESSION["timeformat"]??'12 hour';
		$segment1 = $segment1 = $GLOBALS['segment1'];  
		$segment2 = $segment2 = $GLOBALS['segment2']; 
		$title = "Create Order";
		$Payments = new Payments($this->db);		
		// var_dump($Payments);exit;

		$htmlStr = "";	
		$invoice_no = intval($segment3);
		// echo $segment2name;exit;
		$posObj = $this->db->query("SELECT * FROM pos WHERE invoice_no = :invoice_no AND accounts_id = $accounts_id AND pos_type = 'Order'", array('invoice_no'=>$invoice_no),1);
		if($posObj){

			// var_dump($posObj);exit;

			$Common = new Common($this->db);			

			$pos_onerow = $posObj->fetch(PDO::FETCH_OBJ);

			// var_dump($pos_onerow);exit;


			$pos_id = $pos_onerow->pos_id;


			$order_status = $pos_onerow->order_status;
			// if($order_status==2){
			// 	return "<meta http-equiv = \"refresh\" content = \"0; url = /Orders/\" />";
			// }


			$customers_id = $pos_onerow->customers_id;
			$customerObj = false;
			if($customers_id>0){
				
				$customerObj = $this->db->query("SELECT customers_id, customers_publish, accounts_id, user_id, first_name, last_name, contact_no, CONCAT(first_name, ' ', last_name) as name, email, company, contact_no as phone, marketing_person_id, customer_type, shipping_address_one as address FROM customers WHERE customers_id = $customers_id", array());
				if($customerObj){
					$customerrow = $customerObj->fetch(PDO::FETCH_OBJ);					
				}
				// echo "cust";exit;
				// var_dump($customerrow);exit;
			}
			
			$customFields = 0;
			$queryObj = $this->db->query("SELECT COUNT(custom_fields_id) AS totalrows FROM custom_fields WHERE accounts_id = $prod_cat_man AND field_for = 'customers'", array());
			if($queryObj){
				$customFields = $queryObj->fetch(PDO::FETCH_OBJ)->totalrows;
			}

			

			$employee_id = $pos_onerow->employee_id;

			$payment_type = '';
			$payment_type_id = $pos_onerow->payment_type;
			$payArray = array("onetime"=>"One Time","installment"=>"Installment", "subscription"=>"Subscription");
			// echo $payment_type_id;exit;
			if(array_key_exists($payment_type_id, $payArray)){
				$payment_type = $payArray[$payment_type_id];
			}

			// var_dump($employee_id);exit;

			$salName = '';
			if($employee_id>0)
				$salName = trim(stripslashes($Common->getOneRowFields('users', array('users_id'=>$employee_id), array('users_first_name', 'users_last_name'))));
			
			// $vehicle_driver_id = $pos_onerow->vehicle_driver_id;
			// $vehDriName = '';
			// if($vehicle_driver_id>0){
			// 	$tableObj = $this->db->query("SELECT vehicle, driver, carrier FROM vehicle_driver WHERE vehicle_driver_id = $vehicle_driver_id", array());
			// 	if($tableObj){
			// 		$tableOneRow = $tableObj->fetch(PDO::FETCH_OBJ);
			// 		$vehDriName = trim(stripslashes("$tableOneRow->vehicle, $tableOneRow->driver, $tableOneRow->carrier"));
			// 	}
			// }
			

			$referrer_id = $pos_onerow->referrer_id;
			$refName = '';
			if($referrer_id>0)
				$refName = trim(stripslashes($Common->getOneRowFields('referrer', array('referrer_id'=>$referrer_id), array('name'))));
			


			$dealers_id = $pos_onerow->dealers_id;
			$deaName = '';
			if($dealers_id>0)
				$deaName = trim(stripslashes($Common->getOneRowFields('users', array('users_id'=>$dealers_id), array('users_first_name', 'users_last_name'))));
			

			$distributors_id = $pos_onerow->distributors_id;
			$disName = '';
			if($distributors_id>0)
				$disName = trim(stripslashes($Common->getOneRowFields('distributors', array('distributors_id'=>$distributors_id), array('name'))));
			


			$customername = $customeremail = $offers_email = $customerphone = $customeraddress = $editcustomers = '';
			$available_credit = $prevDues = 0;
			if($customerObj){
				$customers_id = $customerrow->customers_id;
				$first_name = $customerrow->first_name;
				$last_name = $customerrow->last_name;
				$customername = stripslashes($first_name).' '.stripslashes($last_name);
				$customeremail = $customerrow->email;
				$offers_email = $customerrow->offers_email;
				$customerphone = $customerrow->contact_no;
				$credit_limit = $customerrow->credit_limit;
				if($credit_limit>0){
					$availCreditData = $Common->calAvailCr($customers_id, $credit_limit, 1);
					if(array_key_exists('available_credit', $availCreditData)){
						$available_credit = $availCreditData['available_credit'];
					}
				}
				// $prevDues = $credit_limit-$available_credit;
				$editcustomers = "<button class=\"btn btn-default floatright mtop-1 pright10 invoiceorcompleted\" onClick=\"AJget_CustomersPopup($customers_id, $customFields);\">Edit</button>";
			}
			// var_dump($customerObj);exit;


			
			$onclick = "AJget_ProductsPopup('Orders', 0, 0);";
			if(!empty($_SESSION["allowed"]) && !array_key_exists('Products', $_SESSION["allowed"])) {
				$onclick = "noPermissionWarning('Product');";
			}															
			
			$no_of_result_rows = $no_of_default_rows = 0;
			$option1 = $option2 = '';
			$taxes_name1 = $pos_onerow->taxes_name1;
			$taxes_percentage1 = $pos_onerow->taxes_percentage1;
			$tax_inclusive1 = $pos_onerow->tax_inclusive1;
			$taxes_name2 = $pos_onerow->taxes_name2;
			$taxes_percentage2 = $pos_onerow->taxes_percentage2;
			$tax_inclusive2 = $pos_onerow->tax_inclusive2;
			$transport = $pos_onerow->transport;
			$display = '';
			$taxesObj = $this->db->query("SELECT * FROM taxes WHERE accounts_id = $accounts_id AND taxes_publish = 1 ORDER BY taxes_name ASC", array());
			if($taxesObj){
				$no_of_result_rows = $taxesObj->rowCount();
				while($taxesonerow = $taxesObj->fetch(PDO::FETCH_OBJ)){                                            
					$taxes_id = $taxesonerow->taxes_id;
					$staxes_name = $taxesonerow->taxes_name;
					$staxes_percentage = $taxesonerow->taxes_percentage;
					$stax_inclusive = $taxesonerow->tax_inclusive;
					
					$default_tax = $taxesonerow->default_tax;
					if($default_tax>0){
						$no_of_default_rows++;
					}
					$selected1 = '';
					$selected2 = '';
					if($taxes_name1==''){
						$taxes_name1 = $staxes_name;
						$taxes_percentage1 = $staxes_percentage;
						$tax_inclusive1 = $stax_inclusive;
						if($no_of_default_rows==1){
							$taxes_name1 = $staxes_name;
							$taxes_percentage1 = $staxes_percentage;
							$selected1 = ' selected="selected"';
						}
						
						if($no_of_default_rows==2){
							$taxes_name2 = $staxes_name;
							$taxes_percentage2 = $staxes_percentage;
							$tax_inclusive2 = $stax_inclusive;
							$selected2 = ' selected="selected"';
						}
					}
					else{
						if(strcmp($taxes_name1, $staxes_name)==0){
							$selected1 = ' selected="selected"';
						}
						if(strcmp($taxes_name2, $staxes_name)==0){
							$selected2 = ' selected="selected"';
						}
					}
					$tiStr = '';
					if($stax_inclusive>0){$tiStr = ' Inclusive';}
					
					$option1 .= "<option$selected1 value=\"$taxes_id\">$staxes_name ($staxes_percentage%$tiStr)</option>";
					$option2 .= "<option$selected2 value=\"$taxes_id\">$staxes_name ($staxes_percentage%$tiStr)</option>";
				}
			}
			else{
				$display = ' style="display:none"';
			}
			



			if($timeformat=='24 hour'){$payment_datetime =  date('Y-m-d H:i');}
			else{$payment_datetime =  date('Y-m-d g:i a');}

			$paymentgetwayarray = array();
			$vData = $Common->variablesData('payment_options', $accounts_id);
			if(!empty($vData)){
				extract($vData);
				$paymentgetwayarray = explode('||',$payment_options);
			}


			$metOpt = '';
			if(!empty($paymentgetwayarray)){
				foreach($paymentgetwayarray as $onePayOption){
					$onePayOption = trim($onePayOption);
					if($onePayOption !=''){
						$metOpt .= "<option value=\"$onePayOption\">$onePayOption</option>";
					}
				}
			}

			$statusOpt = '';
			$paymentstatusarray = array("Draft", "Paid", "Unpaid", "Cancel");
			if(!empty($paymentstatusarray)){
				foreach($paymentstatusarray as $oneStatusOption){
					$oneStatusOption = trim($oneStatusOption);
					if($oneStatusOption !=''){
						$statusOpt .= "<option value=\"$oneStatusOption\">$oneStatusOption</option>";
					}
				}
			}

			
			$multiple_cash_drawers = 0;
			$cash_drawers = '';
			$cdArray = array();
			$cdData = $Common->variablesData('multiple_drawers', $accounts_id);
			if(!empty($cdData)){
				extract($cdData);
				$cdArray = explode('||',$cash_drawers);
			}
			$drawer = isset($_COOKIE['drawer'])?$_COOKIE['drawer']:'';
			$draOpt = '';
			if($multiple_cash_drawers>0 && !empty($cdArray)){
				$draOpt .= '<select class="form-control" name="drawer" id="drawer" onChange="setCD();">';
				if($drawer==''){
					$draOpt .= "<option value=\"\">$GLOBALS[_Select_Drawer]</option>";
				}
				foreach($cdArray as $oneCDOption){
					$oneCDOption = trim($oneCDOption);
					if(!empty($oneCDOption)){
						$selected = '';
						if($oneCDOption==$drawer){$selected = ' selected';}
						$draOpt .= "<option$selected value=\"$oneCDOption\">$oneCDOption</option>";
					}
				}
				$draOpt .= '</select>';
			}
			else{
				$multiple_cash_drawers = 0;
				$draOpt .= '<input type="hidden" name="drawer" id="drawer" value="">';
			}


			
			$returnURL = "http://$GLOBALS[subdomain].".OUR_DOMAINNAME."/Orders/edit/$invoice_no";
			$activation_code = '';
			$varObj = $this->db->query("SELECT value FROM variables WHERE accounts_id = $accounts_id AND name = 'vantivcc' AND value !=''", array());
			if($varObj){
				$value = $varObj->fetch(PDO::FETCH_OBJ)->value;
				if(!empty($value)){
					$value = unserialize($value);
					extract($value);
				}
			}
			$sqrup_currency_code = '';
			$varObj = $this->db->query("SELECT * FROM variables WHERE accounts_id = $accounts_id AND name = 'cr_card_processing' AND value !=''", array());
			if($varObj){
				$variablesData = $varObj->fetch(PDO::FETCH_OBJ);
				$value = $variablesData->value;
				if(!empty($value)){
					$value = unserialize($value);
					extract($value);
				}
			}


			
			$webcallbackurl = '';
			if(OUR_DOMAINNAME=='bditsoft.com'){
				$webcallbackurl = 'demo.';
			}
			$webcallbackurl .= OUR_DOMAINNAME;
			
			$ashbdclass = '';
			if($available_credit>0){
				$ashbdclass = ' class="bgtitle"';
			}
			
			$default_invoice_printer = 'Small';
			$varObj = $this->db->query("SELECT value FROM variables WHERE accounts_id = $accounts_id AND name = 'invoice_setup'", array());
			if($varObj){
				
				$value = $varObj->fetch(PDO::FETCH_OBJ)->value;		
				// var_dump($value);exit;			
				if(!empty($value)){
					// $value = (array) json_decode($value); //unserialize($value);
					$value = array (
						'invoice_backup_email' => 'imranmailbd@gmail.com',
						'default_invoice_printer' => 'Large',
						'logo_size' => 'Small Logo',
						'logo_placement' => 'Center',
						'title' => 'SK Soft Solutions Inc.',
						'company_info' => 'House # B/153, Road # 22, New DOHS, Mohakhali, Dhaka-1206
					  Mobile: +88 019 1171 8043 . Email: info@skitsbd.com',
						'customer_name' => '1',
						'customer_address' => '1',
						'customer_phone' => '1',
						'secondary_phone' => 0,
						'customer_email' => 0,
						'customer_type' => 0,
						'sales_person' => 0,
						'barcode' => 0,
						'invoice_message_above' => '',
						'print_price_zero' => '1',
						'invoice_message' => '',
						'notes' => 0,
					);
					// var_dump($value);exit;	
					if(array_key_exists('default_invoice_printer', $value)){
						$default_invoice_printer = $value['default_invoice_printer'];
						if($default_invoice_printer=='' || is_null($default_invoice_printer)){$default_invoice_printer = 'Small';}
					}
				}
			}
			// var_dump($pos_id);exit;
			$Carts = new Carts($this->db);
			$cartsHTML = $Carts->loadCartData('Orders', $pos_id);
			// echo $cartsHTML;exit;

			$htmlStr .= "<div class=\"row\">
				<div class=\"col-sm-4\">
					<h1 class=\"singin2\">$title</h1>
				</div>
				<div class=\"col-sm-1 ptopbottom15\">&nbsp;</div> 
				<div class=\"col-sm-7\">            
					<div class=\"floatright mleft15 ptopbottom15\">
						<!-- Split button -->
						<div class=\"btn-group pull-right\">              
							<button type=\"button\" class=\"bg-none btn btn-default dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
								<label><i class=\"fa fa-print\"></i> Print &nbsp;&nbsp;<span class=\"caret txtblack\"></span></label>
								<span class=\"sr-only\">Toggle Dropdown</span>
							</button>
							<ul class=\"dropdown-menu txtleft\">
								<li>
									<a href=\"javascript:void(0);\" onClick=\"printbyurl('/Orders/prints/large/$pos_id');\" title=\"Full Page Printer\">
										Full Page Printer
									</a>
								</li>
								<li role=\"separator\" class=\"divider\"></li>
								<li>
									<a href=\"javascript:void(0);\" onClick=\"printbyurl('/Orders/prints/small/$pos_id');\" title=\"Thermal Printer\">
										Thermal Printer
									</a>
								</li>
								<li role=\"separator\" class=\"divider\"></li>
								<li>
									<a href=\"javascript:void(0)\" onclick=\"emailthispage();\" title=\"Email Order\">
										Email Order
									</a>
								</li>
								<li role=\"separator\" class=\"divider\"></li>
								<li>
									<a href=\"javascript:void(0);\" title=\"Print Pick List\" onClick=\"printbyurl('/Orders/prints/pick/$pos_id');\">
										Print Pick List
									</a>
								</li>
							</ul>
						</div>
					</div>
					<div class=\"floatright mleft15 ptopbottom15\">
						<button onClick=\"javascript:window.location = '/Orders/lists'\" class=\"btn btn-default cursor\"><i class=\"fa fa-list\"></i> List Orders</button>
					</div>                
					<div class=\"clear\"></div>
					 <div class=\"floatright\">                    
						<form method=\"post\" name=\"frmSendOrdersEmail\" id=\"frmSendOrdersEmail\" enctype=\"multipart/form-data\" action=\"#\" onSubmit=\"return emaildetails('/Orders/AJsend_OrdersEmail');\">
							<table align=\"center\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"10\">
							  <tr>
								<td colspan=\"2\">
									<div id=\"showerrormessage\"></div>
									<div id=\"showsuccessmessage\"></div>
								</td>
							  </tr>
							  <tr class=\"emailform\" style=\"display:none\">
								<td class=\"pbottom15\">
									<input type=\"email\" required name=\"email_address\" id=\"email_address\" class=\"form-control\" value=\"$customeremail\" maxlength=\"50\" />
								</td>
								<td class=\"pbottom15\" width=\"170\" align=\"left\" valign=\"middle\" nowrap>
									<input type=\"submit\" class=\"btn btn-success sendbtn\" value=\" Email \" />
									<input type=\"button\" class=\"btn hilightbutton2\" onClick=\"cancelemailform();\" value=\" Cancel \" />
								</td>
							  </tr>
							</table> 
						</form>
					</div>
				</div>
			</div>        
			<div class=\"row\">        
			  <div class=\"col-xs-12 col-sm-4\">
				<div class=\"widget mbottom10\">
					<div class=\"widget-header\">
						<i class=\"fa fa-user\"></i>
						<h3>
							Customer info
						</h3>
						$editcustomers  
					</div>
					<div class=\"widget-content\" id=\"customer_information\">                
						<ul class=\"list-unstyled labelfixedlist label100\">
							<li>
								<label>Customer ID: </label>
								<a class=\"txtunderline txtblue\" href=\"/Customers/view/$customers_id\" title=\"View Customer Details\">$customers_id <i class=\"fa fa-link\"></i></a>
							</li>
							<li>
								<label>Customer: </label>$customername
							</li>
							<li>
								<label>Email: </label><span id=\"customeremail\">$customeremail</span>
							</li>
							<li>
								<label>Phone No: </label>$customerphone
							</li>
						</ul>
				  </div> <!-- /widget-content -->
				</div> <!-- /widget -->
			  </div>   
			  
			  
			  <div class=\"col-xs-12 col-sm-8\">
				 <div class=\"widget mbottom10\">                
					<div class=\"widget-header\">
						<i class=\"fa fa-mobile\"></i>
						<h3>Order Info</h3>
						<div class=\"floatright mtop-1 pright10 invoiceorcompleted\"><a href=\"javascript:void(0);\" onClick=\"changeOrderInfo('$invoice_no', '$pos_onerow->employee_id', '".date('Y-m-d', strtotime($pos_onerow->sales_datetime))."', $pos_onerow->referrer_id, $pos_onerow->dealers_id, $pos_onerow->distributors_id);\" class=\"btn btn-default\">Edit</a></div>
					</div> <!-- /widget-header -->
					<div class=\"widget-content\" id=\"order_info\">
						<div class=\"row pbottom15\">
							<div class=\"col-sm-2 txtright txt14bold\">Invoice No.:</div>
							<div class=\"col-sm-4 txt16bold\">o$invoice_no</div>
							<div class=\"col-sm-2 txtright txt14bold\">Sales Person:</div>
							<div class=\"col-sm-4\">$salName</div>
						</div>
						<div class=\"row pbottom15\">
							<div class=\"col-sm-2 txtright txt14bold\">Referrer:</div>
							<div class=\"col-sm-4\">$refName</div>
							<div class=\"col-sm-2 txtright txt14bold\">Project Manager:</div>
							<div class=\"col-sm-4\">$deaName</div>
						</div>
						<div class=\"row pbottom15\">
							<div class=\"col-sm-2 txtright txt14bold\">Date:</div>
							<div class=\"col-sm-4\">".date(str_replace('y', 'Y', $dateformat), strtotime($pos_onerow->sales_datetime))."</div>
							<div class=\"col-sm-2 txtright txt14bold\">Payment Type:</div>
							<div class=\"col-sm-4\">$payment_type</div>
						</div>
						<!--div class=\"row pbottom15\">
							<div class=\"col-sm-2 txtright txt14bold\">Date:</div>
							<div class=\"col-sm-4\">".date(str_replace('y', 'Y', $dateformat), strtotime($pos_onerow->sales_datetime))."</div>
							<!--div class=\"col-sm-2 txtright txt14bold\">Dealers:</div>
							<div class=\"col-sm-4\">$payment_type</div-->
						</div-->
						<!--div class=\"row pbottom15\">
							<div class=\"col-sm-2 txtright txt14bold\">Distributors:</div>
							<div class=\"col-sm-4\">$disName</div-->
						</div>
					</div> <!-- /widget-content -->
				</div>
			  </div>
			</div>  
			
			
			<div class=\"row\">    
				<div class=\"col-sm-12 prelative\" style=\"padding-left:35px;padding-right:35px;\">
					<div class=\"ibox-content padding0\">
						<div class=\"row\">
							<div class=\"col-sm-12\">                                    
								<table class=\"table table-bordered mbottom0 \" style=\"table-layout: fixed\">
									<thead>
										<tr>
											<th width=\"5%\" class=\"text-right\">#</th>
											<th width=\"46%\" >Description</th>
											<!--th width=\"20%\" class=\"text-right EstimateTitle\">Need/Have/OnPO</th-->
											<th width=\"10%\" class=\"text-right\">QTY</th>
											<!--th width=\"10%\" class=\"text-right\">Shipping Qty</th-->
											<th width=\"12%\" class=\"text-right\">Unit Price</th>
											<th width=\"15%\" class=\"text-right\">Total</th>
											<th width=\"12%\" class=\"txtcenter\"><i class=\"fa fa-trash-o\"></i></th>
										</tr>
									</thead>
									<tbody id=\"invoice_entry_holder\">$cartsHTML</tbody>
									<tbody>
										<tr>
											<td class=\"text-right\" id=\"barcodeserno\">1</td>
											<td>                                    
												<input type=\"hidden\" id=\"temp_pos_cart_id\" name=\"temp_pos_cart_id\" value=\"0\">
												<div class=\"input-group\">
													<input maxlength=\"50\" type=\"text\" id=\"search_sku\" name=\"search_sku\" class=\"form-control search_sku\" placeholder=\"Search by product name or SKU\">
													<span data-toggle=\"tooltip\" title=\"Add New Product\" class=\"input-group-addon cursor\" onClick=\"$onclick\">
														<i class=\"fa fa-plus\"></i> New
													</span>
												</div>                                            
												<span class=\"error_msg\" id=\"error_search_sku\"></span>
												<input type=\"hidden\" name=\"clickYesNo\" id=\"clickYesNo\" value=\"0\">
											</td>
											<td colspan=\"4\" class=\"txtleft\">											
												<!--button type=\"button\" name=\"showcategorylist\" id=\"product-picker-button\" onclick=\"showProductPicker();\" class=\"btn btn-default hilightbutton floatleft\">Open Product Picker</button>
												<button onclick=\"AJget_oneTimePopup(0);\" class=\"btn btn-secondary floatleft mleft15\">Add One Time Product</button-->
											</td>
										</tr>
										<tr>
											<td style=\"padding:0;\" colspan=\"8\">
												<span class=\"error_msg\" id=\"error_productlist\"></span>
												<input type=\"hidden\" autocomplete=\"off\" name=\"pagi_index\" id=\"pagi_index\" value=\"0\">
												<input type=\"hidden\" autocomplete=\"off\" name=\"ppcategory_id\" id=\"ppcategory_id\" value=\"0\">
												<input type=\"hidden\" autocomplete=\"off\" name=\"ppproduct_id\" id=\"ppproduct_id\" value=\"0\">
												<input type=\"hidden\" autocomplete=\"off\" name=\"totalrowscount\" id=\"totalrowscount\" value=\"0\">											
												<div class=\"width100per form-group\" id=\"filterrow\" style=\"display:none\">
													<div class=\"width280 floatleft mleft15\" id=\"filter_name_html\" style=\"display:none\">
														<div class=\"input-group\">
															<input maxlength=\"50\" type=\"text\" placeholder=\"Search name\" value=\"\" class=\"form-control product-filter\" name=\"filter_name\" id=\"filter_name\">
															<span class=\"input-group-addon cursor\" onClick=\"allproductlistcount();\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Search name\">
																<i class=\"fa fa-search\"></i>
															</span>
														</div>
													</div>
													<div class=\"floatright width120\">
														<label id=\"PPfromtodata\"></label>
													</div>
													<div class=\"width280 floatright mleft15\" id=\"all-category-button\" style=\"display:none\">
														<div class=\"input-group\">
															<a href=\"javascript:void(0);\" title=\"All Category List\" onClick=\"reloadProdPkrCategory();\">
																<span class=\"input-group-addon cursor hilightbutton2\">
																	<label>All Category List</label>
															   </span>
															</a>
														</div>
													</div>
												</div>
												<div class=\"width100per prelative\">
													<div class=\"col-sm-12 minheight270\" id=\"product-picker\" style=\"display:none\">
														<div id=\"allcategorylist\" style=\"display:none\"></div>
														<div style=\"display:none\" id=\"allproductlist\"></div>
													</div>												
													<div class=\"prevlist\" style=\"display:none\">
														<button type=\"button\">‹</button>
													</div>
													<div class=\"nextlist\" style=\"display:none\">
														<button type=\"button\">›</button>
													</div>
												</div>
											</td>
										</tr>
										<tr $display>
											<td colspan=\"7\" class=\"bgtitle\" align=\"right\">
												<div class=\"width150 floatright txt14bold\" id=\"taxable_totalstr\">$currency"."0.00</div>
												<div class=\"width200 floatright txt14bold\">Taxable Total :</div>
												<input type=\"hidden\" name=\"taxable_total\" id=\"taxable_total\" value=\"0\" />
											</td>
											<td class=\"bgtitle\">&nbsp;</td>
										</tr>";
										
										if($no_of_result_rows>0){
											if($no_of_result_rows==1){
												$htmlStr .= "<tr>
													<td colspan=\"7\" class=\"txtright\">													
														<input type=\"hidden\" name=\"taxes_name1\" id=\"taxes_name1\" value=\"$taxes_name1\" />
														<input type=\"hidden\" name=\"taxes_percentage1\" id=\"taxes_percentage1\" value=\"$taxes_percentage1\" />
														<input type=\"hidden\" name=\"tax_inclusive1\" id=\"tax_inclusive1\" value=\"$tax_inclusive1\" />
														<input type=\"hidden\" name=\"taxes_total1\" id=\"taxes_total1\" value=\"0\" />
														<div class=\"width150 floatright txt14bold\" id=\"taxes_total1str\">$currency"."0.00</div>
														<div class=\"width200 floatright txt14bold\">$taxes_name1 ($taxes_percentage1%):</div>													
														<input type=\"hidden\" name=\"taxes_name2\" id=\"taxes_name2\" value=\"\" />												
														<input type=\"hidden\" name=\"taxes_percentage2\" id=\"taxes_percentage2\" value=\"0\" />
														<input type=\"hidden\" name=\"tax_inclusive2\" id=\"tax_inclusive2\" value=\"0\" />
														<input type=\"hidden\" name=\"taxes_total2\" id=\"taxes_total2\" value=\"0\" />
														<b class=\"hidden\" id=\"taxes_total2str\">$currency"."0.00</b>													
													</td>
													<td>&nbsp;</td>
												</tr>";
											}
											else{
												$tax1 = '';
												if($no_of_default_rows>1){$tax1 = '1';}
												 $htmlStr .= "<tr>
													<td colspan=\"7\" class=\"txtright\">
														<div class=\"width150 floatright txt14bold ptop5\" id=\"taxes_total1str\">
															$currency"."0.00
														</div>                                                
														<div class=\"width200 floatright txt14bold mleft15\">
															<select id=\"taxes_id1\" name=\"taxes_id1\" class=\"form-control taxes_id\" title=\"1\" onChange=\"onChangeTaxesId(1);\">
																$option1
															</select>
														</div>
														<div class=\"width200 floatright txt14bold ptop5\">
															Tax.$tax1 :
														</div>
														<input type=\"hidden\" name=\"taxes_total1\" id=\"taxes_total1\" value=\"0\" />
														<input type=\"hidden\" name=\"taxes_name1\" id=\"taxes_name1\" value=\"$taxes_name1\" />
														<input type=\"hidden\" name=\"taxes_percentage1\" id=\"taxes_percentage1\" value=\"$taxes_percentage1\" />
														<input type=\"hidden\" name=\"tax_inclusive1\" id=\"tax_inclusive1\" value=\"$tax_inclusive1\" />
														<span class=\"error_msg\" id=\"errmsg_taxes_id1\"></span>
													</td>
													<td>&nbsp;</td>
												</tr>";
												
												if($no_of_default_rows>1){
													 $htmlStr .= "<tr>
														<td colspan=\"7\" class=\"txtright\">
															<div class=\"width150 floatright txt14bold ptop5\" id=\"taxes_total2str\">
																$currency"."0.00
															</div>                                                
															<div class=\"width200 floatright txt14bold mleft15\">
																<select id=\"taxes_id2\" name=\"taxes_id2\" class=\"form-control taxes_id\" title=\"2\" onChange=\"onChangeTaxesId(2);\">
																	<option value=\"0\"></option>
																	$option2
																</select>
															</div>
															<div class=\"width200 floatright txt14bold ptop5\">
																Tax2 :
															</div>
															<input type=\"hidden\" name=\"taxes_total2\" id=\"taxes_total2\" value=\"0\" />
															<input type=\"hidden\" name=\"taxes_name2\" id=\"taxes_name2\" value=\"$taxes_name2\" />
															<input type=\"hidden\" name=\"taxes_percentage2\" id=\"taxes_percentage2\" value=\"$taxes_percentage2\" />
															<input type=\"hidden\" name=\"tax_inclusive2\" id=\"tax_inclusive2\" value=\"$tax_inclusive2\" />
															<span class=\"error_msg\" id=\"errmsg_taxes_id2\"></span>
														</td>
														<td>&nbsp;</td>
													</tr>";
												}
												else{
													$htmlStr .= "<input type=\"hidden\" name=\"taxes_name2\" id=\"taxes_name2\" value=\"\" />												
															<input type=\"hidden\" name=\"taxes_percentage2\" id=\"taxes_percentage2\" value=\"0\" />
															<input type=\"hidden\" name=\"tax_inclusive2\" id=\"tax_inclusive2\" value=\"0\" />
															<input type=\"hidden\" name=\"taxes_total2\" id=\"taxes_total2\" value=\"0\" />
															<b class=\"hidden\" id=\"taxes_total2str\">$currency"."0.00</b>";
												}
											}
										}
										else{
											 $htmlStr .= "<input type=\"hidden\" name=\"taxes_name1\" id=\"taxes_name1\" value=\"\" />
												<input type=\"hidden\" name=\"taxes_percentage1\" id=\"taxes_percentage1\" value=\"0\" />
												<input type=\"hidden\" name=\"tax_inclusive1\" id=\"tax_inclusive1\" value=\"0\" />                                   			
												<input type=\"hidden\" name=\"taxes_total1\" id=\"taxes_total1\" value=\"0\" />
												<b class=\"hidden\" id=\"taxes_total1str\"></b>                                            
												<input type=\"hidden\" name=\"taxes_name2\" id=\"taxes_name2\" value=\"\" />												
												<input type=\"hidden\" name=\"taxes_percentage2\" id=\"taxes_percentage2\" value=\"0\" />
												<input type=\"hidden\" name=\"tax_inclusive2\" id=\"tax_inclusive2\" value=\"0\" />
												<input type=\"hidden\" name=\"taxes_total2\" id=\"taxes_total2\" value=\"0\" />
												<b class=\"hidden\" id=\"taxes_total2str\">$currency"."0.00</b>";
										}
										
										$paymentHTML = $Payments->loadPOSPayment('Orders', $pos_id);
										$htmlStr .= "<tr id=\"nontaxable_totalrow\" style=\"display:none\">
											<td colspan=\"5\" align=\"right\">
												<div class=\"width150 floatright txt14bold\" id=\"nontaxable_totalstr\">$currency"."0.00</div>
												<div class=\"width200 floatright txt14bold\">Non Taxable Total :</div>
												<input type=\"hidden\" name=\"nontaxable_total\" id=\"nontaxable_total\" value=\"0\" />
											</td>
											<td>&nbsp;</td>
										</tr>
										<tr>
											<td colspan=\"5\" align=\"right\" class=\"bgtitle\">
												<div class=\"width150 floatright txt14bold\" id=\"grand_totalstr\">$currency"."0.00</div>
												<div class=\"width200 floatright txt14bold\">Grand Total :</div>
												<input type=\"hidden\" name=\"grand_total\" id=\"grand_total\" value=\"0\" />
											</td>
											<td class=\"bgtitle\">&nbsp;</td>
										</tr>
										<tr style=\"display:none\">
											<td colspan=\"6\" align=\"right\" class=\"bgtitle\" style=\"display:none\">
												<div class=\"width150 floatright txt14bold\">
													<input maxlength=\"12\" type=\"text\" value=\"$transport\" name=\"transport\" id=\"transport\" class=\"pricefield txt14bold txtright form-control\" onBlur=\"updatePOSTable('transport');\">
												</div>
												<div class=\"width200 floatright txt14bold pright20\">Transport/Loading Bill :</div>
											</td>
											<td class=\"bgtitle\">&nbsp;</td>
										</tr>
										<tr style=\"display:none\">
											<td colspan=\"5\" align=\"right\" class=\"bgtitle\">
												<div class=\"width150 floatright txt14bold\" id=\"prevDuesStr\">$currency".number_format($prevDues,2)."</div>
												<div class=\"width200 floatright txt14bold\">Previous Dues :</div>
												<input type=\"hidden\" name=\"prevDues\" id=\"prevDues\" value=\"$prevDues\" />
											</td>
											<td class=\"bgtitle\">&nbsp;</td>
										</tr>
										<tr>
											<td colspan=\"2\">
												&nbsp;
											</td>
											<td colspan=\"4\" class=\"txt16bold bgblack\">
												Take payment
											</td>
										</tr>                                                         
									 </tbody>
									 <tbody id=\"loadPOSPayment\">$paymentHTML</tbody>
									 <tbody>
										<tr >
											<td colspan=\"5\" align=\"right\" id=\"loadPOSPaymentEdit\">
												<!--input type=\"text\" class=\"form-control DateField floatleft width150\" readonly value=\"$payment_datetime\" required name=\"payment_datetime\" id=\"payment_datetime\" /-->
												<div class=\"width120 floatright txt14bold\">
													<input maxlength=\"12\" type=\"text\" value=\"0\" name=\"amount\" id=\"amount\" class=\"pricefield txt14bold txtright form-control\" onKeyUp=\"checkMethod();\">
												</div>
												<div class=\"width20 floatright txt14bold mleft10 ptop5\">
													$currency
												</div>
												<div class=\"width150 floatright txt14bold mleft10\">
													<select class=\"form-control\" name=\"method\" id=\"method\" onChange=\"checkMethod();\">
														$metOpt
													</select>
												</div>
												<div class=\"width80 floatright txt14bold ptop5\">Type :</div>

												<div class=\"width150 floatright txt14bold mleft10\">
													<select class=\"form-control\" name=\"status\" id=\"status\">
														$statusOpt
													</select>
												</div>
												<div class=\"width120 floatright txt14bold ptop5\">Pay Status :</div>
												
												
												<input type=\"text\" class=\"form-control DateField floatright width150\" value=\"$payment_datetime\" required name=\"payment_datetime\" id=\"payment_datetime\" />
												<div class=\"width20 floatright txt14bold mleft10 ptop5\">
													Pay Date : &nbsp;
												</div>
												
												<div class=\"width150 floatright txt14bold mleft10\">
													$draOpt
													<input type=\"hidden\" name=\"multiple_cash_drawers\" id=\"multiple_cash_drawers\" value=\"$multiple_cash_drawers\">
													<input type=\"hidden\" name=\"returnURL\" id=\"returnURL\" value=\"http://$GLOBALS[subdomain].".OUR_DOMAINNAME."/POS/index/edit/\">
													<input type=\"hidden\" name=\"activation_code\" id=\"activation_code\" value=\"$activation_code\">
													<input type=\"hidden\" name=\"sqrup_currency_code\" id=\"sqrup_currency_code\" value=\"$sqrup_currency_code\">
													<input type=\"hidden\" name=\"webcallbackurl\" id=\"webcallbackurl\" value=\"$webcallbackurl\">
													<input type=\"hidden\" name=\"accounts_id\" id=\"accounts_id\" value=\"$accounts_id\">
													<input type=\"hidden\" name=\"user_id\" id=\"user_id\" value=\"$user_id\">
												</div>
												<span id=\"error_amount\" class=\"errormsg\"></span>
											</td>
											<td id=\"buttonPayment\"></td>
										</tr>                             
										<tr>
											<td colspan=\"2\">&nbsp;</td>
											<td class=\"bgtitle\" colspan=\"2\" align=\"right\"><label for=\"amount_due\" id=\"amount_duetxt\">Amount Due :</label></td>
											<td class=\"bgtitle\" align=\"right\">
												<label id=\"amountduestr\">$currency"."0.00</label>
												<input type=\"hidden\" name=\"amount_due\" id=\"amount_due\" value=\"\">
												<input type=\"hidden\" name=\"changemethod\" id=\"changemethod\" value=\"Cash\">
												<input type=\"hidden\" name=\"available_credit\" id=\"available_credit\" value=\"$available_credit\">
											</td>
											<td class=\"bgtitle\">&nbsp;</td>
										</tr>";
										
										if($available_credit>0){
											 $htmlStr .= "<tr>
												<td colspan=\"2\">&nbsp;</td>
												<td colspan=\"4\" align=\"right\">
													<label>Customer has available credit of :</label>
												</td>
												<td align=\"right\">
													<label>$currency".number_format($available_credit,2)."</label>
												</td>
												<td>&nbsp;</td>
											</tr>";
										}
										
										$htmlStr .= "<tr>
											<td colspan=\"2\">&nbsp;</td>
											<td$ashbdclass colspan=\"3\">                                            
												<div class=\"floatright\">
													<input type=\"hidden\" id=\"pos_id\" name=\"pos_id\" value=\"$pos_id\">
													<input type=\"hidden\" name=\"invoice_no\" id=\"invoice_no\" value=\"$invoice_no\">
													<input type=\"hidden\" name=\"customers_id\" id=\"customers_id\" value=\"$customers_id\">
													<input type=\"hidden\" name=\"completed\" id=\"completed\" value=\"0\">
													<input type=\"hidden\" name=\"frompage\" id=\"frompage\" value=\"$segment1\">
													<input type=\"hidden\" name=\"default_invoice_printer\" id=\"default_invoice_printer\" value=\"$default_invoice_printer\">
													<div class=\"input-group\">  
														<div name=\"CompleteBtn\" id=\"CompleteBtn\"  class=\"bgnone  cursor\">
															<span class=\"input-group-addon cursor greenbutton ptopbottom15\" onClick=\"check_frm_orders();\">
																<i class=\"fa fa-money fa-2\"></i>
															</span>
															<span class=\"input-group-addon cursor greenbutton ptopbottom15 pleft0\" onClick=\"check_frm_orders();\">
																<label>Complete</label>
															</span>
														</div>
														<div name=\"CompleteBtnDis\" id=\"CompleteBtnDis\" class=\"bgnone hidediv\">
															<span class=\"input-group-addon ptopbottom15\">
																<i class=\"fa fa-money fa-2\"></i>
															</span>
															<span class=\"input-group-addon ptopbottom15 pleft0\">
																<label>Complete</label>
															</span>
														</div>
													</div>
												</div>                                            
												<div id=\"status_cancelled\" class=\"floatright marginright15\">
													<div class=\"input-group\">                       
														<a href=\"javascript:void(0);\" onClick=\"cancelOrder();\">
															<span class=\"input-group-addon cursor hilightbutton pright0 ptopbottom15\">
																<i class=\"fa fa-remove fa-2\"></i>
															</span>
															<span class=\"input-group-addon cursor hilightbutton ptopbottom15\">
																<label> Cancel Order </label>
														   </span>
														</a>
													</div>
												</div>
											</td>
											<td$ashbdclass>&nbsp;</td>
										</tr>
									</thead>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>


			<link href=\"/assets/css/jqueryimageupload.css\" rel=\"stylesheet\" type=\"text/css\">
			<script type=\"text/javascript\" src=\"/assets/js/jquery.form.min.js\"></script>
			<div class=\"row\">
				<div class=\"col-sm-12\" style=\"padding-left:35px;padding-right:35px;\">
					<div class=\"widget mbottom10\">";
					
			$list_filters = $_SESSION["list_filters"]??array();
			
			$spos_id = $list_filters['spos_id']??$pos_id;
			$history_type = $list_filters['shistory_type']??'';
			$this->pos_id = $spos_id;
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
			
			$htmlStr .= "<input type=\"hidden\" name=\"pageURI\" id=\"pageURI\" value=\"$GLOBALS[segment1]/$GLOBALS[segment2]/$GLOBALS[segment3]\">
						<input type=\"hidden\" name=\"page\" id=\"page\" value=\"$this->page\">
						<input type=\"hidden\" name=\"rowHeight\" id=\"rowHeight\" value=\"$this->rowHeight\">
						<input type=\"hidden\" name=\"totalTableRows\" id=\"totalTableRows\" value=\"$this->totalRows\">
						<input type=\"hidden\" name=\"note_forTable\" id=\"note_forTable\" value=\"pos\">
						<input type=\"hidden\" name=\"spos_id\" id=\"spos_id\" value=\"$pos_id\">
						<input type=\"hidden\" name=\"table_idValue\" id=\"table_idValue\" value=\"$pos_id\">
						<input type=\"hidden\" name=\"publicsShow\" id=\"publicsShow\" value=\"1\">
						<div class=\"widget-header\">
							<div class=\"row\">
								<div class=\"col-sm-4\" style=\"position:relative;\">
									<h3>Order History</h3>
								</div>
								<div class=\"col-sm-4\" style=\"position:relative;\">
									<select class=\"form-control mtop2\" name=\"shistory_type\" id=\"shistory_type\" onchange=\"checkAndLoadFilterData();\">
										$actFeeTitOpt
									</select>
								</div>
								<div class=\"col-sm-4\" style=\"position:relative;\">
									<div class=\"floatright mtop-1 pright10\"><a href=\"javascript:void(0);\" onClick=\"printbyuri('/$GLOBALS[segment1]/prints/large/$pos_id/signature');\" class=\"btn btn-default\">Add Digital Signature</a></div>
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
													<td class=\"titlerow\" align=\"left\" width=\"20%\">User</td>
													<td class=\"titlerow\" align=\"left\" width=\"20%\">Activit/td>
													<td class=\"titlerow\" align=\"left\">Details</td>
												</tr>
											</thead>
											<tbody id=\"tableRows\">$tableRows</tbody>
										</table>
									</div>
								</div>
							</div>
							<div class=\"row mtop10\">
								<div class=\"col-xs-12\">
									<select class=\"form-control width100 floatleft\" name=\"limit\" id=\"limit\" onChange=\"checkloadTableRows();\">
										<option value=\"auto\">Auto</option>
										$limitOpt
									</select>
									<label id=\"fromtodata\"></label>
									<div class=\"floatright\" id=\"Pagination\"></div>
								</div>
							</div>
						</div>					
					</div>
				</div>
			</div>
			<script src=\"/assets/js/a_m12045.js\"></script>
			<script src=\"/assets/js/n_z0324.js\"></script>";
		}
		
		return $htmlStr;
	}

	
	/**
	 * rm
	 */
	public function view($segment3, $segment4){
		$htmlStr = "";
		$ordersObj = $this->db->getObj("SELECT * FROM pos WHERE pos_id = :pos_id", array('pos_id'=>$segment3),1);
		if($ordersObj){
			$ordersarray = $ordersObj->fetch(PDO::FETCH_OBJ);
			$list_filters = $_SESSION["list_filters"]??array();
			$shistory_type = $list_filters['shistory_type']??'';
		
			$pos_id = $ordersarray->pos_id;
			$name = $ordersarray->name;
			$phone = $ordersarray->phone;
			$email = $ordersarray->email;
			$address = $ordersarray->address;

			$pos_publish = $ordersarray->pos_publish;
			$htmlStr = "<div class=\"row\">
				<div class=\"col-sm-8\">
					<h1 class=\"metatitle\">$GLOBALS[title] <i class=\"fa fa-info-circle txt16normal\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"This page displays the information of customer\"></i></h1>
				</div>
				<div class=\"col-sm-4 ptopbottom15\">
					<a href=\"/Orders/lists\" title=\"Orders List\">
						<button class=\"btn btn-default hilightbutton floatright\"><i class=\"fa fa-list\"></i> <strong>Orders List</strong></button>
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
								if($pos_publish>0){
									$htmlStr .= "<input type=\"button\" class=\"btn edit mbottom10 marginright15\" title=\"Edit\" onclick=\"AJget_OrdersPopup($pos_id);\" value=\"Edit\">
									<input type=\"button\" class=\"btn btn-default mbottom10 marginright15\" title=\"Merge Orders\" onclick=\"AJmergeOrdersPopup($pos_id);\" value=\"Merge Orders\">";
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
			
			$this->pos_id = $pos_id;
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
			
			$htmlStr .= "<input type=\"hidden\" name=\"pageURI\" id=\"pageURI\" value=\"$GLOBALS[segment1]/$GLOBALS[segment2]/$pos_id\">
						<input type=\"hidden\" name=\"page\" id=\"page\" value=\"$this->page\">
						<input type=\"hidden\" name=\"rowHeight\" id=\"rowHeight\" value=\"$this->rowHeight\">
						<input type=\"hidden\" name=\"totalTableRows\" id=\"totalTableRows\" value=\"$this->totalRows\">
						<input type=\"hidden\" name=\"note_forTable\" id=\"note_forTable\" value=\"orders\">
						<input type=\"hidden\" name=\"publicsShow\" id=\"table_idValue\" value=\"$pos_id\">
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
			$ordersData = array();
			$orderssql = "SELECT name, email, phone, address, pos_id, offers_email FROM pos WHERE pos_id != $pos_id AND pos_publish = 1";
			$ordersquery = $this->db->getObj($orderssql, array());
			if($ordersquery){
				while($onerow = $ordersquery->fetch(PDO::FETCH_OBJ)){
					$pos_id = $onerow->pos_id;
					
					$email = trim(stripslashes($onerow->email));
					$phone = trim(stripslashes($onerow->phone));
					$name = trim(stripslashes($onerow->name));					
					$offers_email = trim(stripslashes($onerow->offers_email));					
					if($email !=''){
						$name .= " ($email)";
					}
					
					$ordersData[] =array('id' => $pos_id,
											'email' => $email,
											'phone' => $phone,
											'am' => $offers_email,
											'label' => $name
											);
				}
			}

			$htmlStr .= '
			<script type="text/javascript">
				var ordersData = '.json_encode($ordersData).';
			</script>';
			
		}
		
		return $htmlStr;
	}
   
	
	private function filterHAndOptions(){
		$users_id = $_SESSION["users_id"]??0;
		
		$spos_id = $this->pos_id;
		$shistory_type = $this->history_type;
		
		$filterSql = '';
		$bindData = array();
		$bindData['pos_id'] = $spos_id;

		if($shistory_type !=''){
			if(strcmp($shistory_type, 'orders')==0){
				$filterSql = "SELECT 'Order Created' AS afTitle, 'pos' AS tableName FROM pos WHERE pos_id = :pos_id";
			}
			elseif(strcmp($shistory_type, 'appointments')==0){
				$filterSql = "SELECT 'Appointments Created' AS afTitle, 'appointments' AS tableName FROM appointments WHERE pos_id = :pos_id";
			}
			elseif(strcmp($shistory_type, 'notes')==0){
				$filterSql = "SELECT 'Notes Created' AS afTitle, 'notes' AS tableName FROM notes WHERE note_for = 'orders' AND table_id = :pos_id";
			}
			elseif(strcmp($shistory_type, 'track_edits')==0){
				$filterSql = "SELECT 'Track Edits' AS afTitle, 'track_edits' AS tableName FROM track_edits WHERE record_for = 'orders' AND record_id = :pos_id";
			}
			else{
				$filterSql = "SELECT activity_feed_title AS afTitle, 'activity_feed' AS tableName FROM activity_feed WHERE uri_table_name = 'pos' AND activity_feed_link LIKE CONCAT('/Orders/view/', :pos_id)";
			}
		}
		else{
			$filterSql = "SELECT activity_feed_title AS afTitle, 'activity_feed' AS tableName FROM activity_feed WHERE uri_table_name = 'pos' AND activity_feed_link = '/Orders/view/$spos_id' 
			UNION ALL SELECT 'Customer Created' AS afTitle, 'orders' AS tableName FROM pos WHERE pos_id = :pos_id 
			UNION ALL SELECT 'Appointment Created' AS afTitle, 'appointments' AS tableName FROM appointments WHERE pos_id = :pos_id 
			UNION ALL SELECT 'Notes Created' AS afTitle, 'notes' AS tableName FROM notes WHERE note_for = 'orders' AND table_id = :pos_id 
			UNION ALL SELECT 'Track Edits' AS afTitle, 'track_edits' AS tableName FROM track_edits WHERE record_for = 'orders' AND record_id = :pos_id";
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
		$spos_id = intval($this->pos_id);
		$shistory_type = $this->history_type;
		
		$starting_val = ($page-1)*$limit;
		if($starting_val>$totalRows){$starting_val = 0;}
		
		$users_id = $_SESSION["users_id"]??0;
		$currency = $_SESSION["currency"]??'$';
		$dateformat = $_SESSION["dateformat"]??'m/d/Y';
		$timeformat = $_SESSION["timeformat"]??'12 hour';
		
		$bindData = array();
		$bindData['pos_id'] = $spos_id;            
		if($shistory_type !=''){
			if(strcmp($shistory_type, 'orders')==0){
				$filterSql = "SELECT users_id, created_on, 'Customer Created' AS afTitle, 'orders' AS tableName, pos_id AS tableId FROM pos WHERE pos_id = :pos_id";
			}
			elseif(strcmp($shistory_type, 'appointments')==0){
				$filterSql = "SELECT users_id, created_on, 'Appointment Created' AS afTitle, 'appointments' AS tableName, appointments_id AS tableId FROM appointments WHERE pos_id = :pos_id";
			}
			elseif(strcmp($shistory_type, 'notes')==0){
				$filterSql = "SELECT users_id, created_on, 'Notes Created' AS afTitle, 'notes' AS tableName, notes_id AS tableId FROM notes WHERE note_for = 'orders' AND table_id = :pos_id";
			}
			elseif(strcmp($shistory_type, 'track_edits')==0){
				$filterSql = "SELECT users_id, created_on, 'Track Edits' AS afTitle, 'track_edits' AS tableName, track_edits_id AS tableId FROM track_edits WHERE record_for = 'orders' AND record_id = :pos_id";
			}
			else{
				$filterSql = "SELECT users_id, created_on, activity_feed_title AS afTitle, 'activity_feed' AS tableName, activity_feed_id AS tableId FROM activity_feed WHERE uri_table_name = 'orders' AND activity_feed_link LIKE CONCAT('/Orders/view/', :pos_id)";
			}
		}
		else{
			$filterSql = "SELECT users_id, created_on, activity_feed_title AS afTitle, 'activity_feed' AS tableName, activity_feed_id AS tableId FROM activity_feed WHERE uri_table_name = 'orders' AND activity_feed_link = '/Orders/view/$spos_id' 
			UNION ALL SELECT users_id, created_on, 'Customer Created' AS afTitle, 'orders' AS tableName, pos_id AS tableId FROM pos WHERE pos_id = :pos_id 
			UNION ALL SELECT users_id, created_on, 'Appointment Created' AS afTitle, 'appointments' AS tableName, appointments_id AS tableId FROM appointments WHERE pos_id = :pos_id 
			UNION ALL SELECT users_id, created_on, 'Notes Created' AS afTitle, 'notes' AS tableName, notes_id AS tableId FROM notes WHERE note_for = 'orders' AND table_id = :pos_id 
			UNION ALL SELECT users_id, created_on, 'Track Edits' AS afTitle, 'track_edits' AS tableName, track_edits_id AS tableId FROM track_edits WHERE record_for = 'orders' AND record_id = :pos_id";
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
		$pos_id = $_POST['pos_id']??0;
		$shistory_type = $_POST['shistory_type']??'';
		$totalRows = $_POST['totalRows']??0;
		$rowHeight = $_POST['rowHeight']??34;
		$page = $_POST['page']??1;
		if($page<=0){$page = 1;}
		$_SESSION["limit"] = $_POST['limit']??'auto';
		
		$this->pos_id = $pos_id;
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
	
	/**
	 * rm
	 */
	public function aJget_OrdersPopup(){
	
		$pos_id = $_POST['pos_id']??0;
		$ordersData = array();
		$ordersData['login'] = '';
		$ordersData['pos_id'] = 0;				
		$ordersData['name'] = '';
		$ordersData['phone'] = '';
		$ordersData['email'] = '';
		$ordersData['address'] = '';
		$ordersData['offers_email'] = 0;
		if($pos_id>0){
			$ordersObj = $this->db->getObj("SELECT * FROM pos WHERE pos_id = :pos_id", array('pos_id'=>$pos_id),1);
			if($ordersObj){
				$ordersRow = $ordersObj->fetch(PDO::FETCH_OBJ);	

				$ordersData['pos_id'] = $pos_id;
				$ordersData['name'] = stripslashes(trim($ordersRow->name));
				$ordersData['phone'] = trim($ordersRow->phone);
				$ordersData['email'] = trim($ordersRow->email);
				$ordersData['address'] = stripslashes(trim($ordersRow->address));
				$ordersData['offers_email'] = intval($ordersRow->offers_email);				
				$ordersData['created_on'] = $ordersRow->created_on;
				$ordersData['last_updated'] = $ordersRow->last_updated;				
			}
		}
				
		return json_encode($ordersData);
	}
	
	public function aJsave_Orders(){
		
		$savemsg = $message = $str = '';
		
		$users_id = $_SESSION["users_id"]??0;
		$pos_id = $_POST['pos_id']??0;
		
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
		
		if($pos_id==0){
			$totalrows = 0;
			$queryManuObj = $this->db->getObj("SELECT COUNT(pos_id) AS totalrows FROM pos WHERE name = :name AND $dupsql AND pos_publish = 1", $bindData);
			if($queryManuObj){
				$totalrows = $queryManuObj->fetch(PDO::FETCH_OBJ)->totalrows;						
			}
			if($totalrows>0){
				$savemsg = 'error';
				$message .= "<p>This name and email already exists. Try again with different name/email.</p>";
			}
			else{										
				$conditionarray['created_on'] = date('Y-m-d H:i:s');
				
				$pos_id = $this->db->insert('pos', $conditionarray);
				if($pos_id){
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
			$bindData['pos_id'] = $pos_id;
			$queryManuObj = $this->db->getObj("SELECT COUNT(pos_id) AS totalrows FROM pos WHERE name = :name AND $dupsql AND pos_id != :pos_id AND pos_publish = 1", $bindData);
			if($queryManuObj){
				$totalrows = $queryManuObj->fetch(PDO::FETCH_OBJ)->totalrows;						
			}
			if($totalrows>0){
				$savemsg = 'error';
				$message .= "<p>This name and email already exists. Try again with different name/email.</p>";
			}
			else{
				$custObj = $this->db->getData("SELECT * FROM pos WHERE pos_id = $pos_id", array());
				
				$update = $this->db->update('pos', $conditionarray, $pos_id);
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
							$teData['record_for'] = 'pos';
							$teData['record_id'] = $pos_id;
							$teData['details'] = json_encode(array('changed'=>$changed, 'moreInfo'=>$moreInfo));
							$this->db->insert('track_edits', $teData);							
						}
					}
				}
				
				$savemsg = 'update-success';					
			}
		}
		
		$array = array( 'login'=>'',
						'pos_id'=>$pos_id,
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

	public function aJmergeOrders(){
		$returnStr = $savemsg = '';
		$id = 0;
		$pos_id = $_POST['frompos_id']??0;
		$topos_id = $_POST['topos_id']??0;
		$fromCustObj = $this->db->getObj("SELECT * FROM pos WHERE pos_id = :pos_id", array('pos_id'=>$pos_id), 1);
		if($fromCustObj){
			$fromCustRow = $fromCustObj->fetch(PDO::FETCH_OBJ);
			$toCustObj = $this->db->getObj("SELECT * FROM pos WHERE pos_id = :pos_id", array('pos_id'=>$topos_id), 1);
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
					$this->db->update('pos', $updateData, $topos_id);
				}
				$update = $this->db->update('pos', array('pos_publish'=>0), $pos_id);
				if($update){
					$id = $pos_id;
					$savemsg = 'Success';
					$filterSql = "SELECT activity_feed_id FROM activity_feed WHERE uri_table_name = 'pos' AND activity_feed_link LIKE CONCAT('/Orders/view/', :pos_id)";
					$tableObj = $this->db->getObj($filterSql, array('pos_id'=>$pos_id));
					if($tableObj){
						while($oneRow = $tableObj->fetch(PDO::FETCH_OBJ)){
							$activity_feed_link = '/Orders/view/'.$topos_id;
							$this->db->update('activity_feed', array('activity_feed_link'=>$activity_feed_link), $oneRow->activity_feed_id);
						}
					}					
					
					$filterSql = "SELECT track_edits_id FROM track_edits WHERE record_for = 'pos' AND record_id = :pos_id ";
					$tableObj = $this->db->getObj($filterSql, array('pos_id'=>$pos_id));
					if($tableObj){
						while($oneRow = $tableObj->fetch(PDO::FETCH_OBJ)){
							$this->db->update('track_edits', array('record_id'=>$topos_id), $oneRow->track_edits_id);
						}
					}
					
					$filterSql = "SELECT notes_id FROM notes WHERE note_for = 'pos' AND table_id = :pos_id";
					$tableObj = $this->db->getObj($filterSql, array('pos_id'=>$pos_id));
					if($tableObj){
						while($oneRow = $tableObj->fetch(PDO::FETCH_OBJ)){
							$this->db->update('notes', array('table_id'=>$topos_id), $oneRow->notes_id);
						}
					}
					
					$note_for = 'pos';
					$noteData=array('table_id'=> $pos_id,
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