	function validateTransporterReport()
	{
		var stockFrom 		= document.getElementById("dateFrom").value;
		var stockTo  	 	= document.getElementById("dateTill").value;
		
		var reportType 		= document.getElementById("reportType").value;
		
		if (stockFrom=="") {
			alert("Please select From Date.");
			document.getElementById("dateFrom").focus();
			return false;
		}

		if(findDaysDiff(stockFrom)>0){
			alert(" From Date should be less than or equal to current date");
			document.getElementById("dateFrom").focus();
			return false;	
		}
			
		if (stockTo=="") {
			alert("Please select To Date.");
			document.getElementById("dateTill").focus();
			return false;
		}
		
		if(findDaysDiff(stockTo)>0){
			alert("To Date should be less than or equal to current date");
			document.getElementById("dateTill").focus();
			return false;	
		}

		if (reportType=="") {
			alert("Please select a Report Type.");
			document.getElementById("reportType").focus();
			return false;
		} 

		if (reportType=='TRAN') {
			var selTransporter = document.getElementById("selTransporter").value;
			if (selTransporter=="") {
				alert("Please select a Transporter.");
				document.getElementById("selTransporter").focus();
				return false;
			}
		}

		if (reportType=='DIST') {
			var selDistributor 	= document.getElementById("selDistributor").value;	
			if (selDistributor=="") {
				alert("Please select a distributor.");
				document.getElementById("selDistributor").focus();
				return false;
			}
		}

		if (reportType=='STAT') {
			var selState 	= document.getElementById("selState").value;	
			if (selState=="") {
				alert("Please select a State.");
				document.getElementById("selState").focus();
				return false;
			}
		}
		
		
			
		return true;
	}

	/*
		Using to activate only one search option
	*/
	function showSearchOption()
	{	
		var reportType 		= document.getElementById("reportType").value;		
		var selTransporter 	= document.getElementById("selTransporter").value;
		var selDistributor 	= document.getElementById("selDistributor").value;
		var selState 		= document.getElementById("selState").value;
		//var billType		= document.getElementById("billType").value;
		if (reportType=='TRAN') {
			disableSearch('transporterRow', 'distributorRow', 'stateRow');
			clearField('selDistributor', 'selState');
		}
		if (reportType=='DIST') {
			disableSearch('distributorRow', 'transporterRow', 'stateRow');
			clearField('selTransporter', 'selState');
			clearField('billType', '');
		}
		if (reportType=='STAT') {
			disableSearch('stateRow', 'transporterRow', 'distributorRow');
			clearField('selTransporter', 'selDistributor');
			clearField('billType', '');
		}
	}

	// Disable All Search Field
	function disableSerachOptions()
	{
		document.getElementById("transporterRow").style.display='none';
		document.getElementById("distributorRow").style.display='none';
		document.getElementById("stateRow").style.display='none';
	}
	// Disable Not Selected field
	function disableSearch(opt1, opt2, opt3)
	{
		document.getElementById(opt1).style.display='';
		document.getElementById(opt2).style.display='none';
		document.getElementById(opt3).style.display='none';
	}
	// Clear Field
	function clearField(field1, field2)
	{
		if (field1) document.getElementById(field1).value = "";
		if (field2) document.getElementById(field2).value = "";
	}