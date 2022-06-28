function validateTransporterSettlementSummary(form)
{
	
	var supplyFrom	=	form.supplyFrom.value;
	var supplyTill	=	form.supplyTill.value;
	var transporter	=	form.transporter.value;

	if( supplyFrom=="")
		{
			alert("Please select From Date");
			form.supplyFrom.focus();
			return false;
		}
		
	if(supplyTill=="")
		{
			alert("Please select To Date");
			form.supplyTill.focus();
			return false;
		}
	if(findDaysDiff(supplyFrom)>0 || findDaysDiff(supplyTill)>0){
			alert("Selected date should be less than or equal to current date");
			return false;	
	}
	if(checkDateSelected(supplyFrom,supplyTill)>0){
		alert("Please check selected From and To date");
		return false;
	}
	if(transporter=="")
		{
			alert("Please select a Transporter");
			form.transporter.focus();
			return false;
		}
	return true;
	/*	
	if( confirmSave()){
  		return true;
	} else {
		return false;
	}
	*/
}

	function updateTransporterPayment(form)
	{
		if (!validateTransporterSettlementSummary(form)) {
			return false;
		}
	
		if (confirmSave()) {
			return true;
		} else {
			return false;
		}
	}

	function paidAmount()
	{
		var rowCount	=	document.getElementById("hidRowCount").value;
		var totalAmount = 0;
		for (i=1; i<=rowCount; i++)
		{
			var settledAmount = "payingAmount_";
			var checkPaid 	  = "paid_";
			var alreadypaid	  = "alreadyPaid_";
			if(document.getElementById(settledAmount+i)!=undefined && document.getElementById(checkPaid+i)!=undefined && document.getElementById(alreadypaid+i)!=undefined)
			{
				var paidAmount	= parseFloat(document.getElementById(settledAmount+i).value);
				var paid 		= document.getElementById(checkPaid+i).checked;
				var isPaid		=	document.getElementById(alreadypaid+i).value;
				if (paid && isPaid=="")	{
					totalAmount = totalAmount + paidAmount;	
				}
			}
		}
		document.getElementById("totalpaidAmount").value = totalAmount;
	}

	// Submit function
	function getTransporter()
	{
		if(document.getElementById("supplyFrom").value!="" && document.getElementById("supplyTill").value!="")
		{
			document.frmTransporterSettlementSummary.submit();
		}
		return false;
	}