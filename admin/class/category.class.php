<?php

class category {
	private static $category_id;
	private static $categoryQry;
	
	public function setCategory($id) {
		
		$categoryQry = mysql_query("SELECT * FROM booking_categories WHERE category_id = '".mysql_real_escape_string($id)."'");
		
		$categoryRow = mysql_fetch_array($categoryQry);
		category::$categoryQry = $categoryRow;
		category::$category_id=$categoryRow["category_id"];
	}
	
	public function getCategoryId() {
		
		return category::$category_id;
	}
	
	public function getCategoryName() {
		
		return stripslashes(category::$categoryQry["category_name"]);
	}
	
	public function getCategoryActive() {
		
		return category::$categoryQry["category_active"];
	}
	
	public function getCategoryOrder() {
		
		return category::$categoryQry["category_order"];
	}
	
	public function publishCategories($listIds) {
		
		mysql_query("UPDATE booking_categories SET category_active = 1 WHERE category_id IN (".$listIds.")");
	}
	
	public function unpublishCategories($listIds) {
		
		mysql_query("UPDATE booking_categories SET category_active = 0 WHERE category_id IN (".$listIds.")");
	}
	
	public function delCategories($listIds) {
		
		mysql_query("DELETE FROM booking_categories WHERE category_id IN (".$listIds.")");
		$calendarsQry = mysql_query("SELECT * FROM booking_calendars WHERE category_id IN (".$listIds.")");
		while($calendarRow = mysql_fetch_array($calendarsQry)) {
			mysql_query("DELETE FROM booking_calendars WHERE calendar_id =".$calendarRow["calendar_id"]);
			//delete holidays
			mysql_query("DELETE FROM booking_holidays WHERE calendar_id =".$calendarRow["calendar_id"]);
			//check for reservations, if any disable slots, otherwise del slots
			$slotsQry=mysql_query("SELECT * FROM booking_slots WHERE calendar_id ='".mysql_real_escape_string($calendarRow["calendar_id"])."'");
			while($slotRow = mysql_fetch_array($slotsQry)) {
				$numRes=mysql_num_rows(mysql_query("SELECT * FROM booking_reservation WHERE slot_id ='".mysql_real_escape_string($slotRow["slot_id"])."'"));
				if($numRes>0) {
					mysql_query("UPDATE booking_slots SET slot_active = 0 WHERE slot_id  =".$slotRow["slot_id"]);
				} else {
					mysql_query("DELETE FROM booking_slots  WHERE slot_id =".$slotRow["slot_id"]);
				}
				
			}
		}
		
		
	}
	
	public function addCategory($name) {
		
		$newOrder = 0;
		//check order of last calendar
		$calOrderQry = mysql_query("SELECT category_order as max FROM booking_categories ORDER BY category_order DESC LIMIT 1");
		if(mysql_num_rows($calOrderQry)>0) {
			$newOrder=mysql_result($calOrderQry,0,'max')+1;
		}
		mysql_query("INSERT INTO booking_categories (category_name,category_order,category_active) VALUES('".mysql_real_escape_string($name)."',".$newOrder.",0)");
		$category_id=mysql_insert_id();
		return $category_id;
	}
	
	
	public function getCategoryRecordcount() {
		
		return mysql_num_rows(mysql_query("SELECT * FROM booking_categories"));
	}
	
	public function setDefaultCategory($category_id) {
		
		mysql_query("UPDATE booking_categories SET category_order = 0, category_active = 1 WHERE category_id=".$category_id);
		mysql_query("UPDATE booking_categories SET category_order = category_order +1 WHERE category_id <> ".$category_id);
	}
	
	

}

?>