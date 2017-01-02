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
	
	public function addHoliday($date_from,$date_to='',$calendar_id) {
		if($date_to=='') {
			//check if this day already exists
			$holidayCheck = mysql_query("SELECT * FROM booking_holidays WHERE holiday_date ='".mysql_real_escape_string($date_from)."' AND calendar_id='".mysql_real_escape_string($calendar_id)."'");
			if(mysql_num_rows($holidayCheck)>0) {				
				return 0;
			} else {
				mysql_query("INSERT INTO booking_holidays (holiday_date,calendar_id) VALUES('".$date_from."',".$calendar_id.")");
				$lastId = mysql_insert_id();
				//check if there are reservation for that date
				$check=mysql_num_rows(mysql_query("SELECT * FROM booking_reservation r INNER JOIN booking_slots s ON s.slot_id = r.slot_id WHERE s.slot_date = '".mysql_real_escape_string($date_from)."' AND r.calendar_id = '".mysql_real_escape_string($calendar_id)."'"));
				if($check>0) {
					mysql_query("UPDATE booking_slots SET slot_active = 0 WHERE slot_date='".$date_from."' AND calendar_id = ".$calendar_id);
				} else {
					mysql_query("DELETE FROM booking_slots WHERE slot_date = '".$date_from."' AND calendar_id=".$calendar_id);
				}
				return $lastId;
			}
		} else {
			$arrNewIds = Array();
			$datefromnum=str_replace("-","",$date_from);
			$datetonum=str_replace("-","",$date_to);
			$date=date_create($date_from);
			
			while($datefromnum<=$datetonum) {
				
				$dateformat=date_format($date, 'Y-m-d');
				//check if this day already exists
				$holidayCheck = mysql_query("SELECT * FROM booking_holidays WHERE holiday_date ='".mysql_real_escape_string($dateformat)."' AND calendar_id='".mysql_real_escape_string($calendar_id)."'");
				if(mysql_num_rows($holidayCheck)==0) {				
					mysql_query("INSERT INTO booking_holidays (holiday_date,calendar_id) VALUES('".$dateformat."',".$calendar_id.")");
					array_push($arrNewIds,mysql_insert_id());
					//check if there are reservation for that date
					$check=mysql_num_rows(mysql_query("SELECT * FROM booking_reservation r INNER JOIN booking_slots s ON s.slot_id = r.slot_id WHERE s.slot_date = '".mysql_real_escape_string($dateformat)."' AND r.calendar_id = '".mysql_real_escape_string($calendar_id)."'"));
					if($check>0) {
						mysql_query("UPDATE booking_slots SET slot_active = 0 WHERE slot_date='".$dateformat."' AND calendar_id = ".$calendar_id);
					} else {
						mysql_query("DELETE FROM booking_slots WHERE slot_date = '".$dateformat."' AND calendar_id=".$calendar_id);
					}
				}
				if(function_exists("date_add")) {
					date_add($date, date_interval_create_from_date_string('1 days'));
				} else {
					date_modify($date, '+1 day');
				}
				//date_add($date, date_interval_create_from_date_string('1 day'));	
				
				$datefromnum = date_format($date,'Ymd');;
			}
			return $arrNewIds;
		}
	}
	
	public function getHolidayRecordcount($calendar_id) {
		return mysql_num_rows(mysql_query("SELECT * FROM booking_holidays WHERE calendar_id = '".mysql_real_escape_string($calendar_id)."'"));
	}
	
	public function delHolidays($listIds) {
		mysql_query("DELETE FROM booking_holidays WHERE holiday_id IN (".$listIds.")");
	}
	
	public function checkHolidayDate($date_from,$date_to='',$calendar_id) {
		if($date_to=='') {
			$check=mysql_num_rows(mysql_query("SELECT * FROM booking_reservation r INNER JOIN booking_slots s ON s.slot_id = r.slot_id WHERE s.slot_date = '".mysql_real_escape_string($date_from)."' AND r.calendar_id = '".mysql_real_escape_string($calendar_id)."'"));
		} else {
			$check=mysql_num_rows(mysql_query("SELECT * FROM booking_reservation r INNER JOIN booking_slots s ON s.slot_id = r.slot_id WHERE s.slot_date >= '".mysql_real_escape_string($date_from)."' AND s.slot_date <= '".mysql_real_escape_string($date_to)."' AND r.calendar_id = '".mysql_real_escape_string($calendar_id)."'"));
		}
		return $check;
	}

}

?>