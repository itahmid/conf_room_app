<?php
include '../common.php';
mysql_query("UPDATE booking_categories SET category_name= '".mysql_real_escape_string($_REQUEST["name"])."' WHERE category_id=".$_REQUEST["item_id"]);
		
	

?>
