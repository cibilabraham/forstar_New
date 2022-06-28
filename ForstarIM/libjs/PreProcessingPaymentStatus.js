function validatePreProcessPaymentSearch(form)
{
	var supplyFrom	=	form.supplyFrom.value;
	var supplyTill	=	form.supplyTill.value;
	var selProcessor	=	form.selProcessor.value;

	var supplyFrom	=	form.supplyFrom.value;
	var supplyFrom	=	form.supplyFrom.value;
	
	if (supplyFrom=="") {
		alert("Please select from date.");
		form.supplyFrom.focus();
		return false;
	}

	if (supplyTill=="") {
		alert("Please select To date.");
		form.supplyTill.focus();
		return false;
	}

	if (selProcessor=="") {
		alert("Please select a Pre-Processor.");
		form.selProcessor.focus();
		return false;
	}

	if (!form.searchType[0].checked && !form.searchType[1].checked) {
		alert("Please select anyone search option");
		return false;
	}

	if (form.searchType[1].checked) {		
		var qtySearchType = form.qtySearchType.value;
		if (qtySearchType=="") {
			alert("Please select Detailed/Summary qty search.");
			form.qtySearchType.focus();
			return false;
		}
	}	
	return true;	
}
function validatePreProcessPaymentUpdate(form)
{
	if (!validatePreProcessPaymentSearch(form)) return false;	
	if (!form.changeStatus[0].checked && !form.changeStatus[1].checked && !form.changeStatus[2].checked) {
		alert("Please select atleast one status Change option.");
		return false;
	}
	if (!confirmSave()) {
		return false;
	} else {
		return true;
	}
}

function functionLoad(formObj)
	{
		//alert("hai");
		showFnLoading(); 
		formObj.form.submit();
	}