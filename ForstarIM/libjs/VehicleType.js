function validateAddVehicleType(form)
{
	
	var vehicleType	=	form.vehicleType.value;
	
	
	if (vehicleType=="") {
		alert("Please enter a Vehicle Type Name.");
		form.vehicleType.focus();
		return false;
	}

	
	
	if (!confirmSave()) return false;
	return true;

}





