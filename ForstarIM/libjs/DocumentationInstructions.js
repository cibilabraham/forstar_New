		

	function validateDocumentationInstructions(form)
	{
		var name 	= form.name.value;
		
		var entryExist	= document.getElementById("entryExist").value;

		if (name=="") {
			alert("Please enter a documentation instruction.");
			form.name.focus();
			return false;
		}		

		if (entryExist!="") {
			alert("Documentation instruction is already exist in database.");
			form.name.focus();
			return false;
		}

		
		if (!confirmSave()) return false;
		else return true;
	}


