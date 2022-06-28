function validateAddDailyRates(form)
{
	var currentDate		=	form.currentDate.value
	var fish		=	form.selFish.value;
	var landingCenter	=	form.landingCenter.value;	
	var supplier		=	form.supplier.value;
	var hidReceived		=	form.hidReceived.value;
	var processCode		=	form.processCode.value;
	var mode		= 	form.hidMode.value; // If addmode = 1, edit mode = 0;
	
	if(findDaysDiff(currentDate)>0){
		alert(" Date should be less than or equal to current date");
		form.currentDate.focus();
		return false;	
	}

	if (landingCenter=="") {
		alert("Please select a Landing Center");
		form.landingCenter.focus();
		return false;
	}	
	
	if (fish=="") {
		alert("Please select a fish.");
		form.selFish.focus();
		return false;
	}

	if (processCode=="") {
		alert("Please select a Process Code.");
		form.processCode.focus();
		return false;
	}
	var itemCount	=	document.getElementById("hidTableRowCount").value;
	var itemSelected = false;
	//var rateIncDecSel = false;
	var rateIncDecCnt = 0;
	var cnt = 0;
	if (itemCount>0) {
		for (i=0; i<itemCount; i++) {
			var status = document.getElementById("status_"+i).value;	
			if (status!='N') {
				cnt++;
				var selGradeId		=	document.getElementById("selGrade_"+i);
				var countAverage	=	document.getElementById("countAverage_"+i);
				var marketRate		=	document.getElementById("marketRate_"+i);	
				var decRate		= 	document.getElementById("decRate_"+i);
				var higherCount		= 	document.getElementById("higherCount_"+i);
				var lowerCount		= 	document.getElementById("lowerCount_"+i);
		
		
				if (fish!="" && hidReceived!="") {
					if(hidReceived=='G'){				
						if (selGradeId.value=="") {
							alert("Please select a grade");
							selGradeId.focus();
							return false;
						}
					} 
					if(hidReceived=='C') {				
						if (countAverage.value=="") {
							alert("Please enter the Count Average");
							countAverage.focus();
							return false;
						}
					}		
					if (hidReceived=='B') {				
						if (selGradeId.value=="" && countAverage.value=="") {
							alert("Please select Grade/enter Count Average");
							selGradeId.focus();
							return false;
						}
					}
				}		
				
				if (decRate.value=="" || decRate.value == 0) {
					alert("Please enter a Declared Rate");
					decRate.focus();
					return false;
				}		
		
				if (decRate.value!="" && decRate.value!= 0) {
					itemSelected = true;
				}

				if ((parseFloat(higherCount.value)>0) || (parseFloat(lowerCount.value)>0))	{
					//rateIncDecSel = true;
					rateIncDecCnt++;
				}		
			}	// Status check ends here				
		}
	} // Item count size check
	if (!itemSelected) {
		alert("Please add atleast one Count Average/Grade");
		return false;
	}	
	if (cnt>1 && rateIncDecCnt!=0)	{
		alert("Please remove Higher count and lower count rate.");
		return false;
	}

	if (!validateSOProductRepeat()) {
		return false;
	}	
	
	if (landingCenter==0) {
		var dMsg = " The entered rate will apply for all Landing Centers. Do you wish to Continue? ";
		if (!confirm(dMsg)) return false;
	} else if (supplier==0) {
		var dMsg = " The entered rate will apply for all the suppliers. Do you wish to Continue? ";
		if (!confirm(dMsg)) return false;
	}
	
	if (confirmSave()) {
  		return true;
	} else {
		return false;
	}
}

