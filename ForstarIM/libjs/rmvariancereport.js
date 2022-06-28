function validateRMVarianceReportSearch()
{
	var supplyFrom			= document.getElementById("supplyFrom").value;
	var supplyTill		= document.getElementById("supplyTill").value;
	if(supplyFrom=="")
	{
		alert("Please select from  Date");
		supplyFrom.focus();
		return false;
	}
	else if(supplyTill=="")
	{
		alert("Please select to Date");
		supplyTill.focus();
		return false;
	}
	return true;
}


/*function validateFrznPkgReportSearch()
{
	var day			= document.getElementById("dateSelectionSD").checked;
	var dateRange		= document.getElementById("dateSelectionDR").checked;

	// For a date range
	if (dateRange) {
		var supplyFrom	= document.getElementById("supplyFrom");
		var supplyTill	= document.getElementById("supplyTill");
	
		if (supplyFrom.value=="") {
			alert("Please select from Date");
			supplyFrom.focus();
			return false;
		}
		
		if (supplyTill.value=="") {
			alert("Please select till Date");
			supplyTill.focus();
			return false;
		}
	}
	
	//For a Day
	if (day) {
		var selDate = document.getElementById("selDate");
		if (selDate.value=="") {
			alert("Please select a day");
			selDate.focus();
			return false;
		}
	}

	if (!document.getElementById("details").checked && !document.getElementById("summary").checked) {
		alert("Please select atleast one search option.");
		return false;
	}

	return true;
}
*/
	// Chk/Uncheck section
	function remChk(field)
	{	
		if (!document.getElementById(field).checked) chk = false;
		else chk = true;
	
		document.getElementById("details").checked=false;
		document.getElementById("summary").checked=false;
				
		document.getElementById(field).checked = chk;
	}

	function validateStatusUpdate(form)
	{
		if (!validateFrznPkgReportSearch()) return false;	

		var csPurchaseOrder = document.getElementById("csPurchaseOrder");
		var csInvoice		= document.getElementById("csInvoice");

		if (csPurchaseOrder.value=="")
		{
			alert("Please select a purchase order");
			csPurchaseOrder.focus();
			return false;
		}

		if (csInvoice.value=="-1")
		{
			if (!confirm("This process will release all the confirmed invoice and PO.\nYou need to manually confirm all invoices againt the selected PO.\nDo you wish to continue?"))
			{
				return false;
			}
		}
		

if (csInvoice.value=="-1")
		{
		if (!document.getElementById("chkCRA").checked) {
			alert("Please select the option.");
			return false;
		}
		}
else
{
if (!document.getElementById("chkINVR").checked) {
alert("Please select the option.");
return false;
}
		}


		if (!confirmSave()) return false;
		return true;
	}



	function disabPckStk(val)
{
	//alert(val);
	if (val!=-1)
	{
		document.getElementById("chkCRA").style.display="none";
		document.getElementById("chkCRAL").style.display="none";
		$("#chkINVR").show();
		$("#chkINVRL").show();
		//document.getElementById("stockType").style.display="none";
	}
		else 
	{
		document.getElementById("chkINVR").style.display="none";
		document.getElementById("chkINVRL").style.display="none";
		$("#chkCRA").show();
		$("#chkCRAL").show();	


	}
}