function validateAddArea(form)
{
	
	var areaUnitName	=	form.areaUnitName.value;
	var baseUnitReference	=	form.baseUnitReference.value;
	var values	=	form.values.value;
	
	if (areaUnitName=="") {
		alert("Please enter a unit Name.");
		form.areaUnitName.focus();
		return false;
	}

	if (baseUnitReference=="") {
		alert("Please select unit.");
		form.baseUnitReference.focus();
		return false;
	}
	
	if (values=="") {
		alert("Please enter value.");
		form.values.focus();
		return false;
	}
	
	if (!confirmSave()) return false;
	return true;

}





