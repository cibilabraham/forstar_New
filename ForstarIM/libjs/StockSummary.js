function validateStkSummarySearch(form)
{
	var stockFrom = form.stockFrom.value;
	var stockTo   = form.stockTo.value;
	var selStock = form.selStock.value;

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

	if (selStock=="") {
		alert("Please select a Stock");
		form.selStock.focus();
		return false;
	}


// 	if (!confirmContinue()) {
// 		return false;
// 	}
	return true;
}