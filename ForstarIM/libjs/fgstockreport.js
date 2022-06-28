function validateUpdateFrozenPackingReport(form)
{
	var selDate = form.selDate.value;
		
	if ( selDate=="" ) {
		alert("Please Select a Date.");
		form.selDate.focus();
		return false;
	}	
	
	if (!confirmSave()) return false;
	else return true;
}


function validateFactoryUtilizationReportSearch()
{
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
		
	//For a Day
	
	

	return true;
}

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

