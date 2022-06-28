function validateAddDepartment(form)
{
	
	var department	=	form.departmentName.value;
	var incharge	=	form.incharge.value;
	
	if (department=="") {
		alert("Please enter a Department Name.");
		form.departmentName.focus();
		return false;
	}

	if (incharge=="") {
		alert("Please enter In-Charge Name.");
		form.incharge.focus();
		return false;
	}
	
	if (!confirmSave()) return false;
	return true;

}





