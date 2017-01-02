<?php
include '../common.php';
if(isset($_SESSION["admin_id"]) && $_SESSION["admin_id"] > 0) {
	$item_id = $_REQUEST["reservation_id"];	
	
	mysql_query("UPDATE booking_reservation SET reservation_confirmed = 1 WHERE reservation_id = ".$item_id);
	
	if($settingObj->getReservationConfirmationMode() == 3 || ($settingObj->getPaypal()==1 && $settingObj->getPaypalAccount() != '' && $settingObj->getPaypalLocale() != '' && $settingObj->getPaypalCurrency() != '')) {
		//send reservation email to user if setted in config
		$reservationObj->setReservation($item_id);
		$slotsObj->setSlot($reservationObj->getReservationSlotId());
		$calendarObj->setCalendar($reservationObj->getReservationCalendarId());
		if($reservationObj->getReservationEmail() != '') {
			$to = $reservationObj->getReservationEmail();
			
			$mailObj->setMail(4);
			$subject = $mailObj->getMailSubject();
			
			$message=str_replace("[customer-name]",$reservationObj->getReservationName(),$mailObj->getMailText());
			
			
			$res_details.="<strong>".$langObj->getLabel("DORESERVATION_MAIL_USER_VENUE")."</strong>: ".$calendarObj->getCalendarTitle()."<br>";
			if($settingObj->getDateFormat() == "UK") {
				$dateToSend = strftime('%d/%m/%Y',strtotime($slotsObj->getSlotDate()));
			} else if($settingObj->getDateFormat() == "EU") {
				$dateToSend = strftime('%Y/%m/%d',strtotime($slotsObj->getSlotDate()));
			} else {
				$dateToSend = strftime('%m/%d/%Y',strtotime($slotsObj->getSlotDate()));
			}
			
			$res_details.="<strong>".$langObj->getLabel("DORESERVATION_MAIL_USER_DATE")."</strong>: ".$dateToSend."<br>";
			if($slotsObj->getSlotSpecialMode() == 1) {
				if($settingObj->getTimeFormat() == "12") {
					$res_details.="<strong>".$langObj->getLabel("DORESERVATION_MAIL_USER_TIME")."</strong>: ".$slotsObj->getSlotTimeFromAMPM()."-".$slotsObj->getSlotTimeToAMPM();
				} else {
					$res_details.="<strong>".$langObj->getLabel("DORESERVATION_MAIL_USER_TIME")."</strong>: ".$slotsObj->getSlotTimeFrom()."-".$slotsObj->getSlotTimeTo();
				}
				if($slotsObj->getSlotSpecialText()!='') {
					$res_details.=" - ".$slotsObj->getSlotSpecialText();
				}
				$res_details.="<br>";
			} else if($slotsObj->getSlotSpecialMode() == 0 && $slotsObj->getSlotSpecialText() != '') {
				$res_details.="<strong>".$langObj->getLabel("DORESERVATION_MAIL_USER_TIME")."</strong>:".$slotsObj->getSlotSpecialText()."<br>";
			} else {
				if($settingObj->getTimeFormat() == "12") {
					$res_details.="<strong>".$langObj->getLabel("DORESERVATION_MAIL_USER_TIME")."</strong>: ".$slotsObj->getSlotTimeFromAMPM()."-".$slotsObj->getSlotTimeToAMPM()."<br>";
				} else {
					$res_details.="<strong>".$langObj->getLabel("DORESERVATION_MAIL_USER_TIME")."</strong>: ".$slotsObj->getSlotTimeFrom()."-".$slotsObj->getSlotTimeTo()."<br>";
				}
			}
			if($settingObj->getSlotsUnlimited() == 2) {
				$res_details.="<strong>".$langObj->getLabel("DORESERVATION_MAIL_USER_SEATS")."</strong>: ".$reservationObj->getReservationSeats()."<br>";
			}
			if($settingObj->getPaypalDisplayPrice() == 1) {
				$price = "";
				if(!function_exists('money_format')) {
					$price= $utilsObj->money_format('%!.2n',$slotsObj->getSlotPrice())."&nbsp;".$settingObj->getPaypalCurrency();
				} else {
					$price= money_format('%!.2n',$slotsObj->getSlotPrice())."&nbsp;".$settingObj->getPaypalCurrency();
				}
				$res_details.="<strong>".$langObj->getLabel("DORESERVATION_MAIL_USER_PRICE")."</strong>: ".$price."<br>";
			}
			$res_details.="<br><br>";
			
			
			$message=str_replace("[reservation-details]",$res_details,$message);
			if($settingObj->getReservationCancel() == "1") {
				$message.=$mailObj->getMailCancelText();
			}
			if($settingObj->getReservationCancel() == "1") {
				$message=str_replace("[cancellation-link]","<a href='".$settingObj->getSiteDomain()."/cancel.php?reservations=".md5($item_id)."'>".$langObj->getLabel("DORESERVATION_MAIL_USER_MESSAGE4")."</a>",$message);
				$message=str_replace("[cancellation-link-url]",$settingObj->getSiteDomain()."/cancel.php?reservations=".md5($item_id),$message);
			}
			
			$message.="<br><br>".$mailObj->getMailSignature();
			
			
			
			require_once(dirname(__FILE__).'/../../libs/PHPMailer/class.phpmailer.php');
	
			$mail             = new PHPMailer(); // defaults to using php "mail()"
			
			$mail->CharSet = 'UTF-8';
			$body             = $message;
			@$body             = eregi_replace("[\]",'',$body);
			
			$mail->AddReplyTo($settingObj->getEmailFromReservation(),$settingObj->getNameFromReservation());
			
			$mail->SetFrom($settingObj->getEmailFromReservation(), $settingObj->getNameFromReservation());
			
			$address = $to;
			$mail->AddAddress($address, $address);
			
			$mail->Subject    = $subject;
			
			$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; 
			
			$mail->MsgHTML($body);
			
			
			$mail->Send();
		}
	}
}

?>
