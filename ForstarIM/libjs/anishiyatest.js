function ValidateAnishiyaTest(form)
{	
	
	var name	=	form.name.value;
	if (name=="" ) 
	{
	
		alert("Please enter Name.");
		form.name.focus();
		return false;
	}
}