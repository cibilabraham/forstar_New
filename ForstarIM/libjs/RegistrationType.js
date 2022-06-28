function validateAddRegistrationType(form)
{
	
	var registrationType	=	form.registrationType.value;
	var displayCode	=	form.displayCode.value;
	
	if (registrationType=="") {
		alert("Please enter a Registration Type Name.");
		form.registrationType.focus();
		return false;
	}

	if (displayCode=="") {
		alert("Please enter display Code.");
		form.displayCode.focus();
		return false;
	}
	
	if (!confirmSave()) return false;
	return true;

}





