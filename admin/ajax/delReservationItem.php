<?php
include '../common.php';
if(isset($_SESSION["admin_id"]) && $_SESSION["admin_id"] > 0) {
	$item_id = $_REQUEST["item_id"];	
	
	mysql_query("DELETE FROM booking_reservation WHERE reservation_id = ".$item_id);
	
}


include 'reservation.php';
?>
