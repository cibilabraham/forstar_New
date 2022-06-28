function validatePhtMonitoring(form)
{
	
	var date	=	form.date.value;
	var rmLotId	=	form.rmLotId.value;
	var supplier	=	form.supplier.value;
	var supplierGroupName	=	form.supplierGroupName.value;
	var specious	=	form.specious.value;
	var supplyQty	=	form.supplyQty.value;
	var phtCertificateNo	=	form.phtCertificateNo.value;
	//var specious	=	form.specious.value;
	
	
	if (date=="") {
		alert("Please select date.");
		form.date.focus();
		return false;
	}
	if (rmLotId=="") {
		alert("Please select rmLotId.");
		form.rmLotId.focus();
		return false;
	}
	if (supplier=="") {
		alert("Please select supplier.");
		form.supplier.focus();
		return false;
	}
	if (supplierGroupName=="") {
		alert("Please select supplierGroupName.");
		form.supplierGroupName.focus();
		return false;
	}
	if (specious=="") {
		alert("Please display specious.");
		form.specious.focus();
		return false;
	}
	if (supplyQty=="") {
		alert("Please display supplyQty.");
		form.supplyQty.focus();
		return false;
	}
	if (phtCertificateNo=="") {
		alert("Please select phtCertificateNo.");
		form.phtCertificateNo.focus();
		return false;
	}
	

	
	
	if (!confirmSave()) return false;
	return true;

}