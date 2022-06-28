function validateWeightmentAfterGrading(form)
{
	
	var rmLotId	=	form.rmLotId.value;
	var gradeType=	form.gradeType1.value;
	var weight	=	form.weight1.value;
	if (rmLotId=="") {
		alert("Please select Lot Id.");
		form.rmLotId.focus();
		return false;
	}
	
	if (gradeType=="") {
		alert("Please enter gradeType.");
		form.gradeType1.focus();
		return false;
	}
	
	if (weight=="") {
		alert("Please enter weight.");
		form.weight1.focus();
		return false;
	}
	

	
	
	if (!confirmSave()) return false;
	return true;

}