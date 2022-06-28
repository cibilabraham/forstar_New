	function validateCarriageModeMaster(form)
	{
		var carriageMode		= form.carriageMode.value;	
		
		if (carriageMode=="") {
			alert("Please enter a carriage mode name.");
			form.carriageMode.focus();
			return false;
		}
	
		var mode   = document.getElementById("hidMode").value; // Mode =1 : addmode, mode =2 : edit Mode
	
		if (!confirmSave()) {
			return false;
		}
		return true;
	}

	// Confirm Make Defaut
	function confirmMakeDefault(fieldPrefix, rowCount)
	{
		var count = 0;
		var confirmMSg = "Are you sure?";
		for (i=1; i<=rowCount; i++ ) {
			if(document.getElementById(fieldPrefix+i).checked) {
				count++;
			}		
		}
		
		if (count==0) {
			alert("Please select a record to make Default.");
			return false;
		}
		
		if (count>1) {
			alert("Please select only one record to make Default.");
			return false;
		}
		
		if (!confirm(confirmMSg)) return false;
		return true;
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
	

