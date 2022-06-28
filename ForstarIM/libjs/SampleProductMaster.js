function validateSampleProductMaster(form)
{	
	var sampleProductName	= form.sampleProductName.value;	
	
	if (sampleProductName=="") {
		alert("Please enter a sample Product.");
		form.sampleProductName.focus();
		return false;
	}	
	
		
	if (!confirmSave()) {
		return false;
	}
	return true;
}