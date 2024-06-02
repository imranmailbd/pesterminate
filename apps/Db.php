<?php
class Db{
	protected $db;
	public function __construct(){
		try {
			if(strcmp(OUR_DOMAINNAME, LIVE_DOMAIN) == 0 || strcmp(OUR_DOMAINNAME, 'admin.pesterminate.ca') == 0) {
				$username = 'pesterminate';
				$password = 'y@inscgn.o6quJjC';
				$database = 'pesterminate';
			}
			elseif(strcmp(OUR_DOMAINNAME, 'skitsbd.com') == 0) { 
				$username = 'skitsbd_imran';
				$password = 'imran123!@#';
				$database = 'skitsbd_pesterminate';
			}
			elseif(strcmp(OUR_DOMAINNAME, 'skitsbdl.com') == 0) {
				$username = 'root';
				$password = '';
				$database = 'pesterminate';
			}
			else{
				$username = 'root';
				$password = '';
				$database = 'pesterminate';
			}
			$_SESSION["currency"] = '£';
			$this->db = new PDO("mysql:dbname=$database;host=localhost;charset=utf8", $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		}
		catch (PDOException $e) {
			$this->writeIntoLog('Connection failed: ' . $e->getMessage());
			sleep(5);
			call_user_func('__construct');
		}
	}
	
	public function writeIntoLog($message){
		if($message !=''){
			$fileName = './error_log';
			if(is_array($message)){$message = implode(', ', $message);}
			file_put_contents($fileName, date('Y-m-d H:i:s')." $message\n",FILE_APPEND);
		}
	}
	
	public function getObj($statement, $bindData, $paramType=0){
		
		$sql = $this->db->prepare($statement);
		if(!empty($bindData)){
			foreach($bindData as $fieldname=>$fieldvalue){
				if($paramType>0){
					$sql->bindValue(":$fieldname",$fieldvalue, PDO::PARAM_INT);
				}
				else{
					$sql->bindValue(":$fieldname",$fieldvalue, PDO::PARAM_STR);
				}
			}
		}
		$sql->execute();
		$errors = $sql->errorInfo();
		if($errors[2]==''){
			if($sql->rowCount()>0){
				return $sql;
			}
			else{
				return false;
			}
		}
		else{
			$this->writeIntoLog('Query failed: ' . $errors[2]." near $statement");
			return false;
		}
	}
	
	public function getData($statement, $bindData, $paramType=0){
		
		$sql = $this->db->prepare($statement);
		if(count($bindData)>0){
			foreach($bindData as $fieldname=>$fieldvalue){
				if($paramType>0){
					$sql->bindValue(":$fieldname",$fieldvalue, PDO::PARAM_INT);
				}
				else{
					$sql->bindValue(":$fieldname",$fieldvalue, PDO::PARAM_STR);
				}
			}
		}
		$result = $sql->execute();
		if($result){
			$returnData = $sql->fetchAll(PDO::FETCH_ASSOC);
		}
		else{
			$errors = $sql->errorInfo();
			$this->writeIntoLog('Query Pagination failed: ' . $errors[2]." near $statement");
			$returnData = false;
		}
		
		return $returnData;
	}
	
	public function insert($tablename, $fieldsData){
		$fieldsArray = array_keys($fieldsData);
		$str = "INSERT INTO $tablename (`".implode('`, `', $fieldsArray)."`) values(:".implode(', :', $fieldsArray).")";
		$sql = $this->db->prepare($str);
		foreach($fieldsData as $fieldname=>$fieldvalue){
			$sql->bindValue(":$fieldname",$fieldvalue, PDO::PARAM_STR);
		}
		
		$result = $sql->execute();
		if($result){
			return $this->db->lastInsertId();
		}
		else{
			foreach($fieldsData as $field=>$value){
				$str = str_replace(":$field", $value, $str);
			}
			$errors = $sql->errorInfo();
			$this->writeIntoLog('Insert failed: ' . $errors[2]." near $str");
			return false;
		}
	}
	
	public function update($tablename, $fieldsData, $id){
		$idName = $tablename.'_id';
		$fieldsArray = array_keys($fieldsData);
		
		$str = "UPDATE $tablename SET";
		$l=0;
		foreach($fieldsArray as $oneField){
			if($l>0){$str .= ", ";}
			$str .= " $oneField = :$oneField";
			$l++;
		}
		$str .= " WHERE $idName = :$idName";
		
		$sql = $this->db->prepare($str);
		foreach($fieldsData as $field=>$value){
			$sql->bindValue(":$field", $value, PDO::PARAM_STR);
		}
		$sql->bindValue(":$idName",$id, PDO::PARAM_INT);
		$result = $sql->execute();
		if($result){
			return $sql->rowCount();
		}
		else{
			foreach($fieldsData as $field=>$value){
				$str = str_replace(":$field", $value, $str);
			}
			$errors = $sql->errorInfo();
			$this->writeIntoLog('Update failed: ' . $errors[2]." near $str");
			return false;
		}
	}
	
	public function delete($tableName, $fieldName, $fieldValue){
		$str = "DELETE FROM $tableName WHERE $fieldName = :$fieldName";
		$sql = $this->db->prepare($str);
		$sql->bindValue(":$fieldName",$fieldValue, PDO::PARAM_INT);
		$result = $sql->execute();
		if($result){
			return $sql->rowCount();
		}
		else{
			$errors = $sql->errorInfo();
			$this->writeIntoLog('Delete failed: ' . $errors[2]." near $str");
			return false;
		}
	}

	public function supportEmail($emailId = 'info'){
		$emailAddress = array('info'=>"info@".LIVE_DOMAIN,
							'support'=>"support@".LIVE_DOMAIN,
							'do_not_reply'=>"do_not_reply@".LIVE_DOMAIN,
							'Host'=>"mail.skitsbd.com",
							'Username'=>"info@skitsbd.com",
							'Password'=>"info123!@#"
							);
		if(!array_key_exists($emailId, $emailAddress)){
			return 'shobhancse@gmail.com';
		}
		else{
			return $emailAddress[$emailId];
		}
	}

	public function query($statement, $bindData, $paramType=0){
		
		$sql = $this->db->prepare($statement);
		if(!empty($bindData)){
			foreach($bindData as $fieldname=>$fieldvalue){
				if($paramType>0){
					$sql->bindValue(":$fieldname",$fieldvalue, PDO::PARAM_INT);
				}
				else{
					$sql->bindValue(":$fieldname",$fieldvalue, PDO::PARAM_STR);
				}
			}
		}
		
		/*$result = $sql->execute();
		if($result){
			return $sql;
		}
		else{
			$errors = $sql->errorInfo();
			$this->writeIntoLog('Query failed: ' . $errors[2]);
			return false;
		}*/
		
		$sql->execute();		
		$errors = $sql->errorInfo();
		if($errors[2]==''){
			if($sql->rowCount()>0){
				return $sql;
			}
			else{
				return false;
			}
		}
		else{
			$this->writeIntoLog('Query failed: ' . $errors[2]." near $statement");
			return false;
		}
	}

	public function querypagination($statement, $bindData, $paramType=0){
		
		$sql = $this->db->prepare($statement);
		if(count($bindData)>0){
			foreach($bindData as $fieldname=>$fieldvalue){
				if($paramType>0){
					$sql->bindValue(":$fieldname",$fieldvalue, PDO::PARAM_INT);
				}
				else{
					$sql->bindValue(":$fieldname",$fieldvalue, PDO::PARAM_STR);
				}
			}
		}
		$result = $sql->execute();
		if($result){
			$returnData = $sql->fetchAll(PDO::FETCH_ASSOC);
		}
		else{
			$errors = $sql->errorInfo();
			$this->writeIntoLog('Query Pagination failed: ' . $errors[2]." near $statement");
			$returnData = false;
		}
		
		return $returnData;
	}

	public function seoInfo($metaName = 'metaTitle'){
		$SEOmetaData = array();
		$SEOmetaData['metaSiteName'] = "Pesterminate, the best pest control, professional pest control service in Toronto";
		$SEOmetaData['metaTitle'] = "Residential Pest Control in Toronto | Best Pest Control in Toronto";
		$SEOmetaData['metaKeyword'] = "Residential Pest Control in Toronto, Best Pest Control in Toronto, Mice removal treatment in Toronto, Rat Removal treatment in Toronto, Ants and Carpenter Ants Removal treatment in Toronto";
		$SEOmetaData['metaDescription'] = "Residential Pest Control in Toronto. We are a professional pest control company dedicated to helping you keep your home or business free from unwanted pests.";
		$SEOmetaData['metaDomain'] = "https://pesterminate.ca/";
		$SEOmetaData['metaImage'] = $SEOmetaData['metaDomain']."website_assets/images/logo.png";
		$SEOmetaData['metaVideo'] = "http://www.youtube.com/watch?v=xkcoM8eUuL8";
		$SEOmetaData['metaLocale'] = "en_CA";
		$SEOmetaData['metaUrl'] = array();
		$SEOmetaData['metaUrl']['residential-pest-control-in-toronto.html'] = 'Residential Pest Control in Toronto';
		$SEOmetaData['metaUrl']['best-pest-control-in-toronto.html'] = 'Best Pest Control in Toronto';
		$SEOmetaData['metaUrl']['mice-removal-treatment-in-toronto.html'] = 'Mice removal treatment in Toronto';
		$SEOmetaData['metaUrl']['rat-removal-treatment-in-toronto.html'] = 'Rat Removal treatment in Toronto';
		$SEOmetaData['metaUrl']['ants-carpenter-ants-removal-treatment-in-toronto.html'] = 'Ants and Carpenter Ants Removal treatment in Toronto';


		return $SEOmetaData[$metaName]??$SEOmetaData['metaTitle'];
	}
}
?>
