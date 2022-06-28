function validateTransporterStatus(form)
{	
	var selTransporter	= form.selTransporter.value;		
	var selectFrom  = form.selectFrom.value;
	var selectTill  = form.selectTill.value;
	
	if (selTransporter=="") {
		alert("Please select a Transporter.");
		form.selTransporter.focus();
		return false;
	}
	
	if (selectFrom=="") {
		alert("Please select a From date.");
		form.selectFrom.focus();
		return false;
	}

	if (selectTill=="") {
		alert("Please select a To date.");
		form.selectTill.focus();
		return false;
	}	
		
	if (!confirmSave()) {
		return false;
	}
	return true;
}