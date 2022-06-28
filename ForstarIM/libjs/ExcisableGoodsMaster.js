function validateExcisableGoodsMaster(form)
{	
	var egName 	= $("#egName").val();
	
	if (egName=="") {
		alert("Please enter name of excisable goods.");
		$("#egName").focus();
		return false;		
	}

	if (!confirmSave()) return false;	
	return true;
}