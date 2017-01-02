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
		$slotsQry = mysql_query("SELECT DISTINCT slot_time_from FROM booking_slots WHERE slot_date >= DATE_FORMAT(NOW(),'%Y-%m-%d') AND slot_active = 1 AND calendar_id = '".mysql_real_escape_string($calendar_id)."' ORDER BY slot_time_from");
		
		while($slotRow=mysql_fetch_array($slotsQry)) {
			array_push($arraySlots,$slotRow["slot_time_from"]);
		}
		return $arraySlots;
	}	
	
	public function getSlotsList($filter,$order_by,$calendar_id,$num = 0,$pag = 0) {
		$arraySlots = Array();
		if($pag == 0) {
			$slotsQry = mysql_query("SELECT * FROM booking_slots WHERE slot_active = 1 AND calendar_id = '".mysql_real_escape_string($calendar_id)."' ".$filter." ".$order_by);
		} else {
			if($pag == 1) {
				$start = 0;
			} else {
				$start=(($pag-1)*$num)+1;
			}
			$slotsQry = mysql_query("SELECT * FROM booking_slots WHERE slot_active = 1 AND calendar_id = '".mysql_real_escape_string($calendar_id)."' ".$filter." ".$order_by." LIMIT ".$start.",".$num);
		}
		while($slotRow=mysql_fetch_array($slotsQry)) {
			$arraySlots[$slotRow["slot_id"]] = Array();
			$arraySlots[$slotRow["slot_id"]]["slot_date"] = $slotRow["slot_date"];
			$arraySlots[$slotRow["slot_id"]]["slot_time_from"] = $slotRow["slot_time_from"];
			$arraySlots[$slotRow["slot_id"]]["slot_time_to"] = $slotRow["slot_time_to"];
			$arraySlots[$slotRow["slot_id"]]["slot_special_text"] = stripslashes($slotRow["slot_special_text"]);
			$arraySlots[$slotRow["slot_id"]]["slot_price"] = $slotRow["slot_price"];
			$arraySlots[$slotRow["slot_id"]]["slot_av"] = $slotRow["slot_av"];
			$arraySlots[$slotRow["slot_id"]]["slot_av_max"] = $slotRow["slot_av_max"];
			$reservationQry=mysql_query("SELECT SUM(reservation_seats) as res FROM booking_reservation WHERE slot_id = '".mysql_real_escape_string($slotRow["slot_id"])."' AND reservation_cancelled = 0 GROUP BY slot_id");
			$reservation = 0;
			if(mysql_num_rows($reservationQry)>0) {
				$reservation=mysql_result($reservationQry,0,'res');
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
			$arrayReservations[$reservationRow["reservation_id"]]["reservation_phone"] = stripslashes($reservationRow["reservation_phone"]);
			$arrayReservations[$reservationRow["reservation_id"]]["reservation_message"] = stripslashes($reservationRow["reservation_message"]);
			$arrayReservations[$reservationRow["reservation_id"]]["reservation_email"] = $reservationRow["reservation_email"];
			$arrayReservations[$reservationRow["reservation_id"]]["reservation_seats"] = $reservationRow["reservation_seats"];
			$arrayReservations[$reservationRow["reservation_id"]]["reservation_confirmed"] = $reservationRow["reservation_confirmed"];
			$arrayReservations[$reservationRow["reservation_id"]]["reservation_cancelled"] = $reservationRow["reservation_cancelled"];
			$arrayReservations[$reservationRow["reservation_id"]]["slot_active"] = $reservationRow["slot_active"];
		}
		return $arrayReservations;
	}	
	
	public function getCalendarsList($filter = '') {
		$arrayCalendars = Array();
		$calendarsQry = mysql_query("SELECT * FROM booking_calendars WHERE 0=0 ".$filter." ORDER BY calendar_order");
		
		while($calendarRow=mysql_fetch_array($calendarsQry)) {
			$arrayCalendars[$calendarRow["calendar_id"]] = Array();
			$arrayCalendars[$calendarRow["calendar_id"]]["calendar_title"] = stripslashes($calendarRow["calendar_title"]);
			$arrayCalendars[$calendarRow["calendar_id"]]["calendar_order"] = $calendarRow["calendar_order"];
			$arrayCalendars[$calendarRow["calendar_id"]]["calendar_active"] = $calendarRow["calendar_active"];
			$arrayCalendars[$calendarRow["calendar_id"]]["category_id"] = $calendarRow["category_id"];
		}
		return $arrayCalendars;
	}
	
	public function getCalendarsResList() {
		$arrayCalendars = Array();
		$calendarsQry = mysql_query("SELECT c.*, COUNT(r.reservation_id) as tot_reservation FROM booking_calendars c LEFT JOIN booking_reservation r ON r.calendar_id = c.calendar_id  GROUP BY c.calendar_id ORDER BY c.calendar_order");
		
		while($calendarRow=mysql_fetch_array($calendarsQry)) {
			$arrayCalendars[$calendarRow["calendar_id"]] = Array();
			$arrayCalendars[$calendarRow["calendar_id"]]["calendar_title"] = $calendarRow["calendar_title"];
			$arrayCalendars[$calendarRow["calendar_id"]]["calendar_order"] = $calendarRow["calendar_order"];
			$arrayCalendars[$calendarRow["calendar_id"]]["calendar_active"] = $calendarRow["calendar_active"];
			$arrayCalendars[$calendarRow["calendar_id"]]["category_id"] = $calendarRow["category_id"];
			$arrayCalendars[$calendarRow["calendar_id"]]["tot_reservation"] = $calendarRow["tot_reservation"];
		}
		return $arrayCalendars;
	}
	
	public function getMailsList() {
		$arrayMails = Array();
		$mailsQry = mysql_query("SELECT * FROM booking_emails");
		
		while($mailRow=mysql_fetch_array($mailsQry)) {
			$arrayMails[$mailRow["email_id"]] = Array();
			$arrayMails[$mailRow["email_id"]]["email_name"] = $mailRow["email_name"];
		}
		return $arrayMails;
	}
	
	public function getPaypalLocaleList() {
		$arrayLocales = Array();
		$localesQry = mysql_query("SELECT * FROM booking_paypal_locale ORDER BY locale_country");
		
		while($localeRow=mysql_fetch_array($localesQry)) {
			$arrayLocales[$localeRow["locale_id"]] = Array();
			$arrayLocales[$localeRow["locale_id"]]["locale_country"] = $localeRow["locale_country"];
			$arrayLocales[$localeRow["locale_id"]]["locale_code"] = $localeRow["locale_code"];
		}
		return $arrayLocales;
	}
	
	public function getPaypalCurrencyList() {
		$arrayCurrencies = Array();
		$currenciesQry = mysql_query("SELECT * FROM booking_paypal_currency ORDER BY currency_name");
		
		while($currencyRow=mysql_fetch_array($currenciesQry)) {
			$arrayCurrencies[$currencyRow["currency_id"]] = Array();
			$arrayCurrencies[$currencyRow["currency_id"]]["currency_name"] = $currencyRow["currency_name"];
			$arrayCurrencies[$currencyRow["currency_id"]]["currency_code"] = $currencyRow["currency_code"];
		}
		return $arrayCurrencies;
	}
	
	public function getTextsList($page_id) {
		$arrayTexts = Array();
		$textsQry = mysql_query("SELECT * FROM booking_texts WHERE page_id ='".mysql_real_escape_string($page_id)."'");
		
		while($textRow=mysql_fetch_array($textsQry)) {
			$arrayTexts[$textRow["text_id"]] = Array();
			$arrayTexts[$textRow["text_id"]]["text_label"] = $textRow["text_label"];
			$arrayTexts[$textRow["text_id"]]["text_value"] = stripslashes($textRow["text_value"]);
			$arrayTexts[$textRow["text_id"]]["page_id"] = $textRow["page_id"];
		}
		return $arrayTexts;
	}
	
	public function getCategoriesList() {
		$arrayCategories = Array();
		$categoriesQry = mysql_query("SELECT * FROM booking_categories ORDER BY category_order");
		
		while($categoryRow=mysql_fetch_array($categoriesQry)) {
			$arrayCategories[$categoryRow["category_id"]] = Array();
			$arrayCategories[$categoryRow["category_id"]]["category_name"] = stripslashes($categoryRow["category_name"]);
			$arrayCategories[$categoryRow["category_id"]]["category_order"] = $categoryRow["category_order"];
			$arrayCategories[$categoryRow["category_id"]]["category_active"] = $categoryRow["category_active"];
		}
		return $arrayCategories;
	}
}

?>
