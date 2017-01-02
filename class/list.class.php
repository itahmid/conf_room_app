<?php
class lists {	
	public function getTimezonesList() {
		$arrayTimezones = Array();
		$timezonesQry = mysql_query("SELECT * FROM booking_timezones ORDER BY timezone_name");
		
		while($timezoneRow=mysql_fetch_array($timezonesQry)) {
			$arrayTimezones[$timezoneRow["timezone_id"]] = Array();
			$arrayTimezones[$timezoneRow["timezone_id"]]["timezone_name"] = $timezoneRow["timezone_name"];
			$arrayTimezones[$timezoneRow["timezone_id"]]["timezone_value"] = $timezoneRow["timezone_value"];
		}
		return $arrayTimezones;
	}	
	
	public function getHolidaysList($order_by,$calendar_id) {
		$arrayHolidays = Array();
		$holidaysQry = mysql_query("SELECT * FROM booking_holidays WHERE calendar_id = '".mysql_real_escape_string($calendar_id)."' ".$order_by);
		
		while($holidayRow=mysql_fetch_array($holidaysQry)) {
			$arrayHolidays[$holidayRow["holiday_id"]] = Array();
			$arrayHolidays[$holidayRow["holiday_id"]]["holiday_date"] = $holidayRow["holiday_date"];
		}
		return $arrayHolidays;
	}	
	
	public function getSlotsHoursList($calendar_id) {
		$arraySlots = Array();
		$slotsQry = mysql_query("SELECT DISTINCT slot_time_from FROM booking_slots WHERE slot_date >= NOW() AND slot_active = 1 AND calendar_id = '".mysql_real_escape_string($calendar_id)."' ORDER BY slot_time_from");
		
		while($slotRow=mysql_fetch_array($slotsQry)) {
			array_push($arraySlots,$slotRow["slot_time_from"]);
		}
		return $arraySlots;
	}	
	
	public function getSlotsList($filter,$order_by,$calendar_id) {
		$arraySlots = Array();
		$slotsQry = mysql_query("SELECT * FROM booking_slots WHERE slot_active = 1 AND calendar_id = '".mysql_real_escape_string($calendar_id)."' ".$filter." ".$order_by);
		
		while($slotRow=mysql_fetch_array($slotsQry)) {
			$arraySlots[$slotRow["slot_id"]] = Array();
			$arraySlots[$slotRow["slot_id"]]["slot_date"] = $slotRow["slot_date"];
			$arraySlots[$slotRow["slot_id"]]["slot_time_from"] = $slotRow["slot_time_from"];
			$arraySlots[$slotRow["slot_id"]]["slot_special_text"] = stripslashes($slotRow["slot_special_text"]);
			$arraySlots[$slotRow["slot_id"]]["slot_special_mode"] = $slotRow["slot_special_mode"];
			$reservation=mysql_num_rows(mysql_query("SELECT * FROM booking_reservation WHERE slot_id = '".mysql_real_escape_string($slotRow["slot_id"])."' AND reservation_cancelled = 0"));
			if($reservation == 0) {
				$reservation = "NO";
			} else {
				$reservation = "YES";
			}
			$arraySlots[$slotRow["slot_id"]]["slot_reservation"] = $reservation;
		}
		return $arraySlots;
	}	
	
