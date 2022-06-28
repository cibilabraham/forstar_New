function validateDistributorAccount(form)
{	
	var selDate		= form.selDate.value;
	var selDistributor	= form.selDistributor.value;
	var amount		= form.amount.value;
	var amtDescription		= form.amtDescription.value;
	
	
	if (selDate=="") {
		alert("Please select a date.");
		form.selDate.focus();
		return false;
	}
	
	if (selDistributor=="") {
		alert("Please select a Distributor.");
		form.selDistributor.focus();
		return false;
	}

	if (amount=="") {
		alert("Please enter a amount.");
		form.amount.focus();
		return false;
	}

	if (amtDescription=="") {
		alert("Please enter description.");
		form.amtDescription.focus();
		return false;
	}

	if (!confirmSave()) {
		return false;
	}
	return true;
}
