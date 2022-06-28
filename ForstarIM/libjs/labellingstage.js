function validateAddLabellingStage(form)
{
	var label = form.label.value;	
	if (label=="") {
		alert("Please enter a Label Stage.");
		form.label.focus();
		return false;
	}
			
	if (!confirmSave()) return false;
	else return true;
}