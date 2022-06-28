function validateAddHarvestingEquipmentMaster(form)
{		
	var equipmentName	= form.equipmentName.value;
	var tarWt	= form.tarWt.value;
	var equipmentType	= form.equipmentType.value;
		
	if (equipmentName=="") {
		alert("Please enter a equipment Name.");
		form.equipmentName.focus();
		return false;
	}

	if (tarWt=="") {
		alert("Please enter tarWt.");
		form.tarWt.focus();
		return false;
	}
	
	if (equipmentType=="") {
		alert("Please select equipmentType.");
		form.equipmentType.focus();
		return false;
	}
	
	if (!confirmSave()) return false;	
	return true;
}