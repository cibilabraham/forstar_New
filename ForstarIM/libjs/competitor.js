function validateAddCompetitor(form)
{	
	var competitorCode	=	form.competitorCode.value;
	var competitorName	=	form.competitorName.value;
	
	if (competitorCode=="") {
		alert("Please enter a competitor code.");
		form.competitorCode.focus();
		return false;
	}
	if (competitorName=="") {
		alert("Please enter a competitor name.");
		form.competitorName.focus();
		return false;
	}

	if (!confirmSave()) return false;	
	return true;
}