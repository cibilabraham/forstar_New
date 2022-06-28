function validateDisplayRecord(form){
	
	var noRec	=	form.noRec.value;
	
	
	if( noRec=="" )
	{
		alert("Please enter no. of Record to be displayed.");
		form.noRec.focus();
		return false;
	}
	
	if(!isDigit(noRec)){
		alert("Please enter a number.");
		form.noRec.focus();
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

function validateInvoiceDisplayRec(form){
	if(!confirmSave())
	{
		return false;
	}
	else
	{
		return true;
	}

}


function validateDailyFrozenPackingDate(form){
	
	var dfpDate	=form.frozenPackingFrom.value;
	
	
	if (dfpDate=="")
	{
		alert("Please select the date.");
		form.frozenPackingFrom.focus();
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

function validateStockEntryDate(form){
	
	var stNum	=form.STEntry.value;
	
	
	if (stNum=="")
	{
		alert("Please enter the start number.");
		form.STEntry.focus();
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


function validateSupplierDate(form){
	
	var stNum	=form.supplierStdate.value;
	
	
	if (stNum=="")
	{
		alert("Please enter the start number.");
		form.supplierStdate.focus();
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