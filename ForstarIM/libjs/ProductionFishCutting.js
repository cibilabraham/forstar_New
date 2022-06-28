function validateProductionFishCutting(form)
{
	/*var fishName = form.fishName.value;
	var fishCode = form.fishCode.value;
	*/
	var ingMainCategory	= form.ingMainCategory.value;
	var selFish  = form.selFish.value;	
	var costPerKg = form.costPerKg.value;
	var fcRateList = form.fcRateList.value;
	var mode	= document.getElementById("hidMode").value;

	/*
	if (fishName=="") {
		alert("Please enter a Fish name.");
		form.fishName.focus();
		return false;
	}
	
	if (fishCode=="") {
		alert("Please enter a Code.");
		form.fishCode.focus();
		return false;
	}
	*/

	if (ingMainCategory=="") {
		alert("Please select a Category.");
		form.ingMainCategory.focus();
		return false;
	}

	if (selFish=="") {
		alert("Please select a Fish.");
		form.selFish.focus();
		return false;
	}

	if (costPerKg=="") {
		alert("Please enter a cost.");
		form.costPerKg.focus();
		return false;
	}
	/*
	if (fcRateList=="") {
		alert("Please select a Rate List.");
		form.fcRateList.focus();
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
	
	if (!confirmSave()) return false;
	return true;
}