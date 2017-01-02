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
	
	public function getReservationSeats() {
		return reservation::$reservationQry["reservation_seats"];
	}
	
	public function getReservationConfirmed() {
		return reservation::$reservationQry["reservation_confirmed"];
	}
	
	public function getReservationCancelled() {
		return reservation::$reservationQry["reservation_cancelled"];
	}
	
	
	public function insertReservation($settingObj) {
		$listReservations="";
		for($i=0;$i<count($_POST["reservation_slot"]);$i++) {
			$seats=1;
			if(isset($_POST["reservation_seats_".$_POST["reservation_slot"][$i]])) {
				$seats=$_POST["reservation_seats_".$_POST["reservation_slot"][$i]];
			}
			//check if there are available spots for this slot only if configuration is not infinite
			if($settingObj->getSlotsUnlimited() != 1) {
				$rsSlot = mysql_query("SELECT * FROM booking_slots WHERE slot_id='".mysql_real_escape_string($_POST["reservation_slot"][$i])."'");
				$rowSlot = mysql_fetch_array($rsSlot);
				$avSeats = $rowSlot["slot_av"];
				$ok = 0;
				$rsRes = mysql_query("SELECT * FROM booking_reservation WHERE slot_id = '".mysql_real_escape_string($_POST["reservation_slot"][$i])."' AND reservation_cancelled = 0");
				if(mysql_num_rows($rsRes)==0) {
					$ok = 1;
				} else {
					$totSeats = 0;
					while($rowRes = mysql_fetch_array($rsRes)) {
						$totSeats += $rowRes["reservation_seats"];
					}
					if(($totSeats+$seats)<=$avSeats) {
						$ok = 1;
					}
				
				}
			} else {
				$ok = 1;
			}
			if($ok == 1) {
				mysql_query("INSERT INTO booking_reservation(slot_id,reservation_name,reservation_surname,reservation_email,reservation_phone,reservation_message,reservation_seats,reservation_field1,reservation_field2,reservation_field3,reservation_field4,calendar_id)
							 VALUES(".$_POST["reservation_slot"][$i].",'".mysql_real_escape_string($_POST["reservation_name"])."','".mysql_real_escape_string($_POST["reservation_surname"])."','".$_POST["reservation_email"]."','".mysql_real_escape_string($_POST["reservation_phone"])."','".mysql_real_escape_string($_POST["reservation_message"])."',".$seats.",'".mysql_real_escape_string($_POST["reservation_field1"])."','".mysql_real_escape_string($_POST["reservation_field2"])."','".mysql_real_escape_string($_POST["reservation_field3"])."','".mysql_real_escape_string($_POST["reservation_field4"])."',".$_POST["calendar_id"].")");
							
			
				if($listReservations == "") {
					$listReservations.="".md5(mysql_insert_id())."";
				} else {
					$listReservations.=",".md5(mysql_insert_id())."";				
				}
			}
		}
		
		return $listReservations;
	}
	
	public function confirmReservations($listIds) {
		$arrayReservations = explode(",",$listIds);
		$listReservations = "";
		for($i=0;$i<count($arrayReservations);$i++) {
			if($listReservations=="") {
				$listReservations.="'".$arrayReservations[$i]."'";
			} else {
				$listReservations.=",'".$arrayReservations[$i]."'";
			}
		}
		mysql_query("UPDATE booking_reservation SET reservation_confirmed = 1 WHERE MD5(reservation_id) IN (".$listReservations.")");
	}
	
	
	public function cancelReservations($listIds) {
		$arrayReservations = explode(",",$listIds);
		$listReservations = "";
		for($i=0;$i<count($arrayReservations);$i++) {
			if($listReservations=="") {
				$listReservations.="'".$arrayReservations[$i]."'";
			} else {
				$listReservations.=",'".$arrayReservations[$i]."'";
			}
		}
		mysql_query("UPDATE booking_reservation SET reservation_cancelled = 1, reservation_confirmed = 0 WHERE MD5(reservation_id) IN (".$listReservations.")");
		$checkCalendar = mysql_query("SELECT * FROM booking_reservation WHERE MD5(reservation_id) IN (".$listReservations.")");
		$calendar_id = 0;
		if(mysql_num_rows($checkCalendar)>0) {
			$calendar_id=mysql_result($checkCalendar,0,'calendar_id');
		}
		return $calendar_id;
	}
	
	public function deleteReservations($listIds) {
		
		$arrayReservations = explode(",",$listIds);
		$listReservations = "";
		for($i=0;$i<count($arrayReservations);$i++) {
			if($listReservations=="") {
				$listReservations.="'".$arrayReservations[$i]."'";
			} else {
				$listReservations.=",'".$arrayReservations[$i]."'";
			}
		}
		mysql_query("DELETE FROM booking_reservation WHERE MD5(reservation_id) IN (".$listReservations.")");
	}
	
	public function checkReservationPaypalPaid($listIds) {
		
		$arrayReservations = explode(",",$listIds);
		for($i=0;$i<count($arrayReservations);$i++) {
			$checkQry=mysql_query("SELECT * FROM booking_reservation WHERE MD5(reservation_id) = '".mysql_real_escape_string($arrayReservations[$i])."' AND reservation_confirmed = 1");
			if(mysql_num_rows($checkQry)>0) {
				$count++;
			}
		}
		
		if($count == count($arrayReservations)) {
			$result = 1;
		}
		return $result; 
	}
	public function isPassed($listIds) {
		
		$arrayReservations = explode(",",$listIds);
		$result = false;
		$listReservations = "";
		for($i=0;$i<count($arrayReservations);$i++) {
			$reservationQry = mysql_query("SELECT s.* FROM booking_reservation r INNER JOIN booking_slots s ON s.slot_id = r.slot_id WHERE MD5(r.reservation_id) = '".mysql_real_escape_string($arrayReservations[$i])."'");
			$reservationRow = mysql_fetch_array($reservationQry);
			$resDate = str_replace("-","",$reservationRow["slot_date"]).str_replace(":","",$reservationRow["slot_time_from"]);
			if($resDate<date('YmdHis')) {
				$result = true;
			} 
		}
		
		return $result;
		
	}
	
	public function isAdminConfirmed($listIds) {
		
		$arrayReservations = explode(",",$listIds);
		$result = false;
		$listReservations = "";
		for($i=0;$i<count($arrayReservations);$i++) {
			$reservationQry = mysql_query("SELECT * FROM booking_reservation WHERE MD5(reservation_id) = '".mysql_real_escape_string($arrayReservations[$i])."'");
			$reservationRow = mysql_fetch_array($reservationQry);
			if($reservationRow["admin_confirmed_cancelled"] == 1) {
				$result = true;
			}
		}
		return $result;
		
	}
	
	public function getReservationsDetails($listIds) {
		$arrayReservations = explode(",",$listIds);
		$listReservations = "";
		for($i=0;$i<count($arrayReservations);$i++) {
			if($listReservations=="") {
				$listReservations.="'".$arrayReservations[$i]."'";
			} else {
				$listReservations.=",'".$arrayReservations[$i]."'";
			}
		}
		$arrayReservations = Array();
		$reservationsQry =mysql_query("SELECT r.*,s.*,s.calendar_id as res_calendar, DATE_FORMAT(slot_time_from,'%I:%i %p') as slot_time_from_ampm, DATE_FORMAT(slot_time_to,'%I:%i %p') as slot_time_to_ampm FROM booking_reservation r INNER JOIN booking_slots s ON s.slot_id=r.slot_id WHERE MD5(r.reservation_id) IN (".$listReservations.") ORDER BY s.slot_date, s.slot_time_from");
		while($reservationRow=mysql_fetch_array($reservationsQry)) {
			$arrayReservations[$reservationRow["reservation_id"]] = Array();
			$arrayReservations[$reservationRow["reservation_id"]]["calendar_id"] = $reservationRow["res_calendar"];
			$arrayReservations[$reservationRow["reservation_id"]]["reservation_date"] = $reservationRow["slot_date"];
			$arrayReservations[$reservationRow["reservation_id"]]["reservation_time_from"] = $reservationRow["slot_time_from"];
			$arrayReservations[$reservationRow["reservation_id"]]["reservation_time_to"] = $reservationRow["slot_time_to"];
			$arrayReservations[$reservationRow["reservation_id"]]["reservation_time_from_ampm"] = $reservationRow["slot_time_from_ampm"];
			$arrayReservations[$reservationRow["reservation_id"]]["reservation_time_to_ampm"] = $reservationRow["slot_time_to_ampm"];
			$arrayReservations[$reservationRow["reservation_id"]]["reservation_seats"] = $reservationRow["reservation_seats"];
			$arrayReservations[$reservationRow["reservation_id"]]["reservation_price"] = $reservationRow["slot_price"];
			$arrayReservations[$reservationRow["reservation_id"]]["reservation_surname"] = stripslashes($reservationRow["reservation_surname"]);
			$arrayReservations[$reservationRow["reservation_id"]]["reservation_name"] = stripslashes($reservationRow["reservation_name"]);
			$arrayReservations[$reservationRow["reservation_id"]]["reservation_email"] = $reservationRow["reservation_email"];
			$arrayReservations[$reservationRow["reservation_id"]]["reservation_message"] = $reservationRow["reservation_message"];
			$arrayReservations[$reservationRow["reservation_id"]]["reservation_phone"] = $reservationRow["reservation_phone"];
			$arrayReservations[$reservationRow["reservation_id"]]["reservation_field1"] = $reservationRow["reservation_field1"];
			$arrayReservations[$reservationRow["reservation_id"]]["reservation_field2"] = $reservationRow["reservation_field2"];
			$arrayReservations[$reservationRow["reservation_id"]]["reservation_field3"] = $reservationRow["reservation_field3"];
			$arrayReservations[$reservationRow["reservation_id"]]["reservation_field4"] = $reservationRow["reservation_field4"];
			$arrayReservations[$reservationRow["reservation_id"]]["reservation_confirmed"] = $reservationRow["reservation_confirmed"];
			$arrayReservations[$reservationRow["reservation_id"]]["reservation_cancelled"] = $reservationRow["reservation_cancelled"];
			$arrayReservations[$reservationRow["reservation_id"]]["slot_active"] = $reservationRow["slot_active"];
		}
		return $arrayReservations;
		
	}
	

}

?>
