function validateAddHarvestingChemicalMaster(form)
{
	
	var chemicalName	=	form.chemicalName.value;
	
	
	if (chemicalName=="") {
		alert("Please enter a chemical Name.");
		form.chemicalName.focus();
		return false;
	}

	
	
	if (!confirmSave()) return false;
	return true;

}





