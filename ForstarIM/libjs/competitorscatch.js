function validateAddCompetitorsCatch(form)
{
	var landingCenter	=	form.landingCenter.value;
	
	
	if( landingCenter=="")
		{
			alert("Please select a Landing Center");
			form.landingCenter.focus();
			return false;
		}
		
	/*
	else if(!isDigit(qty)) {
	alert("Please enter Digit Only.");
	form.quantity.focus();
	return false;
	}*/

	
	if( confirmSave()){
  		return true;
	} else {
		return false;
	}
}

function passUrlValue(){
//alert("Hai");

	loc= 'CompetitorsCatchFishList.php?catchEditId='+ document.frmCompetitorsCatchList.catchId.value+'&competitorEditId=' + document.frmCompetitorsCatchList.competitor.value ;
//alert(loc);
	parent.document.getElementById('iFrame1').src=loc;
	
	parent.document.frmCompetitorsCatch.catchEditId.value	=	document.frmCompetitorsCatchList.catchId.value;
	parent.document.frmCompetitorsCatch.editCompetitor.value	=	document.frmCompetitorsCatchList.competitor.value;
		
	//parent.document.frmCompetitorsCatch.submit();
}

function validateCompetitorSave(form){
	
	var competitor	=	parent.document.frmCompetitorsCatch.competitor.value;
	//alert(competitor);
	
	if( competitor=="")
		{
			alert("Please select a Competitor");
			parent.document.frmCompetitorsCatch.competitor.focus();
			return false;
		}
	
	//parent.document.frmCompetitorsCatch.submit();
return true;
}

//Reload IFRAME2

function updateFrame(){
	
	parent.iFrame2.document.frmCompetitorsCatchList.submit();
	
}