<?php
include '../common.php';
if(isset($_SESSION["admin_id"]) && $_SESSION["admin_id"] > 0) {
	$item_id = $_REQUEST["reservation_id"];	
	
	mysql_query("UPDATE booking_reservation SET reservation_confirmed = 0,admin_confirmed_cancelled = 1 WHERE reservation_id = ".$item_id);
	
}

?>