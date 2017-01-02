<?php

class lang {
	
	private function doLanguageQuery($label) {
		$languageQry = mysql_query("SELECT * FROM booking_texts WHERE text_label='".mysql_real_escape_string($label)."'");
		return stripslashes(mysql_result($languageQry,0,'text_value'));
	}
	
	public function getLabel($label) {
		return lang::doLanguageQuery($label);
	}
	
	public function updateTexts() {
		$arrayLabels=$_POST["text_label"];
		$arrayTexts=$_POST["text_value"];
		for($i=0;$i<count($arrayLabels);$i++) {
			mysql_query("UPDATE booking_texts
						 SET text_value='".mysql_real_escape_string($arrayTexts[$i])."'
						 WHERE text_label='".$arrayLabels[$i]."'");
		}
	
	}
	
	public function importLang() {
		$result = 0;
		$upload_dir = "../lang";
		if(isset($_FILES["admin_file"]["tmp_name"]) && $_FILES["admin_file"]["tmp_name"] != '') {
			if(move_uploaded_file($_FILES["admin_file"]["tmp_name"], $upload_dir . "/".str_replace(" ","",$_FILES["admin_file"]["name"]))) {
				//include the file
				$arrlang = Array();
				include $upload_dir . "/".str_replace(" ","",$_FILES["admin_file"]["name"]);
				$arrlang = $lang;
				foreach($arrlang as $key => $val) {
					mysql_query("UPDATE booking_texts SET text_value = '".mysql_real_escape_string($val)."' WHERE text_label = '".$key."'"); 
				}
				$result = 1;
				//delete file
				unlink($upload_dir . "/".str_replace(" ","",$_FILES["admin_file"]["name"]));
			}
			
		}
		if(isset($_FILES["public_file"]["tmp_name"]) && $_FILES["public_file"]["tmp_name"] != '') {
			if(move_uploaded_file($_FILES["public_file"]["tmp_name"], $upload_dir. "/".str_replace(" ","",$_FILES["public_file"]["name"]))) {
				//include the file
				$arrlang = Array();
				include $upload_dir . "/".str_replace(" ","",$_FILES["public_file"]["name"]);
				$arrlang = $lang;
				foreach($arrlang as $key => $val) {
					mysql_query("UPDATE booking_texts SET text_value = '".mysql_real_escape_string($val)."' WHERE text_label = '".$key."'"); 
				}
				$result = 1;
				//delete file
				unlink($upload_dir . "/".str_replace(" ","",$_FILES["public_file"]["name"]));
			}
			
		}
		return $result;
	}
	

}

?>
