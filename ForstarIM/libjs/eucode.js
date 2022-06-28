	function validateAddEuCode(form)
	{
		var euCode		=	form.euCode.value;
		
		if (euCode=="") {
			alert("Please enter a EU Code.");
			form.euCode.focus();
			return false;
		}
				
		if (!confirmSave()) return false;
		else return true;
	}