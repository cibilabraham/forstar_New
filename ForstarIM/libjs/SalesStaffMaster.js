function validateSalesStaffMaster(form)
{	
	var salesStaffName	= form.salesStaffName.value;	
	var state	= form.state.value;
	var city	= form.city.value;
	var area	= document.getElementById("area").value;
	var designation = form.designation.value;

	var opState	= form.opState.value;
	
	if (salesStaffName=="") {
		alert("Please enter a Sales Staff Name.");
		form.salesStaffName.focus();
		return false;
	}

	if (designation=="") {
		alert("Please enter a sales staff designation.");
		form.designation.focus();
		return false;
	}
	
	if (state=="") {
		alert("Please select a State.");
		form.state.focus();
		return false;
	}
	
	if (city=="") {
		alert("Please select a City.");
		form.city.focus();
		return false;
	}

	if (area=="") {
		//alert("Please select atleast one working area.");
		alert("Please select area.");
		document.getElementById("area").focus();
		return false;
	}
	
	if (opState=="") {
		alert("Please select a operational State.");
		form.opState.focus();
		return false;
	}
	
	if (!confirmSave()) {
		return false;
	}
	return true;
}