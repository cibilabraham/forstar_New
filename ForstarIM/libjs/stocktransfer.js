function validateStockTransfer(form)
{
	var fromCompany		=	form.fromCompany.value;
	var fromPlant		=	form.fromPlant.value;
	var toCompany		=	form.toCompany.value;
	var toPlant=form.toPlant.value;
	var item		=	form.item.value;
	var quantity=form.quantity.value;
	var fromqty=form.fromqty.value;
	var supplier=form.supplier.value;
	


	if( fromCompany=="" )
	{
		alert("Please select from Company.");
		//form.fromPlant.focus();
		return false;
	}

	
	if( fromPlant=="" )
	{
		alert("Please select from Unit.");
		//form.fromPlant.focus();
		return false;
	}

	if( item=="" )
	{
		alert("Please select item.");
		//form.item.focus();
		return false;
	}

	if( supplier=="" )
	{
		alert("Please select supplier.");
		//form.item.focus();
		return false;
	}


	if (fromqty=="0")
	{
		alert("Sorry you cannot do stock transfer");
		form.fromqty.focus();
		return false;
	}
	
	if( toCompany=="" )
	{
		alert("Please select to Company.");
		//form.fromPlant.focus();
		return false;
	}


	if( toPlant=="" )
	{
		alert("Please select to Unit.");
		form.toPlant.focus();
		return false;
	}

	if (fromPlant==toPlant)
	{

		alert("From unit and to unit is Same.Please Select different units");
		form.toPlant.focus();
		return false;
	}
	
	
	if( quantity=="" )
	{
		alert("Please enter quantity.");
		form.quantity.focus();
		return false;
	}


	
	
	if(!confirmSave()){
			return false;
	}
	return true;
}


