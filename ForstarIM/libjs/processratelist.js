	function validateAddProcessRateList(form)
	{	
		var rateListName	=	form.rateListName.value;	
		var startDate		=	form.startDate.value;
		var hidStartDate	= 	form.hidStartDate.value;
	
		if (rateListName=="" ) {
			alert("Please enter a Rate list Name.");
			form.rateListName.focus();
			return false;
		}
		if (startDate=="") {
			alert("Please select a date.");
			form.startDate.focus();
			return false;
		}
	
		if (startDate!=hidStartDate) {
			var cMsg = "The start date you have selected is "+startDate+". Do you wish to continue?";
			if (!confirm(cMsg)) return false;
		}	
	
		if (!confirmSave()) return false;
		else return true;
	}