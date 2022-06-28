function validateAddQuality(form)
{	
	var qualityCode	=	form.qualityCode.value;
	var qualityName	=	form.qualityName.value;
	
	if (qualityCode=="" ) {
		alert("Please enter a quality code.");
		form.qualityCode.focus();
		return false;
	} 

	if (qualityName=="") {
		alert("Please enter a quality name.");
		form.qualityName.focus();
		return false;
	}

	if (!confirmSave()) return false;	
	return true;
}