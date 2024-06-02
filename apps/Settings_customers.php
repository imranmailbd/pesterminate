<?php
class Settings_customers{
	
	protected $db;
	
	public function __construct($db){$this->db = $db;}
	
	public function custom_fields(){
				
		$Common = new Common($this->db);
		$innerHTMLStr = $Common->load_custom_fields('customers');
				
		$Settings = new Settings($this->db);
		$Settings->pageTitle = $GLOBALS['title'];
		return $Settings->htmlBody($innerHTMLStr);
	}	
    
}
?>