function validateRetailCounterCategory(form)
{	
	var category	=	form.categoryName.value;	
	if (category=="") {
		alert("Please enter a Category Name.");
		form.categoryName.focus();
		return false;
	}	

	if (!confirmSave()) {
		return false;
	}
	return true;
}





