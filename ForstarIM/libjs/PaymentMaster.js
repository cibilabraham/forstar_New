function validateAddPayment(form)
{
	var duration = form.paymentDuration.value;
	
	if(duration=="")
	{
		alert("Please Enter Payment Duration");
		form.paymentDuration.focus();
		return false;
	}
	
	if (!confirmSave()) {
		return false;
	}
	
	return true;
}