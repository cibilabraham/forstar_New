function validateProductionPlanningReport()
{
	var stockFrom = document.getElementById("dateFrom").value;
	var stockTo   = document.getElementById("dateTill").value;	
	
	if (stockFrom=="") {
		alert("Please select From Date");
		document.getElementById("dateFrom").focus();
		return false;
	}
		
	if (stockTo=="") {
		alert("Please select Till Date");
		document.getElementById("dateTill").focus();
		return false;
	}
	return true;
}