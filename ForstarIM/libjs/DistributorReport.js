	function validateDistributorReport()
	{
		var dateFrom = document.getElementById("dateFrom").value;
		var dateTo   = document.getElementById("dateTill").value;
		var selDistributor = document.getElementById("selDistributor").value;		
		
		if (dateFrom=="") {
			alert("Please select From Date.");
			document.getElementById("dateFrom").focus();
			return false;
		}

		/*
		if (findDaysDiff(dateFrom)>0) {
			alert(" From Date should be less than or equal to current date");
			document.getElementById("dateFrom").focus();
			return false;	
		}
		*/
			
		if (dateTo=="") {
			alert("Please select To Date.");
			document.getElementById("dateTill").focus();
			return false;
		}
		/*
		if(findDaysDiff(dateTo)>0){
			alert("To Date should be less than or equal to current date");
			document.getElementById("dateTill").focus();
			return false;	
		}
		*/
		if (selDistributor=="" && !document.getElementById("distACStmnt").checked) {
			alert("Please select a distributor.");
			document.getElementById("selDistributor").focus();
			return false;
		}
			
		if (!document.getElementById("pendingOrder").checked && !document.getElementById("orderDispatched").checked && !document.getElementById("claimPending").checked && !document.getElementById("claimSettled").checked && !document.getElementById("distributorAccount").checked && !document.getElementById("sampleInvoice").checked && !document.getElementById("distOverdue").checked && !document.getElementById("distACStmnt").checked) {
			alert("Please select atleast one search option");
			document.getElementById("pendingOrder").focus();
			return false;
		}

	
		return true;
	}

	/*
		Using to activate only one search option
	*/
	function selectChk(field)
	{	
		if (!document.getElementById(field).checked) chk = false;
		else chk = true;	
		document.getElementById("pendingOrder").checked 	= false;
		document.getElementById("orderDispatched").checked 	= false;		
		document.getElementById("claimPending").checked 	= false;		
		document.getElementById("claimSettled").checked 	= false;
		document.getElementById("distributorAccount").checked	= false;
		document.getElementById("sampleInvoice").checked	= false;
		document.getElementById("distOverdue").checked		= false;
		document.getElementById("distACStmnt").checked		= false;			

		document.getElementById(field).checked = chk;
	}

	function displayQryType()
	{
		var sampleInvoice = document.getElementById("sampleInvoice").checked;
		if (sampleInvoice)  document.getElementById("qryTypeRow").style.display="";
		else document.getElementById("qryTypeRow").style.display="none";
	}

	// Confirm change status
	function confirmChangeStatus(fieldPrefix, rowCount)
	{		
		var count = 0;
		var salesInvoiceRec = false;
		for (i=1; i<=rowCount; i++ ) {
			if (document.getElementById(fieldPrefix+i).checked) {
				var  salesInvoiceId = document.getElementById("salesInvoiceId_"+i).value;
				if (salesInvoiceId!=0) salesInvoiceRec = true;
				count++;
			}			
		}		
		if (count==0) {
			alert("Please select a record to release confirm status.");
			return false;
		}

		
		if (salesInvoiceRec) {
			alert("Sales Invoice confirm release only possible in invoice section. Please select other entries.");
			return false;
		}
		
		if(!confirmSave()) 	return false;
		else 			return true;
	}

	function validateDistACStatus()
	{
		var dateFrom = document.getElementById("dateFrom").value;
		var dateTo   = document.getElementById("dateTill").value;
		var selDistributor = document.getElementById("selDistributor").value;
		var invoiceFilter = document.getElementById("invoiceFilter").value;
				
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
		
		if (selDistributor=="") {
			alert("Please select a distributor.");
			document.getElementById("selDistributor").focus();
			return false;
		}

		if (invoiceFilter=="") {
			alert("Please select a invoice.");
			document.getElementById("invoiceFilter").focus();
			return false;
		}
		
		return true;
	}