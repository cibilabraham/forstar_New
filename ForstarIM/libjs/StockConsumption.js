function validateStkConsumptionSearch(form)
{
	var stockFrom = form.stockFrom.value;
	var stockTo   = form.stockTo.value;
	var details   = form.details.checked;
	var summary  = form.summary.checked;

	if (stockFrom=="") {
		alert("Please select Stock From Date");
		form.stockFrom.focus();
		return false;
	}
		
	if (stockTo=="") {
		alert("Please select Stock Till Date");
		form.stockTo.focus();
		return false;
	}

	if (details=="" && summary=="") {
		alert("Please select atleast one Search option");
		return false;
	}

// 	if (!confirmContinue()) {
// 		return false;
// 	}
	return true;
}

function hideConsumptionSummaryOption()
{
	document.getElementById("summary").checked=false;
}


function hideConsumptionDetailedOption()
{
	document.getElementById("details").checked=false;
}