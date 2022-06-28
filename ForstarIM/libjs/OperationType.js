	function validateOperationType(form)
	{
		var operationTypeName = form.operationTypeName.value;

		if (operationTypeName=="") {
			alert("Please enter a Type of Operation.");
			form.operationTypeName.focus();
			return false;
		}

		if (!confirmSave()) return false;
		else return true;
	}
