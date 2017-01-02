<?php
include '../common.php';
$calendarObj->setCalendar($_GET["calendar_id"]);

//preparing week days
$weekdays=Array();
$weekdays[0] = $langObj->getLabel("SUNDAY");
$weekdays[1] = $langObj->getLabel("MONDAY");
$weekdays[2] = $langObj->getLabel("TUESDAY");
$weekdays[3] = $langObj->getLabel("WEDNESDAY");
$weekdays[4] = $langObj->getLabel("THURSDAY");
$weekdays[5] = $langObj->getLabel("FRIDAY");
$weekdays[6] = $langObj->getLabel("SATURDAY");

$maxColumn = 0;
$arraySlots = $listObj->getSlotsPerDayList($_GET["year"],$_GET["month"],$_GET["day"],$_GET["calendar_id"],$settingObj);
foreach($arraySlots as $slotId => $slot) {
	if(trim($slot["slot_special_text"])!='') {
		$maxColumn = 1;
	}
}
//calculate how many columns we need
$columns=ceil(count($arraySlots)/6);
//max number columns is 9, so if there are too many slots, have to add lines instead of columns
if($settingObj->getPaypalDisplayPrice() == 1 && $settingObj->getSlotsUnlimited() != 2 && $maxColumn == 0) {
	$maxColumn = 2;
	
} else if($settingObj->getSlotsUnlimited() == 2 && $maxColumn == 0) {
	$maxColumn=1;
} else if($maxColumn == 0) {
	if($settingObj->getTimeFormat() == "12") {
		$maxColumn = 3;
	} else {
		$maxColumn = 4;
	}
}

$lines=6;
if($columns>$maxColumn) {
	$columns=$maxColumn;
	$lines=7;
	do {
		$lines++;
	} while(ceil(count($arraySlots)/$lines)>$maxColumn && $lines < 13);
}

$totCols=0;
$page=1;

//get the next and prev dates with available slots
//first I check if there are slots in the future
$datenext = date_create($_GET["year"]."-".$_GET["month"]."-".$_GET["day"]);
date_add($datenext, date_interval_create_from_date_string('1 days'));

if($slotsObj->checkFutureSlots(date_format($datenext,'Y'),date_format($datenext,'m'),date_format($datenext,'d'),$_GET["calendar_id"])) {
	$next =strtotime(date("Y-m-d",mktime(0,0,0,$_GET["month"],$_GET["day"],$_GET["year"])) . "+ 1 day");
	$nextDay = date("d",$next);
	$nextMonth = date("m",$next);
	$nextYear = date("Y",$next);
	$arraySlotsNext = $listObj->getSlotsPerDayList($nextYear,$nextMonth,$nextDay,$_GET["calendar_id"],$settingObj);
	if(count($arraySlotsNext)==0) {
		do {
			$next =strtotime(date("Y-m-d",mktime(0,0,0,$nextMonth,$nextDay,$nextYear)) . "+ 1 day");
			$nextDay = date("d",$next);
			$nextMonth = date("m",$next);
			$nextYear = date("Y",$next);
			$arraySlotsNext = $listObj->getSlotsPerDayList($nextYear,$nextMonth,$nextDay,$_GET["calendar_id"],$settingObj);
		} while(count($arraySlotsNext) == 0);
	}
} else {
	$next = '';
}
$dateprev = date_create($_GET["year"]."-".$_GET["month"]."-".$_GET["day"]);
date_sub($dateprev, date_interval_create_from_date_string('1 days'));
if($slotsObj->checkPastSlots(date_format($dateprev,'Y'),date_format($dateprev,'m'),date_format($dateprev,'d'),$_GET["calendar_id"])) {
	$prev =strtotime(date("Y-m-d",mktime(0,0,0,$_GET["month"],$_GET["day"],$_GET["year"])) . "- 1 day");
	$prevDay = date("d",$prev);
	$prevMonth = date("m",$prev);
	$prevYear = date("Y",$prev);
	$arraySlotsPrev = $listObj->getSlotsPerDayList($prevYear,$prevMonth,$prevDay,$_GET["calendar_id"],$settingObj);
	if(count($arraySlotsPrev)==0) {
		do {
			$prev =strtotime(date("Y-m-d",mktime(0,0,0,$prevMonth,$prevDay,$prevYear)) . "- 1 day");
			$prevDay = date("d",$prev);
			$prevMonth = date("m",$prev);
			$prevYear = date("Y",$prev);
			$arraySlotsPrev = $listObj->getSlotsPerDayList($prevYear,$prevMonth,$prevDay,$_GET["calendar_id"],$settingObj);
		} while(count($arraySlotsPrev) == 0);
	}
} else {
	$prev = '';
}
$date = date_create(date('Y-m-d'));

