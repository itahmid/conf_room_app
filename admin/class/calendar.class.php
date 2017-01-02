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
	
	public function getCalendarOrder() {
		return calendar::$calendarQry["calendar_order"];
	}
	
	public function publishCalendars($listIds) {
		mysql_query("UPDATE booking_calendars SET calendar_active = 1 WHERE calendar_id IN (".$listIds.")");
	}
	
	public function unpublishCalendars($listIds) {
		mysql_query("UPDATE booking_calendars SET calendar_active = 0 WHERE calendar_id IN (".$listIds.")");
	}
	
	public function delCalendars($listIds) {
		mysql_query("DELETE FROM booking_calendars WHERE calendar_id IN (".$listIds.")");
		//delete holidays
		mysql_query("DELETE FROM booking_holidays WHERE calendar_id IN (".$listIds.")");
		//check for reservations, if any disable slots, otherwise del slots
		$slotsQry=mysql_query("SELECT * FROM booking_slots WHERE calendar_id IN (".$listIds.")");
		while($slotRow = mysql_fetch_array($slotsQry)) {
			$numRes=mysql_num_rows(mysql_query("SELECT * FROM booking_reservation WHERE slot_id ='".mysql_real_escape_string($slotRow["slot_id"])."'"));
			if($numRes>0) {
				mysql_query("UPDATE booking_slots SET slot_active = 0 WHERE slot_id  =".$slotRow["slot_id"]);
			} else {
				mysql_query("DELETE FROM booking_slots  WHERE slot_id =".$slotRow["slot_id"]);
			}
			
		}
		
	}
	
	public function addCalendar() {
		$newOrder = 0;
		//check order of last calendar
		$calOrderQry = mysql_query("SELECT calendar_order as max FROM booking_calendars WHERE category_id='".mysql_real_escape_string($_POST["category_id"])."' ORDER BY calendar_order DESC LIMIT 1");
		if(mysql_num_rows($calOrderQry)>0) {
			$newOrder=mysql_result($calOrderQry,0,'max')+1;
		}
		mysql_query("INSERT INTO booking_calendars (category_id,calendar_title,calendar_email,calendar_order,calendar_active) VALUES(".$_POST["category_id"].",'".mysql_real_escape_string($_POST["calendar_title"])."','".mysql_real_escape_string($_POST["calendar_email"])."',".$newOrder.",0)");
		$calendar_id=mysql_insert_id();
		return $calendar_id;
	}
	
	public function updateCalendar() {
		$calendar_id = $_POST["calendar_id"];
		//check if the category has changed
		$this->setCalendar($calendar_id);
		$newOrder=0;
		//update calendars order
		$calOrderQry = mysql_query("SELECT calendar_order as max FROM booking_calendars WHERE category_id='".mysql_real_escape_string($_POST["category_id"])."' ORDER BY calendar_order DESC LIMIT 1");
		if(mysql_num_rows($calOrderQry)>0) {
			$newOrder=mysql_result($calOrderQry,0,'max')+1;
		}
		if($this->getCalendarCategoryId() != $_POST["category_id"]) {
			
			mysql_query("UPDATE booking_calendars SET calendar_order = calendar_order -1 WHERE calendar_order > ".$this->getCalendarOrder()." AND calendar_id <> ".$calendar_id." AND category_id = ".$this->getCalendarCategoryId());
			
		} 
		mysql_query("UPDATE booking_calendars SET calendar_title='".mysql_real_escape_string($_POST["calendar_title"])."',calendar_email='".mysql_real_escape_string($_POST["calendar_email"])."', category_id=".$_POST["category_id"].", calendar_order = ".$newOrder." WHERE calendar_id=".$_POST["calendar_id"]);
	}
	
	public function getCalendarRecordcount() {
		return mysql_num_rows(mysql_query("SELECT * FROM booking_calendars"));
	}
	
	public function setDefaultCalendar($calendar_id,$category_id) {
		mysql_query("UPDATE booking_calendars SET calendar_order = 0, calendar_active = 1 WHERE calendar_id=".$calendar_id." AND category_id=".$category_id);
		mysql_query("UPDATE booking_calendars SET calendar_order = calendar_order +1 WHERE calendar_id <> ".$calendar_id." AND category_id=".$category_id);
	}
	
	public function duplicateCalendars($listIds) {
		$newOrder = 0;
		//check order of last calendar
		$calOrderQry = mysql_query("SELECT calendar_order as max FROM booking_calendars ORDER BY calendar_order DESC LIMIT 1");
		if(mysql_num_rows($calOrderQry)>0) {
			$newOrder=mysql_result($calOrderQry,0,'max')+1;
		}
		$calendarsQry = mysql_query("SELECT * FROM booking_calendars WHERE calendar_id IN (".$listIds.")");
		while($calendarRow=mysql_fetch_array($calendarsQry)) {
			mysql_query("INSERT INTO booking_calendars (category_id,calendar_title,calendar_order,calendar_active) VALUES(".$calendarRow["category_id"].",'duplicate of ".mysql_real_escape_string($calendarRow["calendar_title"])."',".$newOrder.",0)");
			$last_id = mysql_insert_id();
			//duplicate slots
			mysql_query("INSERT INTO booking_slots(slot_special_text,slot_special_mode,slot_date,slot_time_from,slot_time_to,slot_active,calendar_id) SELECT slot_special_text,slot_special_mode,slot_date,slot_time_from,slot_time_to,slot_active, '".$last_id."' FROM booking_slots WHERE calendar_id = ".$calendarRow["calendar_id"]." ORDER BY slot_date,slot_time_from");
			//duplicate holidays
			mysql_query("INSERT INTO booking_holidays(holiday_date,calendar_id) SELECT holiday_date, '".$last_id."' FROM booking_holidays WHERE calendar_id = ".$calendarRow["calendar_id"]." ORDER BY holiday_date");
			
		}
	}

}

?>
