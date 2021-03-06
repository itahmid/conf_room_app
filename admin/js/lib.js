function Trim(strValue)
{	
	return LTrim(RTrim(strValue));
}

function LTrim(strValue)
{
	var LTRIMrgExp = /^\s */;
	return strValue.replace(LTRIMrgExp, '');
}

function RTrim(strValue)
{
	var RTRIMrgExp = /\s *$/;
	return strValue.replace(RTRIMrgExp, '');
}

function isNumeric(formField) 
{
	
	//all numbers, with decimal and not
	var myregExp = /^\d*[0-9](|.\d*[0-9]|,\d*[0-9])?$/;
	if (!myregExp.test(formField.value)) 
	{
		return false;
	} 
	else 
	{
		return true;
    }
}
function isEmailAddr(email,required,label)
{
	var reEmail = /^.+\@.+\..+$/;
	if(Trim(email.value)=='' && required==true)
	{
		//alert(label);
		email.focus();
		return false;
	}
    else if(Trim(email.value)!='')
	{
	   	if(reEmail.test(email.value)==false)
		{
			//alert(label);
			email.focus();
			return false;
		}
		else
		{
			return true;	
		}
	}    
}



function selectCheckbox(form_name,check_name) {	
	var formObj = document.forms[form_name];	
	var boxes = formObj[check_name].length;
	var num = 0;
	if(formObj.selectAll.checked) {
		for (var i = 0; i < boxes; i++) {
			if (formObj[check_name][i].checked == false) {
				formObj[check_name][i].checked = true;
			}
		}
	} else {
		for (var i = 0; i < boxes; i++) {
			if (formObj[check_name][i].checked == true) {
				formObj[check_name][i].checked = false;
			}
		}
	}
	
}
function disableSelectAll(form_name,checked) {
	var formObj = document.forms[form_name];
	if(checked == false) {
		formObj.selectAll.checked=false;
	}
}
function checkBoxesSel(form_name,check_name,alert_text) {
	var formObj = document.forms[form_name];
	var checkboxesL = formObj[check_name].length;
	var message = "";
	
	for(var i=0; i< checkboxesL; i++) {
		if(formObj[check_name][i].checked) {
			message = message + "a";
		}
		
	}
	if(message == "") {
		alert(alert_text);
		return false;
	} else {
		return true;
	}
}

function delItems(form_name,check_name,operation,alert_text,no_items_alert) {
	var formObj = document.forms[form_name];
	if(checkBoxesSel(form_name,check_name,no_items_alert)) {
		if(confirm(alert_text)) {
			formObj.operation.value=operation;
			formObj.submit();
		}
	}
}

