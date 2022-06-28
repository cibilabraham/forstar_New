	function validatePreProcessingReport(form)
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
		if (!document.getElementById("summary").checked && !document.getElementById("details").checked && !document.getElementById("qtySummary").checked) {
			alert("Please select atleast one search option");			
			return false;
		}		
			
	return true;
	}

	function hideSummaryOption()
	{
		document.getElementById("summary").checked=false;
		document.getElementById("qtySummary").checked=false;	
		//document.frmPreProcessingReport.submit();
	}
	
	
	function hideDetailedOption()
	{
		document.getElementById("details").checked=false;
		document.getElementById("qtySummary").checked=false;
		//document.frmPreProcessingReport.submit();
	}

	function hideOtherOption()
	{
		document.getElementById("summary").checked=false;	
		document.getElementById("details").checked=false;
	}
function functionLoad(formObj)
	{
		//alert("hai");
		showFnLoading(); 
		formObj.form.submit();
	}