<?php
class holiday {
	private static $holiday_id;
	private static $holidayQry;
	
	public function setHoliday($id) {
		$holidayQry = mysql_query("SELECT * FROM booking_holidays WHERE holiday_id='".mysql_real_escape_string($id)."'");
		$holidayRow = mysql_fetch_array($holidayQry);
		holiday::$holidayQry = $holidayRow;
		holiday::$holiday_id=$holidayRow["holiday_id"];
	}
	
	public function getHolidayId() {
		return holiday::$holiday_id;
	}
		
	public function getHolidayDate() {
		return holiday::$holidayQry["holiday_date"];
	}
	public function getHolidayRecordcount($calendar_id) {
		return mysql_num_rows(mysql_query("SELECT * FROM booking_holidays WHERE calendar_id = '".mysql_real_escape_string($calendar_id)."'"));
	}
}
?>