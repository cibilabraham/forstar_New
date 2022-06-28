function validateProductionManPower(form)
{
	var manPowerName = form.manPowerName.value;
	var manPowerType = form.manPowerType.value;
	var manPowerUnit = form.manPowerUnit.value;
	var puCost	 = form.puCost.value;
	var totCost	 = form.totCost.value;	
	var manPowerRateList 	= form.manPowerRateList.value;
	var mode		= document.getElementById("hidMode").value;

	if (manPowerName=="") {
		alert("Please enter a name.");
		form.manPowerName.focus();
		return false;
	}
	
	if (manPowerType=="") {
		alert("Please select a type.");
		form.manPowerType.focus();
		return false;
	}
	
	if (manPowerUnit=="" && manPowerType=='F') {
		alert("Please enter a unit.");
		form.manPowerUnit.focus();
		return false;
	}

	if (puCost=="") {
		alert("Please enter a unit Cost.");
		form.puCost.focus();
		return false;
	}

	/*
	if (manPowerRateList=="") {
		alert("Please select a Rate List.");
		form.manPowerRateList.focus();
		return false;
	}
	*/

	if (mode==2) {
		var confirmRateListMsg= confirm("Do you want to save this to new Rate list?");
		if (confirmRateListMsg) {		
			alert("Please create a new Rate list and then update the selected record.");
			return false;
		}		
	}
	
	if (!confirmSave()) {
		return false;
	}
	return true;
}

	// Find man Power total Cost
	function productionManPowerTotalCost(unit, puCost, tCost)
	{
		var totalCost 	 = 	0;
		var purchaseCost = 	0;
		var totalNumber	 =	0;
			
		if (document.getElementById(unit).value) totalNumber	=	parseFloat(document.getElementById(unit).value);
		if (document.getElementById(puCost).value) purchaseCost	=	parseFloat(document.getElementById(puCost).value);
	
		totalCost = totalNumber * purchaseCost;	
		if (!isNaN(totalCost)) document.getElementById(tCost).value = number_format(totalCost,2,'.','');
	}

	function changeManPowerType()
	{
		var manPowerType = document.getElementById("manPowerType").value
		if (manPowerType=='V' && document.getElementById("manPowerUnit").value=="") {
			document.getElementById("manPowerUnit").value= 1;
		}
	}
