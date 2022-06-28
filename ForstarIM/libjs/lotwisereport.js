

function validateSMBSReportSearch()
{
		var supplyFrom	= document.getElementById("supplyFrom");
		var supplyTill	= document.getElementById("supplyTill");
		
		if (supplyFrom.value=="") {
			alert("Please select from Date");
			supplyFrom.focus();
			return false;
		}
		
		if (supplyTill.value=="") {
			alert("Please select till Date");
			supplyTill.focus();
			return false;
		}
		
	//For a Day
	return true;
}

	