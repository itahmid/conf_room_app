<?php
include '../common.php';
if(isset($_SESSION["admin_id"]) && $_SESSION["admin_id"]>0) {
	$old = $_GET["old"];
	$rsCheck=mysql_query("SELECT * FROM booking_admins WHERE admin_id='".mysql_real_escape_string($_SESSION["admin_id"])."' AND admin_password='".md5($old)."'");
	echo mysql_num_rows($rsCheck);
}
?>