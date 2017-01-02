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
	
	public function getSlotAv() {
		return slot::$slotQry["slot_av"];
	}
	
	public function getSlotAvMax() {
		return slot::$slotQry["slot_av_max"];
	}
	
	public function addSlot() {
		$slot_av = 1;
		if(isset($_POST["slot_av"])) {
			$slot_av = $_POST["slot_av"];
		}
		$slot_av_max = $slot_av;
		if(isset($_POST["slot_av_max"])) {
			$slot_av_max = $_POST["slot_av_max"];
		}
		/**********analyzing weekdays through date range*********/
		//separate day, month and year
		$arrDateFrom=explode(",",$_POST["first_date"]);
		if($_POST["second_date"]!='') {
			$arrDateTo=explode(",",$_POST["second_date"]);
		} else {
			$arrDateTo=explode(",",$_POST["first_date"]);
		}
		//get an int for the two dates
		$dateFrom=str_replace(",","",$_POST["first_date"]);
		if($_POST["second_date"]!='') {
			$dateTo=str_replace(",","",$_POST["second_date"]);
		} else {
			$dateTo=str_replace(",","",$_POST["first_date"]);
		}
		//loop over weekdays selected
		$resultDate = array();	
		
		for($i=0;$i<count($_POST["slot_weekday"]);$i++) {
			
			$newdateFrom=$dateFrom;
			
			
			$year=$arrDateFrom[0];			
			$day = $arrDateFrom[2];
			$mo = $arrDateFrom[1];
			
			$date = strtotime(date('Y-m-d',mktime(0,0,0,$mo,$day,$year)));
			$weekday = date("N", $date);
			
			$j = 1;
			
			while ($weekday != $_POST["slot_weekday"][$i] && date("Ymd",$date)<$dateTo) {
				
				$date=strtotime(date("Y-m-d", $date) . "+ 1 day");
				
				$weekday = date("N", $date);
				
			}
			
			if(date("N", $date) == $_POST["slot_weekday"][$i]) {
				array_push($resultDate,date('Y-m-d',$date));
			}
			
			while ($newdateFrom <= $dateTo) {
				
				$test =  strtotime(date("Y-m-d", $date) . "+" . $j . " week");
				$j++;
				if(date("Ymd",$test) <= $dateTo) {
					array_push($resultDate,date("Y-m-d", $test));
				}
				
				$newdateFrom = date("Ymd",$test);
			}
			
		}
		
		$resultTime = Array();
		if($_POST["slot_interval"] == 0) {
			/***********custom times***********/
			//loop over custom times
			for($i=0;$i<count($_POST["slot_interval_custom_from_hour"]);$i++) {
				$resultTime[$i] = Array();
				if(isset($_POST["slot_interval_custom_from_ampm"][$i]) && $_POST["slot_interval_custom_from_ampm"][$i] == 'pm') {
					switch($_POST["slot_interval_custom_from_hour"][$i]) {
						case '1':
							$_POST["slot_interval_custom_from_hour"][$i] = '13';
							break;
						case '2':
							$_POST["slot_interval_custom_from_hour"][$i] = '14';
							break;
						case '3':
							$_POST["slot_interval_custom_from_hour"][$i] = '15';
							break;
						case '4':
							$_POST["slot_interval_custom_from_hour"][$i] = '16';
							break;
						case '5':
							$_POST["slot_interval_custom_from_hour"][$i] = '17';
							break;
						case '6':
							$_POST["slot_interval_custom_from_hour"][$i] = '18';
							break;
						case '7':
							$_POST["slot_interval_custom_from_hour"][$i] = '19';
							break;
						case '8':
							$_POST["slot_interval_custom_from_hour"][$i] = '20';
							break;
						case '9':
							$_POST["slot_interval_custom_from_hour"][$i] = '21';
							break;
						case '10':
							$_POST["slot_interval_custom_from_hour"][$i] = '22';
							break;
						case '11':
							$_POST["slot_interval_custom_from_hour"][$i] = '23';
							break;
						
						
						
					}
				} else if(isset($_POST["slot_interval_custom_from_ampm"][$i]) && $_POST["slot_interval_custom_from_ampm"][$i] == 'am') {
					switch($_POST["slot_interval_custom_from_hour"][$i]) {
						case '12':
							$_POST["slot_interval_custom_from_hour"][$i] = '0';
							break;
					}
				}
				if(isset($_POST["slot_interval_custom_to_ampm"][$i]) && $_POST["slot_interval_custom_to_ampm"][$i] == 'pm') {
					switch($_POST["slot_interval_custom_to_hour"][$i]) {
						case '1':
							$_POST["slot_interval_custom_to_hour"][$i] = '13';
							break;
						case '2':
							$_POST["slot_interval_custom_to_hour"][$i] = '14';
							break;
						case '3':
							$_POST["slot_interval_custom_to_hour"][$i] = '15';
							break;
						case '4':
							$_POST["slot_interval_custom_to_hour"][$i] = '16';
							break;
						case '5':
							$_POST["slot_interval_custom_to_hour"][$i] = '17';
							break;
						case '6':
							$_POST["slot_interval_custom_to_hour"][$i] = '18';
							break;
						case '7':
							$_POST["slot_interval_custom_to_hour"][$i] = '19';
							break;
						case '8':
							$_POST["slot_interval_custom_to_hour"][$i] = '20';
							break;
						case '9':
							$_POST["slot_interval_custom_to_hour"][$i] = '21';
							break;
						case '10':
							$_POST["slot_interval_custom_to_hour"][$i] = '22';
							break;
						case '11':
							$_POST["slot_interval_custom_to_hour"][$i] = '23';
							break;
						
						
						
					}
				} else if(isset($_POST["slot_interval_custom_to_ampm"][$i]) && $_POST["slot_interval_custom_to_ampm"][$i] == 'am') {
					switch($_POST["slot_interval_custom_to_hour"][$i]) {
						case '12':
							$_POST["slot_interval_custom_to_hour"][$i] = '0';
							break;
					}
				}
				if(strlen($_POST["slot_interval_custom_from_hour"][$i]) == 1) {
					$_POST["slot_interval_custom_from_hour"][$i] = '0'.$_POST["slot_interval_custom_from_hour"][$i];
				}
				if(strlen($_POST["slot_interval_custom_from_minute"][$i]) == 1) {
					$_POST["slot_interval_custom_from_minute"][$i] = '0'.$_POST["slot_interval_custom_from_minute"][$i];
				}
				if(strlen($_POST["slot_interval_custom_to_hour"][$i]) == 1) {
					$_POST["slot_interval_custom_to_hour"][$i] = '0'.$_POST["slot_interval_custom_to_hour"][$i];
				}
				if(strlen($_POST["slot_interval_custom_to_minute"][$i]) == 1) {
					$_POST["slot_interval_custom_to_minute"][$i] = '0'.$_POST["slot_interval_custom_to_minute"][$i];
				}
				
				$resultTime[$i]["time_from"] = $_POST["slot_interval_custom_from_hour"][$i].":".$_POST["slot_interval_custom_from_minute"][$i].":00";
				$resultTime[$i]["time_to"] = $_POST["slot_interval_custom_to_hour"][$i].":".$_POST["slot_interval_custom_to_minute"][$i].":00";
			}
			
			
			
		} else {
			
			/**********one or more intervals chosen************/
			$i = 0;
			for($r=0;$r<count($_POST["slot_time_from_hour"]);$r++) {
				if($_POST["slot_time_from_hour"][$r]!='' && $_POST["slot_time_from_minute"][$r]!='' && $_POST["slot_time_to_hour"][$r] != '' && $_POST["slot_time_to_minute"][$r] != '') {
					if(isset($_POST["slot_time_from_ampm"][$r]) && $_POST["slot_time_from_ampm"][$r] == 'pm') {
						switch($_POST["slot_time_from_hour"][$r]) {
							case '1':
								$_POST["slot_time_from_hour"][$r] = '13';
								break;
							case '2':
								$_POST["slot_time_from_hour"][$r] = '14';
								break;
							case '3':
								$_POST["slot_time_from_hour"][$r] = '15';
								break;
							case '4':
								$_POST["slot_time_from_hour"][$r] = '16';
								break;
							case '5':
								$_POST["slot_time_from_hour"][$r] = '17';
								break;
							case '6':
								$_POST["slot_time_from_hour"][$r] = '18';
								break;
							case '7':
								$_POST["slot_time_from_hour"][$r] = '19';
								break;
							case '8':
								$_POST["slot_time_from_hour"][$r] = '20';
								break;
							case '9':
								$_POST["slot_time_from_hour"][$r] = '21';
								break;
							case '10':
								$_POST["slot_time_from_hour"][$r] = '22';
								break;
							case '11':
								$_POST["slot_time_from_hour"][$r] = '23';
								break;
							
							
							
						}
					} else if(isset($_POST["slot_time_from_ampm"][$r]) && $_POST["slot_time_from_ampm"][$r] == 'am') {
						switch($_POST["slot_time_from_hour"][$r]) {
							case '12':
								$_POST["slot_time_from_hour"][$r] = '0';
								break;
						}
					}
					if(isset($_POST["slot_time_to_ampm"][$r]) && $_POST["slot_time_to_ampm"][$r] == 'pm') {
						switch($_POST["slot_time_to_hour"][$r]) {
							case '1':
								$_POST["slot_time_to_hour"][$r] = '13';
								break;
							case '2':
								$_POST["slot_time_to_hour"][$r] = '14';
								break;
							case '3':
								$_POST["slot_time_to_hour"][$r] = '15';
								break;
							case '4':
								$_POST["slot_time_to_hour"][$r] = '16';
								break;
							case '5':
								$_POST["slot_time_to_hour"][$r] = '17';
								break;
							case '6':
								$_POST["slot_time_to_hour"][$r] = '18';
								break;
							case '7':
								$_POST["slot_time_to_hour"][$r] = '19';
								break;
							case '8':
								$_POST["slot_time_to_hour"][$r] = '20';
								break;
							case '9':
								$_POST["slot_time_to_hour"][$r] = '21';
								break;
							case '10':
								$_POST["slot_time_to_hour"][$r] = '22';
								break;
							case '11':
								$_POST["slot_time_to_hour"][$r] = '23';
								break;
							
							
							
						}
					} else if(isset($_POST["slot_time_to_ampm"][$r]) && $_POST["slot_time_to_ampm"][$r] == 'am') {
						switch($_POST["slot_time_to_hour"][$r]) {
							case '12':
								$_POST["slot_time_to_hour"][$r] = '0';
								break;
						}
					}
					//separate hour and minute
					if(strlen($_POST["slot_time_from_hour"][$r]) == 1) {
						$_POST["slot_time_from_hour"][$r] = '0'.$_POST["slot_time_from_hour"][$r];
					}
					if(strlen($_POST["slot_time_from_minute"][$r]) == 1) {
						$_POST["slot_time_from_minute"][$r] = '0'.$_POST["slot_time_from_minute"][$r];
					}
					if(strlen($_POST["slot_time_to_hour"][$r]) == 1) {
						$_POST["slot_time_to_hour"][$r] = '0'.$_POST["slot_time_to_hour"][$r];
					}
					if(strlen($_POST["slot_time_to_minute"][$r]) == 1) {
						$_POST["slot_time_to_minute"][$r] = '0'.$_POST["slot_time_to_minute"][$r];
					}
					$arrTimeFrom = array($_POST["slot_time_from_hour"][$r],$_POST["slot_time_from_minute"][$r]);
					$arrTimeTo = array($_POST["slot_time_to_hour"][$r],$_POST["slot_time_to_minute"][$r]);
					//get an int for the two times
					
					$timeFrom=$_POST["slot_time_from_hour"][$r].$_POST["slot_time_from_minute"][$r];
					$timeTo=$_POST["slot_time_to_hour"][$r].$_POST["slot_time_to_minute"][$r];
					
					$newtimeFrom=$timeFrom;			
					$hour=$arrTimeFrom[0];			
					$minute = $arrTimeFrom[1];
					if($_POST["slot_pause"] == 0 || $_POST["slot_pause"] == '') {
						$j = $_POST["slot_interval_minutes"];
						$totinterval = $_POST["slot_interval_minutes"];
					} else {
						$j = $_POST["slot_interval_minutes"]+$_POST["slot_pause"];
						$totinterval = $_POST["slot_interval_minutes"]+$_POST["slot_pause"]; 
					}
					
					
					$resultTime[$i] = Array();
					$resultTime[$i]["time_from"] = date("H:i:s", mktime($hour,$minute,0));
					$resultTime[$i]["time_to"] = date("H:i:s",strtotime(date("H:i:s", mktime($hour,$minute,0))."+" . $_POST["slot_interval_minutes"] . " minutes"));
					$i++;
					
					while ($newtimeFrom < $timeTo) {
						if(date('Hi',strtotime(date("H:i:s", mktime($hour,$minute,0)) . "+" . $j . " minutes")) <= "2359" && date('Hi',strtotime(date("H:i:s", mktime($hour,$minute,0)) . "+" . $j . " minutes")) > $timeFrom && date('Hi',strtotime(date("H:i:s", mktime($hour,$minute,0)) . "+" . $j . " minutes")) <= $timeTo) {
							
						} else {
							break;
						}
						$test =  strtotime(date("H:i:s", mktime($hour,$minute,0)) . "+" . $j . " minutes");
						if($_POST["slot_pause"] == 0 || $_POST["slot_pause"] == '') {
							$j = $j+$_POST["slot_interval_minutes"];
						} else {
							$j = $j+$_POST["slot_interval_minutes"]+$_POST["slot_pause"];
						}
						
						if(date("Hi",$test) < $timeTo) {
							$resultTime[$i] = Array();
							$resultTime[$i]["time_from"] = date("H:i:s", $test);
							$resultTime[$i]["time_to"] = date("H:i:s",strtotime(date("H:i:s", $test)."+" . $_POST["slot_interval_minutes"] . " minutes"));
							
						}
						
						$newtimeFrom = date("Hi",$test);
						if($newtimeFrom < $timeTo) {
							$i++;
						}
					}
				
				}
			}
			
			
		}
		
		
		//now that I have date and times I can insert the slots
		$count = 0;
		$slot_special_text="";
		$slot_special_mode=1;
		$holidays = 0;
		for($i=0; $i<count($resultDate);$i++) {
			//check holidays, I can't insert slots during holidays
			$holidaysQry = mysql_query("SELECT * FROM booking_holidays WHERE holiday_date = '".mysql_real_escape_string($resultDate[$i])."' AND calendar_id='".mysql_real_escape_string($_POST["calendar_id"])."'");
			if(mysql_num_rows($holidaysQry)==0) {
				for($j=0;$j<count($resultTime);$j++) {
					//before insert have to check if there's a slot with same values
					$checkQry=mysql_query("SELECT * FROM booking_slots WHERE slot_active = 1 AND slot_date = '".mysql_real_escape_string($resultDate[$i])."' AND slot_time_from = '".mysql_real_escape_string($resultTime[$j]["time_from"])."' AND calendar_id='".mysql_real_escape_string($_POST["calendar_id"])."'");
					if(mysql_num_rows($checkQry)==0) {
						
						$slot_special_text = $_POST["special_text"];
						$slot_special_mode = $_POST["special_mode"];
						
						$price = 0;
						if(isset($_POST["slot_price"]) && str_replace(",",".",$_POST["slot_price"])>0) {
							$price = str_replace(",",".",$_POST["slot_price"]);
						}
						mysql_query("INSERT INTO booking_slots(slot_special_text,slot_special_mode,slot_date,slot_time_from,slot_time_to,slot_price,slot_av,slot_av_max,slot_active,calendar_id) VALUES('".mysql_real_escape_string($slot_special_text)."',".$slot_special_mode.",'".$resultDate[$i]."','".$resultTime[$j]["time_from"]."','".$resultTime[$j]["time_to"]."','".$price."',".$slot_av.",".$slot_av_max.",1,".$_POST["calendar_id"].")");
						
						$count++;
						
					}
				}
			} else {
				$holidays++;
			}
			if($count>2000) {
				break;
			}
		}
		if($holidays>0 && $count == 0) {
			$count=-1;
		}
		
		return $count;
	}
	
	public function delSlots($listIds) {
		
		//have to check if there are reservation for one of the ids, if yes, just disable slots, not delete
		$arrIds = explode(",",$listIds);
		for($i=0;$i<count($arrIds);$i++) {
			$reservationQry = mysql_query("SELECT * FROM booking_reservation WHERE slot_id='".mysql_real_escape_string($arrIds[$i])."'");
			if(mysql_num_rows($reservationQry)>0) {
				mysql_query("UPDATE  booking_slots SET slot_active = 0 WHERE slot_id=".$arrIds[$i]);
			} else {
				mysql_query("DELETE FROM booking_slots WHERE slot_id=".$arrIds[$i]);
			}
		}
		
		
	}
	
	public function deleteSlots() {
		if($_POST["second_date_delete"] == '') {
			$_POST["second_date_delete"] = $_POST["first_date_delete"];
		}
		if($_POST["slot_week_day_delete"] == 0) {
			$count=mysql_num_rows(mysql_query("SELECT * FROM booking_slots WHERE slot_time_from = '".mysql_real_escape_string($_POST["slot_hour_delete"])."' AND slot_date >= '".mysql_real_escape_string(str_replace(",","-",$_POST["first_date_delete"]))."' AND slot_date <= '".mysql_real_escape_string(str_replace(",","-",$_POST["second_date_delete"]))."' AND calendar_id='".mysql_real_escape_string($_POST["calendar_id"])."'"));
			/*********all days, no problem*******/
			mysql_query("DELETE FROM booking_slots WHERE slot_time_from = '".$_POST["slot_hour_delete"]."' AND slot_date >= '".str_replace(",","-",$_POST["first_date_delete"])."' AND slot_date <= '".str_replace(",","-",$_POST["second_date_delete"])."' AND calendar_id=".$_POST["calendar_id"]);
			
		} else {
			$count = 0;
			/********getting dates according to weekeday selected*******/
			$datesList = "'0000-00-00'";
			
			//separate day, month and year
			$arrDateFrom=explode(",",$_POST["first_date_delete"]);
			$arrDateTo=explode(",",$_POST["second_date_delete"]);
			//get an int for the two dates
			$dateFrom=str_replace(",","",$_POST["first_date_delete"]);
			$dateTo=str_replace(",","",$_POST["second_date_delete"]);
			
			$newdateFrom=$dateFrom;
				
				
			$year=$arrDateFrom[0];			
			$day = $arrDateFrom[2];
			$mo = $arrDateFrom[1];
			
			$date = strtotime(date('Y-m-d',mktime(0,0,0,$mo,$day,$year)));
			$weekday = date("N", $date);
			
			$j = 1;
			
			while ($weekday != $_POST["slot_week_day_delete"] && date("Ymd",$date)<$dateTo) {
				
				$date=strtotime(date("Y-m-d", $date) . "+ 1 day");
				
				$weekday = date("N", $date);
				
			}
			
			if(date("N", $date) == $_POST["slot_week_day_delete"]) {
				$datesList.=",'".date('Y-m-d',$date)."'";
				
			}
			
			while ($newdateFrom <= $dateTo) {
				
				$test =  strtotime(date("Y-m-d", $date) . "+" . $j . " week");
				
				$j++;
				if(date("Ymd",$test) <= $dateTo) {
					$datesList.=",'".date('Y-m-d',$test)."'";
				}
				
				$newdateFrom = date("Ymd",$test);
			}
			
			$count+=mysql_num_rows(mysql_query("SELECT * FROM booking_slots WHERE slot_time_from = '".mysql_real_escape_string($_POST["slot_hour_delete"])."' AND slot_date IN (".$datesList.") AND calendar_id='".mysql_real_escape_string($_POST["calendar_id"])."'"));
			mysql_query("DELETE FROM booking_slots WHERE slot_time_from = '".$_POST["slot_hour_delete"]."' AND slot_date IN (".$datesList.") AND calendar_id=".$_POST["calendar_id"]);
			
		}
		return $count;
	}
	
	public function disableSlots() {
		if($_POST["second_date_delete"] == '') {
			$_POST["second_date_delete"] = $_POST["first_date_delete"];
		}
		if($_POST["slot_week_day_delete"] == 0) {
			$count=mysql_num_rows(mysql_query("SELECT * FROM booking_slots WHERE slot_time_from = '".mysql_real_escape_string($_POST["slot_hour_delete"])."' AND slot_date >= '".mysql_real_escape_string(str_replace(",","-",$_POST["first_date_delete"]))."' AND slot_date <= '".mysql_real_escape_string(str_replace(",","-",$_POST["second_date_delete"]))."' AND calendar_id='".mysql_real_escape_string($_POST["calendar_id"])."'"));
			/*********all days, no problem*******/
			mysql_query("UPDATE booking_slots SET slot_active = 0 WHERE slot_time_from = '".$_POST["slot_hour_delete"]."' AND slot_date >= '".str_replace(",","-",$_POST["first_date_delete"])."' AND slot_date <= '".str_replace(",","-",$_POST["second_date_delete"])."' AND calendar_id=".$_POST["calendar_id"]);
		} else {
			$count = 0;
			/********getting dates according to weekeday selected*******/
			$datesList = "'0000-00-00'";
			
			//separate day, month and year
			$arrDateFrom=explode(",",$_POST["first_date_delete"]);
			$arrDateTo=explode(",",$_POST["second_date_delete"]);
			//get an int for the two dates
			$dateFrom=str_replace(",","",$_POST["first_date_delete"]);
			$dateTo=str_replace(",","",$_POST["second_date_delete"]);
			
			$newdateFrom=$dateFrom;
				
				
			$year=$arrDateFrom[0];			
			$day = $arrDateFrom[2];
			$mo = $arrDateFrom[1];
			
			$date = strtotime(date('Y-m-d',mktime(0,0,0,$mo,$day,$year)));
			$weekday = date("N", $date);
			
			$j = 1;
			
			while ($weekday != $_POST["slot_week_day_delete"] && date("Ymd",$date)<$dateTo) {
				
				$date=strtotime(date("Y-m-d", $date) . "+ 1 day");
				
				$weekday = date("N", $date);
				
			}
			
			if(date("N", $date) == $_POST["slot_week_day_delete"]) {
				$datesList.=",'".date('Y-m-d',$date)."'";
			}
			
			while ($newdateFrom <= $dateTo) {
				
				$test =  strtotime(date("Y-m-d", $date) . "+" . $j . " week");
				$j++;
				if(date("Ymd",$test) <= $dateTo) {
					$datesList.=",'".date('Y-m-d',$test)."'";
				}
				
				$newdateFrom = date("Ymd",$test);
			}
			$count+=mysql_num_rows(mysql_query("SELECT * FROM booking_slots WHERE slot_time_from = '".mysql_real_escape_string($_POST["slot_hour_delete"])."' AND slot_date IN (".$datesList.") AND calendar_id='".mysql_real_escape_string($_POST["calendar_id"])."'"));
			mysql_query("UPDATE booking_slots SET slot_active = 0 WHERE slot_time_from = '".$_POST["slot_hour_delete"]."' AND slot_date IN (".$datesList.") AND calendar_id=".$_POST["calendar_id"]);
		}
		return $count;
	}
	
	public function checkSlotsReservation() {
		if($_POST["slot_week_day_delete"] == 0) {
			/*********all days, no problem*******/
			$result=mysql_num_rows(mysql_query("SELECT * FROM booking_slots s INNER JOIN booking_reservation r ON s.slot_id = r.slot_id WHERE s.slot_time_from = '".mysql_real_escape_string($_POST["slot_hour_delete"])."' AND s.slot_date >= '".mysql_real_escape_string(str_replace(",","-",$_POST["first_date_delete"]))."' AND s.slot_date <= '".mysql_real_escape_string(str_replace(",","-",$_POST["second_date_delete"]))."' AND s.calendar_id='".mysql_real_escape_string($_POST["calendar_id"])."' AND r.calendar_id='".mysql_real_escape_string($_POST["calendar_id"])."'"));
		} else {
			/********getting dates according to weekeday selected*******/
			$datesList = "'0000-00-00'";
			
			//separate day, month and year
			$arrDateFrom=explode(",",$_POST["first_date_delete"]);
			$arrDateTo=explode(",",$_POST["second_date_delete"]);
			//get an int for the two dates
			$dateFrom=str_replace(",","",$_POST["first_date_delete"]);
			$dateTo=str_replace(",","",$_POST["second_date_delete"]);
			
			$newdateFrom=$dateFrom;
				
				
			$year=$arrDateFrom[0];			
			$day = $arrDateFrom[2];
			$mo = $arrDateFrom[1];
			
			$date = strtotime(date('Y-m-d',mktime(0,0,0,$mo,$day,$year)));
			$weekday = date("N", $date);
			
			$j = 1;
			
			while ($weekday != $_POST["slot_week_day_delete"] && date("Ymd",$date)<$dateTo) {
				
				$date=strtotime(date("Y-m-d", $date) . "+ 1 day");
				
				$weekday = date("N", $date);
				
			}
			
			if(date("N", $date) == $_POST["slot_week_day_delete"]) {
				$datesList.=",'".date('Y-m-d',$date)."'";
			}
			
			while ($newdateFrom <= $dateTo) {
				
				$test =  strtotime(date("Y-m-d", $date) . "+" . $j . " week");
				$j++;
				if(date("Ymd",$test) <= $dateTo) {
					$datesList.=",'".date('Y-m-d',$test)."'";
				}
				
				$newdateFrom = date("Ymd",$test);
			}
			
			$result=mysql_num_rows(mysql_query("SELECT * FROM booking_slots s INNER JOIN booking_reservation r ON s.slot_id = r.slot_id WHERE s.slot_time_from = '".mysql_real_escape_string($_POST["slot_hour_delete"])."' AND s.slot_date IN (".$datesList.") AND s.calendar_id='".mysql_real_escape_string($_POST["calendar_id"])."' AND r.calendar_id='".mysql_real_escape_string($_POST["calendar_id"])."'"));
		}
		return $result;
	}
	
	public function modifySlots() {
		$slot_av="slot_av";
		if(isset($_POST["slot_av_edit"]) && $_POST["slot_av_edit"] != '') {
			$slot_av = $_POST["slot_av_edit"]; 
		}	
		$slot_av_max="slot_av_max";
		if(isset($_POST["slot_av_max_edit"]) && $_POST["slot_av_max_edit"] != '') {
			$slot_av_max = $_POST["slot_av_max_edit"]; 
		}
		$slot_text="slot_special_text";
		if(isset($_POST["slot_special_text_edit"]) && $_POST["slot_special_text_edit"] != '') {
			$slot_text = "'".$_POST["slot_special_text_edit"]."'"; 
		}
		
		$timeFrom = "slot_time_from";
		$timeTo = "slot_time_to";
		if($_POST["time_from_edit_hour"][0] != '' && $_POST["time_from_edit_minute"][0] != '' && $_POST["time_to_edit_hour"][0]!='' && $_POST["time_to_edit_minute"][0]!='') {
			if(isset($_POST["time_from_edit_ampm"][0]) && $_POST["time_from_edit_ampm"][0] == 'pm') {
				switch($_POST["time_from_edit_hour"][0]) {
					case '1':
						$_POST["time_from_edit_hour"][0] = '13';
						break;
					case '2':
						$_POST["time_from_edit_hour"][0] = '14';
						break;
					case '3':
						$_POST["time_from_edit_hour"][0] = '15';
						break;
					case '4':
						$_POST["time_from_edit_hour"][0] = '16';
						break;
					case '5':
						$_POST["time_from_edit_hour"][0] = '17';
						break;
					case '6':
						$_POST["time_from_edit_hour"][0] = '18';
						break;
					case '7':
						$_POST["time_from_edit_hour"][0] = '19';
						break;
					case '8':
						$_POST["time_from_edit_hour"][0] = '20';
						break;
					case '9':
						$_POST["time_from_edit_hour"][0] = '21';
						break;
					case '10':
						$_POST["time_from_edit_hour"][0] = '22';
						break;
					case '11':
						$_POST["time_from_edit_hour"][0] = '23';
						break;
					
					
					
				}
			} else if(isset($_POST["time_from_edit_ampm"][0]) && $_POST["time_from_edit_ampm"][0] == 'am') {
				switch($_POST["time_from_edit_hour"][0]) {
					case '12':
						$_POST["time_from_edit_hour"][0] = '0';
						break;
				}
			}
			if(strlen($_POST["time_from_edit_hour"][0])==1) {
				$_POST["time_from_edit_hour"][0] = '0'.$_POST["time_from_edit_hour"][0];
			}
			if(strlen($_POST["time_from_edit_minute"][0])==1) {
				$_POST["time_from_edit_minute"][0] = '0'.$_POST["time_from_edit_minute"][0];
			}
			$timeFrom = "'".$_POST["time_from_edit_hour"][0].":".$_POST["time_from_edit_minute"][0].":00'";
			if(isset($_POST["time_to_edit_ampm"][0]) && $_POST["time_to_edit_ampm"][0] == 'pm') {
				switch($_POST["time_to_edit_hour"][0]) {
					case '1':
						$_POST["time_to_edit_hour"][0] = '13';
						break;
					case '2':
						$_POST["time_to_edit_hour"][0] = '14';
						break;
					case '3':
						$_POST["time_to_edit_hour"][0] = '15';
						break;
					case '4':
						$_POST["time_to_edit_hour"][0] = '16';
						break;
					case '5':
						$_POST["time_to_edit_hour"][0] = '17';
						break;
					case '6':
						$_POST["time_to_edit_hour"][0] = '18';
						break;
					case '7':
						$_POST["time_to_edit_hour"][0] = '19';
						break;
					case '8':
						$_POST["time_to_edit_hour"][0] = '20';
						break;
					case '9':
						$_POST["time_to_edit_hour"][0] = '21';
						break;
					case '10':
						$_POST["time_to_edit_hour"][0] = '22';
						break;
					case '11':
						$_POST["time_to_edit_hour"][0] = '23';
						break;
					
					
					
				}
			} else if(isset($_POST["time_to_edit_ampm"][0]) && $_POST["time_to_edit_ampm"][0] == 'am') {
				switch($_POST["time_to_edit_hour"][0]) {
					case '12':
						$_POST["time_to_edit_hour"][0] = '0';
						break;
				}
			}
			if(strlen($_POST["time_to_edit_hour"][0])==1) {
				$_POST["time_to_edit_hour"][0] = '0'.$_POST["time_to_edit_hour"][0];
			}
			if(strlen($_POST["time_to_edit_minute"][0])==1) {
				$_POST["time_to_edit_minute"][0] = '0'.$_POST["time_to_edit_minute"][0];
			}
			$timeTo = "'".$_POST["time_to_edit_hour"][0].":".$_POST["time_to_edit_minute"][0].":00'";
		}
		
		if($_POST["slot_week_day_edit"] == 0) {
			$count=mysql_num_rows(mysql_query("SELECT * FROM booking_slots WHERE slot_time_from = '".mysql_real_escape_string($_POST["slot_hour_edit"])."' AND slot_date >= '".mysql_real_escape_string(str_replace(",","-",$_POST["first_date_edit"]))."' AND slot_date <= '".mysql_real_escape_string(str_replace(",","-",$_POST["second_date_edit"]))."' AND calendar_id='".mysql_real_escape_string($_POST["calendar_id"])."'"));
			/*********all days, no problem*******/
			if(isset($_POST["slot_price_edit"]) && $_POST["slot_price_edit"]!='') {
				mysql_query("UPDATE booking_slots SET slot_time_from = ".$timeFrom.",slot_time_to = ".$timeTo.", slot_price='".str_replace(",",".",$_POST["slot_price_edit"])."', slot_av = ".$slot_av.", slot_av_max = ".$slot_av_max.", slot_special_text = ".$slot_text." WHERE slot_time_from = '".$_POST["slot_hour_edit"]."' AND slot_date >= '".str_replace(",","-",$_POST["first_date_edit"])."' AND slot_date <= '".str_replace(",","-",$_POST["second_date_edit"])."' AND calendar_id=".$_POST["calendar_id"]);
			} else {
				mysql_query("UPDATE booking_slots SET slot_time_from = ".$timeFrom.",slot_time_to = ".$timeTo.", slot_av = ".$slot_av.", slot_av_max = ".$slot_av_max.", slot_special_text = ".$slot_text." WHERE slot_time_from = '".$_POST["slot_hour_edit"]."' AND slot_date >= '".str_replace(",","-",$_POST["first_date_edit"])."' AND slot_date <= '".str_replace(",","-",$_POST["second_date_edit"])."' AND calendar_id=".$_POST["calendar_id"]);
			}
		} else {
			$count=0;
			/********getting dates according to weekeday selected*******/
			$datesList = "'0000-00-00'";
			
			//separate day, month and year
			$arrDateFrom=explode(",",$_POST["first_date_edit"]);
			$arrDateTo=explode(",",$_POST["second_date_edit"]);
			//get an int for the two dates
			$dateFrom=str_replace(",","",$_POST["first_date_edit"]);
			$dateTo=str_replace(",","",$_POST["second_date_edit"]);
			
			$newdateFrom=$dateFrom;
				
				
			$year=$arrDateFrom[0];			
			$day = $arrDateFrom[2];
			$mo = $arrDateFrom[1];
			
			$date = strtotime(date('Y-m-d',mktime(0,0,0,$mo,$day,$year)));
			$weekday = date("N", $date);
			
			$j = 1;
			
			while ($weekday != $_POST["slot_week_day_edit"] && date("Ymd",$date)<$dateTo) {
				
				$date=strtotime(date("Y-m-d", $date) . "+ 1 day");
				
				$weekday = date("N", $date);
				
			}
			
			if(date("N", $date) == $_POST["slot_week_day_edit"]) {
				$datesList.=",'".date('Y-m-d',$date)."'";
			}
			
			while ($newdateFrom <= $dateTo) {
				
				$test =  strtotime(date("Y-m-d", $date) . "+" . $j . " week");
				$j++;
				if(date("Ymd",$test) <= $dateTo) {
					$datesList.=",'".date('Y-m-d',$test)."'";
				}
				
				$newdateFrom = date("Ymd",$test);
			}
			$count+=mysql_num_rows(mysql_query("SELECT * FROM booking_slots WHERE slot_time_from = '".mysql_real_escape_string($_POST["slot_hour_edit"])."' AND slot_date IN (".$datesList.") AND calendar_id='".mysql_real_escape_string($_POST["calendar_id"])."'"));
			if(isset($_POST["slot_price_edit"]) && $_POST["slot_price_edit"]!='') {
				mysql_query("UPDATE booking_slots SET slot_time_from = ".$timeFrom.",slot_time_to = ".$timeTo.", slot_price='".str_replace(",",".",$_POST["slot_price_edit"])."', slot_av = ".$slot_av.", slot_av_max = ".$slot_av_max.", slot_special_text = ".$slot_text."  WHERE slot_time_from = '".$_POST["slot_hour_edit"]."' AND slot_date IN (".$datesList.") AND calendar_id=".$_POST["calendar_id"]);
			} else {
				mysql_query("UPDATE booking_slots SET slot_time_from = ".$timeFrom.",slot_time_to = ".$timeTo.", slot_av = ".$slot_av.", slot_av_max = ".$slot_av_max.", slot_special_text = ".$slot_text."  WHERE slot_time_from = '".$_POST["slot_hour_edit"]."' AND slot_date IN (".$datesList.") AND calendar_id=".$_POST["calendar_id"]);
			}
		}
		return $count;
	}
	
	public function checkEditSlotsReservation() {
		if(isset($_POST["time_from_edit_ampm"]) && $_POST["time_from_edit_ampm"] == 'pm') {
			switch($_POST["time_from_edit_hour"]) {
				case '1':
					$_POST["time_from_edit_hour"] = '13';
					break;
				case '2':
					$_POST["time_from_edit_hour"] = '14';
					break;
				case '3':
					$_POST["time_from_edit_hour"] = '15';
					break;
				case '4':
					$_POST["time_from_edit_hour"] = '16';
					break;
				case '5':
					$_POST["time_from_edit_hour"] = '17';
					break;
				case '6':
					$_POST["time_from_edit_hour"] = '18';
					break;
				case '7':
					$_POST["time_from_edit_hour"] = '19';
					break;
				case '8':
					$_POST["time_from_edit_hour"] = '20';
					break;
				case '9':
					$_POST["time_from_edit_hour"] = '21';
					break;
				case '10':
					$_POST["time_from_edit_hour"] = '22';
					break;
				case '11':
					$_POST["time_from_edit_hour"] = '23';
					break;
				
				
				
			}
		} else if(isset($_POST["time_from_edit_ampm"]) && $_POST["time_from_edit_ampm"] == 'am') {
			switch($_POST["time_from_edit_hour"]) {
				case '12':
					$_POST["time_from_edit_hour"] = '0';
					break;
			}
		}
		if(strlen($_POST["time_from_edit_hour"])==1) {
			$_POST["time_from_edit_hour"] = '0'.$_POST["time_from_edit_hour"];
		}
		if(strlen($_POST["time_from_edit_minute"])==1) {
			$_POST["time_from_edit_minute"] = '0'.$_POST["time_from_edit_minute"];
		}
		$timeFrom = $_POST["time_from_edit_hour"].":".$_POST["time_from_edit_minute"];
		if(isset($_POST["time_to_edit_ampm"]) && $_POST["time_to_edit_ampm"] == 'pm') {
			switch($_POST["time_to_edit_hour"]) {
				case '1':
					$_POST["time_to_edit_hour"] = '13';
					break;
				case '2':
					$_POST["time_to_edit_hour"] = '14';
					break;
				case '3':
					$_POST["time_to_edit_hour"] = '15';
					break;
				case '4':
					$_POST["time_to_edit_hour"] = '16';
					break;
				case '5':
					$_POST["time_to_edit_hour"] = '17';
					break;
				case '6':
					$_POST["time_to_edit_hour"] = '18';
					break;
				case '7':
					$_POST["time_to_edit_hour"] = '19';
					break;
				case '8':
					$_POST["time_to_edit_hour"] = '20';
					break;
				case '9':
					$_POST["time_to_edit_hour"] = '21';
					break;
				case '10':
					$_POST["time_to_edit_hour"] = '22';
					break;
				case '11':
					$_POST["time_to_edit_hour"] = '23';
					break;
				
				
				
			}
		} else if(isset($_POST["time_to_edit_ampm"]) && $_POST["time_to_edit_ampm"] == 'am') {
			switch($_POST["time_to_edit_hour"]) {
				case '12':
					$_POST["time_to_edit_hour"] = '0';
					break;
			}
		}
		if(strlen($_POST["time_to_edit_hour"])==1) {
			$_POST["time_to_edit_hour"] = '0'.$_POST["time_to_edit_hour"];
		}
		if(strlen($_POST["time_to_edit_minute"])==1) {
			$_POST["time_to_edit_minute"] = '0'.$_POST["time_to_edit_minute"];
		}
		$timeTo = $_POST["time_to_edit_hour"].":".$_POST["time_to_edit_minute"];
		
		if($_POST["slot_week_day_edit"] == 0) {
			/*********all days, no problem*******/
			$result=mysql_num_rows(mysql_query("SELECT * FROM booking_slots s INNER JOIN booking_reservation r ON s.slot_id = r.slot_id WHERE s.slot_time_from = '".mysql_real_escape_string($_POST["slot_hour_edit"])."' AND s.slot_date >= '".mysql_real_escape_string(str_replace(",","-",$_POST["first_date_edit"]))."' AND s.slot_date <= '".mysql_real_escape_string(str_replace(",","-",$_POST["second_date_edit"]))."' AND s.calendar_id='".mysql_real_escape_string($_POST["calendar_id"])."' AND r.calendar_id='".mysql_real_escape_string($_POST["calendar_id"])."'"));
			//check if there are equal slots
			if($timeFrom.":00" == $_POST["slot_hour_edit"]) {
				$same =0;
			} else {
				$same =mysql_num_rows(mysql_query("SELECT * FROM booking_slots WHERE slot_time_from = '".$timeFrom.":00' AND slot_date >= '".mysql_real_escape_string(str_replace(",","-",$_POST["first_date_edit"]))."' AND slot_date <= '".mysql_real_escape_string(str_replace(",","-",$_POST["second_date_edit"]))."'  AND calendar_id='".mysql_real_escape_string($_POST["calendar_id"])."'"));
			}
			
		} else {
			/********getting dates according to weekeday selected*******/
			$datesList = "'0000-00-00'";
			
			//separate day, month and year
			$arrDateFrom=explode(",",$_POST["first_date_edit"]);
			$arrDateTo=explode(",",$_POST["second_date_edit"]);
			//get an int for the two dates
			$dateFrom=str_replace(",","",$_POST["first_date_edit"]);
			$dateTo=str_replace(",","",$_POST["second_date_edit"]);
			
			$newdateFrom=$dateFrom;
				
				
			$year=$arrDateFrom[0];			
			$day = $arrDateFrom[2];
			$mo = $arrDateFrom[1];
			
			$date = strtotime(date('Y-m-d',mktime(0,0,0,$mo,$day,$year)));
			$weekday = date("N", $date);
			
			$j = 1;
			
			while ($weekday != $_POST["slot_week_day_edit"] && date("Ymd",$date)<$dateTo) {
				
				$date=strtotime(date("Y-m-d", $date) . "+ 1 day");
				
				$weekday = date("N", $date);
				
			}
			
			if(date("N", $date) == $_POST["slot_week_day_edit"]) {
				$datesList.=",'".date('Y-m-d',$date)."'";
			}
			
			while ($newdateFrom <= $dateTo) {
				
				$test =  strtotime(date("Y-m-d", $date) . "+" . $j . " week");
				$j++;
				if(date("Ymd",$test) <= $dateTo) {
					$datesList.=",'".date('Y-m-d',$test)."'";
				}
				
				$newdateFrom = date("Ymd",$test);
			}
			
			$result=mysql_num_rows(mysql_query("SELECT * FROM booking_slots s INNER JOIN booking_reservation r ON s.slot_id = r.slot_id WHERE s.slot_time_from = '".mysql_real_escape_string($_POST["slot_hour_edit"])."' AND s.slot_date IN (".$datesList.") AND s.calendar_id='".mysql_real_escape_string($_POST["calendar_id"])."' AND r.calendar_id='".mysql_real_escape_string($_POST["calendar_id"])."'"));
			
			if($timeFrom.":00" == $_POST["slot_hour_edit"]) {
				$same =0;
			} else {
				//check if there are equal slots
				$same =mysql_num_rows(mysql_query("SELECT * FROM booking_slots WHERE slot_time_from = '".$timeFrom.":00' AND slot_date IN (".$datesList.")  AND calendar_id='".mysql_real_escape_string($_POST["calendar_id"])."'"));
			}
			
		}
		if($same > 0) {
			return -1;
		} else {
			return $result;
		}
	}

}
?>
