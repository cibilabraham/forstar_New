
function validateCancelInvoice(form, prefix, rowcount)
{
	var invoiceType = document.getElementById("invoiceType").value;
	var rowCount	=	rowcount;
	var fieldPrefix	=	prefix;
	var conDelMsg	=	"Do you wish to cancel the selected Invoice?";
	if (!isAnyChecked(rowCount,fieldPrefix)) {
		alert("Please select a record.");
		return false;
	}
	if (invoiceType!="TI") {
		for (var i=1; i<=rowcount; i++) {
			var invoice 	= document.getElementById("invoiceId_"+i).checked;
			var cnclReason 	= document.getElementById("cnclReason_"+i);
			
			if (invoice && cnclReason.value=="") {
				alert("Please enter a reason for cancel the Invoice.");
				cnclReason.focus();
				return false;
			}
		}
	}
	if (confirm(conDelMsg)) {
		return true;
	}		
	return false;
}

function validateCloseInvoice(form, prefix, rowcount)
{
	var rowCount	=	rowcount;
	var fieldPrefix	=	prefix;
	var conDelMsg	=	"Do you wish to close the selected Invoice?";
	if (!isAnyChecked(rowCount,fieldPrefix)) {
		alert("Please select a record.");
		return false;
	}

	for (var i=1; i<=rowcount; i++) {
		var invoice 	= document.getElementById("invoiceId_"+i).checked;
		var cnclReason 	= document.getElementById("cnclReason_"+i);
		
		if (invoice && cnclReason.value=="") {
			alert("Please enter a reason for close the Invoice.");
			cnclReason.focus();
			return false;
		}
	}
	
	if (confirm(conDelMsg)) {
		return true;
	}		
	return false;
}

	function validateChangeInvoice(form, prefix, rowcount)
	{
		var invoiceType = document.getElementById("invoiceType").value;

		var rowCount	=	rowcount;
		var fieldPrefix	=	prefix;
		var conDelMsg	=	"Do you wish to change the status of selected Invoice?";
		if (!isAnyChecked(rowCount,fieldPrefix)) {
			alert("Please select a record.");
			return false;
		}	
		
		for (var i=1; i<=rowcount; i++) {
			var invoice 	= document.getElementById("invoiceId_"+i).checked;
			var cancelled	= document.getElementById("cancelled_"+i);
			if (invoice && cancelled.value=="") {
				alert("Status not exist for the selected invoice. ");
				return false;
			}
		}
		if (confirm(conDelMsg)) {
			return true;
		}		
		return false;
	}

