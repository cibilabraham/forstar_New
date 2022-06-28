	function validateAddProcessorsPayments(form)
	{	
		var paymentDate	= form.paymentDate.value;
		var processor	=	form.selProcessor.value;
		var amount	=	form.amount.value;
	
		if (paymentDate=="") {
			alert("Please select payment date.");
			form.paymentDate.focus();
			return false;
		}

		if (processor=="") {
			alert("Please select a Pre-Processor");
			form.selProcessor.focus();
			return false;
		}
			
		if (amount=="") {
			alert("Please enter Amount");
			form.amount.focus();
			return false;
		}
		if(!isDigit(amount)) {
			alert("The Amount should be Numeric Value");
			form.amount.focus();
			return false;
		}		
		if (confirmSave()) return true;
		else return false;
	}