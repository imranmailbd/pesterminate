<?php
class Manage_Data{
	protected $db;
	private $page, $rowHeight, $totalRows;
	private $sorting_type, $keyword_search, $history_type, $actFeeTitOpt;
	public $pageTitle;
	public function __construct($db){$this->db = $db;}
	
	public function export(){
		$this->pageTitle = $GLOBALS['title'];
		
		$startdate = date('d/m/Y', time()-2592000);
		$enddate = date('d/m/Y');
		
		if(!isset($created_on) || $created_on ==''){
			$created_on = "$startdate - $enddate";
		}
		if($created_on !=''){
			$created_onarray = explode(' - ', $created_on);
			if(!empty($created_onarray) && count($created_onarray)>1){
				$startdate = $created_onarray[0];
				$enddate = $created_onarray[1];
			}
		}
		
		$expTypOpts = array('customer'=>stripslashes('Customers'));
		
		$expTypOpt = " <option value=\"\">Choose data type to Export</option>";
		foreach($expTypOpts as $value=>$label){
			$expTypOpt .= "<option value=\"$value\">$label</option>";
		}
		
		$innerHTMLStr = "<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"/assets/css/daterangepicker.css\" />
		<script type=\"text/javascript\" src=\"/assets/js/moment.min.js\"></script>
		<script type=\"text/javascript\" src=\"/assets/js/daterangepicker.js\"></script>
		<form name=\"frmexport\" id=\"frmexport\" action=\"/Manage_Data/export_data_csv\" enctype=\"multipart/form-data\" onSubmit=\"return checkExport();\" method=\"post\" accept-charset=\"utf-8\">
			<div class=\"row mtop15\">
				<div class=\"col-sm-12 col-md-4\">                   
					<select required name=\"export_type\" id=\"export_type\" class=\"form-control\" onchange=\"checkExportType()\">
						$expTypOpt
					</select>
					<span class=\"errormsg\" id=\"error_export_type\"></span>
				</div>
			</div>
			<hr>
			<div class=\"row\" id=\"allthreecolumn\" style=\"display:none\">
				<div class=\"col-sm-12 col-md-4 mbottom10\"> 
					<div class=\"widget mbottom10\">
						<div class=\"widget-header\">
							<h3>Filter Data</h3>
						</div>
						<div class=\"widget-content\">
							<div class=\"row\">
								<div class=\"col-sm-12 form-group\">
									<label for=\"date_range\" id=\"lbdate_range\">Date Added</label>
									<input type=\"text\" name=\"date_range\" id=\"date_range\" class=\"form-control\" value=\"\" />
								</div>								
							</div>
						</div>
					</div>
				</div>
				<div class=\"col-sm-12 col-md-4 mbottom10\"> 
					<div class=\"widget mbottom10\">
						<div class=\"widget-header\">
							<h3>Select Fields <span class=\"required\">*</span></h3>
						</div> <!-- /widget-header -->
						<div class=\"widget-content\">
							<div class=\"row\"><div class=\"col-sm-12 cursor\" id=\"fieldsList\"></div></div>
							<span class=\"errormsg\" id=\"error_fieldsname\"></span>
						</div>
					</div>
				</div>
				<div class=\"col-sm-12 col-md-4 mbottom10\"> 
					<div class=\"widget mbottom10\">
						<div class=\"widget-header\">
							<h3>Export Options</h3>
						</div> <!-- /widget-header -->
						<div class=\"widget-content\">
							<div class=\"row\">
								<div class=\"col-sm-12\">
									<input type=\"submit\" name=\"submit\" id=\"submit\" class=\"btn btn-success\" value=\" Export \">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>";
		
		$htmlStr = $this->htmlBody($innerHTMLStr);
		return $htmlStr;
	}
	
	public function exportFieldsList(){
		$POST = json_decode(file_get_contents('php://input'), true);
		$fieldsList = $lbdate_range = '';
		$export_type = trim($POST['export_type']??'');
		  
		if($export_type=='customer'){
			$lbdate_range = 'Date Added';
			if($export_type=='customer'){				
				$fieldsList .= "<label class=\"cursor\"><input checked type=\"checkbox\" name=\"fieldsname[]\" value=\"customers_id:ID\"> ID</label><br />";
				$fieldsList .= "<label class=\"cursor\"><input checked type=\"checkbox\" name=\"fieldsname[]\" value=\"name:Name\"> Name</label><br />";
				$fieldsList .= "<label class=\"cursor\"><input checked type=\"checkbox\" name=\"fieldsname[]\" value=\"phone:Phone\"> Phone</label><br />";
				$fieldsList .= "<label class=\"cursor\"><input checked type=\"checkbox\" name=\"fieldsname[]\" value=\"email:Email\"> Email</label><br />";
				$fieldsList .= "<label class=\"cursor\"><input checked type=\"checkbox\" name=\"fieldsname[]\" value=\"address:Address\"> Address</label><br />";
			}					
		}
		
		return json_encode(array('login'=>'', 'fieldsList'=>$fieldsList, 'lbdate_range'=>$lbdate_range));
	}
	
   public function export_data_csv(){
		$dateformat = 'd/m/Y';
		$timeformat = '12 hour';
		if(!isset($_POST)){
			return array('There is no form submit');
		}
		$export_type = $_POST['export_type']??'';
		$date_range = $_POST['date_range']??'';
		$fieldsnameArray = $_POST['fieldsname']??array();
		$Common = new Common($this->db);
		
		$startdate = $enddate = '';
		if($date_range !=''){
			$date_rangearray = explode(' - ', $date_range);
			if(is_array($date_rangearray) && count($date_rangearray)>1){
					$startdate = date('Y-m-d', strtotime($date_rangearray[0])).' 00:00:00';
					$enddate = date('Y-m-d', strtotime($date_rangearray[1])).' 23:59:59';
			}
		}
		
		$newline = "\r\n";
		$enclosure = '"';
		$delim = ",";

		$titleNames = $fieldNames = array();
		if(!empty($fieldsnameArray)){
			foreach($fieldsnameArray as $oneField){
					list($fieldname, $titlename) = explode(':', $oneField);
					if($fieldname !=''){
						$titleNames[] = str_replace('ID', 'Id', $titlename);
						$fieldNames[] = $fieldname;
					}
			}
		}
		
		$data = array();
      $data[] = $titleNames;
      if($export_type == 'customer') {            
			
			$sql = "SELECT ".implode(', ', $fieldNames)." FROM customers WHERE customers_publish = 1";
			$bindData = array();
			if($startdate !='' && $enddate !=''){
					$sql .= " AND (created_on BETWEEN :startdate AND :enddate)";
					$bindData['startdate']= $startdate;
					$bindData['enddate']= $enddate;
			}
			$sql .= " ORDER BY name ASC";
			$this->db->writeIntoLog($sql);
			$query = $this->db->getObj($sql, $bindData);
			if($query){
				while($oneRow = $query->fetch(PDO::FETCH_OBJ)){
					$rowData = array();
               foreach($fieldNames as $oneName){
                  $rowData[] = stripslashes(trim($oneRow->$oneName));
               }
               $data[] = $rowData;
            }
         }
      }
        
		return $data;
		//force_download($filename, $data);
	}
	
   public function archive_Data(){
		$this->pageTitle = $GLOBALS['title'];
		$innerHTMLStr = "<input type=\"hidden\" id=\"archive_Data\" value=\"1\">";
		
		$innerHTMLStr .= "<fieldset class=\"mtop15\">
			<legend>Customer</legend>
			<form enctype=\"text/plain\" method=\"post\" onSubmit=\"return archiveCustomers();\" name=\"frmCustomers\" action=\"#\">
				<div class=\"form-group ptopbottom15\">
					<div class=\"col-sm-12 col-md-3\">
						<label class=\"ptop0\" for=\"customer_name\">Customer Name/Email<span class=\"required\">*</span></label>
					</div>
					<div class=\"col-xs-8 col-sm-9 col-md-7\">
						<input type=\"text\" required class=\"form-control\" name=\"customer_name\" id=\"customer_name\" value=\"\" placeholder=\"Customer Name/Email\" maxlength=\"50\" />
						<span class=\"errormsg\" id=\"errmsg_customers\"></span>
					</div>
					<div class=\"col-xs-4 col-sm-3 col-md-2\">
						<input type=\"hidden\" name=\"customers_id\" id=\"customers_id\" value=\"0\">
						<input type=\"button\" name=\"customers_archive\" id=\"customers_archive\" class=\"btn btn-warning\" value=\"Archive\" onClick=\"archiveCustomers();\">
					</div>                        
				</div>
			</form>                    
		</fieldset>";

		$customersData = array();
		$customerssql = "SELECT name, phone, email, address, customers_id FROM customers WHERE customers_publish = 1";
		$customersquery = $this->db->getObj($customerssql, array());
		if($customersquery){
			while($onerow = $customersquery->fetch(PDO::FETCH_OBJ)){
				$customers_id = $onerow->customers_id;
				
				$name = trim(stripslashes($onerow->name));
				$phone = trim(stripslashes($onerow->phone));
				$email = trim(stripslashes($onerow->email));
				$address = trim(stripslashes($onerow->address));
				$name = stripslashes(trim("$name $phone $email $address"));
				//======================Here calculate all invoiced credit ===============//
				$customersData[] =array('id' => $customers_id,
										'email' => $email,
										'phone' => $phone,
										'am' => $address,
										'label' => $name
										);
			}
		}

		$innerHTMLStr .= '
		<script type="text/javascript">
			j(document).ready(function(){
				var customersData = '.json_encode($customersData).';
			
				j( "#customer_name" ).autocomplete({
					minLength: 0,
					source: customersData,
					focus: function( event, ui ) {
						return false;
					},
					select: function( event, ui ) {
						j(this).val(ui.item.label);
						j( "#customers_id" ).val( ui.item.id );
						archiveCustomers();
						return false;
					}
				});
			});
		</script>';

		$htmlStr = $this->htmlBody($innerHTMLStr);
		return $htmlStr;
	}
	   
   public function jquerycustomers_archive(){
	
		$returnmsg = 'Could not remove customer.';
		$users_id = $_SESSION["users_id"]??0;
		$customers_id = $_POST['customers_id']??0;
		$customer_name = $_POST['customer_name']??'';

		if($customer_name !=''){
			if($customers_id>0){
				$sql = "SELECT name, phone, email, address FROM customers WHERE customers_id = :customers_id ORDER BY customers_id ASC";
				$query = $this->db->getObj($sql, array('customers_id'=>$customers_id),1);
				if($query){
					$onecustomerrow = $query->fetch(PDO::FETCH_OBJ);
					$name = trim(stripslashes($onecustomerrow->name));
					$email = trim(stripslashes($onecustomerrow->email));
					$phone = trim(stripslashes($onecustomerrow->phone));
					$address = trim(stripslashes($onecustomerrow->address));
					$name = stripslashes(trim("$name $phone $email $address"));

					if($customer_name !="$name"){
						$customers_id = 0;
					}
				}
			}

			if($customers_id==0 && $customer_name != ''){

				$autocustomer_name = $customer_name;
				$email = '';
				if(strpos($customer_name, ' (')!== false) {
					$scustomerexplode = explode(' (', $customer_name);
					if(count($scustomerexplode)>1){
						$autocustomer_name = trim($scustomerexplode[0]);
						$email = str_replace(')', '', $scustomerexplode[1]);
					}
				}
				$bindData = array();
				$strextra = " TRIM(CONCAT(name, ' ', phone, ' ', email, ' ', address)) LIKE CONCAT('%', :autocustomer_name, '%')";
				$bindData['autocustomer_name'] = $autocustomer_name;
				$strextra .= " GROUP BY CONCAT(name, ' ', phone, ' ', email, ' ', address)";
				$sql = "SELECT customers_id FROM customers WHERE $strextra ORDER BY customers_id ASC";
				$query = $this->db->getObj($sql, $bindData);
				if($query){
					$customers_id = $query->fetch(PDO::FETCH_OBJ)->customers_id;
				}
			}
		}

		if($customers_id>0){
			$sql = "SELECT name, phone, email, address FROM customers WHERE customers_id = :customers_id AND customers_publish = 1 ORDER BY customers_id ASC";
			$query = $this->db->getObj($sql, array('customers_id'=>$customers_id),1);
			if($query){
				$onerow = $query->fetch(PDO::FETCH_OBJ);

				$autocustomer_name = trim(stripslashes($onerow->name.' '.$onerow->phone.' '.$onerow->email.' '.$onerow->address));

				$updatetable = $this->db->update('customers', array('customers_publish'=>0), $customers_id);
				if($updatetable){
					$note_for = 'customers';
					$noteData=array('table_id'=> $customers_id,
									'note_for'=> $note_for,
									'created_on'=> date('Y-m-d H:i:s'),
									'last_updated'=> date('Y-m-d H:i:s'),
									'users_id'=> $users_id,
									'note'=> "Customers archived successfully $autocustomer_name",
									'publics'=>0);
					$notes_id = $this->db->insert('notes', $noteData);
					
					$returnmsg = 'archive-success';
				}
			}
		}
		else{
			$returnmsg = 'Could not found customer for archive';
		}
		return json_encode(array('login'=>'', 'returnStr'=>$returnmsg));
   }
	
