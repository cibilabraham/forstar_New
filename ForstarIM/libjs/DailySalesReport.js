function validateDailySalesReportSearch(form)
{
	var salesFrom = form.salesFrom.value;
	var salesTo   = form.salesTo.value;
	var selSalesStaff = form.selSalesStaff.value;

	if (salesFrom=="") {
		alert("Please select From Date");
		form.salesFrom.focus();
		return false;
	}
		
	if (salesTo=="") {
		alert("Please select Till Date");
		form.salesTo.focus();
		return false;
	}

	if (selSalesStaff=="") {
		alert("Please select a Sales Staff");
		form.selSalesStaff.focus();
		return false;
	}

	return true;
}