	public function getReservationsList($filter,$order_by,$calendar_id) {
		$arrayReservations = Array();
		$reservationsQry = mysql_query("SELECT * FROM booking_reservation r INNER JOIN booking_slots s ON s.slot_id=r.slot_id WHERE r.calendar_id = '".mysql_real_escape_string($calendar_id)."' AND s.calendar_id = '".mysql_real_escape_string($calendar_id)."' ".$filter." ".$order_by);
		
		while($reservationRow=mysql_fetch_array($reservationsQry)) {
			$arrayReservations[$reservationRow["reservation_id"]] = Array();
			$arrayReservations[$reservationRow["reservation_id"]]["reservation_date"] = $reservationRow["slot_date"];
			$arrayReservations[$reservationRow["reservation_id"]]["reservation_time"] = $reservationRow["slot_time_from"];
			$arrayReservations[$reservationRow["reservation_id"]]["reservation_surname"] = stripslashes($reservationRow["reservation_surname"]);
			$arrayReservations[$reservationRow["reservation_id"]]["reservation_name"] = stripslashes($reservationRow["reservation_name"]);
			$arrayReservations[$reservationRow["reservation_id"]]["reservation_email"] = $reservationRow["reservation_email"];
			$arrayReservations[$reservationRow["reservation_id"]]["reservation_confirmed"] = $reservationRow["reservation_confirmed"];
			$arrayReservations[$reservationRow["reservation_id"]]["reservation_cancelled"] = $reservationRow["reservation_cancelled"];
			$arrayReservations[$reservationRow["reservation_id"]]["slot_active"] = $reservationRow["slot_active"];
		}
		return $arrayReservations;
	}	
	
	public function getCalendarsList($order_by,$category_id=0) {
		$arrayCalendars = Array();
		if($category_id>0) {
			$calendarsQry = mysql_query("SELECT * FROM booking_calendars WHERE category_id='".mysql_real_escape_string($category_id)."' AND calendar_active = 1 ".$order_by);
		} else {
			$calendarsQry = mysql_query("SELECT * FROM booking_calendars WHERE calendar_active = 1 ".$order_by);
		}
		
		while($calendarRow=mysql_fetch_array($calendarsQry)) {
			$arrayCalendars[$calendarRow["calendar_id"]] = Array();
			$arrayCalendars[$calendarRow["calendar_id"]]["calendar_title"] = stripslashes($calendarRow["calendar_title"]);
			$arrayCalendars[$calendarRow["calendar_id"]]["calendar_active"] = $calendarRow["calendar_active"];
			$arrayCalendars[$calendarRow["calendar_id"]]["calendar_order"] = $calendarRow["calendar_order"];
		}
		return $arrayCalendars;
	}
	
	public function getCategoriesList($order_by) {
		$arrayCategories = Array();
		$categoriesQry = mysql_query("SELECT * FROM booking_categories WHERE category_active = 1 ".$order_by);
		
		while($categoryRow=mysql_fetch_array($categoriesQry)) {
			$arrayCategories[$categoryRow["category_id"]] = Array();
			$arrayCategories[$categoryRow["category_id"]]["category_name"] = stripslashes($categoryRow["category_name"]);
			$arrayCategories[$categoryRow["category_id"]]["category_active"] = $categoryRow["category_active"];
		}
		return $arrayCategories;
	}
	
	public function getMonthCalendar($month,$year,$weekday_format="N") {
		$arrayMonth=Array();
		$date = mktime(0,0,0,$month,1,$year); 
		for($n=1;$n <= date('t',$date);$n++){
			$arrayMonth[$n] = Array();
			$arrayMonth[$n]["dayofweek"] = date($weekday_format,mktime(0,0,0,$month,$n,$year));
			$arrayMonth[$n]["daynum"] = date('d',mktime(0,0,0,$month,$n,$year));
			$arrayMonth[$n]["monthnum"] = date('m',mktime(0,0,0,$month,$n,$year));
			$arrayMonth[$n]["yearnum"] = date('Y',mktime(0,0,0,$month,$n,$year));
		}
		return $arrayMonth;
	}
	
