function validateSoaking(form)
{
	//alert('ghh');
	//var rmlotId	=	form.rmlotId.value;
	var rmlotId	=	form.rmlotId.value;
	var currentProcessingStage	=	form.currentProcessingStage.value;
	var supplierDetails	=	form.supplierDetails.value;
	var soakInCount	=	form.soakInCount.value;
	var soakInQuantity	=	form.soakInQuantity.value;
	var soakInTime	=	form.soakInTime.value;
	var soakOutCount	=	form.soakOutCount.value;
	var soakOutQunatity	=	form.soakOutQunatity.value;
	var soakOutTime	=	form.soakOutTime.value;
	var temperature	=	form.temperature.value;
	var gain	=	form.gain.value;
	var chemcalUsed	=	form.chemcalUsed.value;
	var chemcalQty	=	form.chemcalQty.value;
	
	
	
	if (rmlotId=="") {
		alert("Please select rmlotId.");
		form.rmlotId.focus();
		return false;
	}
	if (currentProcessingStage=="") {
		alert("Please select currentProcessingStage.");
		form.currentProcessingStage.focus();
		return false;
	}
	if (supplierDetails=="") {
		alert("Please select supplierDetails.");
		form.supplierDetails.focus();
		return false;
	}
	if (soakInCount=="") {
		alert("Please enter soakInCount.");
		form.soakInCount.focus();
		return false;
	}
	if (soakInQuantity=="") {
		alert("Please enter soakInQuantity.");
		form.soakInQuantity.focus();
		return false;
	}
	
	if (soakInTime=="") {
		alert("Please enter soakInTime.");
		form.soakInTime.focus();
		return false;
	}
	if (soakOutCount=="") {
		alert("Please enter soakOutCount.");
		form.soakOutCount.focus();
		return false;
	}
	if (soakOutQunatity=="") {
		alert("Please enter soakOutQunatity.");
		form.soakOutQunatity.focus();
		return false;
	}
	if (soakOutTime=="") {
		alert("Please enter soakOutTime.");
		form.soakOutTime.focus();
		return false;
	}
	if (temperature=="") {
		alert("Please enter temperature.");
		form.temperature.focus();
		return false;
	}
	
	if (gain=="") {
		alert("Please enter gain.");
		form.gain.focus();
		return false;
	}
	if (chemcalUsed=="") {
		alert("Please enter chemcalUsed.");
		form.chemcalUsed.focus();
		return false;
	}
	if (chemcalQty=="") {
		alert("Please enter chemcalQty.");
		form.chemcalQty.focus();
		return false;
	}

	
	
	if (!confirmSave()) return false;
	return true;

}
