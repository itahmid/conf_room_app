<?php
class admin {
	private static $user_id;
	private static $qryUser;
	
	public function setAdmin($id) {
		
		$rsUser = mysql_query("SELECT * FROM booking_admins WHERE admin_id = '".mysql_real_escape_string($id)."' AND admin_active=1");
		
		$rowUser = mysql_fetch_array($rsUser);
		admin::$qryUser = $rowUser;
		admin::$user_id=$rowUser["admin_id"];
		
	}
	
	public function doLogin($username, $password) {
		$returnvalue = 0;
		$rsLogin = mysql_query("SELECT * FROM booking_admins WHERE admin_username = '".mysql_real_escape_string($username)."' AND admin_password = '".md5($password)."'");
		if(mysql_num_rows($rsLogin)>0) {
			$returnvalue = mysql_result($rsLogin,0,'admin_id');
			
		}
		$this->setAdmin($returnvalue);
		
		return $returnvalue;
	}
	
	public function getAdminId() {
		return admin::$user_id;
	}
	
	public function getAdminUsername() {
		return admin::$qryUser["admin_username"];
	}
	
	public function updatePassword() {
		mysql_query("UPDATE booking_admins SET admin_password='".md5($_POST["password"])."' WHERE admin_id=".$_SESSION["admin_id"]);
		echo mysql_error();
	}
	
}

?>