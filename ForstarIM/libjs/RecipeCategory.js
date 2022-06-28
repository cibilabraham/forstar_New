function validateRecipeCategory(form)
{
	
	var category	=	form.categoryName.value;
	var recpMainCategory =  form.recpMainCategory.value;
	
	if (recpMainCategory=="") {
		alert("Please select a Category.");
		form.ingMainCategory.focus();
		return false;
	}

	if (category=="") {
		alert("Please enter a Sub-Category Name.");
		form.categoryName.focus();
		return false;
	}
	
	if (!confirmSave()) {
		return false;
	}
	return true;
}





