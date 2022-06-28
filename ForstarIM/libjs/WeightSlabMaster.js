function validateWeightSlabMaster(form)
{	
	var name	= form.name.value;
	var wtFrom	= form.wtFrom.value;
	var wtTo	= form.wtTo.value;
	var wtAbove	= form.wtAbove.checked;
	
	if (name=="") {
		alert("Please enter a Weight Slab.");
		form.name.focus();
		return false;
	}	
	
	if (wtFrom=="") {
		alert("Please enter a Weight From.");
		form.wtFrom.focus();
		return false;
	}
	
	if (!checkNumber(wtFrom)) {
		form.wtFrom.focus();	
		return false;
	}
	
	if (wtTo!=0 || wtTo!="") {
		if (!checkNumber(wtTo)) {
			form.wtTo.focus();
			return false;
		}	
	}

	if ((parseFloat(wtFrom)>parseFloat(wtTo) || parseFloat(wtFrom)==parseFloat(wtTo)) && !wtAbove) {
		alert(" Weight To must me greater than weight From.  ") ;
		return false;
	}

	if (wtTo==0 && !wtAbove) {
		alert("Please select above.");
		//form.wtFrom.focus();
		return false;
	}

	if (wtAbove) {
		form.wtTo.value = 0;
	}

	if (!confirmSave()) {
		return false;
	}
	return true;
}





