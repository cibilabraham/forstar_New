	function validateAddGlaze(form)
	{		
		var glazePercent  = form.glazePercent.value;
		
		if (glazePercent=="") {
			alert("Please enter a Glaze Percentage.");
			form.glazePercent.focus();
			return false;
		}
		if(!isADigit(glazePercent)) {
			alert("Please enter a number.");
			form.glazePercent.focus();
			return false;
		} 
		
		if (!confirmSave()) return false;
		else return true;		
	}
	
	//Checking Digit From 0- 
	function isADigit (str) 
	{
		if (str == null) {
			return (false);
		}
		if (isNaN(str)) {
			return (false);
		} else if(str<0) {
			return (false);
		}
		return (true);
	}