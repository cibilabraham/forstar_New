	function validateProductionAnalysisReport(form)
	{
		var dateFrom		= form.dateFrom.value;
		var dateTo		= form.dateTo.value;
		
		if (dateFrom=="") {
			alert("Please select From Date");
			form.dateFrom.focus();
			return false;
		}
		
		if (dateTo=="") {
			alert("Please select To Date");
			form.dateTo.focus();
			return false;
		}		
			
		return true;
	}