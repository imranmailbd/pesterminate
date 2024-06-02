<?php
class Common{
	protected $db;
	public function __construct($db){$this->db = $db;}
	
	//==============Commonly Used on Multiple Places================//	
	public function getOneRowById($tablename, $idVal){
		$returnObj = array();
		$idName = $tablename.'_id';
		$ledObj = $this->db->getObj("SELECT * FROM $tablename WHERE $idName = $idVal", array());
		if($ledObj){
			$returnObj = $ledObj->fetch(PDO::FETCH_OBJ);
		}
		return $returnObj;
	}
	
	public function getOneFieldById($tablename, $idVal, $fieldName){
		$returnVal = '';
		$idName = $tablename.'_id';
		$ledObj = $this->db->getObj("SELECT $fieldName FROM $tablename WHERE $idName = $idVal", array());
		if($ledObj){
			$returnVal = $ledObj->fetch(PDO::FETCH_OBJ)->$fieldName;
		}
		return $returnVal;
	}
	
	public function uploadpicture(){
		
		if(!isset($_FILES) || !array_key_exists('filename', $_FILES)){
			return 'There is no file sent';
		}
		
		if (!is_uploaded_file($_FILES['filename']['tmp_name'])){
         	return "Possible file upload attack. Filename: " . $_FILES['filename']['tmp_name'];
      	}
		
		if(!isset($_SESSION["users_id"])){
			return 'session_ended';
		}
		else{
			
			$fileSize = floor(filesize($_FILES['filename']['tmp_name'])/1024);//KB
			$fileSize = floor($fileSize/1024);//MB
			
			if($fileSize>6){
                return "File size ".$fileSize." issue. Please upload less than 6";
			}
			
			ini_set('memory_limit', '500M');
			
			$frompage = $_POST['frompage'];
			// var_dump($frompage);exit;
			
			$folderpath = "./assets/accounts";
			if(!is_dir($folderpath)){mkdir($folderpath, 0777);}
			
			$fileprename = $_POST['fileprename'];
			$oldfilename = $_POST['oldfilename'];
			
			$imagename = $fileprename.substr(time(),7,3).'.png';
			$width = 300;
			$height = 300;

			if(strcmp($frompage, 'banners')==0){
				$width = 600;
				$height = 450;
			}
			elseif(in_array($frompage, array('services', 'news_articles'))){
				$width = 370;
				$height = 290;
			}
			elseif(in_array($frompage, array('pest_services'))){
				$width = 300;
				$height = 300;
			}
			elseif(strcmp($frompage, 'branches')==0){
				$width = 600;
				$height = 400;
			}
			
			$image_info = getimagesize($_FILES['filename']['tmp_name']);
			$imageType = $image_info[2];//1=gif, 2=jpg/jpeg, 3=png, 
			if ($imageType > 3 ) {
				return 'NOT A JPG/JPEG/PNG/GIF FILE!!!! TRY AGAIN';
			}
			$orig_width = $image_info[0];
      		$orig_height = $image_info[1];

			//======Update Image Size=========//
			$source_aspect_ratio = 1;

			if($orig_height !=0){
				$source_aspect_ratio = $orig_width / $orig_height;
			}

			$thumbnail_aspect_ratio = 1;

			if($height !=0){
				$thumbnail_aspect_ratio = $width / $height;
			}

			if ($orig_width <= $width && $orig_height <= $height) {
				$thumbnail_image_width = $orig_width;
				$thumbnail_image_height = $orig_height;
			} elseif ($thumbnail_aspect_ratio > $source_aspect_ratio) {
				$thumbnail_image_width = (int) ($height * $source_aspect_ratio);
				$thumbnail_image_height = $height;
			} elseif ($orig_width >= $width && $orig_height >= $height) {				
				$thumbnail_image_width = $width;
				$thumbnail_image_height = $height;
			} else {
				$thumbnail_image_width = $width;
				$thumbnail_image_height = (int) ($width / $source_aspect_ratio);
			}
			
			$width = $thumbnail_image_width;
			$height = $thumbnail_image_height;
			// echo $height;exit;
			
			//=========Create Image==============//
			$new_image = imagecreate( $width, $height );				
			if(strcmp($frompage, 'banner')==0){
				$white = imagecolorallocate($new_image, 16, 118, 189);
			}
			elseif(strcmp($frompage, 'pest_services')==0){
				$white = imagecolorallocate($new_image, 0, 0, 0);
				// $white = null; //imagecolorallocate($new_image, 255, 255, 255);
			}
			else{
				$white = imagecolorallocate($new_image, 255, 255, 255);
			}

			imagefill($new_image, 0, 0, $white);

			if ($imageType == '1' ) {
				$image = imagecreatefromgif($_FILES['filename']['tmp_name']);
			}
			elseif ($imageType == '2' ) {
				$image = imagecreatefromjpeg($_FILES['filename']['tmp_name']);
			}
			else {
				$image = imagecreatefrompng($_FILES['filename']['tmp_name']);
			}
			
			if(strcmp($frompage, 'banner')==0){
				$transparent = imagecolorallocatealpha( $new_image, 16, 118, 189, 127 );
			}
			elseif(strcmp($frompage, 'pest_services')==0){
				$transparent = imagecolorallocatealpha( $new_image, 255, 255, 255, 127);
				// $transparent = null; //imagecolorallocatealpha( $new_image, 255, 255, 255, 127);
			}
			else{
				$transparent = imagecolorallocatealpha( $new_image, 255, 255, 255, 127 );
			}

			imagefill( $new_image, 0, 0, $transparent );

			imagecopyresampled($new_image, $image, 0, 0, 0, 0, $width, $height, $orig_width, $orig_height);
			imagepng($new_image, $folderpath.'/'.$imagename, 0);
			imagedestroy($new_image);
			imagedestroy($image);
			
			$picturesrc = str_replace('./', '/', $folderpath.'/'.$imagename);
			
			//==============Image Compression and replace=================//
			if (extension_loaded('imagick')){					
				
				//=============Testing White BG Code=================//
				$hasAlpha = $this->hasAlpha(imagecreatefrompng($_SERVER['DOCUMENT_ROOT'] . $picturesrc));
				var_dump($hasAlpha);
				//=============Testing White BG Code=================//
				
				$im = new Imagick($_SERVER['DOCUMENT_ROOT'] . $picturesrc);
				$im->optimizeImageLayers(); // Optimize the image layers			
				$im->setImageCompression(Imagick::COMPRESSION_JPEG);// Compression and quality
				$im->setImageCompressionQuality(0);
				
				$imagename = str_replace('.png', '.jpg', $imagename);
				$picturesrc1 = str_replace('.png', '.jpg', $picturesrc);
				$im->writeImages($_SERVER['DOCUMENT_ROOT'] . $picturesrc1, true);// Write the image back
				unlink('.'.$picturesrc); // delete file		
				$picturesrc = $picturesrc1;

			}
			
			if(in_array($frompage, array('fieldImages', 'banners', 'news_articles', 'services','pest_services'))){
				if(!empty($oldfilename) && $oldfilename !='/assets/images/default.png'){
					$attachedpath1 = '.'.$oldfilename;
					if(file_exists($attachedpath1))
						unlink($attachedpath1);
				}
				return "<div class=\"currentPicture\">
						<img class=\"img-responsive\" style=\"max-height:600px;\" src=\"$picturesrc\" alt=\"$imagename\" />
					</div>";
			}

		}
	}
	
