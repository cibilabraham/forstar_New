	function validateSalesReport()
	{
		var stockFrom 		= document.getElementById("dateFrom").value;
		var stockTo  	 	= document.getElementById("dateTill").value;
		
		var reportType 		= document.getElementById("reportType").value;
		
		if (stockFrom=="") {
			alert("Please select From Date.");
			document.getElementById("dateFrom").focus();
			return false;
		}
			
		if (stockTo=="") {
			alert("Please select To Date.");
			document.getElementById("dateTill").focus();
			return false;
		}

		

		if (reportType=="") {
			alert("Please select a Type.");
			document.getElementById("reportType").focus();
			return false;
		} 

		if (reportType=='TRAN') {
			/*
			var selTransporter = document.getElementById("selTransporter").value;
			if (selTransporter=="") {
				alert("Please select a Transporter.");
				document.getElementById("selTransporter").focus();
				return false;
			}
			*/
		}

		if (reportType=='DIST') {
			/*
			var selDistributor 	= document.getElementById("selDistributor").value;
			if (selDistributor=="") {
				alert("Please select a distributor.");
				document.getElementById("selDistributor").focus();
				return false;
			}
			*/
		}

		if (reportType=='STAT') {
			/*
			var selState 	= document.getElementById("selState").value;	
			if (selState=="") {
				alert("Please select a State.");
				document.getElementById("selState").focus();
				return false;
			}
			*/
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
		if (reportType=='TRAN') {
			disableSearch('transporterRow', 'distributorRow', 'stateRow', 'zoneRow', 'soCityRow', 'otherRow');
			clearField('selDistributor', 'selState', 'selZone', 'selSOCity');
			unCheck();
		}
		if (reportType=='DIST') {
			disableSearch('distributorRow', 'transporterRow', 'stateRow', 'zoneRow', 'soCityRow', 'otherRow');
			clearField('selTransporter', 'selState', 'selZone', 'selSOCity');
			unCheck();
		}
		if (reportType=='STAT') {
			disableSearch('stateRow', 'transporterRow', 'distributorRow', 'zoneRow', 'soCityRow', 'otherRow');
			clearField('selTransporter', 'selDistributor', 'selZone', 'selSOCity');
			unCheck();
		}
		if (reportType=='ZONE') {
			disableSearch('zoneRow', 'stateRow', 'transporterRow', 'distributorRow', 'soCityRow', 'otherRow');
			clearField('selTransporter', 'selDistributor', 'selDistributor', 'selSOCity');
			unCheck();
		}
		if (reportType=='CITW') {
			disableSearch('soCityRow', 'zoneRow', 'stateRow', 'transporterRow', 'distributorRow', 'otherRow');
			clearField('selTransporter', 'selDistributor', 'selDistributor', 'selZone');
			unCheck();
		}

		if (reportType=='OTHR') {
			disableSearch('otherRow', 'soCityRow', 'zoneRow', 'stateRow', 'transporterRow', 'distributorRow');
			clearField('selTransporter', 'selDistributor', 'selDistributor', 'selZone');		
		}
	}

	// Disable All Search Field
	function disableSerachOptions()
	{
		document.getElementById("transporterRow").style.display='none';
		document.getElementById("distributorRow").style.display='none';
		document.getElementById("stateRow").style.display='none';
		document.getElementById("zoneRow").style.display='none';
		document.getElementById("soCityRow").style.display='none';		
		document.getElementById("otherRow").style.display='none';
	}
	// Disable Not Selected field
	function disableSearch(opt1, opt2, opt3, opt4, opt5, opt6)
	{
		document.getElementById(opt1).style.display='';
		document.getElementById(opt2).style.display='none';
		document.getElementById(opt3).style.display='none';
		document.getElementById(opt4).style.display='none';
		document.getElementById(opt5).style.display='none';
		document.getElementById(opt6).style.display='none';
	}
	// Clear Field
	function clearField(field1, field2, field3, field4)
	{
		document.getElementById(field1).value = "";
		document.getElementById(field2).value = "";
		document.getElementById(field3).value = "";
		document.getElementById(field4).value = "";
	}

	function unCheck()
	{
		document.getElementById("totNetWt").checked = false;
		document.getElementById("totNumPack").checked = false;	
	}

	function removeAllChk(field)
	{		
		if (!document.getElementById(field).checked) chk = false;
		else chk = true;
		document.getElementById("totNetWt").checked = false;
		document.getElementById("totNumPack").checked = false;	
		document.getElementById(field).checked = chk;
	}


	function getRequiredFunction(dateFrom, dateTill, invoiceType, selStatus, selState, selDistributorId, selTransporter, selZoneId, selSOCityId, selCityArr, reportType)
	{
		//alert("DFrom"+dateFrom+","+"DTill="+dateTill+","+invoiceType+","+selStatus+","+selState+","+selDistributorId+","+selTransporter+","+selZoneId+","+selSOCityId+","+selCityArr+","+reportType);
	}