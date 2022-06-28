	function validateTransporterPaymentStatus(form)
	{
		var supplyFrom	=	form.supplyFrom.value;
		var supplyTill	=	form.supplyTill.value;
		var selTransporter =	form.selTransporter.value;		
		
		if (supplyFrom=="") {
			alert("Please select from date.");
			form.supplyFrom.focus();
			return false;
		}
	
		if (supplyTill=="") {
			alert("Please select To date.");
			form.supplyTill.focus();
			return false;
		}
	
		if (selTransporter=="") {
			alert("Please select a Transporter.");
			form.selTransporter.focus();
			return false;
		}	
		return true;	
	}

	
	function validateTransporterPaymentUpdate(form)
	{
		if (!validateTransporterPaymentStatus(form)) return false;
		
		if (!form.changeStatus[0].checked && !form.changeStatus[1].checked) {
			alert("Please select atleast one status Change option.");
			return false;
		}
		if (!confirmSave()) {
			return false;
		} else {
			return true;
		}
	}
	