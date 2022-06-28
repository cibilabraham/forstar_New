function validateAddFreezingStage(form)
{
	var stage	= form.stage.value;
	var yield	= form.yield.value;
		
	if (stage=="") {
		alert("Please enter a RM Stage.");
		form.stage.focus();
		return false;
	}
	
	if (yield=="") {
		alert("Please enter yield in percentage.");
		form.yield.focus();
		return false;
	}

	if (!checkDigit(yield)) {
		alert("Please enter a valid number.");
		return false;
	}
		
	if (!confirmSave()) return false;
	else return true;
}