	public function getSlotsPerDay($year,$month,$daynum, $calendar_id,$settingObj) {
		if(strlen($month) == 1) {
			$month="0".$month;
		}
		if(strlen($daynum) == 1) {
			$daynum="0".$daynum;
		}
		if($year."-".$month."-".$daynum == date('Y-m-d')) {
			$slotsQry = mysql_query("SELECT SUM(s.slot_av) AS av_seats,s.* FROM booking_slots s WHERE s.slot_active=1 AND s.slot_date = '".$year."-".$month."-".$daynum."'  AND REPLACE(s.slot_time_from,':','') >= DATE_FORMAT(NOW(),'%H%i%s') AND s.calendar_id='".mysql_real_escape_string($calendar_id)."' GROUP BY s.slot_id");
		} else {
			$slotsQry = mysql_query("SELECT SUM(s.slot_av) AS av_seats,s.* FROM booking_slots s WHERE s.slot_active = 1 AND s.slot_date = '".$year."-".$month."-".$daynum."'  AND s.calendar_id='".mysql_real_escape_string($calendar_id)."' GROUP BY s.slot_id");
			
		}
		
		$tot = mysql_num_rows($slotsQry);
		if($tot == 0) {
			//it's not soldout
			return -1;
		} else {
			if($settingObj->getSlotsUnlimited() != 1 && $settingObj->getShowSlotsSeats() == 0) {
				while($slotRow = mysql_fetch_array($slotsQry)) {
					$reservationQry=mysql_query("SELECT SUM(reservation_seats) as res FROM booking_reservation WHERE slot_id='".mysql_real_escape_string($slotRow["slot_id"])."' AND reservation_cancelled = 0 GROUP BY slot_id");
					if((mysql_num_rows($reservationQry)>0 && mysql_result($reservationQry,0,'res') == $slotRow["slot_av"]) || (mysql_num_rows($reservationQry)>0 && $settingObj->getSlotsUnlimited() == 0)) {
						//$tot = $tot-mysql_result($reservationQry,0,'res');
						$tot--;
					}
				}
			} else if($settingObj->getSlotsUnlimited() == 2 && $settingObj->getShowSlotsSeats() == 1) {
				$tot=0;
				while($rowSlot = mysql_fetch_array($slotsQry)) {
					if($rowSlot["av_seats"] == 0) {
						$tot++;
					} else {
						$tot = $tot+$rowSlot["av_seats"];
					}
					$reservationQry=mysql_query("SELECT SUM(reservation_seats) as res FROM booking_reservation WHERE slot_id='".mysql_real_escape_string($rowSlot["slot_id"])."' AND reservation_cancelled = 0 GROUP BY slot_id");
					if(mysql_num_rows($reservationQry)>0) {
						//$tot = $tot-mysql_result($reservationQry,0,'res');
						$tot = $tot-mysql_result($reservationQry,0,'res');
					}
				}
				
			}
			return $tot;
		}
	}
	
