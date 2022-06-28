function validateAddDepartment(form)
{	
	var name	=	form.name.value;
	var description	=	form.description.value;
	var type	=	form.type.value;
	
	if (name=="") {
		alert("Please enter a name.");
		form.name.focus();
		return false;
	}

	/*if (description=="") {
		alert("Please enter a description");
		form.description.focus();
		return false;
	}
	*/

	if (type=="") {
		alert("Please enter a type.");
		form.type.focus();
		return false;
	}
	
	if (!confirmSave()) return false;
	else return true;
}