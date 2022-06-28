function validateUnitTransfer(form)
{
	
	var rmlotId	=	form.rmlotId.value;
	var unitName	=	form.unitName.value;
	var processType	=	form.processType.value;
	var lotId	=	form.lotId.value;
	
	
	if (rmlotId=="") {
		alert("Please select rmlotId.");
		form.rmlotId.focus();
		return false;
	}
	if (unitName=="") {
		alert("Please select unitName.");
		form.unitName.focus();
		return false;
	}
	if (processType=="") {
		alert("Please select processType.");
		form.processType.focus();
		return false;
	}
	if (lotId=="") {
		alert("Please generate lotId.");
		form.lotId.focus();
		return false;
	}
	

	
	
	if (!confirmSave()) return false;
	return true;

}