//Validate repeated
function validateSOProductRepeat()
{
	
	if (Array.indexOf != 'function') {  
		Array.prototype.indexOf = function(f, s) {
		if (typeof s == 'undefined') s = 0;
			for (var i = s; i < this.length; i++) {   
			if (f === this[i]) return i; 
			} 
		return -1;  
		}
	}
	
	var rc = document.getElementById("hidTableRowCount").value;
	var prevOrder = 0;
	var arr = new Array();
	var arri=0;
	var cAvgArr = new Array();
	var cAvg = 0;
	var hidReceived		=	document.getElementById("hidReceived").value;
	for( j=0; j<rc; j++ )    {
		var status = document.getElementById("status_"+j).value;	
		if (status!='N') {
			if (hidReceived=='G' || hidReceived=='B') {
				var rv = document.getElementById("selGrade_"+j).value;			
				if (arr.indexOf(rv)!=-1 && rv!="") {
					alert("Please make sure the selected Grade is not duplicate.");
					document.getElementById("selGrade_"+j).focus();
					return false;
				}
				arr[arri++]=rv;
			}
			if (hidReceived=='C' || hidReceived=='B') {
				var countAverage  = document.getElementById("countAverage_"+j).value;
				if (cAvgArr.indexOf(countAverage)!=-1) {
					alert("count Average can not be duplicate.");
					document.getElementById("countAverage_"+j).focus();
					return false;
				}
				cAvgArr[cAvg++]=countAverage;
			}
		}
	}
	return true;
}

	function validateSelect(form)
	{
		var selFish	= form.selFilter.value;
		var dateSelect	= form.selDate.value;
	
		if (selFish!=0 && dateSelect==0) {
			alert("Please select a date");
			form.selDate.focus();
			return false;
		}
		if (selFish==0 && dateSelect!=0) {
			alert("Please select a Fish to view date wise list");
			form.selFilter.focus();
			return false;
		}
		return true;	
	}


//ADD MULTIPLE Item- ADD ROW START
function addNewDailyRateItemRow(tableId,receivedBy,selGradeId,countAvg, highCount, lowCount, mktRate, declRate)
{
	var tbl		= document.getElementById(tableId);
	var lastRow	= tbl.rows.length;	
	var row		= tbl.insertRow(lastRow);
	
	row.height	= "28";
	row.className 	= "whiteRow";
	row.align 	= "center";
	row.id 		= "row_"+fieldId;	
	
	var startCount = 0;
	if (receivedBy=='G'  || receivedBy=='B'){ 
		var cell1	= row.insertCell(startCount);
		startCount = startCount+1;
	}
	if (receivedBy=='C' || receivedBy=='B') {
		var cell2	= row.insertCell(startCount);
		startCount = startCount+1;
	}	
	var cell3	= row.insertCell(startCount);
	startCount 	= startCount+1;
	var cell4	= row.insertCell(startCount);
	startCount 	= startCount+1;
	var cell5	= row.insertCell(startCount);
	startCount 	= startCount+1;
	var cell6	= row.insertCell(startCount);	
	startCount 	= startCount+1;
	var cell7	= row.insertCell(startCount);	
	
	
	if (receivedBy=='G'  || receivedBy=='B'){ 
		cell1.className	= "listing-item"; cell1.align	= "center";
	}
	if (receivedBy=='C' || receivedBy=='B') {
		cell2.className	= "listing-item"; cell2.align	= "center";
	}
	
	cell3.className	= "listing-item"; cell3.align	= "center";
	cell4.className	= "listing-item"; cell4.align	= "center";
	cell5.className	= "listing-item"; cell5.align	= "center";cell5.noWrap = "true";
	cell6.className	= "listing-item"; cell6.align	= "center";cell6.noWrap = "true";
	cell7.className	= "listing-item"; cell7.align	= "center";cell7.noWrap = "true";	
		
	var selectGrade	= "<select name='selGrade_"+fieldId+"' id='selGrade_"+fieldId+"' onchange=\"xajax_assignSelGrade(document.getElementById('selGrade_"+fieldId+"').value,'"+fieldId+"')\" onkeydown=\"return nextField(event,'document.frmDailyRate','selGrade_"+parseInt(fieldId+1)+"', 'countAverage_"+fieldId+"', 'selGrade_"+fieldId+"');\"><option value=''>--Select--</option>";	
	selectGrade += "</select>";
	
	var ds = "N";	
	//if( fieldId >= 1) 
	var imageButton = "<a href='###' onClick=\"setPOItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	//else var imageButton = "&nbsp;&nbsp;&nbsp;&nbsp;";

	var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input name='hidGradeId_"+fieldId+"' type='hidden' id='hidGradeId_"+fieldId+"' value='"+selGradeId+"'>";	
	if (receivedBy=='G'  || receivedBy=='B') { 
		cell1.innerHTML	= selectGrade;
	}
	if (receivedBy=='C' || receivedBy=='B') {
		cell2.innerHTML = "<input name='countAverage_"+fieldId+"' type='text' id='countAverage_"+fieldId+"' size='3' maxlength='5' style='text-align:right' value='"+countAvg+"' autocomplete='off' onkeydown=\"return nextField(event,'document.frmDailyRate','countAverage_"+parseInt(fieldId+1)+"', 'higherCount_"+fieldId+"', 'selGrade_"+fieldId+"');\" />";	
	}
	cell3.innerHTML = "<input name='higherCount_"+fieldId+"' type='text' id='higherCount_"+fieldId+"' value='"+highCount+"' size='3' style='text-align:right;' autocomplete='off' onkeydown=\"return nextField(event,'document.frmDailyRate','higherCount_"+parseInt(fieldId+1)+"', 'lowerCount_"+fieldId+"', 'countAverage_"+fieldId+"');\">";
	cell4.innerHTML = "<input name='lowerCount_"+fieldId+"' type='text' id='lowerCount_"+fieldId+"' value='"+lowCount+"' size='3' style='text-align:right;' autocomplete='off' onkeydown=\"return nextField(event,'document.frmDailyRate','lowerCount_"+parseInt(fieldId+1)+"', 'marketRate_"+fieldId+"', 'higherCount_"+fieldId+"');\">";	
	cell5.innerHTML = "<input name='marketRate_"+fieldId+"'  type='text' id='marketRate_"+fieldId+"'  style='text-align:right' value='"+mktRate+"' size='6' maxlength='7' autocomplete='off' onkeydown=\"return nextField(event,'document.frmDailyRate','marketRate_"+parseInt(fieldId+1)+"', 'decRate_"+fieldId+"', 'lowerCount_"+fieldId+"');\" />"+hiddenFields+"";	
	cell6.innerHTML = "<input name='decRate_"+fieldId+"' type='text' id='decRate_"+fieldId+"' style='text-align:right' value='"+declRate+"' size='6' maxlength='7' autocomplete='off' onkeydown=\"return nextField(event,'document.frmDailyRate','decRate_"+parseInt(fieldId+1)+"', 'decRate_"+fieldId+"', 'marketRate_"+fieldId+"');\" />";	
	cell7.innerHTML = imageButton;	
	
	fieldId		= parseInt(fieldId)+1;		
	document.getElementById("hidTableRowCount").value = fieldId;		
}

