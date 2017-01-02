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
	
	public function getCategoryRecordcount() {
		return mysql_num_rows(mysql_query("SELECT * FROM booking_categories"));
	}
	
	public function getDefaultCategory() {
		$categoryQry = mysql_query("SELECT * FROM booking_categories WHERE category_order = 0 AND category_active = 1");
		if(mysql_num_rows($categoryQry) > 0) {
			$categoryRow = mysql_fetch_array($categoryQry);
			$this->setCategory($categoryRow["category_id"]);
			return true;
		} else {
			return false;
		}
	}
	
	

}

?>