	public function hasAlpha($imgdata) {
		$w = imagesx($imgdata);
		$h = imagesy($imgdata);

		if($w>50 || $h>50){ //resize the image to save processing if larger than 50px:
			$thumb = imagecreatetruecolor(10, 10);
			imagealphablending($thumb, FALSE);
			imagecopyresized( $thumb, $imgdata, 0, 0, 0, 0, 10, 10, $w, $h );
			$imgdata = $thumb;
			$w = imagesx($imgdata);
			$h = imagesy($imgdata);
		}
		//run through pixels until transparent pixel is found:
		for($i = 0; $i<$w; $i++) {
			for($j = 0; $j < $h; $j++) {
				$rgba = imagecolorat($imgdata, $i, $j);
				if(($rgba & 0x7F000000) >> 24) return true;
			}
		}
		return false;
	}
	
	public function AJremove_Picture(){
	
		$returnStr = '';		
		$picturepath = $_POST['picturepath']??'';
		
		$attachedpath = '.'.$picturepath;
		if (file_exists($attachedpath)){
			unlink($attachedpath);
		}
		$returnStr = 'Ok';
		return json_encode(array('login'=>'', 'returnStr'=>$returnStr));
	}	
	
	public function AJarchive_tableRow(){
	
		$returnStr = '';
		$tablename = $_POST['tablename']??'';
		$tableidvalue = $_POST['tableidvalue']??0;
		$publishname = $_POST['publishname']??'';
		if($tableidvalue==0){
			$savemsg = "<p>Error occured while archiving information! Please try again.</p>";
		}
		else{
			$users_id = $_SESSION["users_id"]??0;
			
			$updatetable = $this->db->update($tablename, array($publishname=>0), $tableidvalue);
			if($updatetable>0){
				$conditionarray = array();
				$conditionarray['created_on'] = date('Y-m-d H:i:s');
				$conditionarray['last_updated'] = date('Y-m-d H:i:s');
				$conditionarray['users_id'] = $_SESSION["users_id"];
				$conditionarray['activity_feed_title'] = '';
				$conditionarray['activity_feed_name'] = '';
				$conditionarray['activity_feed_link'] = '';
				if($tablename=='users'){
					$conditionarray['activity_feed_title'] = 'User archived';
					$username = '';
					if($tableidvalue>0){
						$userObj = $this->db->getObj("SELECT users_first_name, users_last_name FROM users WHERE users_id = :users_id", array('users_id'=>$tableidvalue),1);
						if($userObj){
							$userrow = $userObj->fetch(PDO::FETCH_OBJ);
							$username = trim("$userrow->users_first_name $userrow->users_last_name");
						}
					}
					
					$conditionarray['activity_feed_name'] = $username;
					$conditionarray['activity_feed_link'] = "/Settings/setup_users/view/$tableidvalue";
				}
				
				if(!empty($conditionarray)){
					
					$activity_feed_title = $conditionarray['activity_feed_title'];
					$conditionarray['activity_feed_title'] = $activity_feed_title;
					
					$activity_feed_link = $conditionarray['activity_feed_link'];
					$conditionarray['activity_feed_link'] = $activity_feed_link;
					
					$conditionarray['uri_table_name'] = $tablename;
					$conditionarray['uri_table_field_name'] = $publishname;
					$conditionarray['field_value'] = 0;
					
					$this->db->insert('activity_feed', $conditionarray);	
				}
				
				$returnStr = 'archive-success';
			}
			else{
				$returnStr = "<p>Error occured while archiving information! Please try again.</p>";
			}
		}
		return json_encode(array('login'=>'', 'returnStr'=>$returnStr));
	}
	
