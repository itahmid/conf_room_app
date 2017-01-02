<?php 
error_reporting(0);
@set_time_limit(0);
include_once dirname(__FILE__).'/admin/include/db_conn.php';
include_once dirname(__FILE__).'/include/lang.php';
include_once dirname(__FILE__).'/class/settings.class.php';
$settingObj = new setting();
//handling timezone in php and mysql
date_default_timezone_set($settingObj->getTimezone());
$now = new DateTime();
$mins = $now->getOffset() / 60;
$sgn = ($mins < 0 ? -1 : 1);
$mins = abs($mins);
$hrs = floor($mins / 60);
$mins -= $hrs * 60;
$offset = sprintf('%+d:%02d', $hrs*$sgn, $mins);
mysql_query("SET time_zone='$offset';");

define('CALENDAR_PATH',$settingObj->getSiteDomain());
include_once dirname(__FILE__).'/class/list.class.php';
include_once dirname(__FILE__).'/class/holiday.class.php';
include_once dirname(__FILE__).'/class/slot.class.php';
include_once dirname(__FILE__).'/class/reservation.class.php';
include_once dirname(__FILE__).'/class/calendar.class.php';
include_once dirname(__FILE__).'/class/mail.class.php';
include_once dirname(__FILE__).'/class/lang.class.php';
include_once dirname(__FILE__).'/class/category.class.php';
include_once dirname(__FILE__).'/class/utils.class.php';

$listObj = new lists();
$holidayObj = new holiday();
$slotsObj = new slot();
$reservationObj = new reservation();
$calendarObj = new calendar();
$mailObj = new email();
$langObj = new lang();
$categoryObj = new category();
$utilsObj = new utils();

session_start();

?>
