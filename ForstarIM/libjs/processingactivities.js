function validateProcessingActivity(form)
{	
	var name	= form.name.value;
	var selSubModule = document.getElementById("selSubModule").value;
	
	if (name=="") {
		alert("Please enter a Processing Activity Name.");
		form.name.focus();
		return false;
	}

	if (selSubModule=="") {
		alert("Please select atleast one Sub-Module.");
		form.selSubModule.focus();
		return false;
	}
	
	if (!confirmSave()) return false;	
	return true;
}