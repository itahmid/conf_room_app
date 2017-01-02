<?php

class reservation {
	private static $reservation_id;
	private static $reservationQry;
	
	public function setReservation($id) {
		$reservationQry = mysql_query("SELECT * FROM booking_reservation WHERE reservation_id = '".mysql_real_escape_string($id)."'");
		
		$reservationRow = mysql_fetch_array($reservationQry);
		reservation::$reservationQry = $reservationRow;
		reservation::$reservation_id=$reservationRow["reservation_id"];
	}
	
	public function getReservationId() {
		return reservation::$reservation_id;
	}
	
	public function getReservationSlotId() {
		return reservation::$reservationQry["slot_id"];
	}
	
	public function getReservationCalendarId() {
		return reservation::$reservationQry["calendar_id"];
	}
	
	public function getReservationName() {
		return stripslashes(reservation::$reservationQry["reservation_name"]);
	}
	
	public function getReservationSurname() {
		return stripslashes(reservation::$reservationQry["reservation_surname"]);
	}
	
	public function getReservationEmail() {
		return reservation::$reservationQry["reservation_email"];
	}
	
	public function getReservationPhone() {
		return stripslashes(reservation::$reservationQry["reservation_phone"]);
	}
	
	public function getReservationMessage() {
		return stripslashes(reservation::$reservationQry["reservation_message"]);
	}
	
	public function getReservationSeats() {
		return reservation::$reservationQry["reservation_seats"];
	}
	
	public function getReservationField1() {
		return stripslashes(reservation::$reservationQry["reservation_field1"]);
	}
	
	public function getReservationField2() {
		return stripslashes(reservation::$reservationQry["reservation_field2"]);
	}
	
	public function getReservationField3() {
		return stripslashes(reservation::$reservationQry["reservation_field3"]);
	}
	
	public function getReservationField4() {
		return stripslashes(reservation::$reservationQry["reservation_field4"]);
	}
	public function getReservationConfirmed() {
		return reservation::$reservationQry["reservation_confirmed"];
	}
	
	public function getReservationCancelled() {
		return reservation::$reservationQry["reservation_cancelled"];
	}
	
	public function delReservations($listIds) {
		mysql_query("DELETE FROM booking_reservation WHERE reservation_id IN (".$listIds.")");
	}
	
	public function isPassed($listIds) {
		
		$arrayReservations = explode(",",$listIds);
		$result = false;
		$listReservations = "";
		for($i=0;$i<count($arrayReservations);$i++) {
			$reservationQry = mysql_query("SELECT s.* FROM booking_reservation r INNER JOIN booking_slots s ON s.slot_id = r.slot_id WHERE MD5(r.reservation_id) = '".$arrayReservations[$i]."'");
			$reservationRow = mysql_fetch_array($reservationQry);
			$resDate = str_replace("-","",$reservationRow["slot_date"]).str_replace(":","",$reservationRow["slot_time_from"]);
			if($resDate<date('YmdHis')) {
				$result = true;
			} 
		}
		return $result;
		
	}
	

}

?>
