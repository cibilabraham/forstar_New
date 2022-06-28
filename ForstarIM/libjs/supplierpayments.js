	function validateAddSupplierPayments(form)
	{
		var supplier	= form.supplier.value;
		var chequeNo	= form.chequeNo.value;
		var amount	= form.amount.value;
		var paymentDate = form.paymentDate.value;
		var paymentMethod = document.getElementById("paymentMethod");
		var chequeNo	  = document.getElementById("chequeNo");
		var bankName	 = document.getElementById("bankName");
		var paymentType	 = document.getElementById("paymentType");
		var accountEntryNo = document.getElementById("accountEntryNo");	

		if (paymentDate=="") {
			alert("Please select a date");
			form.paymentDate.focus();
			return false;
		}

		if (!isDate(paymentDate)) {
			return false;
		}

		if (supplier=="") {
			alert("Please select a Supplier");
			form.supplier.focus();
			return false;
		}

		if (paymentMethod.value=="") {
			alert("Please select Cheque/DD.");
			paymentMethod.focus();
			return false;	
		}

		if (chequeNo.value=="") {
			var selTxt = paymentMethod.options[paymentMethod.selectedIndex].text;
			alert("Please enter "+selTxt+" No.");	
			chequeNo.focus();
			return false;
		}

		if (bankName.value=="") {
			alert("Please enter issuing bank name.");
			bankName.focus();
			return false;	
		}		

		if (amount=="") {
			alert("Please enter Amount");
			form.amount.focus();
			return false;
		}
	
		if (!isDigit(amount)) {
			alert("The Amount should be Numeric Value.");
			form.amount.focus();
			return false;
		}


		if (paymentType.value=="") {
			alert("Please select a Payment type.");
			paymentType.focus();
			return false;
		}

		if (accountEntryNo.value=="") {
			alert("Please enter account entry no.");
			accountEntryNo.focus();
			return false;
		}

		if (paymentType.value=='S') {
			//var dateType = document.getElementById("dateType");
			var fromDate = document.getElementById("fromDate");
			var toDate   = document.getElementById("toDate");
			//var selChallan = document.getElementById("selChallan");	
			var billingCompany = document.getElementById("billingCompany");
			var selSettlementDate = document.getElementById("selSettlementDate");
			
			/*
			if (dateType.value=="") {
				alert("Please select Based on date.");
				dateType.focus();
				return false;
			}
			*/
			if (fromDate.value=="") {
				alert("Please select From date.");
				fromDate.focus();
				return false;
			}

			if (toDate.value=="") {
				alert("Please select to date.");
				toDate.focus();
				return false;
			}

			if (selSettlementDate.value=="") {
				alert("Please select a Settlement Date.");
				selSettlementDate.focus();
				return false;
			}

			/*
			if (selChallan.value=="") {
				alert("Please select atleast one challan.");
				selChallan.focus();
				return false;
			}
			
			if (billingCompany.value=="") {
				alert("Please select a billing company.");
				billingCompany.focus();
				return false;
			}		
			*/
		} // Payment type S- Ends here
			
		if (confirmSave()) return true;
		else return false;		
	}

	// Show payment type
	function showPaymentType()
	{
		var paymentType = document.getElementById("paymentType").value;
		if (paymentType=='S') {
			document.getElementById("setlmentRow").style.display="";
			document.getElementById("setldDateRow").style.display="";
		} else {
			document.getElementById("setlmentRow").style.display="none";
			document.getElementById("setldDateRow").style.display="none";
		}
	}

	function showPaymentMethod()
	{
		var idexValue = document.getElementById("paymentMethod").selectedIndex;
		if (idexValue) {
			document.getElementById("pMthodId").innerHTML = document.getElementById("paymentMethod").options[idexValue].text;		
		} else {
			document.getElementById("pMthodId").innerHTML ="Cheque/DD";		
		}
	}
	
	function clearSetldFields()
	{
		document.getElementById("fromDate").value = "";
		document.getElementById("toDate").value = "";
		document.getElementById("selChallan").length = 0;
		document.getElementById("dateType").value = "";
		document.getElementById("billingCompany").value = "";
		document.getElementById("selSettlementDate").length = 0;		
	}

	function showSetldRow(display)
	{
		if (display) document.getElementById("setlmentRow").style.display="";
		else document.getElementById("setlmentRow").style.display="none";
	}

	// Link for settlement
	function cfmLinkSetld(form, prefix, rowcount, paymentDate, supplierId, fromDate, toDate, selSetldDate)
	{		
		var rowCount	=	rowcount;
		var fieldPrefix	=	prefix;
		var conDelMsg	=	"Do you wish to link the selected items?";
		var advRowCount = document.getElementById("advAmtRowCount").value;
		
		if (!isAnyChecked(rowCount,fieldPrefix))
		{
			alert("Please select a record to link the settlement date.");
			return false;
		}

		if (!validateLinkUpdate(form)) return false;
		
		for ( i=1; i<=advRowCount; i++ )
		{
			var advChecked = document.getElementById("advEntryId_"+i).checked;
			if (advChecked) {
				var advanceEntryId = document.getElementById("advEntryId_"+i).value;
				var stldAmtType = document.getElementById("stldAmtType_"+i);
				if (stldAmtType.value=="") {
					alert("Please select Full/Part amt.");
					stldAmtType.focus();
					return false;
				}
				var amtPaid	= document.getElementById("amtPaid_"+i).value;
				var partAmt	= document.getElementById("partAmt_"+i);
				if (stldAmtType.value=='PA' && partAmt.value=="") {
					alert("Please enter part amount.");
					partAmt.focus();
					return false;
				}
			}
		}

		if (!confirm(conDelMsg))
		{
			return false;
		}
			
		for ( i=1; i<=advRowCount; i++ )
		{
			var advChecked = document.getElementById("advEntryId_"+i).checked;
			if (advChecked) {
				var advanceEntryId = document.getElementById("advEntryId_"+i).value;
				var stldAmtType = document.getElementById("stldAmtType_"+i);
				
				var amtPaid	= document.getElementById("amtPaid_"+i).value;
				var partAmt	= document.getElementById("partAmt_"+i);
				
				if (partAmt) var partAmtValue = partAmt.value;
				else var partAmtValue = 0;

				// Update  Advance rec (XAJAX Function)
				xajax_updateAdvanceRec(paymentDate, supplierId, fromDate, toDate, selSetldDate, advanceEntryId, stldAmtType.value, amtPaid,  partAmtValue);
			}
		}
	
		return true;	
	}

	
	function showSetldAmtType()
	{
		var advRowCount = document.getElementById("advAmtRowCount").value;
		var recChecked = false;
		for ( i=1; i<=advRowCount; i++ )
		{
			var advChecked = document.getElementById("advEntryId_"+i).checked;
			if (advChecked) {
				recChecked = true;
				showAdvRecRow(i, true);
			} else showAdvRecRow(i, false);
		}
		if (recChecked) showAdvHeadRow(true);	
		else showAdvHeadRow(false);
	}

	
	function showSingleSetldAmtType(i)
	{
		var advRowCount = document.getElementById("advAmtRowCount").value;
		for ( j=1; j<=advRowCount; j++ )
		{	
			var advChecked = document.getElementById("advEntryId_"+j).checked;
			if (!advChecked) {		
				document.getElementById("setldAmtCol_"+j).style.display='';	
				document.getElementById("setldAmtCol_"+j).innerHTML='&nbsp;';
				document.getElementById("partAmtCol_"+j).innerHTML='&nbsp;';
			}
		}

		var advChecked = document.getElementById("advEntryId_"+i).checked;
		if (advChecked) {			
			showAdvRecRow(i, true);
			showAdvHeadRow(true);
		} else {
			showAdvRecRow(i, false);			
		}
	}
	

	function showAdvHeadRow(enable)
	{
		if (enable) document.getElementById("setldAmtTypeHead").style.display='';
		else {
			document.getElementById("setldAmtTypeHead").style.display='none';
			document.getElementById("setldAmtHead").style.display='none';
			hideAllAmtTypeCol();
		}
	}

	function showAdvRecRow(i, enable)
	{
		var displaySetldType = "<select name='stldAmtType_"+i+"' id='stldAmtType_"+i+"' onchange=\"showAmtCol('"+i+"')\">";
		displaySetldType += "<option value=''>--select--</option>"; 
		displaySetldType += "<option value='FA'>Full Amount</option>";
		displaySetldType += "<option value='PA'>Part Amount</option>"; 
		displaySetldType += "</select>";  
		
		
		if (enable) {
			document.getElementById("setldAmtCol_"+i).style.display='';
			document.getElementById("setldAmtCol_"+i).innerHTML=displaySetldType;
		}
		else 	{
			document.getElementById("setldAmtCol_"+i).style.display='';	
			document.getElementById("setldAmtCol_"+i).innerHTML='&nbsp;';				
		}
	}
	
	function hideAllAmtTypeCol()
	{
		var advRowCount = document.getElementById("advAmtRowCount").value;
		for ( i=1; i<=advRowCount; i++ )
		{			
			document.getElementById("setldAmtCol_"+i).style.display='none';
			document.getElementById("partAmtCol_"+i).style.display='none';
		}
	}

	function hideAllAmtCol()
	{
		var advRowCount = document.getElementById("advAmtRowCount").value;
		for ( i=1; i<=advRowCount; i++ )
		{					
			document.getElementById("partAmtCol_"+i).style.display='none';
		}
	}
	

	function showAmtHeadRow(enable)
	{		
		if (enable) {
			document.getElementById("setldAmtTypeHead").style.display='';
			document.getElementById("setldAmtHead").style.display='';			
		}
		else {
			document.getElementById("setldAmtHead").style.display='none';
			hideAllAmtCol();
		}
	}

	function showAdvPartAmtRow(i, enable)
	{
		var amtHTML = "<input type='text' name='partAmt_"+i+"' id='partAmt_"+i+"' size='4' style='text-align:right'/>";

		var partAmtSelected = false;
		var advRowCount = document.getElementById("advAmtRowCount").value;
		for ( j=1; j<=advRowCount; j++ )
		{
			var advChecked = document.getElementById("advEntryId_"+j).checked;
			if (advChecked) {				
				var stldAmtType = document.getElementById("stldAmtType_"+j).value;
				if (stldAmtType=='PA') {
					partAmtSelected = true;
				} else 	{
					document.getElementById("partAmtCol_"+j).style.display='';
					document.getElementById("partAmtCol_"+j).innerHTML='&nbsp;';		
				}
			} else {
				document.getElementById("partAmtCol_"+j).style.display='';
				document.getElementById("partAmtCol_"+j).innerHTML='&nbsp;';		
			}
		}

		if (enable) {
			document.getElementById("partAmtCol_"+i).style.display='';
			document.getElementById("partAmtCol_"+i).innerHTML=amtHTML;
		}
		else if (partAmtSelected) {			
			document.getElementById("partAmtCol_"+i).style.display='';
			document.getElementById("partAmtCol_"+i).innerHTML='&nbsp;';
		} else if (!partAmtSelected) {
			hideAllAmtCol();
		}		
	}
	
	function showAmtCol(i)
	{
		showPartAmtHead();
		var advRowCount = document.getElementById("advAmtRowCount").value;
		for ( j=1; j<=advRowCount; j++ )
		{	
			var advChecked = document.getElementById("advEntryId_"+j).checked;
			if (!advChecked) {
				document.getElementById("partAmtCol_"+j).style.display='';
				document.getElementById("partAmtCol_"+j).innerHTML='&nbsp;';
			}
		}
		var stldAmtType = document.getElementById("stldAmtType_"+i).value;
		if (stldAmtType=='PA') {
			showAdvPartAmtRow(i, true);
		} else showAdvPartAmtRow(i, false); // Hide
	}
	
	function showPartAmtHead()
	{		
		var partAmtSelected = false;
		var advRowCount = document.getElementById("advAmtRowCount").value;
		for ( i=1; i<=advRowCount; i++ )
		{
			var advChecked = document.getElementById("advEntryId_"+i).checked;
			if (advChecked) {				
				var stldAmtType = document.getElementById("stldAmtType_"+i).value;
				if (stldAmtType=='PA') {
					partAmtSelected = true;
				}				
			} 
		}
		if (partAmtSelected) showAmtHeadRow(true);
		else showAmtHeadRow(false);
	}

	function validateLinkUpdate(form)
	{
		var supplier	= form.supplier.value;		
		var paymentDate = form.paymentDate.value;
		var paymentMethod = document.getElementById("paymentMethod");
		var chequeNo	  = document.getElementById("chequeNo");
		var bankName	 = document.getElementById("bankName");
		var paymentType	 = document.getElementById("paymentType");

		if (paymentDate=="") {
			alert("Please select a date");
			form.paymentDate.focus();
			return false;
		}

		if (supplier=="") {
			alert("Please select a Supplier");
			form.supplier.focus();
			return false;
		}

		if (paymentType.value!='S') {
			alert("Please select Settlement Payment type.");
			paymentType.focus();
			return false;
		}


		if (paymentType.value=='S') {			
			var fromDate = document.getElementById("fromDate");
			var toDate   = document.getElementById("toDate");
			
			var billingCompany = document.getElementById("billingCompany");
			var selSettlementDate = document.getElementById("selSettlementDate");
			
			
			if (fromDate.value=="") {
				alert("Please select From date.");
				fromDate.focus();
				return false;
			}

			if (toDate.value=="") {
				alert("Please select to date.");
				toDate.focus();
				return false;
			}

			if (selSettlementDate.value=="") {
				alert("Please select a Settlement Date.");
				selSettlementDate.focus();
				return false;
			}
		} // Payment type S- Ends here	

		return true;				
	}

	// Xajax Relaod List
	function reloadAdvanceList(paymentDate, supplierId, cId, fromDate, toDate, selSetldDate)
	{
		xajax_displayAdvanceEntry(paymentDate, supplierId, cId, fromDate, toDate, selSetldDate);
		xajax_displayOtherEntry(paymentDate, supplierId, cId);
	}
	
