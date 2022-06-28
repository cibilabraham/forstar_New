function validateSupplierIngredient(form)
{
	var selSupplier		=	form.selSupplier.value;	
	var selIngredient	=	document.getElementById("selIngredient").value;		
	var rate	=	document.getElementById("rate").value;		
	var quantity	=	document.getElementById("quantity").value;		
	var effectiveDate	=	document.getElementById("effectiveDate").value;		
	var hidMode	=	document.getElementById("hidMode").value;	
	if(hidMode==2)
	{
			var newEffectiveDate	=	document.getElementById("newEffectiveDate").value;		
	}
	
	if (selSupplier=="") {
		alert("Please select a Supplier.");
		form.selSupplier.focus();
		return false;
	}
	
	if (selIngredient=="") 
	{
		alert("Please select an Ingredient.");
		document.getElementById("selIngredient").focus();
		return false;
	}	

	if (rate=="")
	{
		alert("Please enter rate.");
		document.getElementById("rate").focus();
		return false;
	}	

		if (quantity=="") {
		alert("Please enter quantity.");
		document.getElementById("quantity").focus();
		return false;
	}	

		if (effectiveDate=="") {
		alert("Please select an effectiveDate.");
		document.getElementById("effectiveDate").focus();
		return false;
	}
	
	if(hidMode==2)
	{
		if (newEffectiveDate=="") {
			alert("Please select  New Effective Date.");
			document.getElementById("newEffectiveDate").focus();
			return false;
		}	
	}

	
	if (!confirmSave()) return false;
	return true;
}

function newEffective()
{
	var effectiveDate=$("#effectiveDate").val();
	var res=effectiveDate.split("/");
	var newArray = new Array();
    newArray[0] =res[2];
    newArray[1] = res[1];
    newArray[2] = res[0];
    var newdate=newArray.join('/');
  	//var newdt=yr+"/"+mth+"/"+day;
	var d = new Date(newdate);
	var m = moment(d);
	var dy=m.add('days', 1);
	var dt = m.toDate();
	$( "#newEffectiveDate" ).datepicker( {
		dateFormat: 'dd/mm/yy',
		minDate: new Date(dt)
	});
}