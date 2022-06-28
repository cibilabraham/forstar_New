	function displayUnit()
	{	
		if(document.getElementById("selUnit").value !="" )
		{
			document.getElementById("displayUnit").innerHTML =	document.getElementById("selUnit").value;
		}
	}

	function validateAddGrade(form)
	{	
		var fishCode	= form.gradeCode.value;
		var min		= form.minimum.value;
		var max		= form.maximum.value;
	
		if (fishCode=="") {
			alert("Please enter a grade code.");
			form.gradeCode.focus();
			return false;
		} else if ( min=="") {
			alert("Please enter a minimum grade.");
			form.minimum.focus();
			return false;
		} else if(min<=0) {
			alert("Minimum should be greater than zero.");
			form.minimum.focus();
			return false;
		} else if(!isDigit(min)) {
			alert("Please enter digits only.");
			form.minimum.focus();
			return false;
		} else if ( max=="") {
			alert("Please enter a maximum grade.");
			form.maximum.focus();
			return false;
		} else if(max<=0) {
			alert("Maximum should be greater than zero.");
			form.maximum.focus();
			return false;
		} else if(!isDigit(max)) {
			alert("Please enter digits only.");
			form.maximum.focus();
			return false;
		} 
		if (!confirmSave()) {
			return false;
		} else {
			return true;
		}
	}