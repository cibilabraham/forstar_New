
	function validateChangeChallan(form, prefix, rowcount)
	{
		var rowCount	=	rowcount;
		var fieldPrefix	=	prefix;
		var conDelMsg	=	"Do you wish to change the status of selected challan?";
		if (!isAnyChecked(rowCount,fieldPrefix)) {
			alert("Please select a record.");
			return false;
		}	

		for (var i=1; i<=rowcount; i++) {
			var invoice 	= document.getElementById("challanNo_"+i).checked;
			var cancelled	= document.getElementById("cancelled_"+i);
			if (invoice && cancelled.value=="") {
				alert("Status not exist for the selected challan. ");
				return false;
			}
		}
		if (confirm(conDelMsg)) {
			return true;
		}		
		return false;
	}

