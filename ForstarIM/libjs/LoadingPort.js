

	function validateLoadingPort(form)
	{
	
		var name 	= form.name.value;
		//var entryExist = form.entryExist.value;
		//var entryExist	= document.getElementById("entryExist").value;
		
		if (name=="") {
			alert("Please enter a port of loading name.");
			form.name.focus();
			return false;
		}		
		
		if (!confirmSave()) return false;
		else return true;
	}

