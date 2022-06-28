	function validateMonitoringParameters(form)
	{
		var parameterName 	= form.parameterName.value;
		var unitId	 	= form.unitId.value;

		if (parameterName=="") {
			alert("Please enter a monitoring factor name.");
			form.parameterName.focus();
			return false;
		}

		if (unitId=="") {
			alert("Please select a unit.");
			form.unitId.focus();
			return false;
		}

		if (!confirmSave()) return false;
		else return true;
	}