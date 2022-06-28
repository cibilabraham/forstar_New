function validateFznPkngQuickEntryList(form)
{	
	var qeName	= form.qeName.value;	
	var addEditMode	= false;
	
	if (trim(qeName)=="") {
		alert("Please enter a group name.");
		form.qeName.focus();
		return false;
	}
		
	var rowCount	= document.getElementById("hidTableRowCount").value;	
	var itemsSelected = false;
		
		if (rowCount>0) {
			for (i=0; i<rowCount; i++) {
				var status = document.getElementById("status_"+i).value;
				if (status!='N') {
					var selQEL = document.getElementById("selQEL_"+i);
				
					if (selQEL.value=="") {
						alert("Please select a Quick Entry List.");
						selQEL.focus();
						return false;
					}										
									
					if (selQEL.value!="") {
						itemsSelected = true;
					}
				}
			}  // For Loop Ends Here
		} // Row Count checking End

		if (!itemsSelected) {
			alert("Please add atleast one combination");
			return false;
		}
		if (!validateItemRepeat()) {
			return false;
		}
				
	// End Here checking grade
	if (!confirmSave()) return false;
	else return true;
}
