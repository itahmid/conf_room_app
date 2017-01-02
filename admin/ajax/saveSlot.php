<?php
include '../common.php';
if(isset($_SESSION["admin_id"]) && $_SESSION["admin_id"] > 0) {
$arrTimeFrom = explode(":",$_REQUEST["time_from"]);
	
if($arrTimeFrom[2]!='undefined') {
	if($arrTimeFrom[2] == 'pm') {
		//am pm Have to put it in 24 hour
		switch($arrTimeFrom[0]) {
			case '1':
				$arrTimeFrom[0] = '13';
				break;
			case '2':
				$arrTimeFrom[0] = '14';
				break;
			case '3':
				$arrTimeFrom[0] = '15';
				break;
			case '4':
				$arrTimeFrom[0] = '16';
				break;
			case '5':
				$arrTimeFrom[0] = '17';
				break;
			case '6':
				$arrTimeFrom[0] = '18';
				break;
			case '7':
				$arrTimeFrom[0] = '19';
				break;
			case '8':
				$arrTimeFrom[0] = '20';
				break;
			case '9':
				$arrTimeFrom[0] = '21';
				break;
			case '10':
				$arrTimeFrom[0] = '22';
				break;
			case '11':
				$arrTimeFrom[0] = '23';
				break;
		}
	} else if($arrTimeFrom[2] == 'am') {
		switch($arrTimeFrom[0]) {
			case '12':
				$arrTimeFrom[0] = '0';
				break;
		}
	}
	if(strlen($arrTimeFrom[0]) == 1) {
		$arrTimeFrom[0]='0'.$arrTimeFrom[0];
	}
	if(strlen($arrTimeFrom[1]) == 1) {
		$arrTimeFrom[1]='0'.$arrTimeFrom[1];
	}
} 
$timeFromString=$arrTimeFrom[0].":".$arrTimeFrom[1];
$arrTimeTo = explode(":",$_REQUEST["time_to"]);
if($arrTimeTo[2]!='undefined') {
	if($arrTimeTo[2] == 'pm') {
		//am pm Have to put it in 24 hour
		switch($arrTimeTo[0]) {
			case '1':
				$arrTimeTo[0] = '13';
				break;
			case '2':
				$arrTimeTo[0] = '14';
				break;
			case '3':
				$arrTimeTo[0] = '15';
				break;
			case '4':
				$arrTimeTo[0] = '16';
				break;
			case '5':
				$arrTimeTo[0] = '17';
				break;
			case '6':
				$arrTimeTo[0] = '18';
				break;
			case '7':
				$arrTimeTo[0] = '19';
				break;
			case '8':
				$arrTimeTo[0] = '20';
				break;
			case '9':
				$arrTimeTo[0] = '21';
				break;
			case '10':
				$arrTimeTo[0] = '22';
				break;
			case '11':
				$arrTimeTo[0] = '23';
				break;
		}
	} else if($arrTimeTo[2] == 'am') {
		switch($arrTimeTo[0]) {
			case '12':
				$arrTimeTo[0] = '0';
				break;
		}
	}
	if(strlen($arrTimeTo[0]) == 1) {
		$arrTimeTo[0]='0'.$arrTimeTo[0];
	}
	if(strlen($arrTimeTo[1]) == 1) {
		$arrTimeTo[1]='0'.$arrTimeTo[1];
	}
} 
$timeToString=$arrTimeTo[0].":".$arrTimeTo[1];
	//check if there is a slot with same date/time
	$check = mysql_num_rows(mysql_query("SELECT * FROM booking_slots WHERE slot_date = '".mysql_real_escape_string($_REQUEST["date"])."' AND slot_time_from = '".mysql_real_escape_string($timeFromString).":00' AND slot_id <>'".mysql_real_escape_string($_REQUEST["item_id"])."'"));
	if($check >0) {
		echo 0;
	} else {
		//edit slot
		$av ="slot_av";
		if(isset($_REQUEST["av"]) && $_REQUEST["av"]!='') {
			$av = $_REQUEST["av"];
		}
		$avmax ="slot_av_max";
		if(isset($_REQUEST["avmax"]) && $_REQUEST["avmax"]!='') {
			$avmax = $_REQUEST["avmax"];
		}
		if(isset($_REQUEST["price"]) && $_REQUEST["price"]!='') {
			mysql_query("UPDATE booking_slots SET slot_date= '".$_REQUEST["date"]."', slot_time_from = '".$timeFromString.":00', slot_time_to = '".$timeToString.":00',slot_special_text='".mysql_real_escape_string($_REQUEST["text"])."',slot_price='".str_replace(",",".",$_REQUEST["price"])."',slot_av=".$av.",slot_av_max=".$avmax." WHERE slot_id=".$_REQUEST["item_id"]);
		} else {
			mysql_query("UPDATE booking_slots SET slot_date= '".$_REQUEST["date"]."', slot_time_from = '".$timeFromString.":00', slot_time_to = '".$timeToString.":00',slot_special_text='".mysql_real_escape_string($_REQUEST["text"])."',slot_av=".$av.",slot_av_max=".$avmax." WHERE slot_id=".$_REQUEST["item_id"]);
		}
		$dateQry = mysql_query("SELECT * FROM booking_slots WHERE slot_id='".mysql_real_escape_string($_REQUEST["item_id"])."'");
		if($settingObj->getDateFormat() == "UK") {
			$dateToSend = strftime('%d/%m/%Y',strtotime(mysql_result($dateQry,0,'slot_date')));
		} else if($settingObj->getDateFormat() == "EU") {
			$dateToSend = strftime('%Y/%m/%d',strtotime(mysql_result($dateQry,0,'slot_date')));
		} else {
			$dateToSend = strftime('%m/%d/%Y',strtotime(mysql_result($dateQry,0,'slot_date')));
		}
		echo $dateToSend;
		
	}
}


?>
