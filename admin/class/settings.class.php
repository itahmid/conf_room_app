<?php

class setting {
	
	private function doSettingQuery($setting) {
		$settingQry = mysql_query("SELECT * FROM booking_config WHERE config_name='".mysql_real_escape_string($setting)."'");
		return mysql_result($settingQry,0,'config_value');
	}
	
	public function getReservationConfirmationMode() {
		return setting::doSettingQuery('reservation_confirmation_mode');
	}
	
	public function getTimezone() {
		return setting::doSettingQuery('timezone');
	}
	
	public function getEmailReservation() {
		return setting::doSettingQuery('email_reservation');
	}
	
	public function getEmailFromReservation() {
		return setting::doSettingQuery('email_from_reservation');
	}

	public function getNameFromReservation() {
		return setting::doSettingQuery('name_from_reservation');
	}
	
	public function getSiteDomain() {
		return setting::doSettingQuery('site_domain');
	}
	
	public function getRecaptchaPublicKey() {
		return setting::doSettingQuery('recaptcha_public_key');
	}
	
	public function getRecaptchaPrivateKey() {
		return setting::doSettingQuery('recaptcha_private_key');
	}
	
	public function getMandatoryFields() {
		$list=setting::doSettingQuery('mandatory_fields');
		$arrFields = Array();
		$arrFields = explode(",",$list);
		return $arrFields;
	}
	
	public function getVisibleFields() {
		$list=setting::doSettingQuery('visible_fields');
		$arrFields = Array();
		$arrFields = explode(",",$list);
		return $arrFields;
	}
	
	public function getRedirect() {
		return setting::doSettingQuery('redirect_confirmation_path');
	}
	
	public function getRecaptchaEnabled() {
		return setting::doSettingQuery('recaptcha_enabled');
	}
	
	public function getSlotsPopupEnabled() {
		return setting::doSettingQuery('slots_popup_enabled');
	}
	
	public function getSlotsUnlimited() {
		return setting::doSettingQuery('slots_unlimited');
	}
	
	public function getReservationCancel() {
		return setting::doSettingQuery('reservation_cancel');
	}
	
	public function getCancelRedirect() {
		return setting::doSettingQuery('redirect_cancel_path');
	}
	
	public function getSlotSelection() {
		return setting::doSettingQuery('slot_selection');
	}
	
	public function getDateFormat() {
		return setting::doSettingQuery('date_format');
	}
	
	public function getTimeFormat() {
		return setting::doSettingQuery('time_format');
	}
	
	public function getShowBookedSlots() {
		return setting::doSettingQuery('show_booked_slots');
	}

	public function getShowCategorySelection() {
		return setting::doSettingQuery('show_category_selection');
	}
	
	public function getShowCalendarSelection() {
		return setting::doSettingQuery('show_calendar_selection');
	}
	
	public function getShowFirstFilledMonth() {
		return setting::doSettingQuery('show_first_filled_month');
	}	
	
	public function getShowSlotsSeats() {
		return setting::doSettingQuery('show_slots_seats');
	}
	
	public function getCalendarMonthLimitPast() {
		return setting::doSettingQuery('calendar_month_limit_past');
	}
	
	public function getCalendarMonthLimitFuture() {
		return setting::doSettingQuery('calendar_month_limit_future');
	}
	
	public function getShowTerms() {
		return setting::doSettingQuery('show_terms');
	}
	
	public function getTermsLabel() {
		return setting::doSettingQuery('terms_label');
	}
	
	public function getTermsLink() {
		return setting::doSettingQuery('terms_link');
	}
	
	public function getBookFrom() {
		return setting::doSettingQuery('book_from');
	}

	public function getBookTo() {
		return setting::doSettingQuery('book_to');
	}
	
	public function getPaypal() {
		return setting::doSettingQuery('paypal');
	}
	
	public function getPaypalAccount() {
		return setting::doSettingQuery('paypal_account');
	}
	
	public function getPaypalPrice() {
		return setting::doSettingQuery('paypal_price');
	}
	
	public function getPaypalCurrency() {
		return setting::doSettingQuery('paypal_currency');
	}
	
	public function getPaypalLocale() {
		return setting::doSettingQuery('paypal_locale');
	}
	
	public function getPaypalDisplayPrice() {
		return setting::doSettingQuery('paypal_display_price');
	}
	
	public function getFormText() {
		return setting::doSettingQuery('form_text');
	}
	
