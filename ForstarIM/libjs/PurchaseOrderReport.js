function validatePurchaseOrderReport(form)
{
	var stockFrom = form.stockFrom.value;
	var stockTo   = form.stockTo.value;
	//var selSupplier = form.selSupplier.value;

	if (stockFrom=="") {
		alert("Please select PO From Date");
		form.stockFrom.focus();
		return false;
	}
		
	if (stockTo=="") {
		alert("Please select PO Till Date");
		form.stockTo.focus();
		return false;
	}

// 	if (selSupplier=="") {
// 		alert("Please select a Supplier");
// 		form.selSupplier.focus();
// 		return false;
// 	}



// 	if (!confirmContinue()) {
// 		return false;
// 	}
	return true;
}