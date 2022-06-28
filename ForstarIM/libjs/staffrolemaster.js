function validateAddStaffRole(form)
{	
	var name	=	form.name.value;
	var description	=	form.description.value;
	
	if (name=="") {
		alert("Please enter a name.");
		form.name.focus();
		return false;
	}

	
	if (!confirmSave()) return false;
	else return true;
}