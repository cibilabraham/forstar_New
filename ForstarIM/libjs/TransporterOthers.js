	function validateTransporterOthers(form)
	{
		var fovCharge		=	form.fovCharge.value;
		var docketCharge	=	form.docketCharge.value;
		var serviceTax		=	form.serviceTax.value;
		var octroiServiceCharge	=	form.octroiServiceCharge.value;	
		
		if (fovCharge=="" ) {
			alert("Please enter FOV Charge.");
			form.fovCharge.focus();
			return false;
		}
		
		if (!checkNumber(fovCharge)) {
			form.fovCharge.value = "";
			return false;
		}

		if (docketCharge=="" ) {
			alert("Please enter Docket Charges.");
			form.docketCharge.focus();
			return false;
		}
		
		if (!checkNumber(docketCharge)) {
			form.docketCharge.value = "";
			return false;
		}

		if (serviceTax=="" ) {
			alert("Please enter Service Tax Rate.");
			form.serviceTax.focus();
			return false;
		}
		
		if (!checkNumber(serviceTax)) {
			form.serviceTax.value = "";
			return false;
		}

		if (octroiServiceCharge=="" ) {
			alert("Please enter Octroi Service Charge.");
			form.octroiServiceCharge.focus();
			return false;
		}
		
		if (!checkNumber(octroiServiceCharge)) {
			form.octroiServiceCharge.value = "";
			return false;
		}
			
		if (!confirmSave()) {
			return false;
		} else {
			return true;
		}
	}

