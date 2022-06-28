function validateInvoiceTypeMaster(form)
{
	var invoiceTypeName		= form.invoiceTypeName.value;	
	
	if (invoiceTypeName=="") {
		alert("Please enter a invoice type.");
		form.invoiceTypeName.focus();
		return false;
	}
	var mode   = document.getElementById("hidMode").value; // Mode =1 : addmode, mode =2 : edit Mode
	
	if (!confirmSave()) return false;
	else return true;
}

	
	function enableStateVatButton(mode)
	{
		if (mode==1) {
			document.getElementById("cmdAdd").disabled = false;
			document.getElementById("cmdAdd1").disabled = false;
		} else if (mode==2) {
			document.getElementById("cmdSaveChange").disabled = false;
			document.getElementById("cmdSaveChange1").disabled = false;
		}
	}
	
	function disableStateVatButton(mode)
	{		
		if (mode==1) {
			document.getElementById("cmdAdd").disabled = true;
			document.getElementById("cmdAdd1").disabled = true;
		} else if (mode==2) {
			document.getElementById("cmdSaveChange").disabled = true;
			document.getElementById("cmdSaveChange1").disabled = true;
		}
	}