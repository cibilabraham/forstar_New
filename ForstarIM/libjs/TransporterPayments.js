	function validateTransporterPayments(form)
	{
		var paymentDate = form.paymentDate.value;		
		var transporter	=	form.transporter.value;
		var chequeNo	=	form.chequeNo.value;
		var amount		=	form.amount.value;
	
		if (paymentDate=="") {
			alert("Please select a date of payment.");
			form.paymentDate.focus();
			return false;
		}

		if (transporter=="") {
			alert("Please select a Transporter.");
			form.transporter.focus();
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
		
		if( confirmSave()){
			return true;
		} else {
			return false;
		}
	}

	function validateSelect(form)
	{
		var selFish		=	form.selFilter.value;
		var dateSelect	=	form.selDate.value;
		
		if(selFish!=0 && dateSelect==0){
			alert("Please select a date");
			form.selDate.focus();
			return false;
		}
		if(selFish==0 && dateSelect!=0){
			alert("Please select a Fish to view date wise list");
			form.selFilter.focus();
			return false;
		}
		
		return true;	
	}