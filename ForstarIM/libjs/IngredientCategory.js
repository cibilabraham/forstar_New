function validateIngredientCategory(form)
{
	
	var category	=	form.categoryName.value;
	var ingMainCategory =  form.ingMainCategory.value;
	
	if (ingMainCategory=="") {
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





