function validateAddDailyProcessing(form)
{
	//alert("Hai");
	var fishId 		= 	form.selFish.value;
	var lotId 		= 	form.lotNo.value;
	var packingId 	= 	form.packingType.value;
	var codeId 		=	form.processCode.value;
	var dailyUnit	=	form.unit.value;
	
	if(lotId==""){
	alert("Please enter a lot number");	
	form.lotNo.focus();
	return false;
	}
	if(dailyUnit==""){
	alert("Please select a Unit");	
	form.unit.focus();
	return false;
	}
	
	if(fishId==""){
	alert("Please select a Fish");	
	form.selFish.focus();
	return false;
	}
	
	if(codeId==""){
	alert("Please select a Process Code");	
	form.processCode.focus();
	return false;
	}
	
	if(packingId==""){
	alert("Please select a packing");	
	form.packingType.focus();
	return false;
	}
	
	if( confirmSave()){
  		return true;
	} else {
		return false;
	}
}

function totalGrade(form){

	var rowCount			=	document.getElementById("hidRowCount").value;
	var total	= 0;
	var eQty	=	"quantity_";
	for (i=1; i<=rowCount; i++)
	  {
	  	var quantity		=	0;
	 	 if(document.getElementById(eQty+i).value!=""){
	  	quantity= document.getElementById(eQty+i).value;
	  }
	  
	  total		=	parseInt(total)+parseInt(quantity);
	}
	
	if(!isNaN(total)){
		form.totalQuantity.value = total;	
	}
}

//validate Processing code entry

function validateProcessingGrade(form){
	
	//alert("hai");
	var fishId 		= parent.document.frmDailyProcessing.selFish.value;
	//var lotId 		= parent.document.frmDailyProcessing.lotNo.value;
	var packingId 	= parent.document.frmDailyProcessing.packingType.value;
	var codeId 		=	parent.document.frmDailyProcessing.processCode.value;
	
	/*if(lotId==""){
	alert("Please enter a lot number");	
	parent.document.frmDailyProcessing.lotNo.focus();
	return false;
	}*/
	
	if(fishId==""){
	alert("Please select a Fish");	
	parent.document.frmDailyProcessing.selFish.focus();
	return false;
	}
	
	if(codeId==""){
	alert("Please select a Process Code");	
	parent.document.frmDailyProcessing.processCode.focus();
	return false;
	}
	if(packingId==""){
	alert("Please select a packing");	
	parent.document.frmDailyProcessing.packingType.focus();
	return false;
	}
	
	//parent.document.frmDailyProcessing.submit();
return true;
}


function passValue(){

	loc= 'DailyProcessingGrade.php?codeEditId=' + document.frmDailyProcessingGradeList.codeEditId.value+'&lotEditId=' + document.frmDailyProcessingGradeList.lotEditId.value ;

	parent.document.getElementById('iFrame1').src=loc;
	
	parent.document.frmDailyProcessing.lotEditId.value		=	document.frmDailyProcessingGradeList.lotEditId.value;
	parent.document.frmDailyProcessing.editFishId.value		=	document.frmDailyProcessingGradeList.editFishId.value;
	parent.document.frmDailyProcessing.editProcessId.value	=	document.frmDailyProcessingGradeList.editProcessId.value;
	
	//parent.document.frmDailyProcessing.submit();
}


//Reload IFRAME2

function updateGradeListFrame(){
	parent.iFrame2.document.frmDailyProcessingGradeList.submit();
	
}

