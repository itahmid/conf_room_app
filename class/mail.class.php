<?php

class email {
	private static $mail_id;
	private static $mailQry;
	
	public function setMail($id) {
		$mailQry = mysql_query("SELECT * FROM booking_emails WHERE email_id = '".mysql_real_escape_string($id)."'");
		
		$mailRow = mysql_fetch_array($mailQry);
		email::$mailQry = $mailRow;
		email::$mail_id=$mailRow["email_id"];
	}
	
	public function getMailId() {
		return email::$mail_id;
	}
	
	public function getMailName() {
		return stripslashes(email::$mailQry["email_name"]);
	}
	
	public function getMailSubject() {
		return stripslashes(email::$mailQry["email_subject"]);
	}
	
	public function getMailText() {
		return stripslashes(email::$mailQry["email_text"]);
	}
	
	public function getMailCancelText() {
		return stripslashes(email::$mailQry["email_cancel_text"]);
	}
	
	public function getMailSignature() {
		return stripslashes(email::$mailQry["email_signature"]);
	}
	
	

}

?>