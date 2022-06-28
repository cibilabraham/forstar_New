
function validateAddFishCategory(form)
{	
	var fishType	=	form.fishType.value;
	
	if (fishType=="" ) {
		alert("Please enter a Category Name.");
		form.fishType.focus();
		return false;
	}
	
	if (!confirmSave()) return false;
	else return true;	
}