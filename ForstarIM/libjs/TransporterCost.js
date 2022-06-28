function validateTransporterCost(form)
{
	//alert("hiii");
	var transportCostNSWE = form.transportCostNSWE.value;
	var transportCostNE   = form.transportCostNE.value;
	var transportCostFRZ  = form.transportCostFRZ.value;
	
	if(transportCostNSWE == "")
	{
		alert("Please enter a Transport Cost per Gr Kg - NSWE");
		form.transportCostNSWE.focus();
		return false;
	}
	
	if(transportCostNE == "")
	{
		alert("Please enter a Transport Cost per Gr Kg - NE");
		form.transportCostNE.focus();
		return false;
	}
	
	if(transportCostFRZ == "")
	{
		alert("Transport Cost per Gr Kg - FRZ");
		form.transportCostFRZ.focus();
		return false;
	}
	
	if (!confirmSave()) return false;
	return true;
}