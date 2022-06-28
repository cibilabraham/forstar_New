function validateAddDesignation(form)
{
	
	var designation	=	form.designation.value;
	
	
	if (designation=="") {
		alert("Please enter a designation.");
		form.designation.focus();
		return false;
	}

	
	
	if (!confirmSave()) return false;
	return true;

}





