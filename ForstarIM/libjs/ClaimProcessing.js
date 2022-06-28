function validateClaimProcessing(form)
{
	var isStockAvailable 	= false;
	var selClaim		= form.selClaim.value;
	var dispatchDate	= form.dispatchDate.value;
	var selStatus		= form.selStatus.value;
	var hidReturnMaterialCount	= form.hidReturnMaterialCount.value;
	var isComplete		= form.isComplete.checked;
	var claimType		= form.hidClaimType.value;	
	
	if (selClaim=="") {
		alert("Please select a claim ID.");
		form.selClaim.focus();
		return false;
	}

	if (hidReturnMaterialCount<=0 && claimType=='MR') {
		alert("No Material Return Records found.");
		form.selClaim.focus();
		return false;
	}
			
	if (dispatchDate=="") {
		alert("Please select a Settled Date.");
		form.dispatchDate.focus();
		return false;
	}

	if (!claimProcessingDateCheck(form)) {
		return false;	
	}

	if (selStatus=="") {
		alert("Please select a status.");
		form.selStatus.focus();
		return false;
	}		

	if (!confirmSave()) return false;
	else return true;
}

// claim Processing dispatch date check
function claimProcessingDateCheck(form)
{	
	var d = new Date();
	var t_date = d.getDate();      // Returns the day of the month
	if (t_date<10) {
		t_date = "0"+t_date;
	}
	var t_mon = d.getMonth() + 1;      // Returns the month as a digit
	if (t_mon<10) {
		t_mon = "0"+t_mon;
	}
	var t_year = d.getFullYear();  // Returns 4 digit year
	
	var curr_date	=	t_date + "/" + t_mon + "/" + t_year;
		
	CDT		=	curr_date.split("/");
	var CD_time	=	new Date(CDT[2], CDT[1], CDT[0]);
	
	var dispatchDate	=	document.getElementById("dispatchDate").value;	
	LDT		=	dispatchDate.split("/");
	var LD_time	=	new Date(LDT[2], LDT[1], LDT[0]);
	
	var one_day=1000*60*60*24

	//Calculate difference btw the two dates, and convert to days
	var extendedDays = Math.ceil((LD_time.getTime()-CD_time.getTime())/(one_day));
		
	if (extendedDays<0) {
		alert("Dispatch Date should be greater than or equal to current date");
		document.getElementById("dispatchDate").focus();
		return false;
	}
	return true;	
}