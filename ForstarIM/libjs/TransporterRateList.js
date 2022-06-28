	function validateDistMarginRateListMaster(form)
	{	
		var rateListName	= form.rateListName.value;	
		var startDate		= form.startDate.value;
		var addMode		= document.getElementById("hidAddMode").value;
		var selFunctionality	= form.selFunctionality.value;
		var hidStartDate	= form.hidStartDate.value;
		
		if (rateListName=="") {
			alert("Please enter a Rate list Name.");
			form.rateListName.focus();
			return false;
		}
	
		if (startDate=="") {
			alert("Please select a date.");
			form.startDate.focus();
			return false;
		}
		
		if (addMode!="") {
			var selTransporter = form.selTransporter.value;
			if (selTransporter=="" ) {
				alert("Please select a Transporter.");
				form.selTransporter.focus();
				return false;
			}
		}

		if (selFunctionality=="") {
			alert("Please select a Function.");
			form.selFunctionality.focus();
			return false;
		}

		//if (startDate!=hidStartDate) {
		if (startDate!="") {
			var cMsg = "The start date you have selected is "+startDate+". Do you wish to continue?";
			if (!confirm(cMsg)) return false;
		}	
	
		if (!confirmSave()) return false;
		else return true;
	}