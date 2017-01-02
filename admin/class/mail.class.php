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
	
	public function updateMail() {
		if(isset($_POST["mail_cancel_text"])) {
			$mail_cancel_text = $_POST["mail_cancel_text"];
		} else {
			$mail_cancel_text = "";
		}
		mysql_query("UPDATE booking_emails SET email_name='".mysql_real_escape_string($_POST["mail_name"])."',email_subject='".mysql_real_escape_string($_POST["mail_subject"])."', email_text='".mysql_real_escape_string($_POST["mail_text"])."', email_cancel_text='".mysql_real_escape_string($mail_cancel_text)."',email_signature='".mysql_real_escape_string($_POST["mail_signature"])."' WHERE email_id=".$this->getMailId());
		
	}

}

?>