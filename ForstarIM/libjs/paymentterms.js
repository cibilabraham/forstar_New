function validateAddPaymentTerm(form)
{
	var paymentMode	=	form.paymentMode.value;
	var paymentRealization = document.getElementById("paymentRealization");

	if (paymentMode=="") {
		alert("Please enter a Payment Mode.");
		form.paymentMode.focus();
		return false;
	}

	if (paymentRealization.value=="") {
		alert("Please enter no of days for payment realization.");
		paymentRealization.focus();
		return false;
	}

	if (!isInteger(paymentRealization.value)) {
		alert("Please enter a valid realization days.");
		return false;
	}
	
	if (!confirmSave()) return false;
	else return true;	
}