function setPOItemStatus(id)
{
	if (confirmRemoveItem()) {
		document.getElementById("status_"+id).value = document.getElementById("IsFromDB_"+id).value;
		document.getElementById("row_"+id).style.display = 'none';		
	}
	return false;
}

	function validateDRateCpyFrom(form)
	{
		var cpyFrmDate		=	form.cpyFrmDate.value
		var cpyFrmFish		=	form.cpyFrmFish.value;
		var cpyFrmProcessCode	=	form.cpyFrmProcessCode.value;
				
		if(cpyFrmDate==""){
			alert("Please select a date");
			form.cpyFrmDate.focus();
			return false;	
		}
					
		if (cpyFrmFish=="") {
			alert("Please select a fish.");
			form.cpyFrmFish.focus();
			return false;
		}
	
		if (cpyFrmProcessCode=="") {
			alert("Please select a Process Code.");
			form.processCode.focus();
			return false;
		}	
		//document.getElementById("frmDailyRate").submit();
		return true;
	}

	function resetValues()
	{
		document.getElementById("frmDailyRate").submit();
	}

	// Event, frm Name, Next Field name, Right field name, Left Field Name
	function nextField(e, form, nField, rField, lField)
	{
		var ecode = getKeyCode(e);	
		//alert(name);
		var sName = nField.split("_");
		dArrowName = sName[0]+"_"+(sName[1]-2);
		rightArrow = rField;
		leftArrow  = lField;
				
		if ((ecode==13) || (ecode == 0) || (ecode==40)){
			var nextControl = eval(form+"."+nField);
			if ( nextControl ) { nextControl.focus(); }
			return false;
		}
		if ((ecode==38)){
			var nextControl = eval(form+"."+dArrowName);
			if ( nextControl ) { nextControl.focus(); }
			return false;
		}
		if ((ecode==39)){
			//alert(rightArrow);
			var nextControl = eval(form+"."+rightArrow);
			if ( nextControl ) { nextControl.focus(); }
			return false;
		}
		if ((ecode==37)){
			//alert(leftArrow);
			var nextControl = eval(form+"."+leftArrow);
			if ( nextControl ) { nextControl.focus(); }
			return false;
		}
	}