	public function getSlotsPerDayList($year,$month,$day,$calendar_id,$settingObj) {
		
		$arraySlots=Array();
		if(strlen($month) == 1) {
			$month="0".$month;
		}
		if(strlen($day) == 1) {
			$day="0".$day;
		}
		if($year."-".$month."-".$day == date('Y-m-d')) {
		
			$slotsQry = mysql_query("SELECT * FROM booking_slots WHERE slot_active=1 AND slot_date = '".$year."-".$month."-".$day."' AND REPLACE(slot_time_from,':','') >= DATE_FORMAT(NOW(),'%H%i%s') AND calendar_id='".mysql_real_escape_string($calendar_id)."' ORDER BY slot_time_from");
		} else {
			$slotsQry = mysql_query("SELECT * FROM booking_slots WHERE slot_active = 1 AND slot_date = '".$year."-".$month."-".$day."' AND calendar_id='".mysql_real_escape_string($calendar_id)."' ORDER BY slot_time_from");
		}
		
		while($slotRow = mysql_fetch_array($slotsQry)) {
			if($settingObj->getSlotsUnlimited() == 0 && $settingObj->getShowBookedSlots() == 0) {
				$reservationQry=mysql_query("SELECT * FROM booking_reservation WHERE slot_id='".mysql_real_escape_string($slotRow["slot_id"])."' AND reservation_cancelled = 0");
				if(mysql_num_rows($reservationQry)==0) {
					$arraySlots[$slotRow["slot_id"]] = Array();
					$arraySlots[$slotRow["slot_id"]]["slot_time_from"] = $slotRow["slot_time_from"];
					$arraySlots[$slotRow["slot_id"]]["slot_time_to"] = $slotRow["slot_time_to"];
					$arraySlots[$slotRow["slot_id"]]["slot_special_text"] = stripslashes($slotRow["slot_special_text"]);
					$arraySlots[$slotRow["slot_id"]]["slot_special_mode"] = $slotRow["slot_special_mode"];
					$arraySlots[$slotRow["slot_id"]]["slot_price"] = $slotRow["slot_price"];
					$arraySlots[$slotRow["slot_id"]]["slot_av"] = $slotRow["slot_av"];
					$arraySlots[$slotRow["slot_id"]]["booked"] = 0;
				}
			} else if($settingObj->getSlotsUnlimited() == 1) {
				$arraySlots[$slotRow["slot_id"]] = Array();
				$arraySlots[$slotRow["slot_id"]]["slot_time_from"] = $slotRow["slot_time_from"];
				$arraySlots[$slotRow["slot_id"]]["slot_time_to"] = $slotRow["slot_time_to"];
				$arraySlots[$slotRow["slot_id"]]["slot_special_text"] = stripslashes($slotRow["slot_special_text"]);
				$arraySlots[$slotRow["slot_id"]]["slot_special_mode"] = $slotRow["slot_special_mode"];
				$arraySlots[$slotRow["slot_id"]]["slot_price"] = $slotRow["slot_price"];
				$arraySlots[$slotRow["slot_id"]]["slot_av"] = $slotRow["slot_av"];
				$arraySlots[$slotRow["slot_id"]]["booked"] = 0;
			} else if($settingObj->getSlotsUnlimited() == 0 && $settingObj->getShowBookedSlots() == 1) {
				$reservationQry=mysql_query("SELECT * FROM booking_reservation WHERE slot_id='".mysql_real_escape_string($slotRow["slot_id"])."' AND reservation_cancelled = 0");
				if(mysql_num_rows($reservationQry)>0) {
					$booked=1;
				} else {
					$booked = 0;
				}
				$arraySlots[$slotRow["slot_id"]] = Array();
				$arraySlots[$slotRow["slot_id"]]["slot_time_from"] = $slotRow["slot_time_from"];
				$arraySlots[$slotRow["slot_id"]]["slot_time_to"] = $slotRow["slot_time_to"];
				$arraySlots[$slotRow["slot_id"]]["slot_special_text"] = stripslashes($slotRow["slot_special_text"]);
				$arraySlots[$slotRow["slot_id"]]["slot_special_mode"] = $slotRow["slot_special_mode"];
				$arraySlots[$slotRow["slot_id"]]["slot_price"] = $slotRow["slot_price"];
				$arraySlots[$slotRow["slot_id"]]["slot_av"] = $slotRow["slot_av"];
				$arraySlots[$slotRow["slot_id"]]["booked"] = $booked;
			} else if($settingObj->getSlotsUnlimited() == 2) {
				$booked = 0;
				$reservationQry=mysql_query("SELECT SUM(reservation_seats) as seats FROM booking_reservation WHERE slot_id='".mysql_real_escape_string($slotRow["slot_id"])."' AND reservation_cancelled = 0 GROUP BY slot_id");
				
				if($settingObj->getShowBookedSlots() == 1 && mysql_num_rows($reservationQry)>0 && mysql_result($reservationQry,0,'seats') == $slotRow["slot_av"]) {
					
					
					
						$booked=1;
						$slot_av = 0;
						$arraySlots[$slotRow["slot_id"]] = Array();
						$arraySlots[$slotRow["slot_id"]]["slot_time_from"] = $slotRow["slot_time_from"];
						$arraySlots[$slotRow["slot_id"]]["slot_time_to"] = $slotRow["slot_time_to"];
						$arraySlots[$slotRow["slot_id"]]["slot_special_text"] = stripslashes($slotRow["slot_special_text"]);
						$arraySlots[$slotRow["slot_id"]]["slot_special_mode"] = $slotRow["slot_special_mode"];
						$arraySlots[$slotRow["slot_id"]]["slot_price"] = $slotRow["slot_price"];
						$arraySlots[$slotRow["slot_id"]]["slot_av"] = $slot_av;
						$arraySlots[$slotRow["slot_id"]]["slot_av_max"] = $slot_av;
						$arraySlots[$slotRow["slot_id"]]["booked"] = $booked;
					
				} else {
					$booked=0;
					if(mysql_num_rows($reservationQry)>0 && mysql_result($reservationQry,0,'seats') == $slotRow["slot_av"]) {
					} else if(mysql_num_rows($reservationQry)>0) {
						$slot_av = $slotRow["slot_av"]-mysql_result($reservationQry,0,'seats');
						$slot_av_max=$slotRow["slot_av_max"];
						if($slot_av_max>$slot_av) {
							$slot_av_max = $slot_av;	
						}
						$arraySlots[$slotRow["slot_id"]] = Array();
						$arraySlots[$slotRow["slot_id"]]["slot_time_from"] = $slotRow["slot_time_from"];
						$arraySlots[$slotRow["slot_id"]]["slot_time_to"] = $slotRow["slot_time_to"];
						$arraySlots[$slotRow["slot_id"]]["slot_special_text"] = stripslashes($slotRow["slot_special_text"]);
						$arraySlots[$slotRow["slot_id"]]["slot_special_mode"] = $slotRow["slot_special_mode"];
						$arraySlots[$slotRow["slot_id"]]["slot_price"] = $slotRow["slot_price"];
						$arraySlots[$slotRow["slot_id"]]["slot_av"] = $slot_av;
						$arraySlots[$slotRow["slot_id"]]["slot_av_max"] = $slot_av_max;
						$arraySlots[$slotRow["slot_id"]]["booked"] = $booked;
					} else {							
						$slot_av = $slotRow["slot_av"];
						$slot_av_max=$slotRow["slot_av_max"];
						if($slot_av_max>$slot_av) {
							$slot_av_max = $slot_av;	
						}		
						$arraySlots[$slotRow["slot_id"]] = Array();
						$arraySlots[$slotRow["slot_id"]]["slot_time_from"] = $slotRow["slot_time_from"];
						$arraySlots[$slotRow["slot_id"]]["slot_time_to"] = $slotRow["slot_time_to"];
						$arraySlots[$slotRow["slot_id"]]["slot_special_text"] = stripslashes($slotRow["slot_special_text"]);
						$arraySlots[$slotRow["slot_id"]]["slot_special_mode"] = $slotRow["slot_special_mode"];
						$arraySlots[$slotRow["slot_id"]]["slot_price"] = $slotRow["slot_price"];
						$arraySlots[$slotRow["slot_id"]]["slot_av"] = $slot_av;
						$arraySlots[$slotRow["slot_id"]]["slot_av_max"] = $slot_av_max;
						$arraySlots[$slotRow["slot_id"]]["booked"] = $booked;					
					}
					
					
					
						
					
					
				}
				
			}
		}
		return $arraySlots;
	}
	
