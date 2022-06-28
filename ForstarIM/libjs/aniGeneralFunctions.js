function confirmDelete(form,prefix,rowcount)
{
   
	//showFnLoading();
	var rowCount	=	rowcount;
	var fieldPrefix	=	prefix;
	var conDelMsg	=	"Do you wish to delete the selected items?";
	
	if(!isAnyChecked(rowCount,fieldPrefix))
	{
		alert("Please select a record to delete.");
		return false;
	}
	
	if(confirm(conDelMsg))
	{
		return true;
	}
		
	return false;

}

function isAnyChecked(rowCount,fieldPrefix)
{
	for ( i=1; i<=rowCount; i++ )
	{
		if(document.getElementById(fieldPrefix+i).checked)
		{
			return true;
		}		
	}
	return false;
}