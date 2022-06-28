function validateAddPackagingStructure(form)
{
	var structureName	=	form.structureName.value;
	
	if (structureName=="") {
		alert("Please enter a Packaging Structure Name.");
		form.structureName.focus();
		return false;
	}
			
	if (!confirmSave()) return false;
	else return true;	
}

