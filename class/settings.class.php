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
	
	public function getMetatagTitle() {
		return stripslashes(setting::doSettingQuery('metatag_title'));
	}
	
	public function getMetatagDescription() {
		return stripslashes(setting::doSettingQuery('metatag_description'));
	}
	
	public function getMetatagKeywords() {
		return stripslashes(setting::doSettingQuery('metatag_keywords'));
	}
	
	public function getPageTitle() {
		return stripslashes(setting::doSettingQuery('page_title'));
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
	
	public function getShowSlotsSeats() {
		return setting::doSettingQuery('show_slots_seats');
	}	
	
	public function getShowFirstFilledMonth() {
		return setting::doSettingQuery('show_first_filled_month');
	}
	
	public function getShowCategorySelection() {
		return setting::doSettingQuery('show_category_selection');
	}
	
	public function getShowCalendarSelection() {
		return setting::doSettingQuery('show_calendar_selection');
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
		return str_replace(",",".",setting::doSettingQuery('paypal_price'));
	}
	
	public function getPaypalCurrency() {
		return strtoupper(setting::doSettingQuery('paypal_currency'));
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
	

}

?>
