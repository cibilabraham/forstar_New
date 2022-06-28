function ValidateUpdate(form)
{
	
	var startNumberPrefix = "StartNumber_";
	var endNumberPrefix = "EndNumber_";
	var rowCount = document.getElementById("RowCount").value;

	for( i=1; i<=rowCount; i++ )
	{
		if( document.getElementById("StartNumber_"+i).value=="" )
		{
			alert("Please enter a Start Number.");
			document.getElementById("StartNumber_"+i).focus();
			return false;
		}
		else if( isNaN(document.getElementById("StartNumber_"+i).value)  || !isDigit(document.getElementById("StartNumber_"+i).value))
		{
			alert("Please enter a valid Start Number.");
			document.getElementById("StartNumber_"+i).value = "";
			document.getElementById("StartNumber_"+i).focus();
			return false;
		} 

		if( document.getElementById("EndNumber_"+i).value=="" )
		{
			alert("Please enter a End Number.");
			document.getElementById("EndNumber_"+i).focus();
			return false;
		}
		else if( isNaN(document.getElementById("EndNumber_"+i).value) || !isDigit(document.getElementById("EndNumber_"+i).value))
		{
			alert("Please enter a valid End Number.");
			document.getElementById("EndNumber_"+i).value = "";
			document.getElementById("EndNumber_"+i).focus();
			return false;
		}
		var startn = document.getElementById("StartNumber_"+i).value;
		var endn = document.getElementById("EndNumber_"+i).value;

		if( parseInt(startn) > parseInt(endn) )
		{
			alert("The starting numer should be less than ending number.");
			document.getElementById("StartNumber_"+i).focus();
			return false;
		}
	}

	if( confirm("Do you wish to save the changes?") ) return true;
	return false;		
}