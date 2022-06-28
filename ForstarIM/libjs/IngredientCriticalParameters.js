function validateIngCriticalParameters(form)
{
	
	var name	=	form.name.value;
	var entryType=form.entryType.value;
	if (name=="") {
		alert("Please enter a Name.");
		form.name.focus();
		return false;
	}

	if (entryType=="") {
		alert("Please enter a Entry Type.");
		form.entryType.focus();
		return false;
	}
	
	if (!confirmSave()) {
		return false;
	}
	return true;
}





