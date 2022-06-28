function validateAssignRtCtDisChargeMaster(form)
{	
	var selRetailCounter	= form.selRetailCounter.value;;
	var disCharge	= form.disCharge.value;
	
	if (selRetailCounter=="") {
		alert("Please select a Retail Counter.");
		form.selRetailCounter.focus();
		return false;
	}

	if (disCharge=="") {
		alert("please enter Display Charge");
		document.getElementById("disCharge").focus();
		return false;
	}
	if (disCharge!="" && !isDigit(disCharge)) {
		alert("please enter a number");
		document.getElementById("disCharge").value="";
		return false;
	}
	if (disCharge!="") {
		var disTypeM = document.getElementById("disTypeM").checked;
		var disTypeD = document.getElementById("disTypeD").checked;
		
		if (!disTypeM && !disTypeD) {	
			alert("please select Month/Date");
			return false;
		}
		if (disTypeD) {
			var selectFrom = document.getElementById("selectFrom").value;
			var selectTill = document.getElementById("selectTill").value;
			if (selectFrom=="") {
				alert("Please select from date");
				document.getElementById("selectFrom").focus();
				return false;				
			}
			if (selectTill=="") {
				alert("Please select To date");
				document.getElementById("selectTill").focus();
				return false;				
			}
		}
	}	
		
	if (!confirmSave()) {
		return false;
	}
	return true;
}