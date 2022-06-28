function validateVarianceReport(form)
{
	
	var companyName	=	form.companyName.value;
	var selRMSupplierGroup	=	form.selRMSupplierGroup.value;
	var supplierName	=	form.supplierName.value;
	var pondName	=	form.pondName.value;
	var rmlotId	=	form.rmlotId.value;
	
	
	if (companyName=="") {
		alert("Please select companyName.");
		form.companyName.focus();
		return false;
	}
	if (selRMSupplierGroup=="") {
		alert("Please enter RM Supplier Group.");
		form.selRMSupplierGroup.focus();
		return false;
	}
	if (supplierName=="") {
		alert("Please select supplier Name.");
		form.supplierName.focus();
		return false;
	}
	if (pondName=="") {
		alert("Please select pond Name.");
		form.pondName.focus();
		return false;
	}
	if (rmlotId=="") {
		alert("Please select rm lotId.");
		form.rmlotId.focus();
		return false;
	}
	

	
	
	

}
