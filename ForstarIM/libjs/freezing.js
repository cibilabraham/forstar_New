function validateAddFreezing(form)
{
	var name = form.name.value;

	if (name=="") {
		alert("Please enter a Freezing name.");
		form.name.focus();
		return false;
	}
			
	if (!confirmSave()) return false;
	else return true;	
}