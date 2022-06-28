function validateProductionMarketing(form)
{
	var mktgPositionName = form.mktgPositionName.value;
	var mktgActual = form.mktgActual.value;
	var mktgIdeal = form.mktgIdeal.value;
	var puCost	 = form.puCost.value;
	var totCost	 = form.totCost.value;	
	//var mcRateList	 = form.mcRateList.value;
	var mode	= document.getElementById("hidMode").value;

	if (mktgPositionName=="") {
		alert("Please enter a name.");
		form.mktgPositionName.focus();
		return false;
	}
	
	if (mktgActual=="") {
		alert("Please enter actual unit.");
		form.mktgActual.focus();
		return false;
	}
	
	if (mktgIdeal=="") {
		alert("Please enter Ideal unit.");
		form.mktgIdeal.focus();
		return false;
	}

	if (puCost=="") {
		alert("Please enter a Purchase Cost.");
		form.puCost.focus();
		return false;
	}

	/*
	if (mcRateList=="") {
		alert("Please select a Rate List.");
		form.mcRateList.focus();
		return false;
	}*/

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

// Find Marketing total Cost
function prodnMktgTotalCost(unit, puCost, tCost)
{
	var totalCost 	 = 	0;
	var purchaseCost = 	0;
	var totalNumber	 =	0;
	

	if (document.getElementById(unit).value)  totalNumber	=	parseFloat(document.getElementById(unit).value);
	if (document.getElementById(puCost).value) purchaseCost	=	parseFloat(document.getElementById(puCost).value);

	totalCost = totalNumber * purchaseCost;
	if (!isNaN(totalCost)) document.getElementById(tCost).value = number_format(totalCost,0,'','');
}

// Find Marketing total Cost
function prodnMktgAverageCost(unit, puCost, tCost)
{
	var totalCost 	 = 	0;
	var purchaseCost = 	0;
	var totalNumber	 =	0;
	

	if (document.getElementById(unit).value) totalNumber	=	parseFloat(document.getElementById(unit).value);
	if (document.getElementById(puCost).value) purchaseCost	=	parseFloat(document.getElementById(puCost).value);

	totalCost = totalNumber * purchaseCost;
	if (!isNaN(totalCost)) document.getElementById(tCost).value = number_format(totalCost,0,'','');
}
