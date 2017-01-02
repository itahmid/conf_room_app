<?php 
error_reporting(0);
ini_set("memory_limit",-1);
@set_time_limit(0);
include_once dirname(__FILE__).'/include/db_conn.php';
include_once dirname(__FILE__).'/../include/lang.php';
include_once dirname(__FILE__).'/include/lang.php';
include_once dirname(__FILE__).'/class/settings.class.php';
$settingObj = new setting();
date_default_timezone_set($settingObj->getTimezone());
define('CALENDAR_PATH',$settingObj->getSiteDomain());
include_once dirname(__FILE__).'/class/admin.class.php';
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
$adminObj = new admin();
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
