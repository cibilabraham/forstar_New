function validateChangesUpdateMaster(form)
{
	var rteChk	=	form.rteChk.checked;
	
	if (!rteChk ) {
		alert("Please select RTE.");
		//form.baseCst.focus();
		return false;
	}
		
	if (!confirmSave()) return false;
	else return true;	
}

