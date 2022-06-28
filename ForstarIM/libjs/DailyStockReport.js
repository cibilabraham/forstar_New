	function validateDailyStockReport(form)
	{
		var dateFrom		= form.dateFrom.value;
		var dateTo		= form.dateTo.value;
		
		// if (dateFrom=="") {
		// 	alert("Please select From Date");
		// 	form.dateFrom.focus();
		// 	return false;
		// }
		
		if (dateTo=="") {
			alert("Please select To Date");
			form.dateTo.focus();
			return false;
		}		
			
		return true;
	}

	function validateAddDailyThawing()
	{
		var cMsg = "";
	}

function disabPckStk(val)
{
	if (val=="PRIR")
	{
		document.getElementById("packType").style.display="none";
		document.getElementById("stockType").style.display="none";
	}
		else if (val=="STKR")
	{
			$("#packType").show();
			$("#stockType").show();
	}
}