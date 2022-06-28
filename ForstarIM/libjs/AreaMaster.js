function validateAreaMaster(form)
{		
	var areaName	= form.areaName.value;
	var selCity	= form.selCity.value;
		
	if (areaName=="") {
		alert("Please enter a Area Name.");
		form.areaName.focus();
		return false;
	}

	if (selCity=="") {
		alert("Please select a City.");
		form.selCity.focus();
		return false;
	}
	
	if (!confirmSave()) return false;	
	return true;
}