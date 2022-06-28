function validateUpdatePOOrder(form)
{
	var stockSelected=0;
	var rowCount	=	document.getElementById("hidRowCount").value;

	for (i=1; i<=rowCount; i++) {
		var hidSuppCount = document.getElementById("hidSuppCount_"+i).value;
		if (hidSuppCount>0) {
			var stockId	=	document.getElementById("stockId_"+i);

			if (stockId.checked) {
				stockSelected++;
			}
		}
	}

	if (stockSelected==0) {
		alert("Please select atleast one stock  item");
		return false;
	}

	if (!confirmContinue()) {
		return false;
	}
	return true;
}