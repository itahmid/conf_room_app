<?php
include '../common.php';
if(isset($_SESSION["admin_id"]) && $_SESSION["admin_id"] > 0) {
	$item_id = $_REQUEST["item_id"];	
	
	mysql_query("DELETE FROM booking_calendars WHERE calendar_id = ".$item_id);
	
	//delete, holidays, slots and reservations
	mysql_query("DELETE FROM booking_holidays WHERE calendar_id = ".$item_id);
	mysql_query("DELETE FROM booking_slots WHERE calendar_id=".$item_id);
	mysql_query("DELETE FROM booking_reservation WHERE calendar_id = ".$item_id);
	
}


include 'calendars.php';
?>