	//=====================Front Menu=====================//
	public function front_menu($segment3){
		
		$list_filters = $_SESSION['list_filters']??array();
						
		$keyword_search = $list_filters['keyword_search']??'';
		$this->keyword_search = $keyword_search;
		
		$this->filterAndOptions_front_menu();
		
		$page = !empty($segment3) ? intval($segment3):1;
		if($page<=0){$page = 1;}
		if(!isset($_SESSION['limit'])){$_SESSION['limit'] = 'auto';}
		$limit = $_SESSION['limit'];
		
		$this->rowHeight = 34;
		$this->page = $page;
		$tableRows = $this->loadTableRows_front_menu();
		
		$limOpt = '';
		$limOpts = array(15, 20, 25, 50, 100, 500);
		foreach($limOpts as $oneOpt){
			$selected = '';
			if($limit==$oneOpt){$selected = ' selected';}
			$limOpt .= "<option$selected value=\"$oneOpt\">$oneOpt</option>";
		}
		
		$rootMenuOpts = $subMenuOpts = $rootSubMenuOpts = array();
		$sqlFM = "SELECT * FROM front_menu WHERE (root_menu_id = 0 OR sub_menu_id = 0) ORDER BY front_menu_id ASC";
		$fmObject = $this->db->getObj($sqlFM, array());
		if($fmObject){
			while($oneFMtRow = $fmObject->fetch(PDO::FETCH_OBJ)){
				$front_menu_id = $oneFMtRow->front_menu_id;
				$root_menu_id = $oneFMtRow->root_menu_id;
				$sub_menu_id = $oneFMtRow->sub_menu_id;
				$name = stripslashes(trim($oneFMtRow->name));
				if($root_menu_id==0 && $sub_menu_id==0)
					$rootMenuOpts[$front_menu_id] = $name;
				elseif($root_menu_id>0 && $sub_menu_id==0){
					$subMenuOpts[$front_menu_id] = $name;
					$rootSubMenuOpts[$root_menu_id][$front_menu_id] = $name;
				}
			}
			if(!empty($rootMenuOpts)){asort($rootMenuOpts);}
			if(!empty($subMenuOpts)){asort($subMenuOpts);}
		}

		$rootMenuOpt = '<option value="0">Root Menu</option>';
		if(!empty($rootMenuOpts)){
			foreach($rootMenuOpts as $id=>$name){
				$rootMenuOpt .= "<option value=\"$id\">$name</option>";
			}
		}
		$subMenuOpt = '<option value="0">Sub Menu</option>';
		if(!empty($subMenuOpts)){
			foreach($subMenuOpts as $id=>$name){
				$subMenuOpt .= "<option value=\"$id\">$name</option>";
			}
		}

		$innerHTMLStr = "<input type=\"hidden\" name=\"pageURI\" id=\"pageURI\" value=\"$GLOBALS[segment1]/$GLOBALS[segment2]\">
		<input type=\"hidden\" name=\"page\" id=\"page\" value=\"$this->page\">
		<input type=\"hidden\" name=\"rowHeight\" id=\"rowHeight\" value=\"$this->rowHeight\">
		<input type=\"hidden\" name=\"totalTableRows\" id=\"totalTableRows\" value=\"$this->totalRows\">
		<textarea id=\"rootSubMenuOpts\" class=\"hidden\">".json_encode($rootSubMenuOpts)."</textarea>
		<div class=\"row\">
			<div class=\"col-sm-12 col-sm-12 col-md-7\">
				<div class=\"row\">
					<div class=\"col-sm-12 col-md-6\">
						<h1 class=\"metatitle\">Front Menu List <i class=\"fa fa-info-circle txt16normal\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Front Menu List\"></i></h1>
					</div>				
					<div class=\"col-sm-12 col-md-6\">
						<div class=\"input-group\">
							<input type=\"text\" placeholder=\"Search Front Menu\" value=\"$keyword_search\" id=\"keyword_search\" name=\"keyword_search\" class=\"form-control\" maxlength=\"50\" />
							<span class=\"input-group-addon cursor\" onClick=\"checkAndLoadFilterData();\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Search Front Menu\">
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
										<th class=\"txtcenter\">Root Menu</th>
										<th class=\"txtcenter\">Sub Menu</th>
										<th class=\"txtcenter\">Menu Name</th>
										<th class=\"txtcenter\">Menu URI</th>
										<th class=\"txtcenter\">Position</th>
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
				<h4 class=\"borderbottom\" id=\"formtitle\">Add New Menu</h4>
				<form action=\"#\" name=\"frmfront_menu\" id=\"frmfront_menu\" onsubmit=\"return AJsave_front_menu();\" enctype=\"multipart/form-data\" method=\"post\" accept-charset=\"utf-8\">
					<div class=\"form-group\">
						<label for=\"root_menu_id\">Root Menu</label>
						<select class=\"form-control\" name=\"root_menu_id\" id=\"root_menu_id\">
							$rootMenuOpt
						</select>
						<span id=\"error_root_menu_id\" class=\"errormsg\"></span>
					</div>
					<div class=\"form-group\">
						<label for=\"sub_menu_id\">Sub Menu</label>
						<select class=\"form-control\" name=\"sub_menu_id\" id=\"sub_menu_id\">
							$subMenuOpt
						</select>
						<span id=\"error_sub_menu_id\" class=\"errormsg\"></span>
					</div>
					<div class=\"form-group\">
						<label for=\"name\">Manu Name<span class=\"required\">*</span></label>
						<input type=\"text\" required class=\"form-control\" name=\"name\" id=\"name\" value=\"\" size=\"255\" maxlength=\"255\" onKeyUp=\"createURI('name', 'menu_uri', 0);\" />
						<span id=\"error_name\" class=\"errormsg\"></span>
					</div>
					<div class=\"form-group\">
						<label for=\"menu_uri\">Menu URI<span class=\"required\">*</span></label>
						<input type=\"text\" required class=\"form-control\" name=\"menu_uri\" id=\"menu_uri\" value=\"\" size=\"255\" maxlength=\"255\" onKeyUp=\"createURI('name', 'menu_uri', 1);\" />
						<span id=\"error_menu_uri\" class=\"errormsg\"></span>
					</div>
					<div class=\"form-group\">
						<label for=\"menu_position\">Menu Position<span class=\"required\">*</span></label>
						<input type=\"numeric\" required class=\"form-control\" name=\"menu_position\" id=\"menu_position\" value=\"\" size=\"2\" maxlength=\"99\" />
						<span id=\"error_menu_position\" class=\"errormsg\"></span>
					</div>
					<div class=\"form-group\">
						<input type=\"hidden\" name=\"front_menu_id\" id=\"front_menu_id\" value=\"0\">
						<input type=\"submit\" id=\"submit\" class=\"btn btn-primary backlink\" value=\" Save \" />
						<input type=\"button\" name=\"reset\" id=\"reset\" onClick=\"resetForm_front_menu();\" value=\"Cancel\" class=\"hidediv btn btn-warning floatleft mleft10\" />
						<input type=\"button\" name=\"archive\" id=\"archive\" value=\"Archive\" class=\"hidediv btn btn-danger floatright\" />
					</div>
				</form>
			</div>
		</div>";
		
		$htmlStr = $this->htmlBody($innerHTMLStr);
		return $htmlStr;
	}
	
