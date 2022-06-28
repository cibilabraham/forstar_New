function validateOrderDispatched(form, isComplete)
{	
	var selSOId		= form.selSOId.value;
	var paymentStatus	= form.paymentStatus.value;		
	var dispatchDate	= form.dispatchDate.value;
	//var selStatus		= form.selStatus.value;		
	//var isComplete		= form.isComplete.checked;
	var salesOrderItem	= form.salesOrderItem.value;
	var selTransporter	= form.selTransporter.value;
	//var docketNo		= form.docketNo.value;
	var validDespatchDate	= document.getElementById("validDespatchDate").value;
	var lastDateStatus	= document.getElementById("lastDateStatus").value;
	var dateExtended	= document.getElementById("dateExtended").checked;
	
	if (selSOId=="") {
		alert("Please select a Sales Order ID.");
		form.selSOId.focus();
		return false;
	}

	if (salesOrderItem<=0) {
		alert(" No Products Found! ");
		form.selSOId.focus();
		return false;	
	}
	
	/* Temp Hide
	if (selStatus=="") {
		alert("Please select a status.");
		form.selStatus.focus();
		return false;
	}
	*/
	// Checking the Qty
	if (isComplete!="") {
		/*
		var isStockAvailable 	= false;
		var hidProductRowCount	=	form.hidProductRowCount.value;
		for (var i=1; i<=hidProductRowCount; i++) {
			var existingQty  = parseFloat(document.getElementById("existingQty_"+i).value);
			var orderedQty	 = parseFloat(document.getElementById("orderedQty_"+i).value);
			if (existingQty>orderedQty)  {
				isStockAvailable = true;
			}				
		}
	
		if (!isStockAvailable) {
			alert("Required quantity is not available");
			return false;
		}
		*/
		var validInvoiceDate	= document.getElementById("validInvoiceDate").value;
		var invoiceDate		= document.getElementById("invoiceDate");
		var invoiceNo		= document.getElementById("invoiceNo");

		if (selTransporter=="") {
			alert("Please select a Transporter.");
			form.selTransporter.focus();
			return false;
		}

		/*
		if (docketNo=="") {
			alert("Please enter a Transporter Docket No.");
			form.docketNo.focus();
			return false;
		}
		*/

		if (invoiceNo.value=="") {
			alert("Please enter a Sales Order Number.");
			invoiceNo.focus();
			return false;
		}	
		if (invoiceDate.value=="") {
			alert("Please select a invoice date");
			invoiceDate.focus();
			return false;
		}

		if (validInvoiceDate==1) {
			alert(" Please make sure the selected invoice date is a valid date. ");
			document.getElementById("invoiceDate").focus();
			return false;
		}
		/*
		if (paymentStatus=="") {
			alert("Please enter Payment Description.");
			form.paymentStatus.focus();
			return false;
		}
		*/	
		if (dispatchDate=="") {
			alert("Please select a Dispatch Date.");
			form.dispatchDate.focus();
			return false;
		}
		if (validDespatchDate==1) {
			alert(" Please make sure the selected date of Despatch is a valid date. ");
			document.getElementById("dispatchDate").focus();
			return false;
		}

		if ((lastDateStatus!=dispatchDate) && !dateExtended) {
			alert("Please select Extended option");
			document.getElementById("dateExtended").focus();
			return false;			
		}
	}	

	if (!confirmSave()) {
		return false;
	} else {
		return true;
	}
}

	/*
		Transporter Clear
	*/
	function clearTransporter()
	{
		document.getElementById("selTransporter").value = "";
	}

	// Update SO Main Rec
	function updateSOMainRec(soId, selDate)
	{
		//alert(soId+","+selDate);
		 xajax_updateSOMainRec(soId, selDate);
	}

	function enableOrderDispatchBtn(mode)
	{
		document.getElementById("cmdSaveChange").disabled = false;
		document.getElementById("cmdSaveChange1").disabled = false;
	}
	
	function disableOrderDispatchBtn(mode)
	{		
		document.getElementById("cmdSaveChange").disabled = true;
		document.getElementById("cmdSaveChange1").disabled = true;
	}

	function validateGenPkgIns()
	{		
		var conDelMsg	=	"Do you wish to Generate Packing Instruction?";
		if (confirm(conDelMsg)) return true;		
		return false;
	}