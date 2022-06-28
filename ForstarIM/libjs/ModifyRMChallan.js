function validateModifyRMChallan()
{
	var entryEffectiveWt = document.getElementById("entryEffectiveWt").value;
	var hidEntryEffectiveWt = document.getElementById("hidEntryEffectiveWt").value;

	var paymentBy		=	document.getElementById("paymentBy").checked;
	var totalDeclaredWt  = document.getElementById("totalDeclaredWt").value;

	if (paymentBy) {		
		//if (totalDeclaredWt==0 || totalDeclaredWt=="") {
		if (totalDeclaredWt=="") {
			alert("Please enter declared Wt.");
			return false;
		}

		var dWeight = formatNumber(Math.abs(totalDeclaredWt),2,'','.','','','','','');
		//if (totalDeclaredWt>0 && dWeight!=entryEffectiveWt) {
		if (dWeight!=entryEffectiveWt) {
			alert("Declared and Effective Weight are not matching");	
			return false;
		}
	}
			
	if (entryEffectiveWt!=hidEntryEffectiveWt) {
		alert("Please check the Effective wt (Not matching with the old Effective Wt).");		
		return false;
	}
	
 	if (!confirmSave()) {
		return false;
	} else {
		return true;
	}
}

function validateModifyRMChallanSearch()
{
	var weighNumber = document.getElementById("weighNumber").value;
	var selFish = document.getElementById("selFish").value;
	var selProcesscode = document.getElementById("selProcesscode").value;
	if (weighNumber=="") {
		alert("Please enter a weighment challan number");
		document.getElementById("weighNumber").focus();
		return false;
	}
	if (selFish=="") {
		alert("Please select a fish.");
		document.getElementById("selFish").focus();
		return false;
	}
	if (selProcesscode=="") {
		alert("Please select a process code.");
		document.getElementById("selProcesscode").focus();
		return false;
	}

	if (selProcesscode!="") {
		var selEntry = document.getElementById('selEntry').value;
		if (selEntry=="") {
			alert("Please select an RM Entry.");
			document.getElementById("selEntry").focus();
			return false;
		}
	}

	return true;	
}

function getKeyCode(e)
{
	if(window.event!=undefined) return window.event.keyCode;
	return e.which;
}

function focusNext(e,form,name,i,sos,limit)
{
	var ecode = getKeyCode(e);	
	//alert(window.event);
	if ((ecode==13) || (ecode == 0)){
 		//alert("focus Called");
		var nextControl = eval(form+"."+name);
		totalWt(i,sos,limit);
		if ( nextControl ) { nextControl.focus(); }
		return false;
    }
  }

//For main form
function focusNextBox(e,form,name)
{
	
	var ecode = getKeyCode(e);	
	if ((ecode==13) || (ecode == 0)){
			//alert("focus Called");
		var nextControl = eval(form+"."+name);
		
		if ( nextControl ) { nextControl.focus(); }
		return false;
    }
  }

//Fid Actual wt from Iframe catchEntryGrossWt.php screen
function findActualWt(form){
		actualWt(form);
}


function actualWt(form){
	var totalAdj	=	0;
	var netWt			=	form.entryGrossNetWt.value;
	var adjust			=	form.entryAdjust.value;
	var gradeCountAdj	=	form.gradeCountAdj.value;
	var totalActualWt	=	0;
	
	if(gradeCountAdj==""){
		gradeCountAdj	= 0;
	}
		
	if(netWt!="" || adjust!=""){
		totalAdj	=	parseFloat(adjust)+parseFloat(gradeCountAdj);
			
	if(!isNaN(totalAdj)){
		totalActualWt   =  parseFloat(netWt) - totalAdj;
		form.entryActualWt.value	=	formatNumber(Math.abs(totalActualWt),2,'','.','','','','','');
	}
	//return ;
	effectiveWt(form);
	}
}


function effectiveWt(form){
	//alert("Here");
wastage	=	0;
soft	=	0;
local	=	0;
var total;
var actualWt	=	form.entryActualWt.value;
var wastage		=	form.entryWastage.value;
var soft		=	form.entrySoft.value;
var local		=	form.entryLocal.value;
var localPer	=	form.entryLocalPercent.value;
var wastePer	=	form.entryWastagePercent.value;
var softPer		=	form.entrySoftPercent.value;

if(wastage=="" && soft=="" && local=="")
	{
	total	=	0;
	}
	else {
		if (wastage ==""){
			total		=	parseFloat(soft)+parseFloat(local);
		}
		else if(soft==""){
			total		=	parseFloat(wastage)+parseFloat(local);
		}
		else if(local==""){
			total		=	parseFloat(wastage)+parseFloat(soft);
		}
		else {
			total		=	parseFloat(wastage)+parseFloat(soft)+parseFloat(local);
		}
	}

	if(actualWt!="" && !isNaN(total)){
		form.entryEffectiveWt.value	= formatNumber(Math.abs(actualWt - total),2,'','.','','','','','');
	}

	//Percentage calc;
	if(actualWt!=0){
		form.entryLocalPercent.value 		= 	formatNumber(Math.abs((local*100)/actualWt),2,'','.','','','','','');
		form.entryWastagePercent.value		=	formatNumber(Math.abs((wastage*100)/actualWt),2,'','.','','','','','');
		form.entrySoftPercent.value			=	formatNumber(Math.abs((soft*100)/actualWt),2,'','.','','','','','');
	}
	else
	{
		form.entryLocalPercent.value = 0.00;
		form.entryWastagePercent.value = 0.00;
		form.entrySoftPercent.value	=	0.00;
	}
}
