function validatepuCompetitor(form)
{		
	//var company			=	form.company.value;
	var plantNo			=	form.plantNo.value;
	var plantName		=	form.plantName.value;
	var stdProduction	=	form.stdProduction.value;
	var basedOn			=	form.basedOn.value;

	//alert(company);	
	if (company=="") {
		alert("Please select a Company.");
		//form.company.focus();
		return false;
	}
		
	if (plantNo=="") {
		alert("Please enter a plant Number.");
		form.plantNo.focus();
		return false;
	}
	
	if (plantName=="") {
		alert("Please enter a plant name.");
		form.plantName.focus();
		return false;
	}
/*
	if (stdProduction=="") {
		alert("Please enter standard production.");
		form.stdProduction.focus();
		return false;
	}

	if (basedOn=="")
	{
		alert("Please enter Type based On.");
		//form.basedOn.focus();
		return false;
	}
*/
	if (!confirmSave()) return false;	
	return true;
}