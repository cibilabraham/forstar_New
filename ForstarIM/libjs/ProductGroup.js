function validateProductGroup(form)
{
	
	var category	=	form.categoryName.value;
	
	if (category=="") {
		alert("Please enter a Product Group.");
		form.categoryName.focus();
		return false;
	}
	
	if (!confirmSave()) {
		return false;
	}
	return true;
}





