function validateProcessingReport(form){

	var selDate			=	form.selDate.value;	
	var plant 			=	form.selUnit.value;
	var selLotNo		=	form.selLotNo.value;
	var selFish			=	form.selFish.value;
	var selProcessCode	=	form.selProcessCode.value;
	
	
	if(selDate==""){
		
			alert("Please select a Date");
			form.selDate.focus();
			return false;
	}
	
	
	if( plant==""){
		
			alert("Please select a Unit");
			form.selUnit.focus();
			return false;
		}
		
	if( selLotNo==""){
		
			alert("Please select a Lot No");
			form.selLotNo.focus();
			return false;
		}
	if( selFish==""){
		
			alert("Please select a Fish");
			form.selFish.focus();
			return false;
		}
	if( selProcessCode==""){
		
			alert("Please select a Process Code");
			form.selProcessCode.focus();
			return false;
		}		
		
		
return true;
}

