function validateAddToQuickList(form)
{
	var chkCount = 0;
	var rowCount = document.getElementById("hidRowCount").value;
	for (i=1; i<=rowCount; i++) {
		if( document.getElementById("chkfunctionId_"+i).checked == true ) chkCount++;
	}
	if( chkCount == 0 ){
		alert("Please select atleast one function.");
		document.getElementById("chkfunctionId_"+i).focus();
		return false;
	}
	return true;
}

function validateDeleteFromQuickList(form)
{
	var chkCount = 0;
	var rowCount = document.getElementById("hidQLRowCount").value;
	for (i=1; i<=rowCount; i++) {
		if( document.getElementById("chkQLFuncId_"+i).checked == true ) chkCount++;
	}
	if( chkCount == 0 ){
		alert("Please select atleast one function.");
		document.getElementById("chkQLFuncId_"+i).focus();
		return false;
	}
	return true;
}

function confDelete(form)
{
	if(validateDeleteFromQuickList(form))
	{
		if(confirm("Do you wish to delete the selected items?"))
		{
			return true;
		}
	}
	return false;

}