//Search Button Validation In Listing 
function validatePurchaseStatementSearch(form, url)
{
	
	var supplyFrom		= form.supplyFrom.value;
	var supplyTill		= form.supplyTill.value;
	var billingCompany		= form.billingCompany.value;
	
	
	if (supplyFrom=="") {		
		alert("Please select From Date");
		form.supplyFrom.focus();
		return false;
	}
	
	if (findDaysDiff(supplyFrom)>0) {
		alert("Supply From Date should be less than or equal to current date");
		form.supplyFrom.focus();
		return false;	
	}
	
	if (supplyTill=="") {		
		alert("Please select Till Date");
		form.supplyTill.focus();
		return false;
	}
		
	if (findDaysDiff(supplyTill)>0) {
		alert("Supply Till Date should be less than or equal to current date");
		form.supplyTill.focus();
		return false;	
	}
	if (checkDateSelected(supplyFrom,supplyTill)>0) {
		alert("Please check selected From and To date");
		return false;
	}
	
	if(billingCompany==0){
		alert("Please Select Billing Company");	
	    return false;	
		
	}
	
	
	// Print Windo
	printPStatementWindow(url, 700, 600);

	return true;
}

	function printPStatementWindow(url, width, height)
	{		
		var winl = (screen.width - width) / 2;
		var wint = (screen.height - height) / 2;
		eval("page = window.open('"+url+"', 'Forstar_Foods', 'top="+ wint +", left="+ winl +",  status=1,scrollbars=1,location=0,resizable=1,width="+ width +",height="+ height +"');");
	}

	function getSupplier(formObj)
	{
	showFnLoading(); 
	formObj.form.submit();


	}
	function getbillCompany(formObj)
	{
	showFnLoading(); 
	formObj.form.submit();


	}
	
	function getunits(formObj){
	showFnLoading(); 
	formObj.form.submit();
		
	}