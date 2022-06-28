function validateRtCounterMarginRateList(form)
{
	var rateListName	= form.rateListName.value;
	var startDate		= form.startDate.value;
	var addMode		= document.getElementById("hidAddMode").value;
	var rateListRecSize	= document.getElementById("rateListRecSize").value;
	var selRetailCounter	= form.selRetailCounter.value;
	if (rateListName=="" ) {
		alert("Please enter a Rate list Name.");
		form.rateListName.focus();
		return false;
	}
	if (startDate=="") {
		alert("Please select a date.");
		form.startDate.focus();
		return false;
	}

	if (selRetailCounter=="") {
		alert("Please select a Retail Counter.");
		form.selRetailCounter.focus();
		return false;
	}

	if (addMode!="" && rateListRecSize>0) {
		var copyRateList 	= document.getElementById("copyRateList").value;		
		if (copyRateList=="" && rateListRecSize>0) {
			var cMsg = "Latest Rate list is not selected. Do you wish to Continue?";
			if (!confirm(cMsg)) return false;			
		}
	}

	if (!confirmSave()) return false;
	else return true;	
}

