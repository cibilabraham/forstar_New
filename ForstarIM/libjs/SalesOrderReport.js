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

		/*
		if(findDaysDiff(stockFrom)>0){
			alert(" From Date should be less than or equal to current date");
			document.getElementById("dateFrom").focus();
			return false;	
		}
		*/
			
		if (stockTo=="") {
			alert("Please select To Date.");
			document.getElementById("dateTill").focus();
			return false;
		}

		/*		
		if(findDaysDiff(stockTo)>0){
			alert("To Date should be less than or equal to current date");
			document.getElementById("dateTill").focus();
			return false;	
		}
		*/

		if (reportType=="") {
			alert("Please select a Report Type.");
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
			disableSearch('transporterRow', 'distributorRow', 'stateRow');
			clearField('selDistributor', 'selState');
		}
		if (reportType=='DIST') {
			disableSearch('distributorRow', 'transporterRow', 'stateRow');
			clearField('selTransporter', 'selState');
		}
		if (reportType=='STAT') {
			disableSearch('stateRow', 'transporterRow', 'distributorRow');
			clearField('selTransporter', 'selDistributor');
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
		document.getElementById(field1).value = "";
		document.getElementById(field2).value = "";
	}

	// Validate Sales Order Status Updation
	function validateSalesOrderStatusUpdate(form)
	{		
		var soNum	=	form.soNum.value;
		var invType	= 	form.invType.value;	
		
		if (invType=="") {
			alert("Please select invoice type.");
			form.invType.focus();
			return false;
		}

		if (soNum=="" || !isInteger(soNum) ) {
			alert("Please enter a Sales Order No.");
			form.soNum.focus();
			return false;
		}		
		if (!document.getElementById("changeStatus").checked) {	
			alert("Please select Confirm Option.");
			return false;
		}	
		if(!confirmSave()) 	return false;
		else 			return true;		
	}

	function changeSO()
	{
		var hidInvoiceNumNotExist = document.getElementById("hidInvoiceNumNotExist").value;	// Y/N
		var hidNewInvoiceNumExist = document.getElementById("hidNewInvoiceNumExist").value;
		
		if (hidInvoiceNumNotExist=='Y') {
			document.getElementById("newInvoiceNo").readOnly = true;
			document.getElementById("cmdChangeSONo").disabled = true;
		} else if (hidInvoiceNumNotExist=='N' && hidNewInvoiceNumExist=='Y') {
			document.getElementById("newInvoiceNo").readOnly = false;
			document.getElementById("cmdChangeSONo").disabled = true;
		} else {
			document.getElementById("newInvoiceNo").readOnly = false;
			document.getElementById("cmdChangeSONo").disabled = false;
		}
	}
	
	function updateNewInvoiceNo(existingInvoiceNo, newInvoiceNo)
	{
		if (existingInvoiceNo=="") {
			alert("Please enter an existing Invoice No.");
			document.getElementById("existingInvoiceNo").focus();
			return false;
		}
		if (newInvoiceNo=="") {
			alert("Please enter an Invoice No which is not allotted.");
			document.getElementById("newInvoiceNo").focus();
			return false;
		}
		if (!confirmSave()) return false
		else if (existingInvoiceNo!="" && newInvoiceNo!="") {
			xajax_updateNewInvoiceNo(existingInvoiceNo, newInvoiceNo);	
		}
		return true;
	}

	// Confirm change status
	function confirmChangeStatus(fieldPrefix, rowCount)
	{		
		var count = 0;
		var pendingRec = false;
		for (i=1; i<=rowCount; i++ ) {
			if (document.getElementById(fieldPrefix+i).checked) {
				if (document.getElementById("invStatus_"+i).value=='P') pendingRec = true;
				count++;
			}			
		}		
		if (count==0) {
			alert("Please select a record to release confirm status.");
			return false;
		}

		if (pendingRec) {
			alert("Please select a confirmed order to release the status.");
			return false;
		}
		
		if(!confirmSave()) 	return false;
		else 			return true;
	}