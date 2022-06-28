function validateManageRateListMaster(form)
{
	
	var rateListName	= form.rateListName.value;	
	var startDate		= form.startDate.value;
	var addMode		= document.getElementById("hidAddMode").value;
	
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
	
	if (addMode!="") {
		var selPage = form.selPage.value;
		if (selPage=="" ) {
			alert("Please select a Function.");
			form.selPage.focus();
			return false;
		}
	}	

	if (!confirmSave()) return false;
	else return true;	
}