function validateAddFish(form)
{	
	var fishCode	=	form.fishCode.value;
	var fishName	=	form.fishName.value;
	var category	=	form.fishCategory.value;
	
	if (fishCode=="") {
		alert("Please enter a fish code.");
		form.fishCode.focus();
		return false;
	}

	if (fishName=="") {
		alert("Please enter a fish name.");
		form.fishName.focus();
		return false;
	}

	if (category=="") {
		alert("Please select a category.");
		form.fishCategory.focus();
		return false;
	}
	
	if (!confirmSave()) return false;
	else return true;
}