	public function getSlotsByReservationsList($reservations) {
		$arraySlots = Array();
		$arrayReservations = explode(",",$reservations);
		$listReservations = "";
		for($i=0;$i<count($arrayReservations);$i++) {
			if($listReservations=="") {
				$listReservations.="'".$arrayReservations[$i]."'";
			} else {
				$listReservations.=",'".$arrayReservations[$i]."'";
			}
		}
		
		$slotsQry = mysql_query("SELECT * FROM booking_reservation WHERE MD5(reservation_id) IN (".$listReservations.")");
		while($slotRow=mysql_fetch_array($slotsQry)) {
			array_push($arraySlots,$slotRow["slot_id"]);
		}
		return $arraySlots;
	}	
	
	public function getCustomerDataList($reservations) {
		
		$arrayReservations = explode(",",$reservations);
		$listReservations = "";
		for($i=0;$i<count($arrayReservations);$i++) {
			if($listReservations=="") {
				$listReservations.="'".$arrayReservations[$i]."'";
			} else {
				$listReservations.=",'".$arrayReservations[$i]."'";
			}
		}
		$slotsQry = mysql_query("SELECT * FROM booking_reservation WHERE MD5(reservation_id) IN (".$listReservations.") LIMIT 1");
		$slotRow = mysql_fetch_array($slotsQry);
		
		return $slotRow["reservation_id"];
	}	
	
	
	
}

?>