	public function getReservationFieldType($field) {
		$type="text";
		$typeQry=mysql_query("SELECT * FROM booking_fields_types WHERE reservation_field_name='".mysql_real_escape_string($field)."'");
		if(mysql_num_rows($typeQry)>0) {
			$type=mysql_result($typeQry,'0','reservation_field_type');
		}
		return $type;
	}
	
	public function updateSettings() {
		mysql_query("UPDATE booking_config
					 SET config_value='".$_POST["reservation_confirmation_mode"]."'
					 WHERE config_name='reservation_confirmation_mode'");
		mysql_query("UPDATE booking_config
					 SET config_value='".$_POST["timezone"]."'
					 WHERE config_name='timezone'");
		mysql_query("UPDATE booking_config
					 SET config_value='".$_POST["email_reservation"]."'
					 WHERE config_name='email_reservation'");
		mysql_query("UPDATE booking_config
					 SET config_value='".$_POST["email_from_reservation"]."'
					 WHERE config_name='email_from_reservation'");
		mysql_query("UPDATE booking_config
					 SET config_value='".$_POST["name_from_reservation"]."'
					 WHERE config_name='name_from_reservation'");
		mysql_query("UPDATE booking_config
					 SET config_value='".$_POST["site_domain"]."'
					 WHERE config_name='site_domain'");
		if(isset($_POST["recaptcha_enabled"]) && $_POST["recaptcha_enabled"] == "1") {
			mysql_query("UPDATE booking_config
						 SET config_value='".$_POST["recaptcha_enabled"]."'
						 WHERE config_name='recaptcha_enabled'");
		} else {
			mysql_query("UPDATE booking_config
						 SET config_value='0'
						 WHERE config_name='recaptcha_enabled'");
		}
		mysql_query("UPDATE booking_config
					 SET config_value='".$_POST["recaptcha_public_key"]."'
					 WHERE config_name='recaptcha_public_key'");
		mysql_query("UPDATE booking_config
					 SET config_value='".$_POST["recaptcha_private_key"]."'
					 WHERE config_name='recaptcha_private_key'");
		mysql_query("UPDATE booking_config
					 SET config_value='".$_POST["slots_popup_enabled"]."'
					 WHERE config_name='slots_popup_enabled'");
		
		
		mysql_query("UPDATE booking_config
					 SET config_value='".$_POST["redirect_confirmation_path"]."'
					 WHERE config_name='redirect_confirmation_path'");
		if(isset($_POST["reservation_cancel"]) && $_POST["reservation_cancel"] == "1") {
			mysql_query("UPDATE booking_config
						 SET config_value='".$_POST["reservation_cancel"]."'
						 WHERE config_name='reservation_cancel'");
			mysql_query("UPDATE booking_config
						 SET config_value='".$_POST["redirect_cancel_path"]."'
						 WHERE config_name='redirect_cancel_path'");
			
		} else {
			mysql_query("UPDATE booking_config
						 SET config_value='0'
						 WHERE config_name='reservation_cancel'");
			mysql_query("UPDATE booking_config
						 SET config_value=''
						 WHERE config_name='redirect_cancel_path'");
			
		}
		
		mysql_query("UPDATE booking_config
					 SET config_value='".$_POST["slot_selection"]."'
					 WHERE config_name='slot_selection'");
		mysql_query("UPDATE booking_config
					 SET config_value='".$_POST["date_format"]."'
					 WHERE config_name='date_format'");
		mysql_query("UPDATE booking_config
					 SET config_value='".$_POST["time_format"]."'
					 WHERE config_name='time_format'");
		mysql_query("UPDATE booking_config
					 SET config_value='".$_POST["slots_unlimited"]."'
					 WHERE config_name='slots_unlimited'");
		mysql_query("UPDATE booking_config
					 SET config_value='".$_POST["show_booked_slots"]."'
					 WHERE config_name='show_booked_slots'");
		mysql_query("UPDATE booking_config
					 SET config_value='".$_POST["show_category_selection"]."'
					 WHERE config_name='show_category_selection'");
		mysql_query("UPDATE booking_config
					 SET config_value='".$_POST["show_calendar_selection"]."'
					 WHERE config_name='show_calendar_selection'");
		mysql_query("UPDATE booking_config
					 SET config_value='".$_POST["calendar_month_limit_past"]."'
					 WHERE config_name='calendar_month_limit_past'");
		mysql_query("UPDATE booking_config
					 SET config_value='".$_POST["calendar_month_limit_future"]."'
					 WHERE config_name='calendar_month_limit_future'");
		mysql_query("UPDATE booking_config
					 SET config_value='".$_POST["show_terms"]."'
					 WHERE config_name='show_terms'");
		mysql_query("UPDATE booking_config
					 SET config_value='".$_POST["terms_label"]."'
					 WHERE config_name='terms_label'");
		mysql_query("UPDATE booking_config
					 SET config_value='".$_POST["terms_link"]."'
					 WHERE config_name='terms_link'");
		mysql_query("UPDATE booking_config
					 SET config_value='".$_POST["book_from"]."'
					 WHERE config_name='book_from'");
		mysql_query("UPDATE booking_config
					 SET config_value='".$_POST["book_to"]."'
					 WHERE config_name='book_to'");
		mysql_query("UPDATE booking_config
					 SET config_value='".$_POST["paypal"]."'
					 WHERE config_name='paypal'");
		mysql_query("UPDATE booking_config
					 SET config_value='".$_POST["paypal_account"]."'
					 WHERE config_name='paypal_account'");
		mysql_query("UPDATE booking_config
					 SET config_value='".$_POST["paypal_currency"]."'
					 WHERE config_name='paypal_currency'");
		mysql_query("UPDATE booking_config
					 SET config_value='".$_POST["paypal_locale"]."'
					 WHERE config_name='paypal_locale'");
		mysql_query("UPDATE booking_config
					 SET config_value='".$_POST["paypal_display_price"]."'
					 WHERE config_name='paypal_display_price'");
		mysql_query("UPDATE booking_config
					 SET config_value='".$_POST["form_text"]."'
					 WHERE config_name='form_text'");
		mysql_query("UPDATE booking_config
					 SET config_value='".$_POST["show_first_filled_month"]."'
					 WHERE config_name='show_first_filled_month'");
		mysql_query("UPDATE booking_config
					 SET config_value='".$_POST["show_slots_seats"]."'
					 WHERE config_name='show_slots_seats'");
	}
	
	
	public function updateFormSettings() {
		if(isset($_POST["mandatory_fields"])) {
			$stringMandatory = "";
			for($i=0;$i<count($_POST["mandatory_fields"]);$i++) {
				if($stringMandatory == "") {
					$stringMandatory.=$_POST["mandatory_fields"][$i];
				} else {
					$stringMandatory.=",".$_POST["mandatory_fields"][$i];
				}
			}
			mysql_query("UPDATE booking_config
					 SET config_value='".$stringMandatory."'
					 WHERE config_name='mandatory_fields'");
		}
		
		if(isset($_POST["visible_fields"])) {
			$stringVisible = "";
			for($i=0;$i<count($_POST["visible_fields"]);$i++) {
				if($stringVisible == "") {
					$stringVisible.=$_POST["visible_fields"][$i];
				} else {
					$stringVisible.=",".$_POST["visible_fields"][$i];
				}
			}
			mysql_query("UPDATE booking_config
					 SET config_value='".$stringVisible."'
					 WHERE config_name='visible_fields'");
		}
		
		//update fields type
		$arrayFields = $_POST["reservation_field_name"];
		$arrayTypes = $_POST["field_type"];
		for($i=0;$i<count($arrayFields);$i++) {
			mysql_query("UPDATE booking_fields_types SET reservation_field_type='".$arrayTypes[$i]."' WHERE reservation_field_name='".$arrayFields[$i]."'");
		}
	}
	
	/***METATAGS SECTION***/
	public function getPageTitle() {
		return stripslashes(setting::doSettingQuery('page_title'));
	}
	
	public function getMetatagTitle() {
		return stripslashes(setting::doSettingQuery('metatag_title'));
	}
	
	public function getMetatagDescription() {
		return stripslashes(setting::doSettingQuery('metatag_description'));
	}
	
	public function getMetatagKeywords() {
		return stripslashes(setting::doSettingQuery('metatag_keywords'));
	}
	
	public function updateMetatags() {
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["page_title"])."'
					 WHERE config_name='page_title'");
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["metatag_title"])."'
					 WHERE config_name='metatag_title'");
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["metatag_description"])."'
					 WHERE config_name='metatag_description'");
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["metatag_keywords"])."'
					 WHERE config_name='metatag_keywords'");
		
	}

	/****styles section****/
	
	public function getMonthContainerBg() {
		return stripslashes(setting::doSettingQuery('month_container_bg'));
	}
	
	public function getMonthNameColor() {
		return stripslashes(setting::doSettingQuery('month_name_color'));
	}
	
	public function getYearNameColor() {
		return stripslashes(setting::doSettingQuery('year_name_color'));
	}
	
	public function getDayNamesColor() {
		return stripslashes(setting::doSettingQuery('day_names_color'));
	}
		
	public function getDayGreyBg() {
		return stripslashes(setting::doSettingQuery('day_grey_bg'));
	}
	
	public function getDayWhiteBg() {
		return stripslashes(setting::doSettingQuery('day_white_bg'));
	}
	
	public function getDayWhiteBgHover() {
		return stripslashes(setting::doSettingQuery('day_white_bg_hover'));
	}
	
	public function getDayBlackBg() {
		return stripslashes(setting::doSettingQuery('day_black_bg'));
	}
	
	public function getDayBlackBgHover() {
		return stripslashes(setting::doSettingQuery('day_black_bg_hover'));
	}
	
	public function getDayWhiteLine1DisabledColor() {
		return stripslashes(setting::doSettingQuery('day_white_line1_disabled_color'));
	}
	
	public function getDayBlackLine1Color() {
		return stripslashes(setting::doSettingQuery('day_black_line1_color'));
	}
	
	public function getDayBlackLine1ColorHover() {
		return stripslashes(setting::doSettingQuery('day_black_line1_color_hover'));
	}
	
	public function getDayBlackLine2Color() {
		return stripslashes(setting::doSettingQuery('day_black_line2_color'));
	}
	
	public function getDayWhiteLine2DisabledColor() {
		return stripslashes(setting::doSettingQuery('day_white_line2_disabled_color'));
	}
	
	public function getDayBlackLine2ColorHover() {
		return stripslashes(setting::doSettingQuery('day_black_line2_color_hover'));
	}
	
	public function getDayWhiteLine1Color() {
		return stripslashes(setting::doSettingQuery('day_white_line1_color'));
	}
	
	public function getDayWhiteLine1ColorHover() {
		return stripslashes(setting::doSettingQuery('day_white_line1_color_hover'));
	}
	
	public function getDayWhiteLine2Color() {
		return stripslashes(setting::doSettingQuery('day_white_line2_color'));
	}
	
	public function getDayWhiteLine2ColorHover() {
		return stripslashes(setting::doSettingQuery('day_white_line2_color_hover'));
	}
	
	public function getFormBg() {
		return stripslashes(setting::doSettingQuery('form_bg'));
	}
	
	public function getFormColor() {
		return stripslashes(setting::doSettingQuery('form_color'));
	}
	
	public function getFieldInputBg() {
		return stripslashes(setting::doSettingQuery('field_input_bg'));
	}
	
	public function getFieldInputColor() {
		return stripslashes(setting::doSettingQuery('field_input_color'));
	}
	
	public function getRecaptchaStyle() {
		return stripslashes(setting::doSettingQuery('recaptcha_style'));
	}
	
	public function getDayRedBg() {
		return stripslashes(setting::doSettingQuery('day_red_bg'));
	}
	
	public function getDayRedLine1Color() {
		return stripslashes(setting::doSettingQuery('day_red_line1_color'));
	}
	
	public function getDayRedLine2Color() {
		return stripslashes(setting::doSettingQuery('day_red_line2_color'));
	}
	
	public function getDayWhiteBgDisabled() {
		return stripslashes(setting::doSettingQuery('day_white_bg_disabled'));
	}
	
	public function getMonthNavigationButtonBg() {
		return stripslashes(setting::doSettingQuery('month_navigation_button_bg'));
	}
	
	public function getMonthNavigationButtonBgHover() {
		return stripslashes(setting::doSettingQuery('month_navigation_button_bg_hover'));
	}
	
	public function getBookNowButtonBg() {
		return stripslashes(setting::doSettingQuery('book_now_button_bg'));
	}
	
	public function getBookNowButtonBgHover() {
		return stripslashes(setting::doSettingQuery('book_now_button_bg_hover'));
	}
	
	public function getBookNowButtonColor() {
		return stripslashes(setting::doSettingQuery('book_now_button_color'));
	}
	
	public function getBookNowButtonColorHover() {
		return stripslashes(setting::doSettingQuery('book_now_button_color_hover'));
	}
	
	public function getClearButtonBg() {
		return stripslashes(setting::doSettingQuery('clear_button_bg'));
	}
	
	public function getClearButtonBgHover() {
		return stripslashes(setting::doSettingQuery('clear_button_bg_hover'));
	}
	
	public function getClearButtonColor() {
		return stripslashes(setting::doSettingQuery('clear_button_color'));
	}
	
	public function getClearButtonColorHover() {
		return stripslashes(setting::doSettingQuery('clear_button_color_hover'));
	}
	
	public function getFormCalendarNameColor() {
		return stripslashes(setting::doSettingQuery('form_calendar_name_color'));
	}
	
	
	public function updateStyles() {
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["month_container_bg"])."'
					 WHERE config_name='month_container_bg'");
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["month_name_color"])."'
					 WHERE config_name='month_name_color'");
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["year_name_color"])."'
					 WHERE config_name='year_name_color'");
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["day_names_color"])."'
					 WHERE config_name='day_names_color'");
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["day_grey_bg"])."'
					 WHERE config_name='day_grey_bg'");
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["day_white_bg"])."'
					 WHERE config_name='day_white_bg'");
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["day_white_bg_hover"])."'
					 WHERE config_name='day_white_bg_hover'");
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["day_black_bg"])."'
					 WHERE config_name='day_black_bg'");
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["day_black_bg_hover"])."'
					 WHERE config_name='day_black_bg_hover'");
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["day_white_line1_disabled_color"])."'
					 WHERE config_name='day_white_line1_disabled_color'");
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["day_black_line1_color"])."'
					 WHERE config_name='day_black_line1_color'");
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["day_black_line1_color_hover"])."'
					 WHERE config_name='day_black_line1_color_hover'");
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["day_black_line2_color"])."'
					 WHERE config_name='day_black_line2_color'");
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["day_white_line2_disabled_color"])."'
					 WHERE config_name='day_white_line2_disabled_color'");
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["day_black_line2_color_hover"])."'
					 WHERE config_name='day_black_line2_color_hover'");
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["day_white_line1_color"])."'
					 WHERE config_name='day_white_line1_color'");
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["day_white_line1_color_hover"])."'
					 WHERE config_name='day_white_line1_color_hover'");
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["day_white_line2_color"])."'
					 WHERE config_name='day_white_line2_color'");
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["day_white_line2_color_hover"])."'
					 WHERE config_name='day_white_line2_color_hover'");
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["form_bg"])."'
					 WHERE config_name='form_bg'");
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["form_color"])."'
					 WHERE config_name='form_color'");
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["field_input_bg"])."'
					 WHERE config_name='field_input_bg'");
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["field_input_color"])."'
					 WHERE config_name='field_input_color'");
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["recaptcha_style"])."'
					 WHERE config_name='recaptcha_style'");
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["day_red_bg"])."'
					 WHERE config_name='day_red_bg'");
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["day_red_line1_color"])."'
					 WHERE config_name='day_red_line1_color'");
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["day_red_line2_color"])."'
					 WHERE config_name='day_red_line2_color'");
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["day_white_bg_disabled"])."'
					 WHERE config_name='day_white_bg_disabled'");
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["month_navigation_button_bg"])."'
					 WHERE config_name='month_navigation_button_bg'");
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["month_navigation_button_bg_hover"])."'
					 WHERE config_name='month_navigation_button_bg_hover'");
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["book_now_button_bg"])."'
					 WHERE config_name='book_now_button_bg'");
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["book_now_button_bg_hover"])."'
					 WHERE config_name='book_now_button_bg_hover'");
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["book_now_button_color"])."'
					 WHERE config_name='book_now_button_color'");
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["book_now_button_color_hover"])."'
					 WHERE config_name='book_now_button_color_hover'");
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["clear_button_bg"])."'
					 WHERE config_name='clear_button_bg'");
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["clear_button_bg_hover"])."'
					 WHERE config_name='clear_button_bg_hover'");
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["clear_button_color"])."'
					 WHERE config_name='clear_button_color'");
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["clear_button_color_hover"])."'
					 WHERE config_name='clear_button_color_hover'");
		mysql_query("UPDATE booking_config
					 SET config_value='".mysql_real_escape_string($_POST["form_calendar_name_color"])."'
					 WHERE config_name='form_calendar_name_color'");
	}
}

?>
