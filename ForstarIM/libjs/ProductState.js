function validateProductState(form)
{
	
	var category	=	form.categoryName.value;
	
	if (category=="") {
		alert("Please enter a Product State.");
		form.categoryName.focus();
		return false;
	}
	
	if (!confirmSave()) {
		return false;
	}
	return true;
}





