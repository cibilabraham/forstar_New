function validateAddRePacking(form)
{
	isRePackTypeSelected = false;
	var rePackingCode	=	form.rePackingCode.value;	
	var rePackingReason	=	form.rePackingReason.value;	
	var hidColumnCount	=	form.hidColumnCount.value;
		
	if (rePackingCode=="") {
		alert("Please enter a Re-Packing Code.");
		form.rePackingCode.focus();
		return false;
	}
	
	if (rePackingReason=="") {
		alert("Please enter a Re-Packing Reason.");
		form.rePackingReason.focus();
		return false;
	}
	
	for (i=1; i<=hidColumnCount; i++)  {
		  if (document.getElementById("selRepackType_"+i).value!="") {
			  isRePackTypeSelected = true;
		  }
	}
	
	  
	if (!confirmSave()) return false;
	else return true;	
}