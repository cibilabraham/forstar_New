function validateAddHarvestingEquipmentType(form)
{		
	var equipmentType	= form.equipmentType.value;
	
		
	if (equipmentType=="") {
		alert("Please enter a equipment Type.");
		form.equipmentType.focus();
		return false;
	}

	
	
	if (!confirmSave()) return false;	
	return true;
}