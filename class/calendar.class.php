<?php

class calendar {
	private static $calendar_id;
	private static $calendarQry;
	
	public function setCalendar($id) {
		$calendarQry = mysql_query("SELECT * FROM booking_calendars WHERE calendar_id = '".mysql_real_escape_string($id)."'");
		
		$calendarRow = mysql_fetch_array($calendarQry);
		calendar::$calendarQry = $calendarRow;
		calendar::$calendar_id=$calendarRow["calendar_id"];
	}
	
	public function getCalendarId() {
		return calendar::$calendar_id;
	}
	public function getCalendarCategoryId() {
		return stripslashes(calendar::$calendarQry["category_id"]);
	}
	
	public function getCalendarTitle() {
		return stripslashes(calendar::$calendarQry["calendar_title"]);
	}
	
	public function getCalendarEmail() {
		return stripslashes(calendar::$calendarQry["calendar_email"]);
	}
	
	public function getCalendarActive() {
		return calendar::$calendarQry["calendar_active"];
	}
	
	public function getCalendarRecordcount() {
		return mysql_num_rows(mysql_query("SELECT * FROM booking_calendars"));
	}
	
	public function getDefaultCalendar($category_id) {
		$calendarQry = mysql_query("SELECT * FROM booking_calendars WHERE calendar_order = 0 AND calendar_active = 1 AND category_id='".mysql_real_escape_string($category_id)."'");
		if(mysql_num_rows($calendarQry) > 0) {
			$calendarRow = mysql_fetch_array($calendarQry);
			$this->setCalendar($calendarRow["calendar_id"]);
			return true;
		} else {
			return false;
		}
	}
	
	public function getFirstFilledMonth($calendar_id) {
		$returnvalue=date("Y,m,d");
		$arrDate = explode(",",$returnvalue);
		$month = (intval($arrDate[1])-1);
		$returnvalue = $arrDate[0].",".$month.",".$arrDate[2];
		$slotsQry = mysql_query("SELECT * FROM booking_slots WHERE slot_date >= NOW() AND calendar_id = '".mysql_real_escape_string($calendar_id)."' AND slot_active = 1 ORDER BY slot_date ASC LIMIT 1");
		
		if($calendar_id!= 0 && $calendar_id != '' && mysql_num_rows($slotsQry)>0) {
			$rowSlot = mysql_fetch_array($slotsQry);
			$arrDate = explode("-",$rowSlot["slot_date"]);
			$month = (intval($arrDate[1])-1);
			$returnvalue = $arrDate[0].",".$month.",".$arrDate[2];
			
		}
		return $returnvalue;
	}


}

?>
