function validatePackingLabourCost(form)
{
	var itemName = form.itemName.value;
	var itemCode = form.itemCode.value;
	var costPerItem = form.costPerItem.value;
	//var plcRateList = form.plcRateList.value;
	var mode	= document.getElementById("hidMode").value;

	if (itemName=="") {
		alert("Please enter an Item name.");
		form.itemName.focus();
		return false;
	}
	
	if (itemCode=="") {
		alert("Please enter a Code.");
		form.itemCode.focus();
		return false;
	}
	
	if (costPerItem=="") {
		alert("Please enter a cost.");
		form.costPerItem.focus();
		return false;
	}	
	if (plcRateList=="") {
		alert("Please select a Rate List.");
		form.plcRateList.focus();
		return false;
	}

	if (mode==2) {
		var confirmRateListMsg= confirm("Do you want to save this to new Rate list?");
		if (confirmRateListMsg) {		
			alert("Please create a new Rate list and then update the selected record.");
			return false;
		}		
	}
	
	if (!confirmSave()) return false;
	return true;
}