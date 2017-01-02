<?php

$db_host = "";
$db_user = "";
$db_pass = "";
$db_name = "";

$dblink=mysql_connect($db_host, $db_user, $db_pass) or die('Unable to establish a DB connection');

mysql_query("SET NAMES 'utf8'");
mysql_select_db($db_name, $dblink);

?>
