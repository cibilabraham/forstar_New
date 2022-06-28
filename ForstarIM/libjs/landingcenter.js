function validateAddLandingCenter(form)
{	
	var landingCenterCode	=	form.landingCenterCode.value;
	var landingCenterName	=	form.landingCenterName.value;
	var distance		=	form.distance.value;
	
	if (landingCenterCode=="") {
		alert("Please enter a landing center code.");
		form.landingCenterCode.focus();
		return false;
	}
	if (landingCenterName=="") {
		alert("Please enter a landing center name.");
		form.landingCenterName.focus();
		return false;
	}
	if (distance!="") {
		if (!isDigit(distance) ) {
			alert("Distance should be a number!.");
			form.distance.focus();
			return false;
		}
	}
	if(!confirmSave()) return false;	
	return true;
}