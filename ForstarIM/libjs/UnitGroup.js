function validateUnitGroup(form)
{
	var groupName	=	form.groupName.value;
	
	if (groupName=="") {
		alert("Please enter a Unit Group.");
		form.groupName.focus();
		return false;
	}
	
	if (!confirmSave()) return false;
	return true;
}