	public function AJremove_tableRow(){
	
		$returnStr = $savemsg = '';
		$POST = json_decode(file_get_contents('php://input'), true);
		$users_id = $_SESSION["users_id"]??0;
		$tableName = $POST['tableName']??'';
		$tableIdValue = intval($POST['tableIdValue']??0);
		$description = $POST['description']??'';
		
		$tableIdName = $tableName.'_id';
		$delete_tablerow = $this->db->delete($tableName, $tableIdName, $tableIdValue);
		
		if($delete_tablerow){				
			$savemsg = 'Done';
			$activity_feed_title = "Remove $description";
			$activity_feed_link = "";
			
			$afData = array('created_on' => date('Y-m-d H:i:s'),
							'users_id' => $users_id,
							'activity_feed_title' => $activity_feed_title,
							'activity_feed_name' => $description,
							'activity_feed_link' => $activity_feed_link,
							'uri_table_name' => $tableName,
							'uri_table_field_name' =>"",
							'field_value' => 1
							);
			$this->db->insert('activity_feed', $afData);
			
		}
		else{
			$savemsg = 'error';
			$returnStr .= "<p>Error occured while deleting information! Please try again.</p>";
		}
		return json_encode(array('login'=>'', 'returnStr'=>$returnStr, 'savemsg'=>$savemsg));
	}

