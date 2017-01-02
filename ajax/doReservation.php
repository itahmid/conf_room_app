<?php
include '../common.php';
require_once(dirname(__FILE__).'/../libs/PHPMailer/class.phpmailer.php');
$confirm=0;
$calendarObj->setCalendar($_POST["calendar_id"]);
if($settingObj->getRecaptchaEnabled() == "1") {
	require_once('../include/recaptchalib.php');
	$privatekey = $settingObj->getRecaptchaPrivateKey();
	$resp = recaptcha_check_answer ($privatekey,
								$_SERVER["REMOTE_ADDR"],
								$_POST["recaptcha_challenge_field"],
								$_POST["recaptcha_response_field"]);
	
	if (!$resp->is_valid) {
		// What happens when the CAPTCHA was entered incorrectly
		?>
		<script>
			window.parent.showCaptchaError();
		</script>
		<?php
	} else {
		// Your code here to handle a successful verification
		$listReservations=$reservationObj->insertReservation($settingObj);
		if($listReservations != '') {
			if($settingObj->getReservationConfirmationMode() == 1 && !isset($_POST["with_paypal"])) {
				$reservationObj->confirmReservations($listReservations);
			}
			$confirm = 1;
		} else {
			$confirm = 0;
		}
	}
} else {
	$listReservations=$reservationObj->insertReservation($settingObj);
	if($listReservations != '') {
		if($settingObj->getReservationConfirmationMode() == 1 && !isset($_POST["with_paypal"])) {
			$reservationObj->confirmReservations($listReservations);
		}
		$confirm = 1;
	} else {
		$confirm = 0;
	}
}
if($confirm == 1) {
	if(isset($_POST["with_paypal"])) {
		//set session variables if it's from paypal
		$_SESSION["reservation_paypal_list"] = $listReservations;
	}
	//send reservation email to admin
	//check first if the current calendar has a custom email address
	
	if($calendarObj->getCalendarEmail() != '') {
		$to = $calendarObj->getCalendarEmail();
	} else {
		$to = $settingObj->getEmailReservation();
	}
	
	
	$subject = $langObj->getLabel("DORESERVATION_MAIL_ADMIN_SUBJECT");
	$message=$langObj->getLabel("DORESERVATION_MAIL_ADMIN_MESSAGE1")."<br>";
	
	if(in_array("reservation_name",$settingObj->getVisibleFields())) {
		$message.="<strong>".$langObj->getLabel("DORESERVATION_MAIL_ADMIN_MESSAGE2")."</strong>: ".$_POST["reservation_name"]."<br>";
	}
	if(in_array("reservation_surname",$settingObj->getVisibleFields())) {
		$message.="<strong>".$langObj->getLabel("DORESERVATION_MAIL_ADMIN_MESSAGE3")."</strong>: ".$_POST["reservation_surname"]."<br>";
	}
	if(in_array("reservation_email",$settingObj->getVisibleFields())) {
		$message.="<strong>".$langObj->getLabel("DORESERVATION_MAIL_ADMIN_MESSAGE4")."</strong>: ".$_POST["reservation_email"]."<br>";
	}
	if(in_array("reservation_phone",$settingObj->getVisibleFields())) {
		$message.="<strong>".$langObj->getLabel("DORESERVATION_MAIL_ADMIN_MESSAGE5")."</strong>: ".$_POST["reservation_phone"]."<br>";
	}
	
	if(in_array("reservation_message",$settingObj->getVisibleFields())) {
		$message.="<strong>".$langObj->getLabel("DORESERVATION_MAIL_ADMIN_MESSAGE6")."</strong>: ".$_POST["reservation_message"]."<br>";
	}	
	if(in_array("reservation_field1",$settingObj->getVisibleFields())) {
		$message.="<strong>".$langObj->getLabel("DORESERVATION_MAIL_ADMIN_MESSAGE10")."</strong>: ".$_POST["reservation_field1"]."<br>";
	}
	if(in_array("reservation_field2",$settingObj->getVisibleFields())) {
		$message.="<strong>".$langObj->getLabel("DORESERVATION_MAIL_ADMIN_MESSAGE11")."</strong>: ".$_POST["reservation_field2"]."<br>";
	}
	if(in_array("reservation_field3",$settingObj->getVisibleFields())) {
		$message.="<strong>".$langObj->getLabel("DORESERVATION_MAIL_ADMIN_MESSAGE12")."</strong>: ".$_POST["reservation_field3"]."<br>";
	}
	if(in_array("reservation_field4",$settingObj->getVisibleFields())) {
		$message.="<strong>".$langObj->getLabel("DORESERVATION_MAIL_ADMIN_MESSAGE13")."</strong>: ".$_POST["reservation_field4"]."<br>";
	}
	$message.="<br><strong>".$langObj->getLabel("DORESERVATION_MAIL_ADMIN_MESSAGE7")."</strong>:<br>";
	$message.="<ul type='disc'>";
	//loop through slots
	$paypalHtml = "";
	for($i=0;$i<count($_POST["reservation_slot"]);$i++) {
		$slotsObj->setSlot($_POST["reservation_slot"][$i]);
		$calendarObj->setCalendar($_POST["calendar_id"]);
		///PAYPAL/////
		if($slotsObj->getSlotSpecialMode() == 1) {
			if($settingObj->getTimeFormat() == "12") {
				$time= date('h:i a',strtotime(substr($slotsObj->getSlotTimeFrom(),0,5)))." - ".date('h:i a',strtotime(substr($slotsObj->getSlotTimeTo(),0,5)));
			} else {
				$time= substr($slotsObj->getSlotTimeFrom(),0,5)." - ".substr($slotsObj->getSlotTimeTo(),0,5);
			}
			if($slotsObj->getSlotSpecialText() != '') {
				$time.= " - ".$slotsObj->getSlotSpecialText(); 
			}
		} else if($slotsObj->getSlotSpecialMode() == 0 && $slotsObj->getSlotSpecialText() != '') {
			$time= $slotsObj->getSlotSpecialText(); 
		} else {
			if($settingObj->getTimeFormat() == "12") {
				echo date('h:i a',strtotime(substr($slotsObj->getSlotTimeFrom(),0,5)))." - ".date('h:i a',strtotime(substr($slotsObj->getSlotTimeTo(),0,5)));
			} else {
				echo substr($slotsObj->getSlotTimeFrom(),0,5)." - ".substr($slotsObj->getSlotTimeTo(),0,5);
			}
		}
		if($settingObj->getDateFormat() == "UK") {
			$dateToSend = strftime('%d/%m/%Y',strtotime($slotsObj->getSlotDate()));
		} else if($settingObj->getDateFormat() == "EU") {
			$dateToSend = strftime('%Y/%m/%d',strtotime($slotsObj->getSlotDate()));
		} else {
			$dateToSend = strftime('%m/%d/%Y',strtotime($slotsObj->getSlotDate()));
		}
		$info_slot = $dateToSend." ".$time;
		$seats = 1;
		if($settingObj->getSlotsUnlimited() == 2) {
			$seats=$_POST["reservation_seats_".$_POST["reservation_slot"][$i]];
		}
		$paypalHtml.=trim('<input type="hidden" name="item_name_'.($i+1).'" value="'.$info_slot.'" /><input type="hidden" name="amount_'.($i+1).'" value="'.$slotsObj->getSlotPrice().'" /><input type="hidden" name="quantity_'.($i+1).'" value="'.$seats.'" />');
		/////END PAYPAL////
		$message.="<li>";
		$message.="<strong>".$langObj->getLabel("DORESERVATION_MAIL_ADMIN_CALENDAR")."</strong>: ".$calendarObj->getCalendarTitle()."<br>";
		$dateToSend = strftime('%B %d %Y',strtotime($slotsObj->getSlotDate()));
		if($settingObj->getDateFormat() == "UK") {
			$dateToSend = strftime('%d/%m/%Y',strtotime($slotsObj->getSlotDate()));
		} else if($settingObj->getDateFormat() == "EU") {
			$dateToSend = strftime('%Y/%m/%d',strtotime($slotsObj->getSlotDate()));
		} else {
			$dateToSend = strftime('%m/%d/%Y',strtotime($slotsObj->getSlotDate()));
		}
		$message.="<strong>".$langObj->getLabel("DORESERVATION_MAIL_ADMIN_DATE")."</strong>: ".$dateToSend."<br>";
		if($settingObj->getTimeFormat() == "12") {
			$message.="<strong>".$langObj->getLabel("DORESERVATION_MAIL_ADMIN_TIME")."</strong>: ".$slotsObj->getSlotTimeFromAMPM()."-".$slotsObj->getSlotTimeToAMPM()."<br>";
		} else {
			$message.="<strong>".$langObj->getLabel("DORESERVATION_MAIL_ADMIN_TIME")."</strong>: ".$slotsObj->getSlotTimeFrom()."-".$slotsObj->getSlotTimeTo()."<br>";
		}
		if($settingObj->getSlotsUnlimited() == 2) {
			$message.="<strong>".$langObj->getLabel("DORESERVATION_MAIL_ADMIN_SEATS")."</strong>: ".$_POST["reservation_seats_".$_POST["reservation_slot"][$i]]."<br>";
		}
		if($settingObj->getPaypalDisplayPrice() == 1) {
			$price = "";
			if(!function_exists('money_format')) {
				$price= $utilsObj->money_format('%!.2n',$slotsObj->getSlotPrice())."&nbsp;".$settingObj->getPaypalCurrency();
			} else {
				$price= money_format('%!.2n',$slotsObj->getSlotPrice())."&nbsp;".$settingObj->getPaypalCurrency();
			}
			$message.="<strong>".$langObj->getLabel("DORESERVATION_MAIL_ADMIN_PRICE")."</strong>: ".$price."<br>";
		}
		$message.="</li>";
	}
	$message.="</ul>";
	if($settingObj->getReservationConfirmationMode() == 3) {
		$message.=$langObj->getLabel("DORESERVATION_MAIL_ADMIN_MESSAGE8").'<a href="'.$settingObj->getSiteDomain().'/admin/">'.$langObj->getLabel("DORESERVATION_MAIL_ADMIN_MESSAGE9").'</a>';
	}
	
	
	
	
	
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
	
	if(in_array("reservation_email",$settingObj->getVisibleFields())) {
			
		//send reservation email to user
		$to = $_POST["reservation_email"];
		
		
		//WARNING!! static mail record ids, if deleted/changed, must be changed here also
		switch($settingObj->getReservationConfirmationMode()) {
			case "1":
				$mailObj->setMail(1);
				break;
			case "2":
				$mailObj->setMail(2);
				break;
			case "3":
				$mailObj->setMail(3);
				break;
		}
		if($settingObj->getPaypal()==1 && $settingObj->getPaypalAccount() != '' && $settingObj->getPaypalLocale() != '' && $settingObj->getPaypalCurrency() != '') {
			$mailObj->setMail(1);
		}
		$subject = $mailObj->getMailSubject();
		//setting username in message
		$message=str_replace("[customer-name]",$_POST["reservation_name"],$mailObj->getMailText());
		//check if cancellation is enabled id email is 1
		if($mailObj->getMailId() == 1 && $settingObj->getReservationCancel() == "1") {
			$message.=$mailObj->getMailCancelText();
		}
		//setting reservation detail in message
		//loop through slots
		$res_details = "";
		for($i=0;$i<count($_POST["reservation_slot"]);$i++) {
			$slotsObj->setSlot($_POST["reservation_slot"][$i]);
			$calendarObj->setCalendar($_POST["calendar_id"]);	
			$res_details.="<strong>".$langObj->getLabel("DORESERVATION_MAIL_USER_VENUE")."</strong>: ".$calendarObj->getCalendarTitle()."<br>";
			$dateToSend = strftime('%B %d %Y',strtotime($slotsObj->getSlotDate()));
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
				$res_details.="<strong>".$langObj->getLabel("DORESERVATION_MAIL_USER_SEATS")."</strong>: ".$_POST["reservation_seats_".$_POST["reservation_slot"][$i]]."<br>";
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
		}
		$message=str_replace("[reservation-details]",$res_details,$message);	
		
		
		if($mailObj->getMailId() == 2) {
			//setting reservation confirmation link in message
			//if he must confirm it via mail, I send the link
			$message=str_replace("[confirmation-link]","<a href='".CALENDAR_PATH."/confirm.php?reservations=".$listReservations."'>".$langObj->getLabel("DORESERVATION_MAIL_USER_MESSAGE3")."</a>",$message);
			$message=str_replace("[confirmation-link-url]",CALENDAR_PATH."/confirm.php?reservations=".$listReservations,$message);
		}
		
		if($mailObj->getMailId() == 1 && $settingObj->getReservationCancel() == "1") {
			$message=str_replace("[cancellation-link]","<a href='".CALENDAR_PATH."/cancel.php?reservations=".$listReservations."'>".$langObj->getLabel("DORESERVATION_MAIL_USER_MESSAGE4")."</a>",$message);
			$message=str_replace("[cancellation-link-url]",CALENDAR_PATH."/cancel.php?reservations=".$listReservations,$message);
		}
		$message.="<br><br>".$mailObj->getMailSignature();
		
		
		
		
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
	
	$arrReservations = explode(",",$listReservations);
	$htmlToAppend = "";
	for($i=0;$i<count($arrReservations);$i++) {
		$htmlToAppend.='<input type="hidden" name="item_number_'.($i+1).'" value="'.$arrReservations[$i].'" />';
	}
	
	?>
	<script>
		
		
		<?php
		if(isset($_POST["with_paypal"])) {
			?>
			
			htmlToAppend = "";
			window.parent.$('#slots_purchased').append('<input type="hidden" name="custom" value="<?php echo $listReservations; ?>" />');
			window.parent.$('#slots_purchased').append('<?php echo addslashes($paypalHtml); ?>');
			window.parent.submitPaypal();
			<?php
		} else {
			?>
			window.parent.showResponse(<?php echo $calendarObj->getCalendarId(); ?>);
			<?php
		}
		?>
	</script>
	<?php
} else {
	$publickey = "";
	if($settingObj->getRecaptchaEnabled() == "1") {
		$publickey = $settingObj->getRecaptchaPublicKey();
	}
	?>
	<script>
		window.parent.alert('<?php echo $langObj->getLabel("DORESERVATION_ERROR"); ?>');		
		window.parent.hideResponse(<?php echo $calendarObj->getCalendarId(); ?>,'<?php echo $publickey; ?>');
	</script>
	<?php
}


?>
