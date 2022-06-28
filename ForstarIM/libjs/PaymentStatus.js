//Search Button Validation In Listing 
function validateAccountStatementSearch(form, url)
{
	var supplyFrom		= form.supplyFrom.value;
	var supplyTill		= form.supplyTill.value;
	var supplier		= form.supplier.value;
	
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

	if (supplier=="") {		
		alert("Please select a Supplier.");
		form.supplier.focus();
		return false;
	}

	// Print Windo
	printAStatementWindow(url, 700, 600);

	return true;
}

	function printAStatementWindow(url, width, height)
	{		
		var winl = (screen.width - width) / 2;
		var wint = (screen.height - height) / 2;
		eval("page = window.open('"+url+"', 'Forstar_Foods', 'top="+ wint +", left="+ winl +",  status=1,scrollbars=1,location=0,resizable=1,width="+ width +",height="+ height +"');");
	}


function functionLoad(formObj)
	{
		alert("hai");
		showFnLoading(); 
		formObj.form.submit();
	}