	public function AJget_notesData(){
	
		$returnStr = '';
		$note_for = $_POST['note_for']??'';
		$table_id = $_POST['table_id']??0;
		
		if($table_id>0){
			$Notes = new Notes($this->db);
			$Notes->note_for = $note_for;
			$Notes->table_id = $table_id;		
			$returnStr = $Notes->showNotesData(1);
		}
		return json_encode(array('login'=>'', 'returnStr'=>$returnStr));
	}
	
	public function AJget_notesPopup(){
	
		$returnStr = '';
		$notes_id = $_POST['notes_id']??0;
		$notesData = array();
		$notesData['login'] = '';
		$notesData['notes_id'] = 0;
		$notesData['note'] = '';
		$notesObj = $this->db->getObj("SELECT note FROM notes WHERE notes_id = :notes_id", array('notes_id'=>$notes_id),1);
		if($notesObj){
			$notesData['note'] = trim(stripslashes($notesObj->fetch(PDO::FETCH_OBJ)->note));
			$notesData['notes_id'] = $notes_id;
		}
		return json_encode($notesData);
	}
	
	public function AJsave_notes(){
	
		$returnStr = '';
		
		if(isset($_POST)){
			$notes_id = $_POST['notes_id']??0;
			$note_for = $_POST['note_for']??'';
			$table_id = $_POST['table_id']??0;
			$publics = $_POST['publics']??0;
			$note = addslashes($_POST['note']??'');
			
			if($table_id>0 && !empty($note_for)){
				$noteData = array();
				$noteData['table_id'] = $table_id;
				$noteData['note_for'] = $note_for;
				$noteData['last_updated'] =date('Y-m-d H:i:s');
				$noteData['users_id'] = $_SESSION["users_id"];
				$noteData['note'] = $note;
				$noteData['publics'] = $publics;
				
				if($notes_id==0){
					$noteData['created_on'] = date('Y-m-d H:i:s');
					$notes_id = $this->db->insert('notes', $noteData);
					if($notes_id){
						if(!in_array($note_for, array('expenses', 'commissions'))){
							$this->db->update($note_for, array('last_updated'=>date('Y-m-d H:i:s')), $table_id);
						}
						$returnStr = 'Add';
					}
					else{
						$returnStr = 'Added New Note';
					}
				}
				else{
					$update = $this->db->update('notes', $noteData, $notes_id);
					if($update){
						if(!in_array($note_for, array('expenses', 'commissions'))){
							$this->db->update($note_for, array('last_updated'=>date('Y-m-d H:i:s')), $table_id);
						}
						$returnStr = 'Add';
					}
					else{
						$returnStr = 'Editing New Note';
					}
				}
			}
		}
		return json_encode(array('login'=>'', 'returnStr'=>$returnStr));
	}
	
