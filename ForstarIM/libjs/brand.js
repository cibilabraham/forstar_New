function validateAddBrand(form)
{
	//var selCustomer		=	form.selCustomer.value;
	var brand			=	form.brand.value;
	
	/*
	if ( selCustomer=="" ) {
		alert("Please select a Customer.");
		form.selCustomer.focus();
		return false;
	}
	*/
	if (brand=="") {
		alert("Please enter a Brand.");
		form.brand.focus();
		return false;
	}
	
	if(!confirmSave()) {
		return false;
	} else {
		return true;
	}
}

