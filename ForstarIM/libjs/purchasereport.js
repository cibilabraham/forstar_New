function validatePurchaseReportSearch(form)
{
	var weighNumber	=	form.weighNumber.value;
	
	
	if( weighNumber=="" )
	{
		alert("Please enter a Weighment Challan No.");
		form.weighNumber.focus();
		return false;
	}
	
	return true;
	/*if(!confirmSave())
	{
		return false;
	}
	else
	{
		return true;
	}*/
}

function validatePurchaseReportUpdate(form)
{
	
	var weighNumber	=	form.weighNumber.value;
	
	
	if( weighNumber=="" )
	{
		alert("Please enter a Weighment Challan No.");
		form.weighNumber.focus();
		return false;
	}

	if (!form.changeStatus[0].checked && !form.changeStatus[1].checked && !form.changeStatus[2].checked) {

		alert("Please select atleast one status Change option.");
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

