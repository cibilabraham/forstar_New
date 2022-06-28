function validateProcessMaster(form)
{
	var name = form.name.value;
	var description = form.description.value;
	var waterVal = form.water.value;
	var dieselVal = form.diesel.value;
	var electricityVal = form.electricity.value;
	var gasVal = form.gas.value;
	
	//alert("Water = "+waterVal+"Diesel = "+dieselVal+"Elect = "+electricityVal+"Gas = "+gasVal);
	
	if(name == "")
	{
		alert("Please Enter Name");
		form.name.focus();
		return false;
	}
	
	
	if(waterVal=="" || waterVal<=0)
	{
		alert("Water value must be greater than 0");
		form.water.focus();
		return false;
	}
	
	if(dieselVal=="" || dieselVal<=0)
	{
		alert("Diesel value must be greater than 0");
		form.diesel.focus();
		return false;
	}
	
	if(electricityVal=="" || electricityVal<=0)
	{
		alert("Electricity value must be greater than 0");
		form.electricity.focus();
		return false;
	}
	
	if(gasVal=="" || gasVal<=0)
	{
		alert("Gas value must be greater than 0");
		form.gas.focus();
		return false;
	}
	
	return true;
}