if(function_exists("date_add")) {
	date_add($date, date_interval_create_from_date_string($settingObj->getBookFrom().' days'));
} else {
	date_modify($date, '+'.$settingObj->getBookFrom().' day');
}
//date_add($date, date_interval_create_from_date_string($settingObj->getBookFrom().' days'));
$bookfromdate = date_format($date, 'Ymd');
$date = date_create(date('Y-m-d'));
if(function_exists("date_add")) {
	date_add($date, date_interval_create_from_date_string($settingObj->getBookTo().' days'));
} else {
	date_modify($date, '+'.$settingObj->getBookTo().' day');
}

$booktodate = date_format($date, 'Ymd');
//only current month is navigable, so I stop navigation at the start of month or if the date is lower than today
if($prev == '' || $_GET["month"] > $prevMonth || date("Ymd",$prev) < $bookfromdate) {
	?>
    <div class="prev_day" style="float:left;color:#CCC;margin-right:20px"><?php echo $langObj->getLabel("GETBOOKINGFORM_PREV_DAY"); ?></div>
    <?php
} else {
	?>
	<div class="prev_day" style="float:left;margin-right:20px"><a href="javascript:getBookingForm(<?php echo $prevYear; ?>,<?php echo $prevMonth; ?>,<?php echo $prevDay; ?>, <?php echo $_GET["calendar_id"]; ?>,'<?php echo $settingObj->getRecaptchaPublicKey(); ?>');" style="color:#000"><?php echo $langObj->getLabel("GETBOOKINGFORM_PREV_DAY"); ?></a></div>
	<?php
}

//only current month is navigable, so I stop navigation at the end of month
if($next == '' || $_GET["month"] < $nextMonth || date("Ymd",$next) > $booktodate) {
	?>
    <div class="next_day" style="float:left;color:#CCC"><?php echo $langObj->getLabel("GETBOOKINGFORM_NEXT_DAY"); ?></div>
    <?php
} else {
	?>
	<div class="next_day" style="float:left"><a href="javascript:getBookingForm(<?php echo $nextYear; ?>,<?php echo $nextMonth; ?>,<?php echo $nextDay; ?>, <?php echo $_GET["calendar_id"]; ?>,'<?php echo $settingObj->getRecaptchaPublicKey(); ?>');" style="color:#000"><?php echo $langObj->getLabel("GETBOOKINGFORM_NEXT_DAY"); ?></a></div>
	<?php
}
?>

<!-- close -->
<div class="font_custom close_booking"><a href="javascript:closeBooking(<?php echo $calendarObj->getCalendarId(); ?>,'<?php echo $settingObj->getRecaptchaPublicKey(); ?>',<?php echo $_GET["year"]; ?>,<?php echo $_GET["month"]; ?>);"><?php echo $langObj->getLabel("GETBOOKINGFORM_CLOSE"); ?>&nbsp; X</a></div>
<div class="cleardiv"></div>

<script type="text/javascript">
	<?php if($settingObj->getSlotSelection() == "1") {
		?>
		$(function() {
			$('#booking_slots').find('input').each(function() {
				 
				  $(this).click(function() {
					  $('#booking_slots').find('input').removeAttr('checked');
					  $(this).attr("checked","checked");
				  });
			  });
		});
		<?php 
	} 
	?>
	
</script>

