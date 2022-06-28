function validateAddMCPacking(form)
{
	var code		=	form.code.value;
	var numPacks	=	form.numPacks.value;	
	
	if (code=="") {
		alert("Please enter a MC Packing Code.");
		form.code.focus();
		return false;
	}

	if (numPacks=="") {
		alert("Please enter Number of Packs.");
		form.numPacks.focus();
		return false;
	}
	
	if(!isDigit(numPacks)){
		alert("Please enter a number.");
		form.numPacks.focus();
		return false;
	}
	
	if (!confirmSave()) return false;
	else return true;	
}