	function validateRefreshTimeLimit(form)
	{	
		var refreshTime		=	form.refreshTime.value;
		var selSubModule	=	document.getElementById("selSubModule").value;
	
		if (selSubModule=="") {
			alert("Please select a Sub-Module.");
			form.selSubModule.focus();
			return false;
		}
		
		if (refreshTime=="" && refreshTime==0) {
			alert("Please enter a time (in seconds).");
			form.refreshTime.focus();
			return false;
		}
	
		if (!confirmSave()) {
			return false;
		}
		return true;
	
	}





