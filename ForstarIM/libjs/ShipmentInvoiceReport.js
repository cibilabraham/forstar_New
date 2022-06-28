	function validateDNReport()
	{
		var dateFrom = document.getElementById("dateFrom").value;
		var dateTo   = document.getElementById("dateTill").value;
			
		
		if (dateFrom=="") {
			alert("Please select From Date.");
			document.getElementById("dateFrom").focus();
			return false;
		}

				
		if (dateTo=="") {
			alert("Please select To Date.");
			document.getElementById("dateTill").focus();
			return false;
		}
	

	
		return true;
	}