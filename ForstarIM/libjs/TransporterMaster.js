function validateTransporterMaster(form)
{	
	var name	= form.name.value;
	
	if (name=="") {
		alert("Please enter a Transporter Name.");
		form.name.focus();
		return false;
	}	
	if (!confirmSave()) {
		return false;
	}
	return true;
}





