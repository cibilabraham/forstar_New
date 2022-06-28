function validateAddStaff(form)
{	
	var name	=	form.name.value;
	var functions	=	form.functions.value;
	var cost	=	form.cost.value;
	var type	=	form.type.value;
	var department	=	form.department.value;
	var effectiveDate	=	form.effectiveDate.value;
	var multiplyFactor = form.multiplyFactor.value;
	
	if (name=="") {
		alert("Please enter a name.");
		form.name.focus();
		return false;
	}

	if (functions=="") {
		alert("Please enter a function");
		form.functions.focus();
		return false;
	}

	if (cost=="") {
		alert("Please enter a Salary.");
		form.cost.focus();
		return false;
	}
	
	if (type=="") {
		alert("Please select  type.");
		form.type.focus();
		return false;
	}

	if (department=="") {
		alert("Please select  department.");
		form.department.focus();
		return false;
	}

	if (effectiveDate=="") {
		alert("Please select  Effective Date.");
		form.effectiveDate.focus();
		return false;
	}
	
	if(multiplyFactor=="") {
		alert("Multiplication Factor value must be greater than 0");
		form.multiplyFactor.focus();
		return false;
	}
	
	if (!confirmSave()) return false;
	else return true;
}


function totalCost()
{
	
	var cost = $("#cost").val();
	var allownce = $('#allowance').val();
	var proposedCost = $("#proposedCost").val();
	var multiplyFactor = $('#multiplyFactor').val();
	
	//alert("Factor--"+multiplyFactor);
	if(allownce!="")
	{
		var actualCost= (((cost/12)*proposedCost) + parseFloat(allownce))*multiplyFactor;
	}
	else
	{
		var actualCost=((cost/12)*proposedCost)*multiplyFactor;
	}
	//alert(actualCost);
	var totalCost=number_format(actualCost,2,'.','');	
	$("#actualCost").val(totalCost);
	
}

function displayCalendar()
{
	Calendar.setup 
	(	
		{
			inputField  : "effectiveDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "effectiveDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
}

function isNumber(evt) 
{
    var iKeyCode = (evt.which) ? evt.which : evt.keyCode
    if (iKeyCode != 46 && iKeyCode > 31 && (iKeyCode < 48 || iKeyCode > 57))
    return false;
	return true;
}   