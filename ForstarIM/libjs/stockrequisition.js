function validateStockRequisition(form)
{
	var department		=	form.department.value;
	var item		=	form.item.value;
	var company		=	form.company.value;
	var unit		=	form.unit.value;
	var qty		=	form.qty.value;
	
	if( department=="" )
	{
		alert("Please select a Department.");
		return false;
	}
	
	if( item=="" )
	{
		alert("Please select a Item.");
		return false;
	}
	if( company=="0" )
	{
		alert("Please select a company.");
		return false;
	}

	if( unit=="0" )
	{
		alert("Please select a Unit.");
		return false;
	}

	if( qty=="" )
	{
		alert("Please enter Quantity.");
		form.qty.focus();
		return false;
	}
	
	
	if(!confirmSave())
	{
		return false;
	}
	return true;
}

