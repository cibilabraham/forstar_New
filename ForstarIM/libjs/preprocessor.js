function validatePreProcessor(form)
{	
	var preProcessorCode	=	form.preProcessorCode.value;
	var preProcessorName	=	form.preProcessorName.value;
	var Phone		=	form.preProcessorTelNo.value;
	var emailID		=	form.preProcessorEmail.value;
	var Pincode		=	form.preProcessorPincode.value;
	var selPlant		= document.getElementById("selPlant").value;
	var selActivity		= document.getElementById("selActivity").value;
	
	if (preProcessorCode=="") {
		alert("Please enter a Pre Processor code.");
		form.preProcessorCode.focus();
		return false;
	}
	else if( preProcessorName=="" ) {
		alert("Please enter a Pre-Processor name.");
		form.preProcessorName.focus();
		return false;

	}

	if (checkInternationalPhone(Phone)==false){
		alert("Please Enter a Valid Phone Number");
		form.preProcessorTelNo.focus();
		return false;
	}

      if (emailID!="") {
		if (echeck(emailID)==false) {
			form.preProcessorEmail.focus();
			return false;
		}
	}

	if (isPositiveInteger(Pincode)==false) {
		form.preProcessorPincode.focus();
		return false;
	}	
	
	if (selPlant=="") {
		alert("Please select one or more Plants/Units");
		form.selPlant.focus();
		return false;
	}

	if (selActivity=="") {
		alert("Please select one or more Activity");
		form.selActivity.focus();
		return false;
	}
	
	if (!confirmSave()) {
		return false;
	}
	return true;
}

	// Confirm Change Status
	function confirmChangeStatus(fieldPrefix, rowCount)
	{
		var count = 0;
		for (i=1; i<=rowCount; i++ )
		{
			if(document.getElementById(fieldPrefix+i).checked) {
				count++;
			}		
		}
		
		if (count==0) {
			alert("Please select a record to change processor status.");
			return false;
		}
		return true;
	}

	function validateUptdStatus(processorId, rowId)
	{
		if (!confirm("Do you wish to change processor status?")) {
			return false;
		}
		// Ajax 
		xajax_changeProcessorStatus(processorId, rowId);		
		return true;
	}