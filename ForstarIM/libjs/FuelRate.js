function validateFuelRate(form)
{
	
	var date	=	form.date.value;
	var fuelRate =  form.fuelRate.value;
	
	if (date=="") {
		alert("Please select a date.");
		form.date.focus();
		return false;
	}

	if (fuelRate=="") {
		alert("Please enter Fuel Rate");
		form.fuelRate.focus();
		return false;
	}
	
	if (!confirmSave()) {
		return false;
	}
	return true;
}





