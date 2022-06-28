function validateRMTestData(form)
{
	
	var unit	=	form.unit.value;
	var rmLotId	=	form.rmLotId.value;
	var rmTestName	=	form.rmTestName.value;
	var dateOfTesting	=	form.dateOfTesting.value;
	var result	=	form.result.value;
	var selCompanyName =	form.selCompanyName.value;
	
	
	if (selCompanyName=="") {
		alert("Please select Company Name.");
		form.selCompanyName.focus();
		return false;
	}
	if (unit=="") {
		alert("Please select unit.");
		form.unit.focus();
		return false;
	}
	if (rmLotId=="") {
		alert("Please select rmLotId.");
		form.rmLotId.focus();
		return false;
	}
	if (rmTestName=="") {
		alert("Please select rmTestName.");
		form.rmTestName.focus();
		return false;
	}
	if (dateOfTesting=="") {
		alert("Please select dateOfTesting.");
		form.dateOfTesting.focus();
		return false;
	}
	if (result=="") {
		alert("Please enter result.");
		form.result.focus();
		return false;
	}

	
	
	if (!confirmSave()) return false;
	return true;

}
