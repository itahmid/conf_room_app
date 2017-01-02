<?php

class slot {
	private static $slot_id;
	private static $slotQry;
	
	public function setSlot($id) {
		$slotQry = mysql_query("SELECT * FROM booking_slots WHERE slot_id = '".mysql_real_escape_string($id)."'");
		
		$slotRow = mysql_fetch_array($slotQry);
		slot::$slotQry = $slotRow;
		slot::$slot_id=$slotRow["slot_id"];
	}
	
	public function getSlotId() {
		return slot::$slot_id;
	}
	
	public function getSlotCalendarId() {
		return slot::$slotQry["calendar_id"];
	}
	
	public function getSlotDate() {
		return slot::$slotQry["slot_date"];
	}
	
	public function getSlotTimeFrom() {
		return slot::$slotQry["slot_time_from"];
	}
	
	public function getSlotTimeTo() {
		return slot::$slotQry["slot_time_to"];
	}
	
	public function getSlotTimeFromAMPM() {
		return date('h:i a',strtotime(slot::$slotQry["slot_time_from"]));
	}
	
	public function getSlotTimeToAMPM() {
		return date('h:i a',strtotime(slot::$slotQry["slot_time_to"]));
	}
	
	public function getSlotSpecialText() {
		return stripslashes(slot::$slotQry["slot_special_text"]);
	}
	
	public function getSlotSpecialMode() {
		return slot::$slotQry["slot_special_mode"];
	}
	
	public function getSlotPrice() {
		return slot::$slotQry["slot_price"];
	}
	
	public function checkFutureSlots($year,$month,$day,$calendar_id) {
		$slotsQry = mysql_query("SELECT * FROM booking_slots WHERE slot_date = '".$year."-".$month."-".$day."' AND slot_active = 1 AND calendar_id='".mysql_real_escape_string($calendar_id)."'");
		$totRighe = 0;
		if(mysql_num_rows($slotsQry)>0) {
			while($slotRow = mysql_fetch_array($slotsQry)) {
				//check reservations
				$reservationQry=mysql_query("SELECT * FROM booking_reservation WHERE slot_id='".mysql_real_escape_string($slotRow["slot_id"])."' AND reservation_cancelled = 0");
				if(mysql_num_rows($reservationQry)>0) {
					
				} else {
					$totRighe++;
				}
			}
			if($totRighe>0) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
		
		
	}
	
	public function checkPastSlots($year,$month,$day,$calendar_id) {
		$slotsQry = mysql_query("SELECT * FROM booking_slots WHERE slot_date = '".$year."-".$month."-".$day."' AND slot_active = 1 AND calendar_id='".mysql_real_escape_string($calendar_id)."'");
		$totRighe = 0;
		if(mysql_num_rows($slotsQry)>0) {
			while($slotRow = mysql_fetch_array($slotsQry)) {
				//check reservations
				$reservationQry=mysql_query("SELECT * FROM booking_reservation WHERE slot_id='".mysql_real_escape_string($slotRow["slot_id"])."' AND reservation_cancelled = 0");
				if(($slotRow["slot_date"] == date('Y-m-d') && str_replace(":","",$slotRow["slot_time_from"])<date('His')) || mysql_num_rows($reservationQry)>0) {
					
				} else {
					$totRighe++;
				}
			}
			if($totRighe>0) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
		
	}

}

?>
