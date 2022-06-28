function validateAddPHTCertificate(form)
{
	var PHTCertificateNo	=	form.PHTCertificateNo.value;
	var species	=	form.species.value;
	var supplierGroup	=	form.supplierGroup.value;
	var supplier	=	form.supplier.value;
	var pondName	=	form.pondName.value;
	var dateOfIssue	=	form.dateOfIssue.value;
	var dateOfExpiry	=	form.dateOfExpiry.value;
	var receivedDate	=	form.receivedDate.value;
		
	
	if (PHTCertificateNo=="") {
		alert("Please enter a PHTCertificateNo .");
		form.PHTCertificateNo.focus();
		return false;
	}
	
	if (species=="") {
		alert("Please select a species .");
		form.species.focus();
		return false;
	}
	
	if (supplierGroup=="") {
		alert("Please select a supplierGroup .");
		form.supplierGroup.focus();
		return false;
	}
	
	if (supplier=="") {
		alert("Please select a supplier .");
		form.supplier.focus();
		return false;
	}
	
	if (pondName=="") {
		alert("Please select a pond Name .");
		form.pondName.focus();
		return false;
	}
	
	if (dateOfIssue=="") {
		alert("Please select Issue Date.");
		form.dateOfIssue.focus();
		return false;
	}
	if (dateOfExpiry=="") {
		alert("Please select Expiry Date .");
		form.dateOfExpiry.focus();
		return false;
	}
	if (receivedDate=="") {
		alert("Please select received Date .");
		form.receivedDate.focus();
		return false;
	}
}