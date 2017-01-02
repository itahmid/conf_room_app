<?php

class lang {
	
	private function doLanguageQuery($label) {
		$languageQry = mysql_query("SELECT * FROM booking_texts WHERE text_label='".mysql_real_escape_string($label)."'");
		return stripslashes(mysql_result($languageQry,0,'text_value'));
	}
	
	public function getLabel($label) {
		return stripslashes(lang::doLanguageQuery($label));
	}
	
	

}

?>
