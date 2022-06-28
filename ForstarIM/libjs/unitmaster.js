function validateAddUnit(form)
{	
	var unit		=	form.unit.value;
	var selReceive	=	form.selReceive.value;	
	
	if (unit=="" ) {
		alert("Please enter a Unit Name.");
		form.unit.focus();
		return false;
	}
	
	if (selReceive=="" ) {
		alert("Please select a Receive Type.");
		form.selReceive.focus();
		return false;
	}
	
	if (!confirmSave()) return false;
	else return true;	
}