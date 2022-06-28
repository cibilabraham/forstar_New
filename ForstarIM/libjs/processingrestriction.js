function validateProcessingRestriction(form)
{
	
	var selpage		=	form.selpage.value;
	var selActivity		=	form.selActivity.value;
	
	if( selpage=="" )
	{
		alert("Please select an application Screen name.");
		form.selpage.focus();
		return false;
	}
	if( selActivity=="" )
	{
		alert("Please select an activity.");
		form.selActivity.focus();
		return false;
	}
	
	if(!confirmSave())
	{
		return false;
	}
	return true;

}