<!-- leftside -->
<div class="booking_left">
    <!-- title -->
    <div class="font_custom booking_title"><span id="booking_day"><?php echo $_GET["day"]." ".$weekdays[intval(date('w',mktime(0,0,0,$_GET["month"],$_GET["day"],$_GET["year"])))]; ?></span> - <span style="color:<?php echo $settingObj->getFormCalendarNameColor(); ?>;" id="calendar_name"><?php echo $calendarObj->getCalendarTitle(); ?></span><span style="float:right;width:30px;cursor:pointer" id="next">&nbsp;</span><span style="float:right;width:30px;cursor:pointer" id="prev">&nbsp;</span></div>
    <div class="cleardiv"></div>
    <?php
	if($settingObj->getFormText()!='') {
		?>
        <div class="form_text"><?php echo $settingObj->getFormText(); ?></div>
        <?php
	}
	?>
    <!-- slots available -->
    <div class="booking_slots_container" id="booking_slots">
        <div id="slideshow">
            <div id="page<?php echo $page; ?>">
            	<?php
				$onclick="";
				if($settingObj->getPaypal() == 1 && $settingObj->getPaypalAccount()!='' && $settingObj->getPaypalCurrency()!='' && $settingObj->getPaypalLocale() != '') {
					$onclick="javascript:addToPaypalForm();";
				}
	
				
					?>
                     <div class="booking_slots_column">
                    <?php
                    $z=1;
                    foreach($arraySlots as $slotId => $slot) {
						
						$disabled = "";
						if(isset($slot["slot_av"]) && $slot["slot_av"] == 0 && $settingObj->getSlotsUnlimited() == 2) {
							$disabled = "disabled";
						}
						if($slot["booked"] == 1) {
							
							$disabled="disabled";
						}
                      ?>
                      <div class="booking_slots_row">
                      <?php
					  if($slot["booked"] == 1) {
							echo '<div class="booked_slot">';
							
						}
					  ?>
                        <div class="booking_check"><input type="checkbox" name="reservation_slot[]" value="<?php echo $slotId; ?>" tmt:minchecked="1" tmt:message="<?php echo $langObj->getLabel("GETBOOKINGFORM_SLOT_ALERT"); ?>" <?php echo $disabled; ?> onclick="<?php echo $onclick; ?>"/></div>
                        <div class="booking_slot">
                        <?php 
							if($slot["slot_special_mode"] == 1) {
							
								if($settingObj->getTimeFormat() == "12") {
									echo date('h:i a',strtotime(substr($slot["slot_time_from"],0,5)))." - ".date('h:i a',strtotime(substr($slot["slot_time_to"],0,5)));
								} else {
									echo substr($slot["slot_time_from"],0,5)." - ".substr($slot["slot_time_to"],0,5);
								}
								
								
							} else if($slot["slot_special_mode"] == 0 && $slot["slot_special_text"] != '') {
								
								//echo $slot["slot_special_text"]; 
							} else {
								
								if($settingObj->getTimeFormat() == "12") {
									echo date('h:i a',strtotime(substr($slot["slot_time_from"],0,5)))." - ".date('h:i a',strtotime(substr($slot["slot_time_to"],0,5)));
								} else {
									echo substr($slot["slot_time_from"],0,5)." - ".substr($slot["slot_time_to"],0,5);
								}
								
							}
							
							
                            
							
							
						?>
                        
                        </div>
                        <?php
                        //add seats num if set
						if($settingObj->getSlotsUnlimited() == 2) {
							
							?>
                            <div class="booking_seats">
                            	<?php echo $langObj->getLabel("SELECT_SEATS"); ?>:&nbsp;
                                <select name="reservation_seats_<?php echo $slotId; ?>" id="seats_<?php echo $slotId; ?>" onchange="<?php echo $onclick; ?>" style="width: 40px;" <?php echo $disabled; ?>>
                                	<?php
									
									for($u=1;$u<=$slot["slot_av_max"];$u++) {
										?>
                                        <option value="<?php echo $u; ?>"><?php echo $u; ?></option>
                                        <?php
									}
									?>
                                </select>
                            </div>
                            <?php
						}
                        
						if($settingObj->getPaypalDisplayPrice() == 1) {
							?>
                            <div class="booking_price">
                            	<?php
								if($slot["slot_price"]>0) {
									
									if(!function_exists('money_format')) {
										echo $utilsObj->money_format('%!.2n',$slot["slot_price"]); ?>&nbsp;<?php echo $settingObj->getPaypalCurrency();
									} else {
										echo money_format('%!.2n',$slot["slot_price"]); ?>&nbsp;<?php echo $settingObj->getPaypalCurrency();
									}
									
									
								} else {
									echo $langObj->getLabel("FREE");
								}
								?>
                            	
                            </div>
                            <?php
						}
						
						if($slot["slot_special_text"] != '' && ($slot["slot_special_mode"] == 1 || $slot["slot_special_mode"] == 0)) {
							?>
                            <div class="booking_text">
                            <?php echo $slot["slot_special_text"]; ?>
                            </div>
                            <?php
						}
						
						if($slot["booked"] == 1) {
							echo '</div>';
						}
						?>
                        
                        <div class="cleardiv"></div>
                      </div>
                      
                      <?php
                      if($z % $lines == 0) {
                          $totCols++;
                          ?>
                          </div>
                          <?php
                          $display="";
                          if($totCols % $maxColumn == 0 && $z < count($arraySlots)) {
                              $page++;
                              
                              ?>
                              </div>
                              <div id="page<?php echo $page; ?>">
                              <?php
                          }
                          ?>
                          <div class="booking_slots_column">
                          <?php
                      }
                      $z++;
                    }
                    ?>
                    
                </div>
                   
                
            </div>
        </div>
    </div>
</div>
<script>
	$(function() {
		$('#calendar_id').val(<?php echo $calendarObj->getCalendarId(); ?>);
		<?php
		if($page > 1) {
		?>
		  var slider = $('#slideshow').bxSlider({
			infiniteLoop: false,
			controls: false,
			onAfterSlide: function(currentSlideNumber, totalSlideQty, currentSlideHtmlObject){
							$('#prev').html('<a href="#"></a>');
							 $('#next').html('<a href="#"></a>');
						  if(currentSlideNumber+1 == totalSlideQty) {
							  $('#next').html("");
							  $('#prev').html('<a href="#"></a>');
						  }
						  if(currentSlideNumber == 0) {
							  $('#prev').html('');
							  $('#next').html('<a href="#"></a>');
						  } 
						}
		  });
		  $('#prev').click(function(){
			slider.goToPreviousSlide();
			return false;
		  });
		
		  $('#next').click(function(){
			slider.goToNextSlide();
			return false;
		  });
		<?php
		}
		?>
		 
	});
</script>

|
<?php
if($page>1) {
	echo "1";
} else {
	echo "0";
}
?>
|<?php echo $langObj->getLabel("GETBOOKINGFORM_CAPTCHA_ALERT"); ?>

