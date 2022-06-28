function validateAddSealNumber(form)
{
	
	var sealNo	=	form.sealNo.value;
	//var purpose	=	form.purpose.value;
	
	if (sealNo=="") {
		alert("Please enter a seal No.");
		form.sealNo.focus();
		return false;
	}

/*	if (purpose=="") {
		alert("Please select purpose.");
		form.purpose.focus();
		return false;
	}*/
	
	if (!confirmSave()) return false;
	return true;

}

function displayView()
	{
		var functionType = document.getElementById("status").value;
		if ((functionType=='Used') ){
		document.getElementById("purposeField").style.display='none';
		document.getElementById("changeField").style.display='none';
		}
		else {
			document.getElementById("purposeField").style.display='';
			//document.getElementById("purpose").value='';
			document.getElementById("changeField").style.display='';
			//document.getElementById("changeStatus").value='';
		}
		
		
		

	}





