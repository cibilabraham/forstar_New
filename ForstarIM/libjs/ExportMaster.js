function validateAddExport(form)
{
    var name = form.exportName.value;
	
	if(name == "")
	{
		alert("Please Enter Export Name");
		form.exportName.focus();
		return false;
	}
	
	if (!confirmSave()) {
		return false;
	}
	
	return true;
}