	//=================Commonly Used=========//
	public function getOneRowFields($tableName, $condition, $returnFields){
		$returnStr = '';
		$filters = array();
		foreach($condition as $fieldName=>$fieldVal){$filters[] = "$fieldName='$fieldVal'";}
		$FieldSql = "SELECT ";
		if(is_array($returnFields)){$FieldSql .= implode(', ', $returnFields);}
		else{$FieldSql .= $returnFields;}
		$FieldSql .= " FROM $tableName WHERE ".implode(' AND ', $filters)." LIMIT 0, 1";		
		$FieldObj = $this->db->getObj($FieldSql, array());
		if($FieldObj){
			while($FieldRow = $FieldObj->fetch(PDO::FETCH_OBJ)){
				if(is_array($returnFields)){
					$fieldsVal = array();
					foreach($returnFields as $fieldName){$fieldsVal[] = $FieldRow->$fieldName;}
					$returnStr .= implode(' ', $fieldsVal);
				}
				else{$returnStr .= $FieldRow->$returnFields;}
			}
		}
		return stripslashes(trim($returnStr));
	}
	
	public function build_sorter($key){
		return function ($a, $b) use ($key) {
			return strcmp($a[$key], $b[$key]);
		};
	}
	
	public function calculateTax($taxable_total, $taxes_rate, $tax_inclusive){
		$returntax = 0.00;
		$taxes_percentage = $taxes_rate*0.01;
		if($tax_inclusive>0){
			$returntax = $taxable_total-round($taxable_total/($taxes_percentage+1),2);
		}
		else{
			$returntax = round($taxable_total*$taxes_percentage,2);
		}
		return $returntax;
	}
	
