	function validateStateMaster(form)
	{	
		var stateName	= form.stateName.value;	
		var salesZone	= form.salesZone.value;	
		
		if (stateName=="") {
			alert("Please enter a State Name.");
			form.stateName.focus();
			return false;
		}
		if (salesZone=="") {
			alert("Please select a Zone.");
			form.salesZone.focus();
			return false;
		}
		
		if (!confirmSave()) return false;
		return true;
	}