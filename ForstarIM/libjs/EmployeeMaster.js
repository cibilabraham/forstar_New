function validateAddEmployeeMaster(form)
{
	var name		=	form.name.value;
	var designation			=	form.designation.value;
	var department			=	form.department.value;
	var address			=	form.address.value;
	var telephone			=	form.telephone.value;
	
	
	if ( name=="" ) {
		alert("Please enter name.");
		form.name.focus();
		return false;
	}
	
	if (designation=="") {
		alert("Please select designation.");
		form.designation.focus();
		return false;
	}
	if (department=="") {
		alert("Please select department.");
		form.department.focus();
		return false;
	}
	if (address=="") {
		alert("Please enter a address.");
		form.address.focus();
		return false;
	}
	if (telephone=="") {
		alert("Please enter a telephone.");
		form.telephone.focus();
		return false;
	}
	
	if(!confirmSave()) {
		return false;
	} else {
		return true;
	}
}

