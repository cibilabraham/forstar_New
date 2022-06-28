function validateMarginStructure (form)
{
	var marginStructureCode = form.marginStructureCode.value;
	var marginStructureName = form.marginStructureName.value;
	var markUp		= form.priceCalcType[0].checked;	
	var markDown		= form.priceCalcType[1].checked;
	var schemeChk		= form.schemeChk.checked;	

	if (marginStructureCode=="") {
		alert("Please enter a Margin Structure Code.");
		form.marginStructureCode.focus();
		return false;
	}

	if (marginStructureName=="") {
		alert("Please enter a Margin Structure Name.");
		form.marginStructureName.focus();
		return false;
	}

	if (schemeChk!="") {
		var selSchemeHeadId = form.selSchemeHeadId.value;
		if (selSchemeHeadId=="") {
			alert("Please select a Margin structure Scheme Code.");
			form.selSchemeHeadId.focus();
			return false;
		}
	}	

	if (!markUp && !markDown) {
		alert("Please select any one Price calculation type");
		return false;
	}			

	if (!confirmSave()) return false;
	else return true;	
}
// Show Scheme head
function showSchemeHead()
{
	if (document.getElementById("schemeChk").checked) {
		document.getElementById( "selScheme" ).style.display = "block";	
	} else {
		document.getElementById( "selScheme" ).style.display = "none";	
	}
}

// Hide Scheme head when loading
function hidSchemeHead()
{
	document.getElementById( "selScheme" ).style.display = "none";	
}