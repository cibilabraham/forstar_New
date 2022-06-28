function validateStockIssuanceReport()
{
	var stockFrom = document.getElementById("dateFrom").value;
	var stockTo   = document.getElementById("dateTill").value;
	var selStock = document.getElementById("selStock").value;
	var selDepartment = document.getElementById("selDepartment").value;

	if (selStock=="" && selDepartment=="") {
		alert("Please select By stock OR By Department");
		document.getElementById("selStock").focus();
		return false;
	}

	if (stockFrom=="") {
		alert("Please select From Date");
		document.getElementById("dateFrom").focus();
		return false;
	}
		
	if (stockTo=="") {
		alert("Please select Till Date");
		document.getElementById("dateTill").focus();
		return false;
	}
	return true;
}

function changeSelValue(selType)
{
	if (selType=='S') {
		document.getElementById("selDepartment").value="";
	} else if (selType=='D') {
		document.getElementById("selStock").value="";
	}
	
}