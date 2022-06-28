function validateReport(form)
{
	
	var companyName	=	form.companyName.value;
	var reportName	=	form.reportName.value;
	var transcationName	=	form.transcationName.value;
	var reportField	=	form.reportField.value;
	
	
	
	if (companyName=="") {
		alert("Please select companyName.");
		form.companyName.focus();
		return false;
	}
	if (reportName=="") {
		alert("Please enter reportName.");
		form.reportName.focus();
		return false;
	}
	if (transcationName=="") {
		alert("Please select transcation Name.");
		form.transcationName.focus();
		return false;
	}
	if (reportField=="") {
		alert("Please select reportField.");
		form.reportField.focus();
		return false;
	}
	

	
	
	

}
