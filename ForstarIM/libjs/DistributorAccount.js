function validateDistributorAccount(form, acConfirm)
{	
	var entryType 		= document.getElementById("entryType");
	var selDate		= form.selDate.value;
	var selDistributor	= form.selDistributor.value;
	var selCity		= document.getElementById("selCity");
	var commonReason	= document.getElementById("commonReason");
	var otherReason		= document.getElementById("otherReason");
	var paymentReceived	= document.getElementById("hidPaymentReceived").value;	
	var chequeReturnEntry 	= document.getElementById("hidChequeReturnEntry").value;
	var mode		= document.getElementById("hidMode").value;
	var defaultReasonType	= document.getElementById("defaultReasonType");
	var currentDate		= document.getElementById("currentDate").value;
	var despatchDate	= document.getElementById("despatchDate").value;
	var creditPeriod	= document.getElementById("creditPeriod").value;	
	if (defaultReasonType.value!='AP' || mode==0) var referenceInvoice    = document.getElementById("referenceInvoice");
	var amount		= document.getElementById("amount");
	var advEntryExist		= document.getElementById("advanceEntryExist").value;
	var advEntryConfirmed	= document.getElementById("advanceEntryConfirmed").value;

	if (acConfirm && advEntryExist!="") {
		if (advEntryConfirmed=="") {
			alert("Please adjust advance amount.");
			return false;
		}
	}

	if (entryType.value=="") {
		alert("Please select a entry type.");
		entryType.focus();
		return false;
	}

	if (selDate=="") {
		alert("Please select a date.");
		form.selDate.focus();
		return false;
	}
	
	if (selDistributor=="") {
		alert("Please select a Distributor.");
		form.selDistributor.focus();
		return false;
	}

	if (selCity.value=="") {
		alert("Please select a City.");
		selCity.focus();
		return false;
	}
	
	if (commonReason.value=="") {
		alert("Please select a reason");
		commonReason.focus();
		return false;	
	}

	if (commonReason.value=="OT" && otherReason.value=="") {
		alert("Please enter reason.");
		otherReason.focus();
		return false;
	}

	if (amount.value=="") {
			alert("Please enter a amount.");
			amount.focus();
			return false;
		}
		
		if (!chkValidNumber(amount.value)) {
			amount.focus();
			return false;
		}
	/*	
	if (defaultReasonType.value!='AP' || mode==0) {
		if (referenceInvoice.value==""  && defaultReasonType.value!='AP' && paymentReceived=="") {
			//alert("Please select atleast one reference invoice.");
			alert("Please select a reference invoice.");
			referenceInvoice.focus();
			return false;
		}
	}
	*/

	if ((defaultReasonType.value!='AP' || mode==0) || (acConfirm && defaultReasonType.value=='AP' && mode==0)) {
			
			var pmtType		= document.getElementById("pmtType").value; /* S-Single, M-Multiple*/
			var bcApplicable	= false;
			var pcApplicable 	= false;
	
			if (pmtType=='M') {
				if (defaultReasonType.value=='PR' || defaultReasonType.value=='AP') {
					var bankCharges		= parseFloat(document.getElementById("bankCharges").value);
					if (validNumber(bankCharges)) bcApplicable = true;
		
				} else if (defaultReasonType.value=='CR') {
					var chqReturnBankCharge = parseFloat(document.getElementById("chqReturnBankCharge").value);
					if (validNumber(chqReturnBankCharge)) bcApplicable = true;
		
					var penaltyCharge	= parseFloat(document.getElementById("penaltyCharge").value);
					if (validNumber(penaltyCharge)) pcApplicable = true;
				}
			}			

			if (pmtType!="") {
				var rowCount	= document.getElementById("hidTableRowCount").value;
				var invRecSize	= document.getElementById("invRecSize").value;

				var refInvSelected = false;
				var totPendingAmt = 0;
				var crPeriodExceed = false;
				var balDueAmt = "";
				var bcApplied = false;
				var pcApplied = false;
				var invCount = 0;				
				
				if (rowCount>0) {					
					for (i=0; i<rowCount; i++) {
						var status = document.getElementById("status_"+i).value;
						if (status!='N') {
							var refInv 		= document.getElementById("refInv_"+i);
							var refAmt 		= document.getElementById("refAmt_"+i);
							var refInvDespatchDate	= document.getElementById("hidDespatchDate_"+i).value;
							balDueAmt		= document.getElementById("hidBalDueAmt_"+i).value;
							totPendingAmt = parseFloat(totPendingAmt)+parseFloat(refAmt.value);
							var bcApp		= document.getElementById("bcApp_"+i).checked;
							var pcApp		= document.getElementById("pcApp_"+i).checked;

							if (bcApp && refInv.value!='ADV') bcApplied = true;
							if (pcApp) pcApplied = true;			
									
							fy = refInv.value.split("_");

							if (refInv.value=="" || fy[0]=='FY') {
								alert("Please select a Ref. invoice.");
								refInv.value = "";
								refInv.focus();
								return false;
							}							
							
							//&& refInv.value!="ADV"
							if (refInv.value!="" && fy[0]!='FY') {
								refInvSelected = true;
								invCount++;
							}
								
							if (refAmt.value=="") {
								alert("Please enter amount.");
								refAmt.focus();
								return false;
							}

							if (!chkValidNumber(refAmt.value)) {
								refAmt.focus();
								return false;
							}

							if (refInv.value!="ADV" && parseFloat(refAmt.value)>parseFloat(balDueAmt)) {
								alert("Please check pending payment value.\nPending payment value is greater than the balance due amount (BAL DUE="+balDueAmt+").");
								return false;
							}
							
							if (defaultReasonType.value=="PR" || defaultReasonType.value=='AP') {
								
								var pmtMode		= document.getElementById("paymentMode").value;
								var chqueDate		= document.getElementById("chqDate").value;
								if ((pmtMode=="CHQ" || pmtMode=="RT") && chqueDate!="") {
									var days = checkDateSelected(chqueDate,refInvDespatchDate);
									var diffDays = parseInt(days)-parseInt(creditPeriod);
									//alert(diffDays+","+days+"-"+creditPeriod);
									if (diffDays>0) crPeriodExceed = true;
								}
							}							
						}
					}  // For Loop Ends Here
				} // Row Count checking End

				//alert(invCount);

				if (refInvSelected==false) {
					alert("Please select atleast one ref. invoice");
					return false;
				}

				if (bcApplicable && !bcApplied) {
					alert("Please select a bank charge applicable invoice. ");
					return false;
				}

				if (pcApplicable && !pcApplied) {
					alert("Please select a penalty charge applicable invoice. ");
					return false;
				}

				if (crPeriodExceed) {
					/*
					var cfmMsg = "Cheque date is beyond the credit period.\nDo you wish to continue?";
					if (!confirm(cfmMsg)) return false;
					*/
					alert("Please check Cheque date.\nCheque date is beyond the credit period.");
					return false;
				}
			
				if (!validateRefInvRepeat()) {
					return false;
				}
				var selAmount = document.getElementById("amount").value;
				if (parseFloat(totPendingAmt)>parseFloat(selAmount)) {
					alert("Ref.invoice allocation amount is greater than the total amount.");
					return false;
				}

				var balAdvAmt = "";
				if (defaultReasonType.value=='PR') {
					balAdvAmt = document.getElementById("balAdvAmt").value;
				}
				
				//&& advEntryExist==""
				if (parseFloat(Math.ceil(totPendingAmt))!=parseFloat(Math.ceil(selAmount)) && balAdvAmt=="" ) {
					alert("Please adjust balance amount.");
					return false;
				}

				if (acConfirm && !refInvSelected) {
					alert("Please select atleast one ref. invoice");
					return false;
				}
				
			} 
			/*
			else {
			// Single Ref inv section
				if (referenceInvoice.value==""  && defaultReasonType.value!='AP') {
					alert("Please select a reference invoice.");
					referenceInvoice.focus();
					return false;
				}

				if (defaultReasonType.value=="PR" || defaultReasonType.value=='AP') {
					var pmtMode		= document.getElementById("paymentMode").value;
					var chqueDate		= document.getElementById("chqDate").value;
					if ((pmtMode=="CHQ" || pmtMode=="RT") && chqueDate!="") {
						var days = checkDateSelected(chqueDate,despatchDate);
						var diffDays = parseInt(days)-parseInt(creditPeriod);
						//alert(diffDays+","+days+"-"+creditPeriod);
						if (diffDays>0) {							
							alert("Please check Cheque date.\nCheque date is beyond the credit period.");
							return false;
						}
					}
				}

				if (acConfirm && referenceInvoice.value=="") {
					alert("Please select a reference invoice.");
					referenceInvoice.focus();
					return false;
				}
			// Single Ref inv section Ends here
			}
			*/
	}	

	// $paymentModeArr = array("CHQ"=>"Cheque", "CH"=>"Cash", "RT"=>"RTGS");
	// Payment Received
	if (entryType.value=="PR" || paymentReceived!="" || defaultReasonType.value=='AP') {
		
		var paymentMode		= document.getElementById("paymentMode");
		var chqRtgsNo		= document.getElementById("chqRtgsNo");
		var chqDate		= document.getElementById("chqDate");
		//var bankName		= document.getElementById("bankName");
		//var accountNo		= document.getElementById("accountNo");
		//var branchLocation	= document.getElementById("branchLocation");
		var depositedBankAccount 	= document.getElementById("depositedBankAccount");
		var valueDate			= document.getElementById("valueDate");
		var bankCharges			= document.getElementById("bankCharges");
		var bankChargeDescription	= document.getElementById("bankChargeDescription");
		var amount			= document.getElementById("amount");
		var distBankAccount		= document.getElementById("distBankAccount");

		/*
		if ((defaultReasonType.value!='AP' || mode==0) || (acConfirm && defaultReasonType.value=='AP' && mode==0)) {
			var pmtType			= document.getElementById("pmtType").value; // S-Single, M-Multiple
			
			if (pmtType=='M') {
				var rowCount	= document.getElementById("hidTableRowCount").value;
				var refInvSelected = false;
				
				if (rowCount>0) {
					for (i=0; i<rowCount; i++) {
						var status = document.getElementById("status_"+i).value;
						if (status!='N') {
							var refInv = document.getElementById("refInv_"+i);
							var refAmt = document.getElementById("refAmt_"+i);
							
								if (refInv.value=="") {
									alert("Please select a Ref. invoice.");
									refInv.focus();
									return false;
								}							
							
								if (refInv.value!="") {
									refInvSelected = true;
								}
								
								if (refAmt.value=="") {
									alert("Please enter amount.");
									refAmt.focus();
									return false;
								}
						}
					}  // For Loop Ends Here
				} // Row Count checking End
				if (refInvSelected==false) {
					alert("Please select atleast one ref. invoice");
					return false;
				}
			
				if (!validateRefInvRepeat()) {
					return false;
				}
			} else {
				if (referenceInvoice.value==""  && defaultReasonType.value!='AP') {
					//alert("Please select atleast one reference invoice.");
					alert("Please select a reference invoice.");
					referenceInvoice.focus();
					return false;
				}
			}
		}
		*/
		if (paymentMode.value=="") {
			alert("Please select a payment mode.");
			paymentMode.focus();
			return false;
		}

		if ((paymentMode.value=="CHQ" || paymentMode.value=="RT") && chqRtgsNo.value=="") {
			alert("Please enter Cheque/RTGS No.");
			chqRtgsNo.focus();
			return false;
		}

		if ((paymentMode.value=="CHQ" || paymentMode.value=="RT") && chqDate.value=="") {
			alert("Please enter Cheque/RTGS date.");
			chqDate.focus();
			return false;
		}
		
		if (chqDate.value!="" && valueDate.value!="" && convertTime(chqDate.value)>convertTime(valueDate.value)) {
			alert("Please check cheque date.\nCheque date should not be greater than the value date.");
			chqDate.focus();
			return false;
		}
		
		
		if (acConfirm && (paymentMode.value=="CHQ" || paymentMode.value=="RT") && distBankAccount.value=="") {
			alert("Please select a distributor bank account.");
			distBankAccount.focus();
			return false;
		}

		/*
		if (acConfirm && (paymentMode.value=="CHQ" || paymentMode.value=="RT") && bankName.value=="") {
			alert("Please enter a bank name.");
			bankName.focus();
			return false;
		}

		if (acConfirm && (paymentMode.value=="CHQ" || paymentMode.value=="RT") && accountNo.value=="") {
			alert("Please enter a bank account no.");
			accountNo.focus();
			return false;
		}

		if (acConfirm && (paymentMode.value=="CHQ" || paymentMode.value=="RT") && branchLocation.value=="") {
			alert("Please enter a branch location.");
			branchLocation.focus();
			return false;
		}
		*/

		if (acConfirm && (paymentMode.value=="CHQ" || paymentMode.value=="RT") && depositedBankAccount.value=="") {
			alert("Please select a deposited in COMPANY BANK ACCOUNT.");
			depositedBankAccount.focus();
			return false;
		}

		if (acConfirm && valueDate.value=="" || ((paymentMode.value=="RT" || paymentMode.value=="CH") && valueDate.value=="")) {
			alert("Please enter a value date.");
			valueDate.focus();
			return false;
		}
		
		if (valueDate.value!="" && convertTime(valueDate.value)>convertTime(currentDate)) {
			alert("Please check value date.\nValue date should not be greater than the current date.");
			valueDate.focus();
			return false;
		}

		/*
		if (amount.value=="") {
			alert("Please enter a amount.");
			amount.focus();
			return false;
		}
		
		if (!chkValidNumber(amount.value)) {
			amount.focus();
			return false;
		}
		*/

		if ((bankCharges.value!="" && bankCharges.value!=0)) {
			if (!chkValidNumber(bankCharges.value)) {
				bankCharges.focus();
				return false;
			}
		}

		if ((bankCharges.value!="" && bankCharges.value!=0) && bankChargeDescription.value=="") {
			alert("Please enter bank charge description.");
			bankChargeDescription.focus();
			return false;
		}
		
	}

	// Other Modes	
	if (entryType.value!="" && entryType.value!="PR" && paymentReceived=="" && defaultReasonType.value!='AP') {
		var amount		= form.amount.value;
		var amtDescription	= form.amtDescription.value;

		if (amount=="") {
			alert("Please enter a amount.");
			form.amount.focus();
			return false;
		}
		
		if (!chkValidNumber(amount)) {
			form.amount.focus();
			return false;
		}
	
		/*
		if (amtDescription=="") {
			alert("Please enter description.");
			form.amtDescription.focus();
			return false;
		}	
		*/

		// Cheque Return Entry
		if (chequeReturnEntry=="CR") {
			var chqReturnBankCharge = document.getElementById("chqReturnBankCharge");
			var penaltyCharge = document.getElementById("penaltyCharge");


			if (mode==1) {
				var pendingCheque = document.getElementById("pendingCheque");
				if (pendingCheque.value=="") {
					alert("Please select a pending cheque.");
					pendingCheque.focus();
					return false;
				}
			}	
			

			if (chqReturnBankCharge.value=="") {
				alert("Please enter bank charges.");
				chqReturnBankCharge.focus();
				return false;
			}
			
			if (!chkValidNumber(chqReturnBankCharge.value)) {
				chqReturnBankCharge.focus();
				return false;
			}

			if (penaltyCharge.value=="") {
				alert("Please enter penalty charge.");
				penaltyCharge.focus();
				return false;
			}

			if (!chkValidNumber(penaltyCharge.value)) {
				penaltyCharge.focus();
				return false;
			}
		}
	} // Other entry type ends here

	var chkListSel = false;
	
	if (commonReason.value!="" && commonReason.value!="OT") {
			var chkListRowCount	= document.getElementById("chkListRowCount").value;
			
			for (var i=1; i<=chkListRowCount; i++) {
				var chkListId 	= document.getElementById("chkListId_"+i);
				var required	= document.getElementById("required_"+i);
				var chkListName	= document.getElementById("chkListName_"+i).value;
				
				if (required.value=="Y" && !chkListId.checked) {
					alert("Please verify "+chkListName);
					chkListId.focus();
					return false;
				}
				
				if (chkListId.checked) {
					chkListSel = true;
				}
			}

			if (!chkListSel && chkListRowCount>0) {
				alert("Please select atleast one check list.");
				return false;
			}	
		} // Reason chk list end

	if (!confirmSave()) return false;
	return true;
}


	function reasonOT()
	{
		var commonReason = document.getElementById("commonReason").value;
		if (commonReason=="OT") {
			document.getElementById("otherRn").style.display="";
		} else document.getElementById("otherRn").style.display="none";
	}

	// Entry Type selection
	function etOption(prType)
	{
		var entryType = document.getElementById("entryType").value;
		var hidPR = document.getElementById("hidPR").value;	
		//alert(entryType+"="+hidPR);
		if (prType=="PR" || hidPR=="PR")  {
			document.getElementById("PRType").style.display= "";
			document.getElementById("OtherEntryType").style.display= "none";
		} else if (entryType=="AC" || entryType=="AD") {
			document.getElementById("OtherEntryType").style.display= "";
			document.getElementById("PRType").style.display= "none";
		} else {
			document.getElementById("PRType").style.display= "none";
			document.getElementById("OtherEntryType").style.display= "none";
		}
	}

	//ADD MULTIPLE Item- ADD ROW START
	function addNewRefInv(tableId, chkListName, chkPointEntryId)
	{
		var tbl		= document.getElementById(tableId);	
		var lastRow	= tbl.rows.length-1;	
		var row		= tbl.insertRow(lastRow);
		
		row.height	= "28";
		row.className 	= "whiteRow";
		row.align 	= "center";
		row.id 		= "row_"+fieldId;	
		
		var cell1	= row.insertCell(0);
		var cell2	= row.insertCell(1);
		var cell3	= row.insertCell(2);
		var cell4	= row.insertCell(3);
		var cell5	= row.insertCell(4);
		var cell6	= row.insertCell(5);
	
		cell1.className	= "listing-item"; cell1.align	= "center";
		cell2.className	= "listing-item"; cell2.align	= "center";
		cell3.className	= "listing-item"; cell3.align	= "center";
		cell4.className	= "listing-item"; cell4.align	= "center";
		cell5.className	= "listing-item"; cell5.align	= "center";
		cell6.className	= "listing-item"; cell6.align	= "center";
		cell4.id = "bcCol_"+fieldId;
		cell5.id = "pcCol_"+fieldId;
		
		var invList	= "<select name='refInv_"+fieldId+"' id='refInv_"+fieldId+"' onchange=\"validateRefInvRepeat();xajax_refInvVal('"+fieldId+"', document.getElementById('refInv_"+fieldId+"').value)\">";		
		<?php
			foreach ($invoiceRecs as $invoiceId=>$invoiceNo) {
		?>
		invList		+= "<option value='<?=$invoiceId?>'><?=$invoiceNo?></option>";
		<?php
			}
		?>
		invList		+= "</select>";
		
		var ds = "N";	
		//if( fieldId >= 1) 
		var imageButton = "<a href='###' onClick=\"setRefInvItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
			
		var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input name='chkListEntryId_"+fieldId+"' type='hidden' id='chkListEntryId_"+fieldId+"' value='"+chkPointEntryId+"'><input name='hidRefInvId_"+fieldId+"' type='hidden' id='hidRefInvId_"+fieldId+"' value=''><input name='hidDespatchDate_"+fieldId+"' type='hidden' id='hidDespatchDate_"+fieldId+"' value='' readonly><input name='hidBalDueAmt_"+fieldId+"' type='hidden' id='hidBalDueAmt_"+fieldId+"' value='' readonly>";	
		
		cell1.innerHTML	= invList;
		cell2.innerHTML	= "<input type='text' name='refInvAmt_"+fieldId+"' id='refInvAmt_"+fieldId+"' size='8' style='text-align:right; border:none;' readonly>";	
		cell3.innerHTML	= "<input type='text' name='refAmt_"+fieldId+"' id='refAmt_"+fieldId+"' size='8' style='text-align:right;' onkeyup='calcPendingAmt();chkBalAsAdvAmt();' autocomplete='off'>";	
		cell4.innerHTML	= "<input type='checkbox' name='bcApp_"+fieldId+"' id='bcApp_"+fieldId+"' value='Y' class='chkBox' onclick=\"bcChk('"+fieldId+"');\">";
		cell5.innerHTML	= "<input type='checkbox' name='pcApp_"+fieldId+"' id='pcApp_"+fieldId+"' value='Y' class='chkBox' onclick=\"pcChk('"+fieldId+"');\">";
		cell6.innerHTML = imageButton+hiddenFields;	
		
		fieldId		= parseInt(fieldId)+1;	
		document.getElementById("hidTableRowCount").value = fieldId;
		chkPmtType();						
	}

	function setRefInvItemStatus(id)
	{
		if (confirmRemoveItem()) {			
			document.getElementById("status_"+id).value = document.getElementById("IsFromDB_"+id).value;
			document.getElementById("row_"+id).style.display = 'none';		
			calcPendingAmt();
			chkPmtType();
			chkBalAsAdvAmt();
		}
		return false;
	}


	/* ------------------------------------------------------ */
	// Duplication check starts here
	/* ------------------------------------------------------ */
	var cArr = new Array();
	var cArri = 0;	
	function validateRefInvRepeat()
	{			
		if (Array.indexOf != 'function') {  
			Array.prototype.indexOf = function(f, s) {
			if (typeof s == 'undefined') s = 0;
				for (var i = s; i < this.length; i++) {   
				if (f === this[i]) return i; 
				} 
			return -1;  
			}
		}
		
		var rc = document.getElementById("hidTableRowCount").value;
		var prevOrder = 0;
		var arr = new Array();
		var arri=0;

		for (j=0; j<rc; j++) {
			var status = document.getElementById("status_"+j).value;
			if (status!='N') {
				var rv = document.getElementById("refInv_"+j).value;
				
				if ( arr.indexOf(rv) != -1 )    {
					alert("Please make sure the Ref. Invoice is not duplicate.");
					document.getElementById("refInv_"+j).value="";
					document.getElementById("refInv_"+j).focus();
					return false;
				}		
				arr[arri++]=rv;
			}
		}

		var pmtType = document.getElementById("pmtType").value;
		var amount = document.getElementById("amount").value;
		var amtEntered = true;
		if (pmtType=="M" && amount=="") {

			alert("Please enter amount");	
			document.getElementById("amount").focus();

			for (j=0; j<rc; j++) {
				var status = document.getElementById("status_"+j).value;
				if (status!='N') {					
					document.getElementById("refInv_"+j).value="";
				}
			}
			return false;
		}

		return true;
	}

	function disPmtType()
	{	
		disRefInvSec();

		document.getElementById("multipleRefInvRow").style.display="";		
		//refInvSection
		/*
		var pmtType = document.getElementById("pmtType").value;
		if (pmtType=='S') {
			document.getElementById("singleRefInvRow").style.display="";
			document.getElementById("singleInvRefVal").style.display="";
			document.getElementById("multipleRefInvRow").style.display="none";
		} else {
			document.getElementById("singleRefInvRow").style.display="none";
			document.getElementById("singleInvRefVal").style.display="none";
			document.getElementById("multipleRefInvRow").style.display="";		
		}
		*/
	}

	function getRefInv()
	{
		var selDistributor 	= document.getElementById("selDistributor").value;
		var selCity 		= document.getElementById("selCity").value;
		var defaultReasonType	= document.getElementById("defaultReasonType").value;
		var selMode		= document.getElementById("selMode").value;

		var rc = document.getElementById("hidTableRowCount").value;
		for (var i=0; i<rc; i++) {
			xajax_getInvoices("refInv_"+i, selDistributor, selCity, '', '');
		}
	}

	// Calc Pending Amt
	function calcPendingAmt()
	{
		var rc = document.getElementById("hidTableRowCount").value;
		var refAmt = 0;
		var totRefPendingAmt = 0;
		for (i=0; i<rc; i++) {
			var status = document.getElementById("status_"+i).value;
			if (status!='N') {
				refAmt = document.getElementById("refAmt_"+i).value;
				totRefPendingAmt = parseFloat(totRefPendingAmt)+parseFloat(refAmt);
			}
		}

		if (!isNaN(totRefPendingAmt)) document.getElementById("totPmtVal").value = number_format(totRefPendingAmt,2,'.','');
	}

	function disRefInvSec()
	{		
		var amt = document.getElementById("amount").value;
		if (!validNumber(amt)) {
			document.getElementById("refInvSection").style.display="none";	
		} else {
			 document.getElementById("refInvSection").style.display="";
		}
	}

	// Check adv amt
	/*
	function chkAdvAmt()
	{
		var amt = parseFloat(document.getElementById("amount").value);
		var defaultReasonType	= document.getElementById("defaultReasonType").value;
		var mode		= document.getElementById("hidMode").value;

		if (defaultReasonType!='AP' || mode==0) {
			var pmtType = document.getElementById("pmtType").value;
			if (pmtType=='S' && amt!=0) {
				var balDueAmt = parseFloat(document.getElementById("balDueAmt").value); // Single Invoice Pending Amt val
				//alert(amt+">"+balDueAmt);
				var referenceInvoice = document.getElementById("referenceInvoice").value;
				if (amt>balDueAmt) {
					document.getElementById("pmtType").value = 'M';
					document.getElementById("refInv_0").value=referenceInvoice;
					xajax_refInvVal('0', referenceInvoice);
					disPmtType();
				}
			}
		}
	}
	*/

	// Display Payment Mode
	function disPmtMode()
	{
		var paymentMode = document.getElementById("paymentMode");

		if ((paymentMode.value=="CHQ" || paymentMode.value=="RT")) {
			document.getElementById("chqRTGSRow").style.display="";
			document.getElementById("chqDateRow").style.display="";
			document.getElementById("distBACRow").style.display="";
			document.getElementById("cpnyBACRow").style.display="";
		} else {
			document.getElementById("chqRTGSRow").style.display="none";
			document.getElementById("chqDateRow").style.display="none";
			document.getElementById("distBACRow").style.display="none";
			document.getElementById("cpnyBACRow").style.display="none";
			document.getElementById("distBankAccount").value 	= "";
			document.getElementById("depositedBankAccount").value 	= "";
		}
	}

	function chkPmtType()
	{
		var rc = document.getElementById("hidTableRowCount").value;
		var numInv = 0;
		for (i=0; i<rc; i++) {
			var status = document.getElementById("status_"+i).value;
			if (status!='N') {
				numInv = parseInt(numInv)+1;
			}
		}

		if (numInv>1) {
			document.getElementById("pmtType").value = "M";
		} else document.getElementById("pmtType").value = "S";

		displayExtraCharge();
	}

	// Bc Check
	function bcChk(rowId)
	{	
		if (!document.getElementById("bcApp_"+rowId).checked) chk = false;
		else chk = true;	
		var rc = document.getElementById("hidTableRowCount").value;
		for (j=0; j<rc; j++) {
			document.getElementById("bcApp_"+j).checked = false;
		}
		document.getElementById("bcApp_"+rowId).checked = chk;
	}

	// Penalty Charge chk
	function pcChk(rowId)
	{	
		if (!document.getElementById("pcApp_"+rowId).checked) chk = false;
		else chk = true;	
		var rc = document.getElementById("hidTableRowCount").value;
		for (j=0; j<rc; j++) {
			document.getElementById("pcApp_"+j).checked = false;
		}
		document.getElementById("pcApp_"+rowId).checked = chk;
	}

	function displayExtraCharge()
	{		
		var defaultReasonType 	= document.getElementById("defaultReasonType").value;
		var pmtType = document.getElementById("pmtType").value;
		var bcApplicable	= false;
		var pcApplicable 	= false;

		if (defaultReasonType=='PR' || defaultReasonType=='AP') {
			var bankCharges		= parseFloat(document.getElementById("bankCharges").value);
			if (validNumber(bankCharges)) bcApplicable = true;

		} else if (defaultReasonType=='CR') {
			var chqReturnBankCharge = parseFloat(document.getElementById("chqReturnBankCharge").value);
			if (validNumber(chqReturnBankCharge)) bcApplicable = true;

			var penaltyCharge	= parseFloat(document.getElementById("penaltyCharge").value);
			if (validNumber(penaltyCharge)) pcApplicable = true;
		}

		var rc = document.getElementById("hidTableRowCount").value;
		
		for (i=0; i<rc; i++) {			
			document.getElementById("bcCol_"+i).style.display = "none";
			document.getElementById("pcCol_"+i).style.display = "none";

			if (pmtType=='M') {
				if (bcApplicable) {
					document.getElementById("bcCol_"+i).style.display = "";
				} else document.getElementById("bcApp_"+i).checked = false;
				
				if (pcApplicable) {
					document.getElementById("pcCol_"+i).style.display = "";
				} else document.getElementById("pcApp_"+i).checked = false;
			} else {
				document.getElementById("bcApp_"+i).checked = false;
				document.getElementById("pcApp_"+i).checked = false;
			}
		} // Loop Ends here

		if (pmtType=='M') {
			document.getElementById("bcAppHCol").style.display = "none";
			document.getElementById("pcAppHCol").style.display = "none";
			document.getElementById("bcAppFCol").style.display = "none";
			document.getElementById("pcAppFCol").style.display = "none";			
			if (bcApplicable) {
				document.getElementById("bcAppHCol").style.display = "block";
				document.getElementById("bcAppFCol").style.display = "block";
			}
			
			if (pcApplicable) {
				document.getElementById("pcAppHCol").style.display = "";
				document.getElementById("pcAppFCol").style.display = "";
			}
		} else {			
			document.getElementById("bcAppHCol").style.display = "none";
			document.getElementById("pcAppHCol").style.display = "none";
			document.getElementById("bcAppFCol").style.display = "none";
			document.getElementById("pcAppFCol").style.display = "none";
		}
	}

	// time ticker
	//to store timeout ID
	var tID;
	function tickTimer(t, distributorACId)
	{		
		//if time is in range
		if (t>=0) {
			var timeCalc = Math.floor(t);
			//alert(timeCalc/60);
			document.getElementById("timeTickerRow").innerHTML= "Time Remaining "+Math.floor(t/60) + ":" + (t%60)+" seconds.";
			t=t-1;
			tID=setTimeout("tickTimer('"+t+"','"+distributorACId+"')",1000);
		}
		//stop the timeout event
		else
		{			
			setTimeout("killTimer('"+tID+"')",1000);
			document.getElementById("hidDistributorACId").value=distributorACId;
			document.getElementById("timeTickerRow").innerHTML = "Edit Lock Released.";
		}
		//alert(tID+","+distributorACId);
	}	
	//function to stop the timeout event
	function killTimer(id)
	{		
		clearTimeout(id);
		document.getElementById("frmDistributorAccount").submit();
	}
	// time ticker Ends Here

	var t ='<?=$refreshTimeLimit?>';	
	var sTime = Math.floor(t/60)+":"+(t%60);	
	var limit= sTime;		
	
	if (document.images){	
		var parselimit=limit.split(":");
		parselimit=parselimit[0]*60+parselimit[1]*1;
	}
	var curtime = 0;
	function beginrefresh()
	{		
		if (!document.images) return;
		if (parselimit==1) {
			document.getElementById("frmDistributorAccount").submit();
		}
		else { 			
			parselimit = parselimit-1 ;
			var curmin=Math.floor(parselimit/60);
			var cursec=parselimit%60;
			if (curmin!=0)  curtime=curmin+" minutes and "+cursec+" seconds left until page refresh!";
			else curtime=cursec+" seconds left until page refresh!";
			
			document.getElementById("refreshMsgRow").innerHTML = curtime;
			setTimeout("beginrefresh()",1000);
		}
	}

	function refreshDistAC()
	{
		var uptdMsg	= "Do you wish to refresh distributor account list?";
		if(confirm(uptdMsg)) {
			xajax_refreshDistAC();
			return true;
		}
		return false;
	}

	function chkFYSelection()
	{
		var invoiceFilter = document.getElementById("invoiceFilter");
		var fy = invoiceFilter.value.split("_");
		if (fy[0]=='FY') invoiceFilter.value = "";
		
	}

	function filterRefInv()
	{
		var selDistributor 	= document.getElementById("selDistributor").value;
		var selCity 		= document.getElementById("selCity").value;
		var defaultReasonType	= document.getElementById("defaultReasonType").value;
		var selMode		= document.getElementById("selMode").value;
		if (defaultReasonType!='AP') {
			var rc = document.getElementById("hidTableRowCount").value;
			xajax_filterRefInv(selDistributor, selCity, defaultReasonType, selMode, rc);
		}
	}

	// Check balance allocation amt as advance amt
	function chkBalAsAdvAmt()
	{		
		var defaultReasonType	= document.getElementById("defaultReasonType");
		var rowCount	= document.getElementById("hidTableRowCount").value;
		var invRecSize	= document.getElementById("invRecSize").value;
		var amount	= document.getElementById("amount").value;
		document.getElementById("balAdvAmt").value = "";
		document.getElementById("balAdvAmtRow").style.display = "none";
		var mode	= document.getElementById("hidMode").value;

		var invCount = 0;
		var totPendingAmt = 0;
		var balAdvAmt = 0;
		var advRow = "";
		var advRowCount = 0;
		
		if (defaultReasonType.value=='PR' || (mode==0 && defaultReasonType.value=='AP')) {
			for (i=0; i<rowCount; i++) {
				var status = document.getElementById("status_"+i).value;
				if (status!='N') {
					var refInv 		= document.getElementById("refInv_"+i);
					var refAmt 		= document.getElementById("refAmt_"+i);
					refAmt			= (refAmt.value!="")?refAmt.value:0;

					balDueAmt		= document.getElementById("hidBalDueAmt_"+i).value;
					totPendingAmt = parseFloat(totPendingAmt)+parseFloat(refAmt);
					var fy = refInv.value.split("_");
			
					if (parseFloat(refAmt)==parseFloat(balDueAmt) && refInv.value!="" && fy[0]!='FY') {
						invCount++;
					}

					if (refInv.value=='ADV') {
						balAdvAmt = refAmt;						
						advRow = i;
						advRowCount++;
					}
				} // Status check ends here
			} // RC Loop Ends here
			
			if (advRow!="" || advRowCount!=0) {
				balAdvAmt = parseFloat(amount)-parseFloat(totPendingAmt);			
				//document.getElementById("balAdvAmtRow").style.display = "";
				document.getElementById("balAdvAmt").value = number_format(balAdvAmt,2,'.','');				
				document.getElementById("refAmt_"+advRow).value = number_format(balAdvAmt,2,'.','');
				calcPendingAmt();
			}			

			if (invRecSize==invCount && advRow=="") {
				balAdvAmt = parseFloat(amount)-parseFloat(totPendingAmt);
				if (balAdvAmt>1) {
					document.getElementById("balAdvAmtRow").style.display = "";
					document.getElementById("balAdvAmt").value = number_format(balAdvAmt,2,'.','');
				}
			}
		} // Payment received ends here
	}

	function distBankAC()
	{
		var distributorId 	= document.getElementById("selDistributor").value;
		var cityId 		= document.getElementById("selCity").value;
		var defaultReasonType	= document.getElementById("defaultReasonType");
		if ((defaultReasonType.value=='PR' || defaultReasonType.value=='AP') && distributorId && cityId) {
			xajax_getDistBankAC(distributorId,cityId);
		}
	}

	// Valid Advance amt entry check
	function validAdvAmt()
	{		
		var distributorId 	= document.getElementById("selDistributor").value;
		var defaultReasonType	= document.getElementById("defaultReasonType").value;
		var mode		= document.getElementById("hidMode").value;
		var advAmtRestrictionEnabled = document.getElementById("advAmtRestrictionEnabled").value;

		if (mode==1 && defaultReasonType=='AP' && distributorId && advAmtRestrictionEnabled!="") {
			xajax_overdueAmt(distributorId, mode);
		}
	}

	function enableDistACBtn(mode)
	{
		if (mode==1) {
			document.getElementById("cmdAdd").disabled = false;
			document.getElementById("cmdAdd1").disabled = false;
		} else if (mode==0) {
			document.getElementById("cmdSaveChange").disabled = false;
			//document.getElementById("cmdSaveChange1").disabled = false;
		}
	}
	
	function disableDistACBtn(mode)
	{		
		if (mode==1) {
			document.getElementById("cmdAdd").disabled = true;
			document.getElementById("cmdAdd1").disabled = true;
		} else if (mode==0) {
			document.getElementById("cmdSaveChange").disabled = true;
			//document.getElementById("cmdSaveChange1").disabled = true;
		}
	}

	