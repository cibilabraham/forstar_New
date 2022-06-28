function validateStockItemUnit(form)
{
	var unitGroup	= 	form.unitGroup.value;
	var unitName	=	form.unitName.value;
	
	if (unitGroup=="") {
		alert("Please select a Unit Group.");
		form.unitGroup.focus();
		return false;
	}

	if (unitName=="") {
		alert("Please enter a Unit Name.");
		form.unitName.focus();
		return false;
	}
	
	if (!confirmSave()) {
		return false;
	}
	return true;
}