   public function randomPassword() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 6; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
   }
	
	public function gen_uuid() {
		return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			// 32 bits for "time_low"
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
	
			// 16 bits for "time_mid"
			mt_rand( 0, 0xffff ),
	
			// 16 bits for "time_hi_and_version",
			// four most significant bits holds version number 4
			mt_rand( 0, 0x0fff ) | 0x4000,
	
			// 16 bits, 8 bits for "clk_seq_hi_res",
			// 8 bits for "clk_seq_low",
			// two most significant bits holds zero and one for variant DCE1.1
			mt_rand( 0, 0x3fff ) | 0x8000,
	
			// 48 bits for "node"
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
		);
	}
	
	public function time_since($since) {
		$chunks = array(array(60 * 60 * 24 * 365 , 'year'),
						array(60 * 60 * 24 * 30 , 'month'),
						array(60 * 60 * 24 * 7, 'week'),
						array(60 * 60 * 24 , 'day'),
						array(60 * 60 , 'hour'),
						array(60 , 'minute'),
						array(1 , 'second')
					);
	
		for ($i = 0, $j = count($chunks); $i < $j; $i++) {
			$seconds = $chunks[$i][0];
			$name = $chunks[$i][1];
			if (($count = floor($since / $seconds)) != 0) {
				break;
			}
		}
	
		$print = ($count == 1) ? '1 '.$name : "$count {$name}s";
		return $print.' ago';
	}	
	
	public function sendSMS($smstophone='', $smsmessage=''){
		if(empty($smstophone) && isset($_POST['smstophone'])){
			$smstophone = $_POST['smstophone'];
			$smsmessage = strip_tags(stripslashes($_POST['smsmessage']));
		}
		$returnStr = 'Error! could not sent SMS.';
		if(!empty($smstophone) && !empty($smsmessage)){
			$smsmessage .= " ($_SESSION[company_name])";
			
			$url = "http://login.bulksmsbd.com/maskingapi.php";
			$postData = array('username' => '01712633273',
							'password' => 'shiful',
							'number'=>$smstophone,
							'senderid'=>'JS CABLE TV',
							'message'=>$smsmessage);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_getObj($postData));
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($ch);
			curl_close($ch);
			$p = explode("|",$response);
			$sendstatus = $p[0];
			$returnArray = array(1000=>'Invalid user or Password',
								1002 =>'Empty Number',
								1003 =>'Invalid message or empty message',
								1004 =>'Invalid number',
								1005 =>'All Number is Invalid',	
								1006 =>'insufficient Balance',
								1009 =>'Inactive Account',
								1010 =>'Max number limit exceeded',
								1101 =>'Success');
			if($sendstatus=='1101'){
				$returnStr = 'sent';
			}
			elseif(array_key_exists($sendstatus, $returnArray)){
				$returnStr = $returnArray[$sendstatus];
			}
			else{
				$returnStr = 'Error! could not sent SMS.';
			}
		}
		return $returnStr;
	}
	
	public function convert_number($number) { 
		$number = intval($number);
		$my_number = $number;

		if (($number < 0) || ($number > 999999999)) { 
			throw new Exception("Number is out of range");
		} 
		
		$Kt = floor($number / 10000000); /* Koti */
		$number -= $Kt * 10000000;
		$Gn = floor($number / 100000);  /* lakh  */ 
		$number -= $Gn * 100000; 
		$kn = floor($number / 1000);     /* Thousands (kilo) */ 
		$number -= $kn * 1000; 
		$Hn = floor($number / 100);      /* Hundreds (hecto) */ 
		$number -= $Hn * 100; 
		$Dn = floor($number / 10);       /* Tens (deca) */ 
		$n = $number % 10;               /* Ones */ 

		$res = "";
		if ($Kt){ 
			$res .= convert_number($Kt) . " Crore "; 
		} 
		if ($Gn){ 
			$res .= convert_number($Gn) . " Lakh"; 
		} 

		if ($kn){ 
			$res .= (empty($res) ? "" : " ") . 
				convert_number($kn) . " Thousand"; 
		} 

		if ($Hn){ 
			$res .= (empty($res) ? "" : " ") . 
				convert_number($Hn) . " Hundred"; 
		} 

		$ones = array("", "One", "Two", "Three", "Four", "Five", "Six", 
			"Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen", 
			"Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eightteen", 
			"Nineteen"); 
		$tens = array("", "", "Twenty", "Thirty", "Fourty", "Fifty", "Sixty", 
			"Seventy", "Eigthy", "Ninety"); 

		if ($Dn || $n){ 
			if (!empty($res)){ 
				$res .= " and "; 
			} 

			if ($Dn < 2){ 
				$res .= $ones[$Dn * 10 + $n]; 
			} 
			else{ 
				$res .= $tens[$Dn]; 

				if ($n){ 
					$res .= "-" . $ones[$n]; 
				} 
			} 
		} 

		if (empty($res)){ 
			$res = "zero"; 
		} 

		return $res;
	} 
	
	public function makewords($numval, $currency='$'){
		$numval = str_replace(',','',number_format(round($numval,2), 2));
		$moneystr = "";
		
		// handle the Crore
		$crval = (integer)($numval / 10000000);
		if($crval > 0)  {
			$moneystr = $this->getwords($crval) . " Crore";
		}
		
		// handle the Lakh
		$workval = $numval - ($crval * 10000000); // get rid of millions
		$milval = (integer)($workval / 100000);
		if($milval > 0)  {
			$moneystr = $this->getwords($milval) . " Lacs";
		}
		 
		// handle the thousands
		$workval = $numval - ($milval * 100000); // get rid of millions
		$thouval = (integer)($workval / 1000);
		if($thouval > 0)  {
			$workword = $this->getwords($thouval);
			if ($moneystr == "")    {
				$moneystr = $workword . " Thousand";
			}
			else{
				$moneystr .= " " . $workword . " Thousand";
			}
		}
		 
		// handle all the rest of the dollars
		$workval = $workval - ($thouval * 1000); // get rid of thousands
		$tensval = (integer)($workval);
		if ($moneystr == ""){
			if ($tensval > 0){
				$moneystr = $this->getwords($tensval);
			}else{
				$moneystr = "Zero";
			}
		}
		else{ // non zero values in hundreds AND up
			$workword = $this->getwords($tensval);
			$moneystr .= " " . $workword;
		}
		 
		// plural or singular 'dollar'
		$workval = (integer)($numval);
		if ($currency=='$'){
			$moneystr .= " Dollar";
		}else{
			$moneystr .= " Taka";
		}
	
		
		// do the cents - use printf so that we get the
		// same rounding as printf
		$workstr = sprintf("%3.2f",$numval); // convert to a string
		$intstr = substr($workstr,strlen($numval) - 2, 2);
		$workint = (integer)($intstr);
		if ($workint>1){
			$moneystr .= " &amp; ";
		}
		if ($workint == 0){
			//$moneystr .= "Zero";
		}
		else{
		  $moneystr .= $this->getwords($workint);
		}
		if ($workint>1){
			if($currency=='$'){
				$moneystr .= " Cent";
				if ($workint>1){$moneystr .= "s";}
			}
			else{
				$moneystr .= " Paisa";
			}
		}
		 
		// done 
		return $moneystr.'.';
	}
	
	public function getwords($workval){
		$numwords = array(1 => "One",
						  2 => "Two",
						  3 => "Three",
						  4 => "Four",
						  5 => "Five",
						  6 => "Six",
						  7 => "Seven",
						  8 => "Eight",
						  9 => "Nine",
						  10 => "Ten",
						  11 => "Eleven",
						  12 => "Twelve",
						  13 => "Thirteen",
						  14 => "Fourteen",
						  15 => "Fifteen",
						  16 => "Sixteen",
						  17 => "Seventeen",
						  18 => "Eighteen",
						  19 => "Nineteen",
						  20 => "Twenty",
						  30 => "Thirty",
						  40 => "Forty",
						  50 => "Fifty",
						  60 => "Sixty",
						  70 => "Seventy",
						  80 => "Eighty",
						  90 => "Ninety");
		 
		// handle the 100's
		$retstr = "";
		$hundval = (integer)($workval / 100);
		if ($hundval > 0){
		  $retstr = $numwords[$hundval] . " Hundred";
		}
		 
		// handle units AND teens
		$workstr = "";
		$tensval = $workval - ($hundval * 100); // dump the 100's
		 
		// do the teens
		if (($tensval < 20) && ($tensval > 0)){
		  $workstr = $numwords[$tensval];
		   // got to break out the units AND tens
		}
		else{
		  	$tempval = ((integer)($tensval / 10)) * 10; // dump the units
		  	
			if($tempval > 0)
		  		$workstr = $numwords[$tempval]; // get the tens
			else
				$workstr = '';
				
		  $unitval = $tensval - $tempval; // get the unit value
		  if ($unitval > 0){
			$workstr .= " " . $numwords[$unitval];
			}
		}
		 
		// join the parts together 
		if ($workstr != ""){
			  if ($retstr != ""){
				$retstr .= " " . $workstr;
			  }else{
				$retstr = $workstr;
				}
		}
	
		return $retstr;
	}
	
	public function taka_format($amount = 0, $floatPoints=2){
		if($floatPoints==0){$amount = round($amount);}
		else{$amount = round($amount, $floatPoints);}
		$negYN = 1;
		if($amount<0){$negYN = -1;}
		$amount = $amount*$negYN;
		$tmp = explode(".",$amount);  // for float or double values
		$strMoney = "";
		$amount = $tmp[0];
		$strMoney .= substr($amount, -3,3 ) ;
		$amount = substr($amount, 0,-3 ) ;
		while(strlen($amount)>0)
		{
			$strMoney = substr($amount, -2,2 ).",".$strMoney;
			$amount = substr($amount, 0,-2 );
		}
		$floatVal = 0;
		if(isset($tmp[1])){
			$floatVal = $tmp[1];
		}
		$floatVal = sprintf("%02d", $floatVal);
		
		if($negYN<0){$strMoney = '-'.$strMoney;}
		if($floatPoints>0){
			$strMoney .= '.'.$floatVal;
		}
		return $strMoney;
	}
	
}
?>