function validateAddFrozenPackingRateList(form)
{
	var name		=	form.name.value;
	var startDate	=	form.startDate.value;
	
	if ( name=="" ) 
	{
		alert("Please enter name.");
		form.name.focus();
		return false;
	}
	
	if (startDate=="") 
	{
		alert("Please select Start Date.");
		form.startDate.focus();
		return false;
	}
	
	if(!confirmSave())
	{
		return false;
	} 
	else 
	{
		return true;
	}
}

