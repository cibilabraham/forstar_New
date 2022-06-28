function validateAddPacking(form)
{
	
	var packingCode		=	form.packingCode.value;
	var packingWeight	=	form.packingWeight.value;
	

	if( packingCode=="" )
	{
		alert("Please enter a Packing code.");
		form.packingCode.focus();
		return false;
	}
	
	if( packingWeight=="" )
	{
		alert("Please enter a Packing Weight.");
		form.packingWeight.focus();
		return false;
	}
	if(!confirmSave())
	{
		return false;
	}
	else
	{
		return true;
	}
}