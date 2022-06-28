function validateAssignSchemeMaster(form)
{	
	var selScheme	= form.selScheme.value;	
	var schemeCategory	= form.schemeCategory.value;	// Scheme  For
	var selectFrom  = form.selectFrom.value;
	var selectTill  = form.selectTill.value;
	
	if (selScheme=="") {
		alert("Please select a Scheme.");
		form.selScheme.focus();
		return false;
	}

	if (schemeCategory=="") {
		alert("Please select a Scheme Category.");
		form.schemeCategory.focus();
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