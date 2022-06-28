	function validateSalesZoneMaster(form)
	{	
		var name	= form.name.value;
		
		if (name=="") {
			alert("Please enter a Zone Name.");
			form.name.focus();
			return false;
		}
		
		if (!confirmSave()) return false;
		return true;
	}	

	function enableSalesZoneBtn(mode)
	{
		if (mode==1) {
			document.getElementById("cmdAdd").disabled = false;
			document.getElementById("cmdAdd1").disabled = false;
		} else if (mode==2) {
			document.getElementById("cmdSaveChange").disabled = false;
			document.getElementById("cmdSaveChange1").disabled = false;
		}
	}
	
	function disableSalesZoneBtn(mode)
	{		
		if (mode==1) {
			document.getElementById("cmdAdd").disabled = true;
			document.getElementById("cmdAdd1").disabled = true;
		} else if (mode==2) {
			document.getElementById("cmdSaveChange").disabled = true;
			document.getElementById("cmdSaveChange1").disabled = true;
		}
	}





