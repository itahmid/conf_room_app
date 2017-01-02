<?php
include '../common.php';

$item_id = $_REQUEST["category_id"];	

mysql_query("UPDATE booking_categories SET category_active = 1 WHERE category_id = ".$item_id);



?>