function validateStkPurchaseRejectSearch(form)
{
	var stockFrom = form.stockFrom.value;
	var stockTo   = form.stockTo.value;
	var selSupplier = form.selSupplier.value;

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

	if (selSupplier=="") {
		alert("Please select a Supplier");
		form.selSupplier.focus();
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