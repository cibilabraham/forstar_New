	function validateCheckPoint(form)
	{
		var name		=	form.name.value;	

		if (name=="") {
			alert("Please enter a Check Point.");
			form.name.focus();
			return false;
		}
		
		if (!confirmSave()) return false;
		return true;
	}





