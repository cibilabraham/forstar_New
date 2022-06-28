function validateAddStatus(form)
{	
	var status		=	form.status.value;
	
	if (status=="") {
		alert("Please enter a Status.");
		form.status.focus();
		return false;
	}	
	
	if (!confirmSave()) return false;
	else return true;	
}