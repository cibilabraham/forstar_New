	function validateProductIdentifierMaster(form)
	{	
		var selDistributor	= form.selDistributor.value;
		var selProduct		= form.selProduct.value;
		var indexNo		= form.indexNo.value;	
		
		if (selDistributor=="") {
			alert("Please select a Distributor.");
			form.selDistributor.focus();
			return false;
		}
	
		if (selProduct=="") {
			alert("Please select a Product.");
			form.selProduct.focus();
			return false;
		}

		if (indexNo=="") {
			alert("Please enter a Index No.");
			form.indexNo.focus();
			return false;
		}
		
		if (!confirmSave()) return false;
		return true;
	}