	public function AJsave_front_menu(){
	
		
		$users_id = $_SESSION["users_id"]??0;
		$returnStr = 'Ok';		
		$savemsg = '';
		
		$front_menu_id = intval($_POST['front_menu_id']??0);
		$root_menu_id = intval($_POST['root_menu_id']??0);
		$sub_menu_id = intval($_POST['sub_menu_id']??0);
		$name = addslashes($_POST['name']??'');
		$menu_uri = addslashes($_POST['menu_uri']??'');
		
		$conditionarray = array();
		$conditionarray['root_menu_id'] = $root_menu_id;
		$conditionarray['sub_menu_id'] = $sub_menu_id;
		$conditionarray['name'] = $name;
		$conditionarray['menu_uri'] = $menu_uri;
		$conditionarray['last_updated'] = date('Y-m-d H:i:s');
		$conditionarray['users_id'] = $users_id;
		$conditionarray['menu_position'] = intval($_POST['menu_position']??0);
		
		$duplSql = "SELECT front_menu_publish, front_menu_id FROM front_menu WHERE root_menu_id = :root_menu_id AND sub_menu_id = :sub_menu_id AND name = :name";
		$bindData = array('root_menu_id'=>$root_menu_id, 'sub_menu_id'=>$sub_menu_id, 'name'=>$name);
		if($front_menu_id>0){
			$duplSql .= " AND front_menu_id != :front_menu_id";
			$bindData['front_menu_id'] = $front_menu_id;
		}
		$duplSql .= " LIMIT 0, 1";
		$duplRows = 0;
		$front_menuObj = $this->db->getData($duplSql, $bindData);
		if($front_menuObj){
			foreach($front_menuObj as $onerow){
				$duplRows = 1;
				$front_menu_publish = $onerow['front_menu_publish'];
				$front_menu_id = $onerow['front_menu_id'];
				$conditionarray['front_menu_publish'] = 1;
				$this->db->update('front_menu', $conditionarray, $front_menu_id);
				$duplRows = 0;
				$returnStr = 'Update';
			}
		}
		
		if($duplRows>0 || empty($name)){
			$savemsg = 'error';
			$returnStr = "<p>This name is already exist! Please try again with different name.</p>";
		}
		else{			
			if($front_menu_id==0){
				$conditionarray['created_on'] = date('Y-m-d H:i:s');
				$front_menu_id = $this->db->insert('front_menu', $conditionarray);
				if($front_menu_id){						
					$returnStr = 'Add';
				}
				else{
					$returnStr = 'Error occured while adding new menu! Please try again.';
				}
			}
			else{
				$update = $this->db->update('front_menu', $conditionarray, $front_menu_id);
				if($update){
					$activity_feed_title = 'Menu was edited';
					$activity_feed_link = "/Manage_Data/front_menu/view/$front_menu_id";
					
					$afData = array('created_on' => date('Y-m-d H:i:s'),
									'users_id' => $_SESSION["users_id"],
									'activity_feed_title' =>  $activity_feed_title,
									'activity_feed_name' => $name,
									'activity_feed_link' => $activity_feed_link,
									'uri_table_name' => "front_menu",
									'uri_table_field_name' =>"front_menu_publish",
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
	
	public function aJgetPage_front_menu($segment3){
	
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
			$this->filterAndOptions_front_menu();
			$jsonResponse['totalRows'] = $totalRows = $this->totalRows;
		}
		$this->page = $page;
		$this->totalRows = $totalRows;
		$this->rowHeight = $rowHeight;
		
		$jsonResponse['tableRows'] = $this->loadTableRows_front_menu();
		
		return json_encode($jsonResponse);
	}
	
	private function filterAndOptions_front_menu(){
		
		$keyword_search = $this->keyword_search;
		
		$_SESSION["current_module"] = "Our_billing";
		$_SESSION["list_filters"] = array('keyword_search'=>$keyword_search);
		
		$filterSql = "FROM front_menu WHERE front_menu_publish = 1";
		$bindData = array();
		if($keyword_search !=''){
			$keyword_search = addslashes(trim($keyword_search));
			if ( $keyword_search == "" ) { $keyword_search = " "; }
			$keyword_searches = explode (" ", $keyword_search);
			if ( strpos($keyword_search, " ") === false ) {$keyword_searches[0] = $keyword_search;}
			$num = 0;
			while ( $num < sizeof($keyword_searches) ) {
				$filterSql .= " AND name LIKE CONCAT('%', :keyword_search$num, '%')";
				$bindData['keyword_search'.$num] = trim($keyword_searches[$num]);
				$num++;
			}
		}
		$totalRows = 0;
		$strextra ="SELECT COUNT(front_menu_id) AS totalrows $filterSql";
		$query = $this->db->getObj($strextra, $bindData);
		if($query){
			$totalRows = $query->fetch(PDO::FETCH_OBJ)->totalrows;
		}
		$this->totalRows = $totalRows;		
	}
	
   private function loadTableRows_front_menu(){
		
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
		
		$filterSql = "FROM front_menu WHERE front_menu_publish = 1";
		$bindData = array();
		if($keyword_search !=''){
			$keyword_search = addslashes(trim($keyword_search));
			if ( $keyword_search == "" ) { $keyword_search = " "; }
			$keyword_searches = explode (" ", $keyword_search);
			if ( strpos($keyword_search, " ") === false ) {$keyword_searches[0] = $keyword_search;}
			$num = 0;
			while ( $num < sizeof($keyword_searches) ) {
				$filterSql .= " AND name LIKE CONCAT('%', :keyword_search$num, '%')";
				$bindData['keyword_search'.$num] = trim($keyword_searches[$num]);
				$num++;
			}
		}		
		
		$sqlquery = "SELECT * $filterSql ORDER BY CONCAT_WS(' ', menu_position, root_menu_id, name) ASC LIMIT $starting_val, $limit";
		$query = $this->db->getData($sqlquery, $bindData);
		$str = '';
		if($query){
			$rootOrSubIds = array();
			$FMSql = "SELECT front_menu_id, name FROM front_menu WHERE (root_menu_id  = 0 OR sub_menu_id = 0) AND front_menu_publish = 1 ORDER BY front_menu_id ASC";
			$FMObj = $this->db->getObj($FMSql, array());
			if($FMObj){
				while($oneRow = $FMObj->fetch(PDO::FETCH_OBJ)){
					$rootOrSubIds[$oneRow->front_menu_id] = trim(stripslashes($oneRow->name));
				}
			}

			foreach($query as $oneFMtRow){
				$front_menu_id = $oneFMtRow['front_menu_id'];
				$root_menu_id = $oneFMtRow['root_menu_id'];
				$sub_menu_id = $oneFMtRow['sub_menu_id'];
				
				$name = stripslashes(trim($oneFMtRow['name']));

				$subManuName = '';
				if($root_menu_id==0){
					$rootManuName = $name;
					$name = '';
				}
				else{
					$rootManuName = $rootOrSubIds[$root_menu_id]??'';
					if($sub_menu_id==0){
						$subManuName = $name;
						$name = '';
					}
					else{
						$subManuName = $rootOrSubIds[$sub_menu_id]??'';
					}
				}
				
				$menu_uri = $oneFMtRow['menu_uri'];
				$menu_position = $oneFMtRow['menu_position'];

				$str .= "<tr class=\"cursor\" onClick=\"getOneRowInfo('front_menu', $front_menu_id);\">
							<td data-title=\"Root Menu Name\" align=\"left\">$rootManuName</td>
							<td data-title=\"Sub Manu Name\" align=\"left\">$subManuName</td>
							<td data-title=\"Name\" align=\"left\">$name</td>
							<td data-title=\"Manu URI\" align=\"left\">$menu_uri</td>
							<td data-title=\"Manu Position\" align=\"left\">$menu_position</td>
						</tr>";
			}
		}
		else{
			$str .= "<tr><td colspan=\"5\" class=\"errormsg\">There is no Menu found.</td></tr>";
		}
		return $str;
   }
	
	//========================For Banners module=======================//    		
	public function banners($segment3){
		
		$this->pageTitle = $GLOBALS['title'];
		$list_filters = $_SESSION['list_filters']??array();
		
		$keyword_search = $list_filters['keyword_search']??'';
		$this->keyword_search = $keyword_search;
		
		$this->filterAndOptions_banners();
		
		$page = !empty($segment3) ? intval($segment3):1;
		if($page<=0){$page = 1;}
		if(!isset($_SESSION['limit'])){$_SESSION['limit'] = 'auto';}
		$limit = $_SESSION['limit'];
		
		$this->rowHeight = 34;
		$this->page = $page;
		$tableRows = $this->loadTableRows_banners();
		
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
						<h1 class=\"metatitle\">Banners List <i class=\"fa fa-info-circle txt16normal\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Banners List\"></i></h1>
					</div>				
					<div class=\"col-sm-12 col-md-6\">
						<div class=\"input-group\">
							<input type=\"text\" placeholder=\"Search Banners\" value=\"$keyword_search\" id=\"keyword_search\" name=\"keyword_search\" class=\"form-control\" maxlength=\"50\" />
							<span class=\"input-group-addon cursor\" onClick=\"checkAndLoadFilterData();\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Search Banners\">
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
										<th class=\"txtcenter\">Description</th>
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
				<h4 class=\"borderbottom\" id=\"formtitle\">Add New Banners</h4>
				<form action=\"#\" name=\"frmbanners\" id=\"frmbanners\" onsubmit=\"return AJsave_banners();\" enctype=\"multipart/form-data\" method=\"post\" accept-charset=\"utf-8\">
					<div class=\"form-group\">
						<label for=\"name\">Name<span class=\"required\">*</span></label>
						<input type=\"text\" required class=\"form-control\" name=\"name\" id=\"name\" value=\"\" size=\"255\" maxlength=\"255\"/>
						<span id=\"error_name\" class=\"errormsg\"></span>
					</div>
					<div class=\"form-group\">
						<label for=\"description\">Description<span class=\"required\">*</span></label>
						<textarea rows=\"15\" cols=\"50\" required class=\"form-control\" name=\"description\" id=\"description\"></textarea>
						<span id=\"error_description\" class=\"errormsg\"></span>
					</div>
					<div class=\"form-group\">
						<input type=\"hidden\" name=\"banners_id\" id=\"banners_id\" value=\"0\">
						<input type=\"submit\" id=\"submit\" class=\"btn btn-primary backlink\" value=\" Save \" />
						<input type=\"button\" name=\"reset\" id=\"reset\" onClick=\"resetForm_banners();\" value=\"Cancel\" class=\"hidediv btn btn-warning floatleft mleft10\" />
						<input type=\"button\" name=\"archive\" id=\"archive\" value=\"Archive\" class=\"hidediv btn btn-danger floatright\" />
					</div>
				</form>
				<!--link href=\"/assets/css/jqueryimageupload.css\" rel=\"stylesheet\" type=\"text/css\">
				<script type=\"text/javascript\" src=\"/assets/js/jquery.form.min.js\"></script-->
			</div>
		</div>";
		
		$htmlStr = $this->htmlBody($innerHTMLStr);
		return $htmlStr;
	}
	
	public function AJsave_banners(){
	
		
		$users_id = $_SESSION["users_id"]??0;
		$returnStr = 'Ok';		
		$savemsg = '';
		$banners_id = $_POST['banners_id']??0;
		$name = trim(addslashes($_POST['name']??''));
		$description = trim(addslashes($_POST['description']??''));
		
		$conditionarray = array();
		$conditionarray['name'] = $name;
		$conditionarray['description'] = $description;
		$conditionarray['last_updated'] = date('Y-m-d H:i:s');
		$conditionarray['users_id'] = $users_id;
		
		$duplSql = "SELECT banners_publish, banners_id FROM banners WHERE name = :name";
		$bindData = array('name'=>$name);
		if($banners_id>0){
			$duplSql .= " AND banners_id != :banners_id";
			$bindData['banners_id'] = $banners_id;
		}
		$duplSql .= " LIMIT 0, 1";
		$duplRows = 0;
		$bannersObj = $this->db->getData($duplSql, $bindData);
		if($bannersObj){
			foreach($bannersObj as $onerow){
				$duplRows = 1;
				$banners_publish = $onerow['banners_publish'];
				if($banners_id==0 && $banners_publish==0){
					$banners_id = $onerow['banners_id'];
					$this->db->update('banners', array('banners_publish'=>1), $banners_id);
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
			if($banners_id==0){
				$conditionarray['created_on'] = date('Y-m-d H:i:s');
				$banners_id = $this->db->insert('banners', $conditionarray);
				if($banners_id){						
					$returnStr = 'Add';
				}
				else{
					$returnStr = 'Adding new Banners';
				}
			}
			else{
				$update = $this->db->update('banners', $conditionarray, $banners_id);
				if($update){
					$activity_feed_title = 'Banners was edited';
					$activity_feed_link = "/Manage_Data/banners/view/$banners_id";
					
					$afData = array('created_on' => date('Y-m-d H:i:s'),
									'users_id' => $_SESSION["users_id"],
									'activity_feed_title' =>  $activity_feed_title,
									'activity_feed_name' => $name,
									'activity_feed_link' => $activity_feed_link,
									'uri_table_name' => "banners",
									'uri_table_field_name' =>"banners_publish",
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
	
	public function aJgetPage_banners($segment3){
	
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
			$this->filterAndOptions_banners();
			$jsonResponse['totalRows'] = $totalRows = $this->totalRows;
		}
		$this->page = $page;
		$this->totalRows = $totalRows;
		$this->rowHeight = $rowHeight;
		
		$jsonResponse['tableRows'] = $this->loadTableRows_banners();
		
		return json_encode($jsonResponse);
	}
	
	private function filterAndOptions_banners(){
		
		$keyword_search = $this->keyword_search;
		
		$_SESSION["current_module"] = "Manage_Data";
		$_SESSION["list_filters"] = array('keyword_search'=>$keyword_search);
		
		$filterSql = "FROM banners WHERE banners_publish = 1";
		$bindData = array();
		if($keyword_search !=''){
			$keyword_search = addslashes(trim($keyword_search));
			if ( $keyword_search == "" ) { $keyword_search = " "; }
			$keyword_searches = explode (" ", $keyword_search);
			if ( strpos($keyword_search, " ") === false ) {$keyword_searches[0] = $keyword_search;}
			$num = 0;
			while ( $num < sizeof($keyword_searches) ) {
				$filterSql .= " AND name LIKE CONCAT('%', :keyword_search$num, '%')";
				$bindData['keyword_search'.$num] = trim($keyword_searches[$num]);
				$num++;
			}
		}
		
		$totalRows = 0;
		$strextra ="SELECT COUNT(banners_id) AS totalrows $filterSql";
		$query = $this->db->getObj($strextra, $bindData);
		if($query){
			$totalRows = $query->fetch(PDO::FETCH_OBJ)->totalrows;
		}
		$this->totalRows = $totalRows;		
	}
	
   private function loadTableRows_banners(){
		
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
		
		$filterSql = "FROM banners WHERE banners_publish = 1";
		$bindData = array();
		if($keyword_search !=''){
			$keyword_search = addslashes(trim($keyword_search));
			if ( $keyword_search == "" ) { $keyword_search = " "; }
			$keyword_searches = explode (" ", $keyword_search);
			if ( strpos($keyword_search, " ") === false ) {$keyword_searches[0] = $keyword_search;}
			$num = 0;
			while ( $num < sizeof($keyword_searches) ) {
				$filterSql .= " AND name LIKE CONCAT('%', :keyword_search$num, '%')";
				$bindData['keyword_search'.$num] = trim($keyword_searches[$num]);
				$num++;
			}
		}
		
		$sqlquery = "SELECT * $filterSql ORDER BY name ASC LIMIT $starting_val, $limit";
		$query = $this->db->getData($sqlquery, $bindData);
		$str = '';
		if($query){
			foreach($query as $onerow){

				$banners_id = $onerow['banners_id'];
				$name = trim(stripslashes((string) $onerow['name']));
				$description = trim(stripslashes((string) $onerow['description']));
				
				$str .= "<tr class=\"cursor\">
							<td data-title=\"ID\" onClick=\"getOneRowInfo('banners', $banners_id);\" align=\"right\">$banners_id</td>
							<td data-title=\"Name\" onClick=\"getOneRowInfo('banners', $banners_id);\" align=\"left\">$name</td>
							<td data-title=\"Description\" align=\"left\">$description</td>
						</tr>";
			}
		}
		else{
			$str .= "<tr><td class=\"errormsg\" colspan=\"3\">There is no banners meet this criteria</td></tr>";
		}
		return $str;
   }
	
	//========================For Pages module=======================//    		
	public function pages($segment3){
		
		$this->pageTitle = $GLOBALS['title'];
		$list_filters = $_SESSION['list_filters']??array();
		
		$keyword_search = $list_filters['keyword_search']??'';
		$this->keyword_search = $keyword_search;
		
		$this->filterAndOptions_pages();
		
		$page = !empty($segment3) ? intval($segment3):1;
		if($page<=0){$page = 1;}
		if(!isset($_SESSION['limit'])){$_SESSION['limit'] = 'auto';}
		$limit = $_SESSION['limit'];
		
		$this->rowHeight = 34;
		$this->page = $page;
		$tableRows = $this->loadTableRows_pages();
		
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
						<h1 class=\"metatitle\">Pages List <i class=\"fa fa-info-circle txt16normal\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Pages List\"></i></h1>
					</div>				
					<div class=\"col-sm-12 col-md-6\">
						<div class=\"input-group\">
							<input type=\"text\" placeholder=\"Search Pages\" value=\"$keyword_search\" id=\"keyword_search\" name=\"keyword_search\" class=\"form-control\" maxlength=\"50\" />
							<span class=\"input-group-addon cursor\" onClick=\"checkAndLoadFilterData();\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Search Pages\">
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
										<th class=\"txtcenter\">URI Value</th>
										<th class=\"txtcenter\">Short Description</th>
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
				<h4 class=\"borderbottom\" id=\"formtitle\">Add New Pages</h4>
				<form action=\"#\" name=\"frmpages\" id=\"frmpages\" onsubmit=\"return AJsave_pages();\" enctype=\"multipart/form-data\" method=\"post\" accept-charset=\"utf-8\">
					<div class=\"form-group\">
						<label for=\"name\">Name<span class=\"required\">*</span></label>
						<input type=\"text\" required class=\"form-control\" name=\"name\" id=\"name\" value=\"\" size=\"255\" maxlength=\"255\" onKeyUp=\"createURI('name', 'uri_value', 0);\" />
						<span id=\"error_name\" class=\"errormsg\"></span>
					</div>
					<div class=\"form-group\">
						<label for=\"uri_value\">URI Value<span class=\"required\">*</span></label>
						<input type=\"text\" required class=\"form-control\" name=\"uri_value\" id=\"uri_value\" value=\"\" size=\"255\" maxlength=\"255\" onKeyUp=\"createURI('name', 'uri_value', 1);\" />
						<span id=\"error_uri_value\" class=\"errormsg\"></span>
					</div>
					<div class=\"form-group\">
						<label for=\"short_description\">Short Description<span class=\"required\">*</span></label>
						<textarea rows=\"5\" cols=\"50\" class=\"form-control\" name=\"short_description\" id=\"short_description\"></textarea>
						<span id=\"error_short_description\" class=\"errormsg\"></span>
					</div>
					<div class=\"form-group\">
						<label for=\"description\">Description<span class=\"required\">*</span></label>
						<textarea rows=\"15\" cols=\"50\" class=\"form-control\" name=\"description\" id=\"description\"></textarea>
						<span id=\"error_description\" class=\"errormsg\"></span>
					</div>
					<div class=\"form-group\">
						<input type=\"hidden\" name=\"pages_id\" id=\"pages_id\" value=\"0\">
						<input type=\"submit\" id=\"submit\" class=\"btn btn-primary backlink\" value=\" Save \" />
						<input type=\"button\" name=\"reset\" id=\"reset\" onClick=\"resetForm_pages();\" value=\"Cancel\" class=\"hidediv btn btn-warning floatleft mleft10\" />
						<input type=\"button\" name=\"archive\" id=\"archive\" value=\"Archive\" class=\"hidediv btn btn-danger floatright\" />
					</div>
				</form>
				<link href=\"/assets/css/jqueryimageupload.css\" rel=\"stylesheet\" type=\"text/css\">
				<script type=\"text/javascript\" src=\"/assets/js/jquery.form.min.js\"></script>
				<script language=\"JavaScript\" type=\"text/javascript\">
					//var descriptionField = document.getElementById(\"descriptionField\");
					//descriptionField.appendChild(wysiwyrEditor('description'));
				</script>
			</div>
		</div>";
		
		$htmlStr = $this->htmlBody($innerHTMLStr);
		return $htmlStr;
	}
	
	public function AJsave_pages(){
	
		
		$users_id = $_SESSION["users_id"]??0;
		$returnStr = 'Ok';		
		$savemsg = '';
		$pages_id = $_POST['pages_id']??0;
		$name = trim(addslashes($_POST['name']??''));
		$uri_value = trim(addslashes($_POST['uri_value']??''));
		$short_description = trim(addslashes($_POST['short_description']??''));
		$description = trim(addslashes($_POST['description']??''));
		
		$conditionarray = array();
		$conditionarray['name'] = $name;
		$conditionarray['uri_value'] = $uri_value;
		$conditionarray['short_description'] = $short_description;
		$conditionarray['description'] = $description;
		$conditionarray['last_updated'] = date('Y-m-d H:i:s');
		$conditionarray['users_id'] = $users_id;
		
		$duplSql = "SELECT pages_publish, pages_id FROM pages WHERE (name = :name OR uri_value = :uri_value)";
		$bindData = array('name'=>$name, 'uri_value'=>$uri_value);
		if($pages_id>0){
			$duplSql .= " AND pages_id != :pages_id";
			$bindData['pages_id'] = $pages_id;
		}
		$duplSql .= " LIMIT 0, 1";
		$duplRows = 0;
		$pagesObj = $this->db->getData($duplSql, $bindData);
		if($pagesObj){
			foreach($pagesObj as $onerow){
				$duplRows = 1;
				$pages_publish = $onerow['pages_publish'];
				if($pages_id==0 && $pages_publish==0){
					$pages_id = $onerow['pages_id'];
					$this->db->update('pages', array('pages_publish'=>1), $pages_id);
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
			if($pages_id==0){
				$conditionarray['created_on'] = date('Y-m-d H:i:s');
				$pages_id = $this->db->insert('pages', $conditionarray);
				if($pages_id){						
					$returnStr = 'Add';
				}
				else{
					$returnStr = 'Adding new Pages';
				}
			}
			else{
				$update = $this->db->update('pages', $conditionarray, $pages_id);
				if($update){
					$activity_feed_title = 'Pages was edited';
					$activity_feed_link = "/Manage_Data/pages/view/$pages_id";
					
					$afData = array('created_on' => date('Y-m-d H:i:s'),
									'users_id' => $_SESSION["users_id"],
									'activity_feed_title' =>  $activity_feed_title,
									'activity_feed_name' => $name,
									'activity_feed_link' => $activity_feed_link,
									'uri_table_name' => "pages",
									'uri_table_field_name' =>"pages_publish",
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
	
	public function aJgetPage_pages($segment3){
	
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
			$this->filterAndOptions_pages();
			$jsonResponse['totalRows'] = $totalRows = $this->totalRows;
		}
		$this->page = $page;
		$this->totalRows = $totalRows;
		$this->rowHeight = $rowHeight;
		
		$jsonResponse['tableRows'] = $this->loadTableRows_pages();
		
		return json_encode($jsonResponse);
	}
	
	private function filterAndOptions_pages(){
		
		$keyword_search = $this->keyword_search;
		
		$_SESSION["current_module"] = "Manage_Data";
		$_SESSION["list_filters"] = array('keyword_search'=>$keyword_search);
		
		$filterSql = "FROM pages WHERE pages_publish = 1";
		$bindData = array();
		if($keyword_search !=''){
			$keyword_search = addslashes(trim($keyword_search));
			if ( $keyword_search == "" ) { $keyword_search = " "; }
			$keyword_searches = explode (" ", $keyword_search);
			if ( strpos($keyword_search, " ") === false ) {$keyword_searches[0] = $keyword_search;}
			$num = 0;
			while ( $num < sizeof($keyword_searches) ) {
				$filterSql .= " AND CONCAT_WS(' ', name, short_description, description) LIKE CONCAT('%', :keyword_search$num, '%')";
				$bindData['keyword_search'.$num] = trim($keyword_searches[$num]);
				$num++;
			}
		}
		
		$totalRows = 0;
		$strextra ="SELECT COUNT(pages_id) AS totalrows $filterSql";
		$query = $this->db->getObj($strextra, $bindData);
		if($query){
			$totalRows = $query->fetch(PDO::FETCH_OBJ)->totalrows;
		}
		$this->totalRows = $totalRows;		
	}
	
   private function loadTableRows_pages(){
		
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
		
		$filterSql = "FROM pages WHERE pages_publish = 1";
		$bindData = array();
		if($keyword_search !=''){
			$keyword_search = addslashes(trim($keyword_search));
			if ( $keyword_search == "" ) { $keyword_search = " "; }
			$keyword_searches = explode (" ", $keyword_search);
			if ( strpos($keyword_search, " ") === false ) {$keyword_searches[0] = $keyword_search;}
			$num = 0;
			while ( $num < sizeof($keyword_searches) ) {
				$filterSql .= " AND CONCAT_WS(' ', name, short_description, description) LIKE CONCAT('%', :keyword_search$num, '%')";
				$bindData['keyword_search'.$num] = trim($keyword_searches[$num]);
				$num++;
			}
		}
		
		$sqlquery = "SELECT * $filterSql ORDER BY name ASC LIMIT $starting_val, $limit";
		$query = $this->db->getData($sqlquery, $bindData);
		$str = '';
		if($query){
			foreach($query as $onerow){

				$pages_id = $onerow['pages_id'];
				$name = stripslashes($onerow['name']);
				$uri_value = stripslashes($onerow['uri_value']);
				$short_description = stripslashes($onerow['short_description']);
				
				//############
				$pageImgUrl = '';
                $filePath = "./assets/accounts/page_$pages_id".'_';
                $pics = glob($filePath."*.jpg");
				if(!$pics){
					$pics = glob($filePath."*.png");
				}
                if($pics){
                    foreach($pics as $onePicture){
                        $prodImg = str_replace("./assets/accounts/", '', $onePicture);
                        $pageImgUrl = str_replace('./', '/', $onePicture);
                    }
                }
                if(!empty($pageImgUrl)){
                    $pageImg = "<div class=\"currentPicture\"><img alt=\"".strip_tags(addslashes($name))."\" class=\"img-responsive maxheight250\" src=\"$pageImgUrl\"></div>";
                }
                else{
                    $pageImg = "<button class=\"btn btn-default\" onClick=\"upload_dialog('Upload Page Picture', 'pages', 'page_$pages_id"."_');\">Upload<button>";
                }
				//############
				
				$str .= "<tr class=\"cursor\" onClick=\"getOneRowInfo('pages', $pages_id);\">
							<td data-title=\"ID\" align=\"right\">$pages_id</td>
							<td data-title=\"Name\" align=\"left\">$name</td>
							<td data-title=\"URI Value\" align=\"left\">$uri_value</td>
							<td data-title=\"Short Description\" align=\"left\">$short_description</td>
							<td data-title=\"Banner Picture\" align=\"left\" id=\"page$pages_id\" class=\"positionrelative\">$pageImg</td>
						</tr>";
			}
		}
		else{
			$str .= "<tr><td colspan=\"4\" class=\"errormsg\">There is no pages meet this criteria</td></tr>";
		}
		return $str;   
	}
	
	//========================For Customer Reviews module=======================//    		
	public function customer_reviews($segment3){
		
		$this->pageTitle = $GLOBALS['title'];
		$list_filters = $_SESSION['list_filters']??array();
		
		$keyword_search = $list_filters['keyword_search']??'';
		$this->keyword_search = $keyword_search;
		
		$this->filterAndOptions_customer_reviews();
		
		$page = !empty($segment3) ? intval($segment3):1;
		if($page<=0){$page = 1;}
		if(!isset($_SESSION['limit'])){$_SESSION['limit'] = 'auto';}
		$limit = $_SESSION['limit'];
		
		$this->rowHeight = 34;
		$this->page = $page;
		$tableRows = $this->loadTableRows_customer_reviews();
		
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
						<h1 class=\"metatitle\">Customer Reviews <i class=\"fa fa-info-circle txt16normal\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Customer Reviews List\"></i></h1>
					</div>				
					<div class=\"col-sm-12 col-md-6\">
						<div class=\"input-group\">
							<input type=\"text\" placeholder=\"Search Customer Reviews\" value=\"$keyword_search\" id=\"keyword_search\" name=\"keyword_search\" class=\"form-control\" maxlength=\"50\" />
							<span class=\"input-group-addon cursor\" onClick=\"checkAndLoadFilterData();\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Search Customer Reviews\">
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
										<th class=\"txtcenter\">Reviews Date</th>
										<th class=\"txtcenter\">Rating</th>
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
				<h4 class=\"borderbottom\" id=\"formtitle\">Add New Customer Reviews</h4>
				<form action=\"#\" name=\"frmcustomer_reviews\" id=\"frmcustomer_reviews\" onsubmit=\"return AJsave_customer_reviews();\" enctype=\"multipart/form-data\" method=\"post\" accept-charset=\"utf-8\">
					<div class=\"form-group\">
						<label for=\"name\">Name<span class=\"required\">*</span></label>
						<input type=\"text\" required class=\"form-control\" name=\"name\" id=\"name\" value=\"\" size=\"100\" maxlength=\"100\" />
						<span id=\"error_name\" class=\"errormsg\"></span>
					</div>
					<div class=\"form-group\">
						<label for=\"address\">Address<span class=\"required\">*</span></label>
						<input type=\"text\" required class=\"form-control\" name=\"address\" id=\"address\" value=\"\" size=\"255\" maxlength=\"255\" />
						<span id=\"error_name\" class=\"errormsg\"></span>
					</div>
					<div class=\"form-group\">
						<label for=\"reviews_date\">Reviews Date<span class=\"required\">*</span></label>
						<input type=\"text\" required class=\"form-control DateField\" name=\"reviews_date\" id=\"reviews_date\" value=\"\" size=\"10\" maxlength=\"10\"/>
						<span id=\"error_reviews_date\" class=\"errormsg\"></span>
					</div>
					<div class=\"form-group\">
						<label for=\"reviews_rating\">Reviews Rating<span class=\"required\">*</span></label>
						<select required class=\"form-control\" name=\"reviews_rating\" id=\"reviews_rating\">
							<option value=\"5\">5 Star</option>
							<option value=\"4.5\">4.5 Star</option>
							<option value=\"4.0\">4.0 Star</option>
						</select>
						<span id=\"error_reviews_rating\" class=\"errormsg\"></span>
					</div>
					<div class=\"form-group\">
						<label for=\"description\">Description<span class=\"required\">*</span></label>
						<textarea rows=\"5\" cols=\"50\" required class=\"form-control\" name=\"description\" id=\"description\"></textarea>
						<span id=\"error_description\" class=\"errormsg\"></span>
					</div>
					<div class=\"form-group\">
						<input type=\"hidden\" name=\"customer_reviews_id\" id=\"customer_reviews_id\" value=\"0\">
						<input type=\"submit\" id=\"submit\" class=\"btn btn-primary backlink\" value=\" Save \" />
						<input type=\"button\" name=\"reset\" id=\"reset\" onClick=\"resetForm_customer_reviews();\" value=\"Cancel\" class=\"hidediv btn btn-warning floatleft mleft10\" />
						<input type=\"button\" name=\"archive\" id=\"archive\" value=\"Archive\" class=\"hidediv btn btn-danger floatright\" />
					</div>
				</form>
				<script language=\"JavaScript\" type=\"text/javascript\">
					//var descriptionField = document.getElementById(\"descriptionField\");
					//descriptionField.appendChild(wysiwyrEditor('description'));
				</script>
			</div>
		</div>";
		
		$htmlStr = $this->htmlBody($innerHTMLStr);
		return $htmlStr;
	}
	
	public function AJsave_customer_reviews(){
	
		
		$users_id = $_SESSION["users_id"]??0;
		$returnStr = 'Ok';		
		$savemsg = '';
		$customer_reviews_id = $_POST['customer_reviews_id']??0;
		$name = trim(addslashes($_POST['name']??''));
		$address = trim(addslashes($_POST['address']??''));
		$reviews_date = trim(addslashes($_POST['reviews_date']??''));
		if(strlen($reviews_date)==10){
			$reviews_date = date('Y-m-d', strtotime($reviews_date));
		}
		else{
			$reviews_date = date('Y-m-d');
		}    
		$reviews_rating = trim(addslashes($_POST['reviews_rating']??''));
		$description = trim(addslashes($_POST['description']??''));
		
		$conditionarray = array();
		$conditionarray['name'] = $name;
		$conditionarray['address'] = $address;
		$conditionarray['reviews_date'] = $reviews_date;
		$conditionarray['reviews_rating'] = $reviews_rating;
		$conditionarray['description'] = $description;
		$conditionarray['last_updated'] = date('Y-m-d H:i:s');
		$conditionarray['users_id'] = $users_id;
		
		$duplSql = "SELECT customer_reviews_publish, customer_reviews_id FROM customer_reviews WHERE name = :name AND reviews_date = :reviews_date";
		$bindData = array('name'=>$name, 'reviews_date'=>$reviews_date);
		if($customer_reviews_id>0){
			$duplSql .= " AND customer_reviews_id != :customer_reviews_id";
			$bindData['customer_reviews_id'] = $customer_reviews_id;
		}
		$duplSql .= " LIMIT 0, 1";
		$duplRows = 0;
		$customer_reviewsObj = $this->db->getData($duplSql, $bindData);
		if($customer_reviewsObj){
			foreach($customer_reviewsObj as $onerow){
				$duplRows = 1;
				$customer_reviews_publish = $onerow['customer_reviews_publish'];
				if($customer_reviews_id==0 && $customer_reviews_publish==0){
					$customer_reviews_id = $onerow['customer_reviews_id'];
					$this->db->update('customer_reviews', array('customer_reviews_publish'=>1), $customer_reviews_id);
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
			if($customer_reviews_id==0){
				$conditionarray['created_on'] = date('Y-m-d H:i:s');
				$customer_reviews_id = $this->db->insert('customer_reviews', $conditionarray);
				if($customer_reviews_id){						
					$returnStr = 'Add';
				}
				else{
					$returnStr = 'Adding new Customer Reviews';
				}
			}
			else{
				$update = $this->db->update('customer_reviews', $conditionarray, $customer_reviews_id);
				if($update){
					$activity_feed_title = 'Customer Reviews was edited';
					$activity_feed_link = "/Manage_Data/customer_reviews/view/$customer_reviews_id";
					
					$afData = array('created_on' => date('Y-m-d H:i:s'),
									'users_id' => $_SESSION["users_id"],
									'activity_feed_title' =>  $activity_feed_title,
									'activity_feed_name' => $name,
									'activity_feed_link' => $activity_feed_link,
									'uri_table_name' => "customer_reviews",
									'uri_table_field_name' =>"customer_reviews_publish",
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
	
	public function aJgetPage_customer_reviews($segment3){
	
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
			$this->filterAndOptions_customer_reviews();
			$jsonResponse['totalRows'] = $totalRows = $this->totalRows;
		}
		$this->page = $page;
		$this->totalRows = $totalRows;
		$this->rowHeight = $rowHeight;
		
		$jsonResponse['tableRows'] = $this->loadTableRows_customer_reviews();
		
		return json_encode($jsonResponse);
	}
	
	private function filterAndOptions_customer_reviews(){
		
		$keyword_search = $this->keyword_search;
		
		$_SESSION["current_module"] = "Manage_Data";
		$_SESSION["list_filters"] = array('keyword_search'=>$keyword_search);
		
		$filterSql = "FROM customer_reviews WHERE customer_reviews_publish = 1";
		$bindData = array();
		if($keyword_search !=''){
			$keyword_search = addslashes(trim($keyword_search));
			if ( $keyword_search == "" ) { $keyword_search = " "; }
			$keyword_searches = explode (" ", $keyword_search);
			if ( strpos($keyword_search, " ") === false ) {$keyword_searches[0] = $keyword_search;}
			$num = 0;
			while ( $num < sizeof($keyword_searches) ) {
				$filterSql .= " AND CONCAT_WS(' ', name, address, description) LIKE CONCAT('%', :keyword_search$num, '%')";
				$bindData['keyword_search'.$num] = trim($keyword_searches[$num]);
				$num++;
			}
		}
		
		$totalRows = 0;
		$strextra ="SELECT COUNT(customer_reviews_id) AS totalrows $filterSql";
		$query = $this->db->getObj($strextra, $bindData);
		if($query){
			$totalRows = $query->fetch(PDO::FETCH_OBJ)->totalrows;
		}
		$this->totalRows = $totalRows;		
	}
	
  	private function loadTableRows_customer_reviews(){
		
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
		
		$filterSql = "FROM customer_reviews WHERE customer_reviews_publish = 1";
		$bindData = array();
		if($keyword_search !=''){
			$keyword_search = addslashes(trim($keyword_search));
			if ( $keyword_search == "" ) { $keyword_search = " "; }
			$keyword_searches = explode (" ", $keyword_search);
			if ( strpos($keyword_search, " ") === false ) {$keyword_searches[0] = $keyword_search;}
			$num = 0;
			while ( $num < sizeof($keyword_searches) ) {
				$filterSql .= " AND CONCAT_WS(' ', name, address, description) LIKE CONCAT('%', :keyword_search$num, '%')";
				$bindData['keyword_search'.$num] = trim($keyword_searches[$num]);
				$num++;
			}
		}
		
		$sqlquery = "SELECT * $filterSql ORDER BY name ASC LIMIT $starting_val, $limit";
		$query = $this->db->getData($sqlquery, $bindData);
		$str = '';
		if($query){
			foreach($query as $onerow){

				$customer_reviews_id = $onerow['customer_reviews_id'];
				$name = stripslashes($onerow['name']);
				$address = stripslashes($onerow['address']);
				$reviews_date = stripslashes($onerow['reviews_date']);
				$reviews_rating = stripslashes($onerow['reviews_rating']);

				$str .= "<tr class=\"cursor\" onClick=\"getOneRowInfo('customer_reviews', $customer_reviews_id);\">
							<td data-title=\"ID\" align=\"right\">$customer_reviews_id</td>
							<td data-title=\"Name, address\" align=\"left\">$name, $address</td>
							<td data-title=\"Reviews Date\" align=\"left\">$reviews_date</td>
							<td data-title=\"Reviews Rating\" align=\"left\">$reviews_rating</td>
						</tr>";
			}
		}
		else{
			$str .= "<tr><td colspan=\"4\" class=\"txtred\">There is no Customer Reviews meet this criteria</td></tr>";
		}
		return $str;
  	}
  	
	//========================For Why choose us module=======================//    		
	public function why_choose_us($segment3){
		
		$this->pageTitle = $GLOBALS['title'];
		$list_filters = $_SESSION['list_filters']??array();
		
		$keyword_search = $list_filters['keyword_search']??'';
		$this->keyword_search = $keyword_search;
		
		$this->filterAndOptions_why_choose_us();
		
		$page = !empty($segment3) ? intval($segment3):1;
		if($page<=0){$page = 1;}
		if(!isset($_SESSION['limit'])){$_SESSION['limit'] = 'auto';}
		$limit = $_SESSION['limit'];
		
		$this->rowHeight = 34;
		$this->page = $page;
		$tableRows = $this->loadTableRows_why_choose_us();
		
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
						<h1 class=\"metatitle\">Why choose us List <i class=\"fa fa-info-circle txt16normal\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Why choose us List\"></i></h1>
					</div>				
					<div class=\"col-sm-12 col-md-6\">
						<div class=\"input-group\">
							<input type=\"text\" placeholder=\"Search Why choose us\" value=\"$keyword_search\" id=\"keyword_search\" name=\"keyword_search\" class=\"form-control\" maxlength=\"50\" />
							<span class=\"input-group-addon cursor\" onClick=\"checkAndLoadFilterData();\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Search Why choose us\">
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
										<th class=\"txtcenter\">Description</th>
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
				<h4 class=\"borderbottom\" id=\"formtitle\">Add New Why choose us</h4>
				<form action=\"#\" name=\"frmwhy_choose_us\" id=\"frmwhy_choose_us\" onsubmit=\"return AJsave_why_choose_us();\" enctype=\"multipart/form-data\" method=\"post\" accept-charset=\"utf-8\">
					<div class=\"form-group\">
						<label for=\"name\">Name<span class=\"required\">*</span></label>
						<input type=\"text\" required class=\"form-control\" name=\"name\" id=\"name\" value=\"\" size=\"255\" maxlength=\"255\"/>
						<span id=\"error_name\" class=\"errormsg\"></span>
					</div>
					<div class=\"form-group\">
						<label for=\"description\">Description<span class=\"required\">*</span></label>
						<textarea rows=\"15\" cols=\"50\" required class=\"form-control\" name=\"description\" id=\"description\"></textarea>
						<span id=\"error_description\" class=\"errormsg\"></span>
					</div>
					<div class=\"form-group\">
						<input type=\"hidden\" name=\"why_choose_us_id\" id=\"why_choose_us_id\" value=\"0\">
						<input type=\"submit\" id=\"submit\" class=\"btn btn-primary backlink\" value=\" Save \" />
						<input type=\"button\" name=\"reset\" id=\"reset\" onClick=\"resetForm_why_choose_us();\" value=\"Cancel\" class=\"hidediv btn btn-warning floatleft mleft10\" />
						<input type=\"button\" name=\"archive\" id=\"archive\" value=\"Archive\" class=\"hidediv btn btn-danger floatright\" />
					</div>
				</form>
				<!--link href=\"/assets/css/jqueryimageupload.css\" rel=\"stylesheet\" type=\"text/css\">
				<script type=\"text/javascript\" src=\"/assets/js/jquery.form.min.js\"></script-->
			</div>
		</div>";
    		
		$htmlStr = $this->htmlBody($innerHTMLStr);
		return $htmlStr;
	}
	
	public function AJsave_why_choose_us(){
		
		$users_id = $_SESSION["users_id"]??0;
		$returnStr = 'Ok';		
		$savemsg = '';
		$why_choose_us_id = $_POST['why_choose_us_id']??0;
		$name = trim(addslashes($_POST['name']??''));
		$description = trim(addslashes($_POST['description']??''));
		
		$conditionarray = array();
		$conditionarray['name'] = $name;
		$conditionarray['description'] = $description;
		$conditionarray['last_updated'] = date('Y-m-d H:i:s');
		$conditionarray['users_id'] = $users_id;
		
		$duplSql = "SELECT why_choose_us_publish, why_choose_us_id FROM why_choose_us WHERE name = :name";
		$bindData = array('name'=>$name);
		if($why_choose_us_id>0){
			$duplSql .= " AND why_choose_us_id != :why_choose_us_id";
			$bindData['why_choose_us_id'] = $why_choose_us_id;
		}
		$duplSql .= " LIMIT 0, 1";
		$duplRows = 0;
		$why_choose_usObj = $this->db->getData($duplSql, $bindData);
		if($why_choose_usObj){
			foreach($why_choose_usObj as $onerow){
				$duplRows = 1;
				$why_choose_us_publish = $onerow['why_choose_us_publish'];
				if($why_choose_us_id==0 && $why_choose_us_publish==0){
					$why_choose_us_id = $onerow['why_choose_us_id'];
					$this->db->update('why_choose_us', array('why_choose_us_publish'=>1), $why_choose_us_id);
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
			if($why_choose_us_id==0){
				$conditionarray['created_on'] = date('Y-m-d H:i:s');
				$why_choose_us_id = $this->db->insert('why_choose_us', $conditionarray);
				if($why_choose_us_id){						
					$returnStr = 'Add';
				}
				else{
					$returnStr = 'Adding new Why choose us';
				}
			}
			else{

				$update = $this->db->update('why_choose_us', $conditionarray, $why_choose_us_id);
				if($update){
					$activity_feed_title = 'Why choose us was edited';
					$activity_feed_link = "/Manage_Data/why_choose_us/view/$why_choose_us_id";
					
					$afData = array('created_on' => date('Y-m-d H:i:s'),
									'users_id' => $_SESSION["users_id"],
									'activity_feed_title' =>  $activity_feed_title,
									'activity_feed_name' => $name,
									'activity_feed_link' => $activity_feed_link,
									'uri_table_name' => "why_choose_us",
									'uri_table_field_name' =>"why_choose_us_publish",
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
	
	public function aJgetPage_why_choose_us($segment3){
	
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
			$this->filterAndOptions_why_choose_us();
			$jsonResponse['totalRows'] = $totalRows = $this->totalRows;
		}
		$this->page = $page;
		$this->totalRows = $totalRows;
		$this->rowHeight = $rowHeight;
		
		$jsonResponse['tableRows'] = $this->loadTableRows_why_choose_us();
		
		return json_encode($jsonResponse);
	}
	
	private function filterAndOptions_why_choose_us(){
		
		$keyword_search = $this->keyword_search;
		
		$_SESSION["current_module"] = "Manage_Data";
		$_SESSION["list_filters"] = array('keyword_search'=>$keyword_search);
		
		$filterSql = "FROM why_choose_us WHERE why_choose_us_publish = 1";
		$bindData = array();
		if($keyword_search !=''){
			$keyword_search = addslashes(trim($keyword_search));
			if ( $keyword_search == "" ) { $keyword_search = " "; }
			$keyword_searches = explode (" ", $keyword_search);
			if ( strpos($keyword_search, " ") === false ) {$keyword_searches[0] = $keyword_search;}
			$num = 0;
			while ( $num < sizeof($keyword_searches) ) {
				$filterSql .= " AND name LIKE CONCAT('%', :keyword_search$num, '%')";
				$bindData['keyword_search'.$num] = trim($keyword_searches[$num]);
				$num++;
			}
		}
		
		$totalRows = 0;
		$strextra ="SELECT COUNT(why_choose_us_id) AS totalrows $filterSql";
		$query = $this->db->getObj($strextra, $bindData);
		if($query){
			$totalRows = $query->fetch(PDO::FETCH_OBJ)->totalrows;
		}
		$this->totalRows = $totalRows;		
	}
	
  	private function loadTableRows_why_choose_us(){
		
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
		
		$filterSql = "FROM why_choose_us WHERE why_choose_us_publish = 1";
		$bindData = array();
		if($keyword_search !=''){
			$keyword_search = addslashes(trim($keyword_search));
			if ( $keyword_search == "" ) { $keyword_search = " "; }
			$keyword_searches = explode (" ", $keyword_search);
			if ( strpos($keyword_search, " ") === false ) {$keyword_searches[0] = $keyword_search;}
			$num = 0;
			while ( $num < sizeof($keyword_searches) ) {
				$filterSql .= " AND name LIKE CONCAT('%', :keyword_search$num, '%')";
				$bindData['keyword_search'.$num] = trim($keyword_searches[$num]);
				$num++;
			}
		}
		
		$sqlquery = "SELECT * $filterSql ORDER BY name ASC LIMIT $starting_val, $limit";
		$query = $this->db->getData($sqlquery, $bindData);
		$str = '';
		if($query){
			foreach($query as $onerow){

				$why_choose_us_id = $onerow['why_choose_us_id'];
				$name = trim(stripslashes((string) $onerow['name']));
				$description = trim(stripslashes((string) $onerow['description']));
				
				$str .= "<tr class=\"cursor\">
							<td data-title=\"ID\" onClick=\"getOneRowInfo('why_choose_us', $why_choose_us_id);\" align=\"right\">$why_choose_us_id</td>
							<td data-title=\"Name\" onClick=\"getOneRowInfo('why_choose_us', $why_choose_us_id);\" align=\"left\">$name</td>
							<td data-title=\"Description\" align=\"left\">$description</td>
						</tr>";
			}
		}
		else{
			$str .= "<tr><td class=\"errormsg\" colspan=\"3\">There is no why_choose_us meet this criteria</td></tr>";
		}
		return $str;
  	}


	//========================For pest service module=======================//    		
	public function pest_services($segment3){
		
		$this->pageTitle = $GLOBALS['title'];
		$list_filters = $_SESSION['list_filters']??array();
		
		$keyword_search = $list_filters['keyword_search']??'';
		$this->keyword_search = $keyword_search;
		
		$this->filterAndOptions_pest_services();
		
		$page = !empty($segment3) ? intval($segment3):1;
		if($page<=0){$page = 1;}
		if(!isset($_SESSION['limit'])){$_SESSION['limit'] = 'auto';}
		$limit = $_SESSION['limit'];
		
		$this->rowHeight = 34;
		$this->page = $page;
		$tableRows = $this->loadTableRows_pest_services();
		
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
						<h1 class=\"metatitle\">Pest service List <i class=\"fa fa-info-circle txt16normal\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Pest service List\"></i></h1>
					</div>				
					<div class=\"col-sm-12 col-md-6\">
						<div class=\"input-group\">
							<input type=\"text\" placeholder=\"Search Why choose us\" value=\"$keyword_search\" id=\"keyword_search\" name=\"keyword_search\" class=\"form-control\" maxlength=\"50\" />
							<span class=\"input-group-addon cursor\" onClick=\"checkAndLoadFilterData();\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Search Why choose us\">
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
										<th class=\"txtcenter\">Price</th>
										<th class=\"txtcenter\">Thumb Image</th>
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
				<h4 class=\"borderbottom\" id=\"formtitle\">Add New Why choose us</h4>
				<form action=\"#\" name=\"frmpest_services\" id=\"frmpest_services\" onsubmit=\"return AJsave_pest_services();\" enctype=\"multipart/form-data\" method=\"post\" accept-charset=\"utf-8\">
					<div class=\"form-group\">
						<label for=\"name\">Name<span class=\"required\">*</span></label>
						<input type=\"text\" required class=\"form-control\" name=\"name\" id=\"name\" value=\"\" size=\"255\" maxlength=\"255\"/>
						<span id=\"error_name\" class=\"errormsg\"></span>
					</div>
					<div class=\"form-group\">
						<label for=\"price\">Price<span class=\"required\">*</span></label>
						<input type=\"text\" required class=\"form-control\" name=\"price\" id=\"price\" value=\"\" size=\"255\" maxlength=\"255\"/>
						<span id=\"error_price\" class=\"errormsg\"></span>
					</div>
					<div class=\"form-group\">
						<label for=\"description\">Description</label>
						<textarea rows=\"5\" cols=\"50\" class=\"form-control\" name=\"description\" id=\"description\"></textarea>
						<span id=\"error_description\" class=\"errormsg\"></span>
					</div>
					<div class=\"form-group\">
						<input type=\"hidden\" name=\"pest_services_id\" id=\"pest_services_id\" value=\"0\">
						<input type=\"submit\" id=\"submit\" class=\"btn btn-primary backlink\" value=\" Save \" />
						<input type=\"button\" name=\"reset\" id=\"reset\" onClick=\"resetForm_pest_services();\" value=\"Cancel\" class=\"hidediv btn btn-warning floatleft mleft10\" />
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
	
	public function AJsave_pest_services(){
		
		$users_id = $_SESSION["users_id"]??0;
		$returnStr = 'Ok';		
		$savemsg = '';
		$pest_services_id = $_POST['pest_services_id']??0;
		$name = trim(addslashes($_POST['name']??''));
		$price = trim(addslashes($_POST['price']??''));
		$description = trim(addslashes($_POST['description']??''));
		
		$conditionarray = array();
		$conditionarray['name'] = $name;
		$conditionarray['price'] = $price;
		$conditionarray['description'] = $description;
		$conditionarray['last_updated'] = date('Y-m-d H:i:s');
		$conditionarray['users_id'] = $users_id;
		
		$duplSql = "SELECT pest_services_publish, pest_services_id FROM pest_services WHERE name = :name";
		$bindData = array('name'=>$name);
		if($pest_services_id>0){
			$duplSql .= " AND pest_services_id != :pest_services_id";
			$bindData['pest_services_id'] = $pest_services_id;
		}
		$duplSql .= " LIMIT 0, 1";
		$duplRows = 0;
		$pest_servicesObj = $this->db->getData($duplSql, $bindData);
		if($pest_servicesObj){
			foreach($pest_servicesObj as $onerow){
				$duplRows = 1;
				$pest_services_publish = $onerow['pest_services_publish'];
				if($pest_services_id==0 && $pest_services_publish==0){
					$pest_services_id = $onerow['pest_services_id'];
					$this->db->update('pest_services', array('pest_services_publish'=>1), $pest_services_id);
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
			if($pest_services_id==0){
				$conditionarray['created_on'] = date('Y-m-d H:i:s');
				$pest_services_id = $this->db->insert('pest_services', $conditionarray);
				if($pest_services_id){						
					$returnStr = 'Add';
				}
				else{
					$returnStr = 'Adding new Pest service';
				}
			}
			else{

				$update = $this->db->update('pest_services', $conditionarray, $pest_services_id);
				if($update){
					$activity_feed_title = 'Why choose us was edited';
					$activity_feed_link = "/Manage_Data/pest_services/view/$pest_services_id";
					
					$afData = array('created_on' => date('Y-m-d H:i:s'),
									'users_id' => $_SESSION["users_id"],
									'activity_feed_title' =>  $activity_feed_title,
									'activity_feed_name' => $name,
									'activity_feed_link' => $activity_feed_link,
									'uri_table_name' => "pest_services",
									'uri_table_field_name' =>"pest_services_publish",
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
	
	public function aJgetPage_pest_services($segment3){
	
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
			$this->filterAndOptions_pest_services();
			$jsonResponse['totalRows'] = $totalRows = $this->totalRows;
		}
		$this->page = $page;
		$this->totalRows = $totalRows;
		$this->rowHeight = $rowHeight;
		
		$jsonResponse['tableRows'] = $this->loadTableRows_pest_services();
		
		return json_encode($jsonResponse);
	}
	
	private function filterAndOptions_pest_services(){
		
		$keyword_search = $this->keyword_search;
		
		$_SESSION["current_module"] = "Manage_Data";
		$_SESSION["list_filters"] = array('keyword_search'=>$keyword_search);
		
		$filterSql = "FROM pest_services WHERE pest_services_publish = 1";
		$bindData = array();
		if($keyword_search !=''){
			$keyword_search = addslashes(trim($keyword_search));
			if ( $keyword_search == "" ) { $keyword_search = " "; }
			$keyword_searches = explode (" ", $keyword_search);
			if ( strpos($keyword_search, " ") === false ) {$keyword_searches[0] = $keyword_search;}
			$num = 0;
			while ( $num < sizeof($keyword_searches) ) {
				$filterSql .= " AND name LIKE CONCAT('%', :keyword_search$num, '%')";
				$bindData['keyword_search'.$num] = trim($keyword_searches[$num]);
				$num++;
			}
		}
		
		$totalRows = 0;
		$strextra ="SELECT COUNT(pest_services_id) AS totalrows $filterSql";
		$query = $this->db->getObj($strextra, $bindData);
		if($query){
			$totalRows = $query->fetch(PDO::FETCH_OBJ)->totalrows;
		}
		$this->totalRows = $totalRows;		
	}
	
  	private function loadTableRows_pest_services(){
		
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
		
		$filterSql = "FROM pest_services WHERE pest_services_publish = 1";
		$bindData = array();
		if($keyword_search !=''){
			$keyword_search = addslashes(trim($keyword_search));
			if ( $keyword_search == "" ) { $keyword_search = " "; }
			$keyword_searches = explode (" ", $keyword_search);
			if ( strpos($keyword_search, " ") === false ) {$keyword_searches[0] = $keyword_search;}
			$num = 0;
			while ( $num < sizeof($keyword_searches) ) {
				$filterSql .= " AND name LIKE CONCAT('%', :keyword_search$num, '%')";
				$bindData['keyword_search'.$num] = trim($keyword_searches[$num]);
				$num++;
			}
		}
		
		$sqlquery = "SELECT * $filterSql ORDER BY name ASC LIMIT $starting_val, $limit";
		
		$query = $this->db->getData($sqlquery, $bindData);
		// var_dump($sqlquery);exit;

		$str = '';

		if($query){
			foreach($query as $onerow){

				$pest_services_id = $onerow['pest_services_id'];
				$name = trim(stripslashes((string) $onerow['name']));
				$price = trim(stripslashes((string) $onerow['price']));

				//############
				$serviceImgUrl = '';
                $filePath = "./assets/accounts/srvn_$pest_services_id".'_';
                $pics = glob($filePath."*.jpg");
				if(!$pics){
					$pics = glob($filePath."*.png");
				}
                if($pics){
                    foreach($pics as $onePicture){
                        $prodImg = str_replace("./assets/accounts/", '', $onePicture);
                        $serviceImgUrl = str_replace('./', '/', $onePicture);
                    }
                }
                if(!empty($serviceImgUrl)){
                    $serviceImg = "<div class=\"currentPicture\"><img alt=\"".strip_tags(addslashes($name))."\" class=\"img-responsive maxheight250\" src=\"$serviceImgUrl\"></div>";
                }
                else{
                    $serviceImg = "<button class=\"btn btn-default\" onClick=\"upload_dialog('Upload Pest Service Thumb', 'pest_services', 'srvn_$pest_services_id"."_');\">Upload<button>";
                }
				//############
				
				$str .= "<tr class=\"cursor\">
							<td data-title=\"ID\" onClick=\"getOneRowInfo('pest_services', $pest_services_id);\" align=\"right\">$pest_services_id</td>
							<td data-title=\"Name\" onClick=\"getOneRowInfo('pest_services', $pest_services_id);\" align=\"left\">$name</td>
							<td data-title=\"Price\" align=\"left\">$price</td>
							<td data-title=\"Service Thumb\" align=\"left\" id=\"srvn$pest_services_id\" class=\"positionrelative\">$serviceImg</td>
						</tr>";
			}
		}
		else{
			$str .= "<tr><td class=\"errormsg\" colspan=\"3\">There is no pest_services meet this criteria</td></tr>";
		}
		
		return $str;
  	}
	
	
	//========================For Videos module=======================//    		
	public function videos($segment3){
		
		$this->pageTitle = $GLOBALS['title'];
		$list_filters = $_SESSION['list_filters']??array();
		
		$keyword_search = $list_filters['keyword_search']??'';
		$this->keyword_search = $keyword_search;
		
		$this->filterAndOptions_videos();
		
		$page = !empty($segment3) ? intval($segment3):1;
		if($page<=0){$page = 1;}
		if(!isset($_SESSION['limit'])){$_SESSION['limit'] = 'auto';}
		$limit = $_SESSION['limit'];
		
		$this->rowHeight = 34;
		$this->page = $page;
		$tableRows = $this->loadTableRows_videos();
		
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
						<h1 class=\"metatitle\">All Videos List <i class=\"fa fa-info-circle txt16normal\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"All Videos List\"></i></h1>
					</div>				
					<div class=\"col-sm-12 col-md-6\">
						<div class=\"input-group\">
							<input type=\"text\" placeholder=\"Search here\" value=\"$keyword_search\" id=\"keyword_search\" name=\"keyword_search\" class=\"form-control\" maxlength=\"50\" />
							<span class=\"input-group-addon cursor\" onClick=\"checkAndLoadFilterData();\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Search here\">
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
										<th class=\"txtcenter\">Name</th>
										<th class=\"txtcenter\">Videos URL</th>
										<th class=\"txtcenter\">Thumb Image</th>
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
				<h4 class=\"borderbottom\" id=\"formtitle\">Add / Change Video</h4>
				<form action=\"#\" name=\"frmvideos\" id=\"frmvideos\" onsubmit=\"return AJsave_videos();\" enctype=\"multipart/form-data\" method=\"post\" accept-charset=\"utf-8\">
					<div class=\"form-group\">
						<label for=\"name\">Video Name<span class=\"required\">*</span></label>
						<input type=\"text\" required class=\"form-control\" name=\"name\" id=\"name\" value=\"\" size=\"100\" maxlength=\"100\" />
						<span id=\"error_name\" class=\"errormsg\"></span>
					</div>
					<div class=\"form-group\">
						<label for=\"youtube_url\">Video URL<span class=\"required\">*</span></label>
						<input type=\"text\" required class=\"form-control\" name=\"youtube_url\" id=\"youtube_url\" value=\"\" size=\"100\" maxlength=\"100\" />
						<span id=\"error_youtube_url\" class=\"errormsg\"></span>
					</div>
					<div class=\"form-group\">
						<input type=\"hidden\" name=\"videos_id\" id=\"videos_id\" value=\"0\">
						<input type=\"submit\" id=\"submit\" class=\"btn btn-primary backlink\" value=\" Save \" />
						<input type=\"button\" name=\"reset\" id=\"reset\" onClick=\"resetForm_videos();\" value=\"Cancel\" class=\"hidediv btn btn-warning floatleft mleft10\" />
						<input type=\"button\" name=\"archive\" id=\"archive\" value=\"Archive\" class=\"hidediv btn btn-danger floatright\" />
					</div>
				</form>
				<link href=\"/assets/css/jqueryimageupload.css\" rel=\"stylesheet\" type=\"text/css\">
				<script type=\"text/javascript\" src=\"/assets/js/jquery.form.min.js\"></script>
				<script language=\"JavaScript\" type=\"text/javascript\">
					//var descriptionField = document.getElementById(\"descriptionField\");
					//descriptionField.appendChild(wysiwyrEditor('description'));
				</script>
			</div>
		</div>";
		
		$htmlStr = $this->htmlBody($innerHTMLStr);
		return $htmlStr;
	}
	
	public function AJsave_videos(){		
		$users_id = $_SESSION["users_id"]??0;
		$returnStr = 'Ok';		
		$savemsg = '';
		$videos_id = $_POST['videos_id']??0;
		$name = trim(addslashes((string) $_POST['name']??''));
		$youtube_url = trim(addslashes((string) $_POST['youtube_url']??''));
		
		$conditionarray = array();
		$conditionarray['name'] = $name;
		$conditionarray['youtube_url'] = $youtube_url;
		$conditionarray['last_updated'] = date('Y-m-d H:i:s');
		$conditionarray['users_id'] = $users_id;
		
		$duplSql = "SELECT videos_publish, videos_id FROM videos WHERE name = :name AND youtube_url = :youtube_url";
		$bindData = array('name'=>$name, 'youtube_url'=>$youtube_url);
		if($videos_id>0){
			$duplSql .= " AND videos_id != :videos_id";
			$bindData['videos_id'] = $videos_id;
		}

		$duplSql .= " LIMIT 0, 1";
		$duplRows = 0;
		$videosObj = $this->db->getData($duplSql, $bindData);
		if($videosObj){
			foreach($videosObj as $onerow){
				$duplRows = 1;
				$videos_publish = $onerow['videos_publish'];
				if($videos_id==0 && $videos_publish==0){
					$videos_id = $onerow['videos_id'];
					$this->db->update('videos', array('videos_publish'=>1), $videos_id);
					$duplRows = 0;
					$returnStr = 'Update';
				}
			}
		}
		
		if($duplRows>0 || empty($name)){
			$savemsg = 'error';
			$returnStr = "<p>Videos URL Already Exist</p>";
		}
		else{			
			if($videos_id==0){
				$conditionarray['created_on'] = date('Y-m-d H:i:s');
				$videos_id = $this->db->insert('videos', $conditionarray);
				if($videos_id){						
					$returnStr = 'Add';
				}
				else{
					$returnStr = 'Error occured while adding new Videos! Please try again.';
				}
			}
			else{
				$update = $this->db->update('videos', $conditionarray, $videos_id);
				if($update){
					$activity_feed_title = 'Videos was edited';
					$activity_feed_link = "/Manage_Data/videos/view/$videos_id";
					
					$afData = array('created_on' => date('Y-m-d H:i:s'),
									'users_id' => $_SESSION["users_id"],
									'activity_feed_title' =>  $activity_feed_title,
									'activity_feed_name' => $name,
									'activity_feed_link' => $activity_feed_link,
									'uri_table_name' => "videos",
									'uri_table_field_name' =>"videos_publish",
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
	
	public function aJgetPage_videos($segment3){
	
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
			$this->filterAndOptions_videos();
			$jsonResponse['totalRows'] = $totalRows = $this->totalRows;
		}
		$this->page = $page;
		$this->totalRows = $totalRows;
		$this->rowHeight = $rowHeight;
		
		$jsonResponse['tableRows'] = $this->loadTableRows_videos();
		
		return json_encode($jsonResponse);
	}
	
	private function filterAndOptions_videos(){
		
		$keyword_search = $this->keyword_search;
		
		$_SESSION["current_module"] = "Manage_Data";
		$_SESSION["list_filters"] = array('keyword_search'=>$keyword_search);
		
		$filterSql = "FROM videos WHERE videos_publish = 1";
		$bindData = array();
		if($keyword_search !=''){
			$keyword_search = addslashes(trim($keyword_search));
			if ( $keyword_search == "" ) { $keyword_search = " "; }
			$keyword_searches = explode (" ", $keyword_search);
			if ( strpos($keyword_search, " ") === false ) {$keyword_searches[0] = $keyword_search;}
			$num = 0;
			while ( $num < sizeof($keyword_searches) ) {
				$filterSql .= " AND CONCAT_WS(' ', name, youtube_url) LIKE CONCAT('%', :keyword_search$num, '%')";
				$bindData['keyword_search'.$num] = trim($keyword_searches[$num]);
				$num++;
			}
		}
		
		$totalRows = 0;
		$strextra ="SELECT COUNT(videos_id) AS totalrows $filterSql";
		$query = $this->db->getObj($strextra, $bindData);
		if($query){
			$totalRows = $query->fetch(PDO::FETCH_OBJ)->totalrows;
		}
		$this->totalRows = $totalRows;		
	}
	
   private function loadTableRows_videos(){
		
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
		
		$filterSql = "FROM videos WHERE videos_publish = 1";
		$bindData = array();
		if($keyword_search !=''){
			$keyword_search = addslashes(trim($keyword_search));
			if ( $keyword_search == "" ) { $keyword_search = " "; }
			$keyword_searches = explode (" ", $keyword_search);
			if ( strpos($keyword_search, " ") === false ) {$keyword_searches[0] = $keyword_search;}
			$num = 0;
			while ( $num < sizeof($keyword_searches) ) {
				$filterSql .= " AND CONCAT_WS(' ', name, youtube_url) LIKE CONCAT('%', :keyword_search$num, '%')";
				$bindData['keyword_search'.$num] = trim($keyword_searches[$num]);
				$num++;
			}
		}
		
		$sqlquery = "SELECT * $filterSql ORDER BY youtube_url ASC LIMIT $starting_val, $limit";
		$query = $this->db->getData($sqlquery, $bindData);
		$str = '';
		if($query){
			foreach($query as $onerow){

				$videos_id = $onerow['videos_id'];
				$name = trim(stripslashes((string) $onerow['name']));
				$youtube_url = trim(stripslashes((string) $onerow['youtube_url']));

				//############
				$videoImgUrl = '';
                $filePath = "./assets/accounts/video_$videos_id".'_';
                $pics = glob($filePath."*.jpg");
				if(!$pics){
					$pics = glob($filePath."*.png");
				}
                if($pics){
                    foreach($pics as $onePicture){
                        $prodImg = str_replace("./assets/accounts/", '', $onePicture);
                        $videoImgUrl = str_replace('./', '/', $onePicture);
                    }
                }
                if(!empty($videoImgUrl)){
                    $videoImg = "<div class=\"currentPicture\"><img alt=\"".strip_tags(addslashes($name))."\" class=\"img-responsive maxheight250\" src=\"$videoImgUrl\"></div>";
                }
                else{
                    $videoImg = "<button class=\"btn btn-default\" onClick=\"upload_dialog('Upload Video Thumb', 'videos', 'video_$videos_id"."_');\">Upload<button>";
                }
				//############

				$str .= "<tr class=\"cursor\" onClick=\"getOneRowInfo('videos', $videos_id);\">
							<td data-title=\"Name\" align=\"left\">$name</td>
							<td data-title=\"Videos URL\" align=\"left\">$youtube_url</td>
							<td data-title=\"Video Thumb\" align=\"left\" id=\"video$videos_id\" class=\"positionrelative\">$videoImg</td>
						</tr>";
			}
		}
		else{
			$str .= "<tr><td colspan=\"2\" class=\"errormsg\">No Videos meet the criteria given</td></tr>";
		}
		return $str;
   }
	
	public function AJget_CustomersPopup(){
		$POST = json_decode(file_get_contents('php://input'), true);
		$customers_id = $POST['customers_id']??0;
		$customFields = $POST['customFields']??0;
		$Common = new Common($this->db);
		$customersData = array();
		$customersData['login'] = '';
		$customersData['customers_id'] = 0;				
		$customersData['first_name'] = '';
		$customersData['last_name'] = '';
		$customersData['email'] = '';
		$customersData['offers_email'] = '';
		$customersData['company'] = '';		
		$customersData['contact_no'] = '';
		$customersData['fax'] = '';	
		$customersData['secondary_phone'] = '';		
		$customersData['shipping_address_one'] = '';
		$customersData['shipping_address_two'] = '';
		$customersData['shipping_city'] = '';
		$customersData['shipping_state'] = '';
		$customersData['shipping_zip'] = '';
		$customersData['shipping_country'] = '';
		$customersData['created_on'] = '';
		$customersData['last_updated'] = '';
		$customersData['website'] = '';
		$customersData['alert_message'] = '';
		$custom_data = '';
		if($customers_id>0){
			$customersObj = $this->db->getObj("SELECT * FROM customers WHERE customers_id = :customers_id", array('customers_id'=>$customers_id),1);
			if($customersObj){
				$customersRow = $customersObj->fetch(PDO::FETCH_OBJ);	

				$customersData['customers_id'] = $customers_id;
				$customersData['first_name'] = stripslashes(trim($customersRow->first_name));
				$customersData['last_name'] = stripslashes(trim($customersRow->last_name));
				$customersData['email'] = trim($customersRow->email);
				$customersData['offers_email'] = $customersRow->offers_email;
				$customersData['company'] = stripslashes(trim($customersRow->company));
				$customersData['contact_no'] = trim($customersRow->contact_no);
				$customersData['secondary_phone'] = trim($customersRow->secondary_phone);
				$customersData['fax'] = trim($customersRow->fax);
				
				$customersData['shipping_address_one'] = stripslashes(trim($customersRow->shipping_address_one));
				$customersData['shipping_address_two'] = stripslashes(trim($customersRow->shipping_address_two));
				$customersData['shipping_city'] = stripslashes(trim($customersRow->shipping_city));
				$customersData['shipping_state'] = stripslashes(trim($customersRow->shipping_state));
				$customersData['shipping_zip'] = trim($customersRow->shipping_zip);
				$customersData['shipping_country'] = stripslashes(trim($customersRow->shipping_country));
				if($customersRow->shipping_country=='0'){
					$customersData['shipping_country'] = '';
				}
				$customersData['created_on'] = $customersRow->created_on;
				$customersData['last_updated'] = $customersRow->last_updated;
				$customersData['website'] = stripslashes(trim($customersRow->website));
				$customersData['alert_message'] = stripslashes(trim($customersRow->alert_message));
				$custom_data = trim($customersRow->custom_data);
			}
		}
		//$customersData['customFieldsHTML'] = $Common->customFormFields('customers', $custom_data);
		
		
		echo json_encode($customersData);
	}
	
	public function htmlBody($pageMiddle){
		$gettingStartedModules = $GLOBALS['viewFunctions'];
		unset($gettingStartedModules['sview']);
		$returnHTML = "<div class=\"row\">
			<div class=\"col-sm-12\">
				<h1 class=\"singin2\">$this->pageTitle <i class=\"fa fa-info-circle txt16normal\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"This page captures the accounts settings\"></i></h1>
			</div>   
		</div>
		<div class=\"row\">
			<div class=\"col-md-2 col-sm-3 pright0\">
				<div style=\"margin-top:0;\" class=\"bs-callout well bs-callout-info\">					
					<a href=\"javascript:void(0);\" id=\"settingsleftsidemenu\">
						<i class=\"fa fa-align-justify fa-2\"></i>                                        
					</a>
					<ul class=\"leftsidemenu settingslefthide\">";
						foreach($gettingStartedModules as $module=>$moduletitle){
							$linkstr = "<a href=\"/Manage_Data/$module\" title=\"$moduletitle\"><span>$moduletitle</span></a>";
							$activeclass = '';
							if(strcmp($GLOBALS['segment2'],$module)==0){
								$linkstr = "<h4>$moduletitle</h4>";
								$activeclass = ' class="activeclass"';
							}
							$returnHTML .= "<li$activeclass>$linkstr</li>";
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
		
	public function getOneRowInfo(){
	
		$returnData = array();
		$returnData['login'] = '';
		
		$tableName = $_POST['tableName']??'';
		$tableId = $_POST['tableId']??0;
		$tableIdName = $tableName.'_id';
		$tableObj = $this->db->getData("SELECT * FROM $tableName WHERE $tableIdName = $tableId", array());
		if(!empty($tableObj)){
			foreach ($tableObj[0] as $key=>$value) {
				$returnData[$key] = stripslashes($value);
			}
		}
		return json_encode($returnData);
	}

	public function AJremoveData(){
	
		$returnData = array();
		$returnData['login'] = '';
		
		$tableName = $_POST['tableName']??'';
		$tableId = $_POST['tableId']??0;
		$nameVal = $_POST['nameVal']??'';
		$publishName = $tableName.'_publish';
		
		$updatetable = $this->db->update($tableName, array($publishName=>0), $tableId);
		if($updatetable){
			$savemsg = 'archive-success';
		}
		else{
			$savemsg = "<p>Error occured while archiving information! Please try again.</p>";
		}
		return json_encode(array('login'=>'', 'returnStr'=>$savemsg));
	}



	//========================For Photo Gallery module=======================//    		
	public function photo_gallery($segment3){
		
		$this->pageTitle = $GLOBALS['title'];
		$list_filters = $_SESSION['list_filters']??array();
		
		$keyword_search = $list_filters['keyword_search']??'';
		$this->keyword_search = $keyword_search;
		
		$this->filterAndOptions_photo_gallery();
		
		$page = !empty($segment3) ? intval($segment3):1;
		if($page<=0){$page = 1;}
		if(!isset($_SESSION['limit'])){$_SESSION['limit'] = 'auto';}
		$limit = $_SESSION['limit'];
		
		$this->rowHeight = 34;
		$this->page = $page;
		$tableRows = $this->loadTableRows_photo_gallery();
		
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
					<div class=\"col-sm-12 col-sm-6\">
						<h1 class=\"metatitle\">All Photo Gallery List</h1>
					</div>				
					<div class=\"col-sm-12 col-sm-6\">
						<div class=\"input-group\">
							<input type=\"text\" placeholder=\"Search Name\" value=\"$keyword_search\" id=\"keyword_search\" name=\"keyword_search\" class=\"form-control\" maxlength=\"50\" />
							<span class=\"input-group-addon cursor\" onClick=\"checkAndLoadFilterData();\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Search Name\">
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
			<link href=\"/assets/css/jqueryimageupload.css\" rel=\"stylesheet\" type=\"text/css\">
			<script type=\"text/javascript\" src=\"/assets/js/jquery.form.min.js\"></script>
			<div class=\"col-sm-12 col-sm-12 col-md-5\">            
				<h4 class=\"borderbottom\" id=\"formtitle\">Add Photo Gallery</h4>
				<form action=\"#\" name=\"frmphoto_gallery\" id=\"frmphoto_gallery\" onsubmit=\"return AJsave_photo_gallery();\" enctype=\"multipart/form-data\" method=\"post\" accept-charset=\"utf-8\">
					<div class=\"form-group\">
						<label for=\"name\">Name<span class=\"required\">*</span></label>
						<input type=\"text\" required class=\"form-control\" name=\"name\" id=\"name\" value=\"\" size=\"35\" maxlength=\"100\" />
						<span id=\"error_name\" class=\"errormsg\"></span>
					</div>
					<div class=\"form-group\">
						<input type=\"hidden\" name=\"photo_gallery_id\" id=\"photo_gallery_id\" value=\"0\">
						<input type=\"submit\" id=\"submit\" class=\"btn btn-primary backlink\" value=\" Save \" />
						<input type=\"button\" name=\"reset\" id=\"reset\" onClick=\"resetForm_photo_gallery();\" value=\"Cancel\" class=\"hidediv btn btn-warning floatleft mleft10\" />
						<input type=\"button\" name=\"archive\" id=\"archive\" value=\"Archive\" class=\"hidediv btn btn-danger floatright\" />
					</div>
				</form>
			</div>
		</div>";
		
		$htmlStr = $this->htmlBody($innerHTMLStr);
		return $htmlStr;
	}
	
	public function AJsave_photo_gallery(){	
		
		$users_id = $_SESSION["users_id"]??0;
		$returnStr = 'Ok';		
		$savemsg = '';
		$photo_gallery_id = $_POST['photo_gallery_id']??0;
		$name = addslashes($_POST['name']??'');
		
		$conditionarray = array();
		$conditionarray['name'] = $name;
		$conditionarray['last_updated'] = date('Y-m-d H:i:s');
		$conditionarray['users_id'] = $users_id;
		
		$duplSql = "SELECT photo_gallery_publish, photo_gallery_id FROM photo_gallery WHERE name = :name";
		$bindData = array('name'=>$name);
		if($photo_gallery_id>0){
			$duplSql .= " AND photo_gallery_id != :photo_gallery_id";
			$bindData['photo_gallery_id'] = $photo_gallery_id;
		}
		$duplSql .= " LIMIT 0, 1";
		$duplRows = 0;
		$photo_galleryObj = $this->db->getData($duplSql, $bindData);
		if($photo_galleryObj){
			foreach($photo_galleryObj as $onerow){
				$duplRows = 1;
				$photo_gallery_publish = $onerow['photo_gallery_publish'];
				if($photo_gallery_id==0 && $photo_gallery_publish==0){
					$photo_gallery_id = $onerow['photo_gallery_id'];
					$this->db->update('photo_gallery', array('photo_gallery_publish'=>1), $photo_gallery_id);
					$duplRows = 0;
					$returnStr = 'Update';
				}
			}
		}
		
		if($duplRows>0 || empty($name)){
			$savemsg = 'error';
			$returnStr = "Name Already Exist";
		}
		else{			
			if($photo_gallery_id==0){
				$conditionarray['created_on'] = date('Y-m-d H:i:s');
				$photo_gallery_id = $this->db->insert('photo_gallery', $conditionarray);
				if($photo_gallery_id){						
					$returnStr = 'Add';
				}
				else{
               $savemsg = 'error';
					$returnStr = 'Error occured while adding new data! Please try again.';
				}
			}
			else{
				$update = $this->db->update('photo_gallery', $conditionarray, $photo_gallery_id);
				if($update){
					$activity_feed_title = 'Photo Gallery was edited';
					$activity_feed_link = "/Manage_Data/photo_gallery/";
					
					$afData = array('created_on' => date('Y-m-d H:i:s'),
									'users_id' => $_SESSION["users_id"],
									'activity_feed_title' =>  $activity_feed_title,
									'activity_feed_name' => $name,
									'activity_feed_link' => $activity_feed_link,
									'uri_table_name' => "photo_gallery",
									'uri_table_field_name' =>"photo_gallery_publish",
									'field_value' => 1);
					$this->db->insert('activity_feed', $afData);
					
					$returnStr = 'Update';
				}
				else{
               $savemsg = 'error';
					$returnStr = 'No changes / Error occurred while updating data! Please try again.';
				}
			}
		}
		
		return json_encode(array('login'=>'', 'returnStr'=>$returnStr, 'savemsg'=>$savemsg));
	}
	
	public function aJgetPage_photo_gallery($segment3){
	
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
			$this->filterAndOptions_photo_gallery();
			$jsonResponse['totalRows'] = $totalRows = $this->totalRows;
		}
		$this->page = $page;
		$this->totalRows = $totalRows;
		$this->rowHeight = $rowHeight;
		
		$jsonResponse['tableRows'] = $this->loadTableRows_photo_gallery();
		
		return json_encode($jsonResponse);
	}
	
	private function filterAndOptions_photo_gallery(){
		
		$keyword_search = $this->keyword_search;
		
		$_SESSION["current_module"] = "Manage_Data";
		$_SESSION["list_filters"] = array('keyword_search'=>$keyword_search);
		
		$filterSql = "FROM photo_gallery WHERE photo_gallery_publish = 1";
		$bindData = array();
		if($keyword_search !=''){
			$keyword_search = addslashes(trim($keyword_search));
			if ( $keyword_search == "" ) { $keyword_search = " "; }
			$keyword_searches = explode (" ", $keyword_search);
			if ( strpos($keyword_search, " ") === false ) {$keyword_searches[0] = $keyword_search;}
			$num = 0;
			while ( $num < sizeof($keyword_searches) ) {
				$filterSql .= " AND name LIKE CONCAT('%', :keyword_search$num, '%')";
				$bindData['keyword_search'.$num] = trim($keyword_searches[$num]);
				$num++;
			}
		}
		
		$totalRows = 0;
		$strextra ="SELECT COUNT(photo_gallery_id) AS totalrows $filterSql";
		$query = $this->db->getObj($strextra, $bindData);
		if($query){
			$totalRows = $query->fetch(PDO::FETCH_OBJ)->totalrows;
		}
		$this->totalRows = $totalRows;		
	}
	
   	private function loadTableRows_photo_gallery(){
		
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
		
		$filterSql = "FROM photo_gallery WHERE photo_gallery_publish = 1";
		$bindData = array();
		if($keyword_search !=''){
			$keyword_search = addslashes(trim($keyword_search));
			if ( $keyword_search == "" ) { $keyword_search = " "; }
			$keyword_searches = explode (" ", $keyword_search);
			if ( strpos($keyword_search, " ") === false ) {$keyword_searches[0] = $keyword_search;}
			$num = 0;
			while ( $num < sizeof($keyword_searches) ) {
				$filterSql .= " AND name LIKE CONCAT('%', :keyword_search$num, '%')";
				$bindData['keyword_search'.$num] = trim($keyword_searches[$num]);
				$num++;
			}
		}
		
		$sqlquery = "SELECT * $filterSql ORDER BY name ASC LIMIT $starting_val, $limit";
		$query = $this->db->getData($sqlquery, $bindData);
		$str = '';
		if($query){
			foreach($query as $onerow){

				$photo_gallery_id = $onerow['photo_gallery_id'];
				$name = stripslashes($onerow['name']);

				$filePath = "./assets/accounts/photo_$photo_gallery_id".'_';
            $pics = glob($filePath."*.jpg");
				if(empty($pics) || !$pics){
					$pics = glob($filePath."*.png");
				}
            $picturesStr = '';
            $rowSpan = 1;
            if($pics){
               foreach($pics as $onePicture){
                  $prodImg = str_replace("./assets/accounts/", '', $onePicture);
                  $photo_galleryImgUrl = str_replace('./', '/', $onePicture);
                  $picturesStr .= "<tr class=\"cursor\">
							<td data-title=\"photo_gallery Picture\" align=\"center\" id=\"photo_gallery$photo_gallery_id$rowSpan\" class=\"positionrelative\">
                        <div class=\"currentPicture\"><img alt=\"$prodImg\" class=\"img-responsive maxheight100\" src=\"$photo_galleryImgUrl\"></div>
                     </td>
						</tr>";
                  $rowSpan++;
               }
            }
				
				$str .= "<tr class=\"cursor\">
					<td rowspan=\"$rowSpan\" onClick=\"getOneRowInfo('photo_gallery', $photo_gallery_id);\" data-title=\"Name\" align=\"left\">$name</td>
					<td data-title=\"photo_gallery Picture\" align=\"center\" class=\"positionrelative\">
						<button class=\"btn btn-default\" onClick=\"upload_dialog('Upload Photo Gallery Picture', 'photo_gallery', 'photo_$photo_gallery_id"."_');\">Upload<button>
					</td>
				</tr>
				$picturesStr";
			}
		}
		else{
			$str .= "<tr><td colspan=\"3\" class=\"red18bold\">No data meet the criteria given</td></tr>";
		}
		return $str;
   	}


	//========================For SEO Info module=======================//    		
	public function seo_info($segment3){
		
		$this->pageTitle = $GLOBALS['title'];
		$list_filters = $_SESSION['list_filters']??array();
		
		$keyword_search = $list_filters['keyword_search']??'';
		$this->keyword_search = $keyword_search;
		
		$this->filterAndOptions_seo_info();
		
		$page = !empty($segment3) ? intval($segment3):1;
		if($page<=0){$page = 1;}
		if(!isset($_SESSION['limit'])){$_SESSION['limit'] = 'auto';}
		$limit = $_SESSION['limit'];
		
		$this->rowHeight = 34;
		$this->page = $page;
		$tableRows = $this->loadTableRows_seo_info();
		
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
						<h1 class=\"metatitle\">SEO Info List <i class=\"fa fa-info-circle txt16normal\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"SEO Info List\"></i></h1>
					</div>				
					<div class=\"col-sm-12 col-md-6\">
						<div class=\"input-group\">
							<input type=\"text\" placeholder=\"Search SEO Info\" value=\"$keyword_search\" id=\"keyword_search\" name=\"keyword_search\" class=\"form-control\" maxlength=\"50\" />
							<span class=\"input-group-addon cursor\" onClick=\"checkAndLoadFilterData();\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Search SEO Info\">
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
										<th class=\"txtcenter\">Title</th>
										<th class=\"txtcenter\">URI Value</th>
										<th class=\"txtcenter\">Keywords</th>
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
				<h4 class=\"borderbottom\" id=\"formtitle\">Add New SEO Info</h4>
				<form action=\"#\" name=\"frmseo_info\" id=\"frmseo_info\" onsubmit=\"return AJsave_seo_info();\" enctype=\"multipart/form-data\" method=\"post\" accept-charset=\"utf-8\">
					<div class=\"form-group\">
						<label for=\"seo_title\">Title<span class=\"required\">*</span></label>
						<input type=\"text\" required class=\"form-control\" name=\"seo_title\" id=\"seo_title\" value=\"\" size=\"200\" maxlength=\"200\" />
						<span id=\"error_seo_title\" class=\"errormsg\"></span>
					</div>
					<div class=\"form-group\">
						<label for=\"uri_value\">URI Value<span class=\"required\">*</span></label>
						<input type=\"text\" required class=\"form-control\" name=\"uri_value\" id=\"uri_value\" value=\"\" size=\"200\" maxlength=\"200\" onKeyUp=\"createURI('uri_value', 'uri_value', 1, 1);\" />
						<span id=\"error_uri_value\" class=\"errormsg\"></span>
					</div>
					<div class=\"form-group\">
						<label for=\"seo_keywords\">Keywords<span class=\"required\">*</span></label>
						<textarea rows=\"5\" cols=\"50\" class=\"form-control\" name=\"seo_keywords\" id=\"seo_keywords\"></textarea>
						<span id=\"error_seo_keywords\" class=\"errormsg\"></span>
					</div>
					<div class=\"form-group\">
						<label for=\"description\">Description<span class=\"required\">*</span></label>
						<textarea rows=\"15\" cols=\"50\" class=\"form-control\" name=\"description\" id=\"description\"></textarea>
						<span id=\"error_description\" class=\"errormsg\"></span>
					</div>
                    <div class=\"form-group\">
						<label for=\"video_url\">Video URL<span class=\"required\">*</span></label>
						<input type=\"text\" required class=\"form-control\" name=\"video_url\" id=\"video_url\" value=\"\" size=\"200\" maxlength=\"200\" />
						<span id=\"error_video_url\" class=\"errormsg\"></span>
					</div>
					
					<div class=\"form-group\">
						<input type=\"hidden\" name=\"seo_info_id\" id=\"seo_info_id\" value=\"0\">
						<input type=\"submit\" id=\"submit\" class=\"btn btn-primary backlink\" value=\" Save \" />
						<input type=\"button\" name=\"reset\" id=\"reset\" onClick=\"resetForm_seo_info();\" value=\"Cancel\" class=\"hidediv btn btn-warning floatleft mleft10\" />
						<input type=\"button\" name=\"archive\" id=\"archive\" value=\"Archive\" class=\"hidediv btn btn-danger floatright\" />
					</div>
				</form>
				<link href=\"/assets/css/jqueryimageupload.css\" rel=\"stylesheet\" type=\"text/css\">
				<script type=\"text/javascript\" src=\"/assets/js/jquery.form.min.js\"></script>
				<script language=\"JavaScript\" type=\"text/javascript\">
					//var descriptionField = document.getElementById(\"descriptionField\");
					//descriptionField.appendChild(wysiwyrEditor('description'));
				</script>
			</div>
		</div>";
		
		$htmlStr = $this->htmlBody($innerHTMLStr);
		return $htmlStr;
	}
	
	public function AJsave_seo_info(){
	
		
		$users_id = $_SESSION["users_id"]??0;
		$returnStr = 'Ok';		
		$savemsg = '';
		$seo_info_id = $_POST['seo_info_id']??0;
		$seo_title = trim(addslashes($_POST['seo_title']??''));
		$uri_value = trim(addslashes($_POST['uri_value']??''));
		$seo_keywords = trim(addslashes($_POST['seo_keywords']??''));
		$description = trim(addslashes($_POST['description']??''));
		$video_url = trim(addslashes($_POST['video_url']??''));
		
		$conditionarray = array();
		$conditionarray['seo_title'] = $seo_title;
		$conditionarray['uri_value'] = $uri_value;
		$conditionarray['seo_keywords'] = $seo_keywords;
		$conditionarray['description'] = $description;
		$conditionarray['video_url'] = $video_url;
		$conditionarray['last_updated'] = date('Y-m-d H:i:s');
		$conditionarray['users_id'] = $users_id;
		
		$duplSql = "SELECT seo_info_publish, seo_info_id FROM seo_info WHERE (seo_title = :seo_title OR uri_value = :uri_value)";
		$bindData = array('seo_title'=>$seo_title, 'uri_value'=>$uri_value);
		if($seo_info_id>0){
			$duplSql .= " AND seo_info_id != :seo_info_id";
			$bindData['seo_info_id'] = $seo_info_id;
		}
		$duplSql .= " LIMIT 0, 1";
		$duplRows = 0;
		$seo_infoObj = $this->db->getData($duplSql, $bindData);
		if($seo_infoObj){
			foreach($seo_infoObj as $onerow){
				$duplRows = 1;
				$seo_info_publish = $onerow['seo_info_publish'];
				if($seo_info_id==0 && $seo_info_publish==0){
					$seo_info_id = $onerow['seo_info_id'];
					$this->db->update('seo_info', array('seo_info_publish'=>1), $seo_info_id);
					$duplRows = 0;
					$returnStr = 'Update';
				}
			}
		}
		
		if($duplRows>0 || empty($seo_title)){
			$savemsg = 'error';
			$returnStr = "<p>This title is already exist! Please try again with different title.</p>";
		}
		else{			
			if($seo_info_id==0){
				$conditionarray['created_on'] = date('Y-m-d H:i:s');
				$seo_info_id = $this->db->insert('seo_info', $conditionarray);
				if($seo_info_id){						
					$returnStr = 'Add';
				}
				else{
					$returnStr = 'Adding new SEO Info';
				}
			}
			else{
				$update = $this->db->update('seo_info', $conditionarray, $seo_info_id);
				if($update){
					$activity_feed_title = 'SEO Info was edited';
					$activity_feed_link = "/Manage_Data/seo_info/view/$seo_info_id";
					
					$afData = array('created_on' => date('Y-m-d H:i:s'),
									'users_id' => $_SESSION["users_id"],
									'activity_feed_title' =>  $activity_feed_title,
									'activity_feed_name' => $seo_title,
									'activity_feed_link' => $activity_feed_link,
									'uri_table_name' => "seo_info",
									'uri_table_field_name' =>"seo_info_publish",
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
	
	public function aJgetPage_seo_info($segment3){
	
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
			$this->filterAndOptions_seo_info();
			$jsonResponse['totalRows'] = $totalRows = $this->totalRows;
		}
		$this->page = $page;
		$this->totalRows = $totalRows;
		$this->rowHeight = $rowHeight;
		
		$jsonResponse['tableRows'] = $this->loadTableRows_seo_info();
		
		return json_encode($jsonResponse);
	}
	
	private function filterAndOptions_seo_info(){
		
		$keyword_search = $this->keyword_search;
		
		$_SESSION["current_module"] = "Manage_Data";
		$_SESSION["list_filters"] = array('keyword_search'=>$keyword_search);
		
		$filterSql = "FROM seo_info WHERE seo_info_publish = 1";
		$bindData = array();
		if($keyword_search !=''){
			$keyword_search = addslashes(trim($keyword_search));
			if ( $keyword_search == "" ) { $keyword_search = " "; }
			$keyword_searches = explode (" ", $keyword_search);
			if ( strpos($keyword_search, " ") === false ) {$keyword_searches[0] = $keyword_search;}
			$num = 0;
			while ( $num < sizeof($keyword_searches) ) {
				$filterSql .= " AND CONCAT_WS(' ', seo_title, seo_keywords, description) LIKE CONCAT('%', :keyword_search$num, '%')";
				$bindData['keyword_search'.$num] = trim($keyword_searches[$num]);
				$num++;
			}
		}
		
		$totalRows = 0;
		$strextra ="SELECT COUNT(seo_info_id) AS totalrows $filterSql";
		$query = $this->db->getObj($strextra, $bindData);
		if($query){
			$totalRows = $query->fetch(PDO::FETCH_OBJ)->totalrows;
		}
		$this->totalRows = $totalRows;		
	}
	
   	private function loadTableRows_seo_info(){
		
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
		
		$filterSql = "FROM seo_info WHERE seo_info_publish = 1";
		$bindData = array();
		if($keyword_search !=''){
			$keyword_search = addslashes(trim($keyword_search));
			if ( $keyword_search == "" ) { $keyword_search = " "; }
			$keyword_searches = explode (" ", $keyword_search);
			if ( strpos($keyword_search, " ") === false ) {$keyword_searches[0] = $keyword_search;}
			$num = 0;
			while ( $num < sizeof($keyword_searches) ) {
				$filterSql .= " AND CONCAT_WS(' ', seo_title, seo_keywords, description) LIKE CONCAT('%', :keyword_search$num, '%')";
				$bindData['keyword_search'.$num] = trim($keyword_searches[$num]);
				$num++;
			}
		}
		
		$sqlquery = "SELECT * $filterSql ORDER BY seo_title ASC LIMIT $starting_val, $limit";
		$query = $this->db->getData($sqlquery, $bindData);
		$str = '';
		if($query){
			foreach($query as $onerow){

				$seo_info_id = $onerow['seo_info_id'];
				$seo_title = stripslashes($onerow['seo_title']);
				$uri_value = stripslashes($onerow['uri_value']);
				$seo_keywords = stripslashes($onerow['seo_keywords']);
				
				$pageImgUrl = '';
                $filePath = "./assets/accounts/seo_$seo_info_id".'_';
                $pics = glob($filePath."*.jpg");
				if(!$pics){
					$pics = glob($filePath."*.png");
				}
                if($pics){
                    foreach($pics as $onePicture){
                        $prodImg = str_replace("./assets/accounts/", '', $onePicture);
                        $pageImgUrl = str_replace('./', '/', $onePicture);
                    }
                }
                if(!empty($pageImgUrl)){
                    $pageImg = "<div class=\"currentPicture\"><img alt=\"".strip_tags(addslashes($seo_title))."\" class=\"img-responsive maxheight250\" src=\"$pageImgUrl\"></div>";
                }
                else{
                    $pageImg = "<button class=\"btn btn-default\" onClick=\"upload_dialog('Upload Page Picture', 'seo_info', 'seo_$seo_info_id"."_');\">Upload<button>";
                }
				
				$str .= "<tr class=\"cursor\" onClick=\"getOneRowInfo('seo_info', $seo_info_id);\">
							<td data-title=\"ID\" align=\"right\">$seo_info_id</td>
							<td data-title=\"Name\" align=\"left\">$seo_title</td>
							<td data-title=\"URI Value\" align=\"left\">$uri_value</td>
							<td data-title=\"Short Description\" align=\"left\">$seo_keywords</td>
							<td data-title=\"Banner Picture\" align=\"left\" id=\"page$seo_info_id\" class=\"positionrelative\">$pageImg</td>
						</tr>";
			}
		}
		else{
			$str .= "<tr><td colspan=\"4\" class=\"errormsg\">There is no seo_info meet this criteria</td></tr>";
		}
		return $str;   
	}

	
	
}
?>