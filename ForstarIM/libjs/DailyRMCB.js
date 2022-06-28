var pcArr = new Array(); 

function validateDailyRMCB(form)
{
	var selectDate		= form.selectDate.value
	var pcRowCount 		= document.getElementById("pcRowCount").value;
	var rowCount		= document.getElementById("hidTableRowCount").value;
		
	if (selectDate=="") {
		alert("Please select a date.");
		form.selectDate.focus();
		return false;	
	}

	if (findDaysDiff(selectDate)>0) {
		alert(" Date should be less than or equal to current date");
		form.selectDate.focus();
		return false;	
	}

	
	var cbEntered = false;	
	for (var i=1; i<=pcRowCount; i++) {
		var ppmCB 	= document.getElementById("ppmCB_"+i);
		var prodnCB 	= document.getElementById("prodnCB_"+i);
		if (ppmCB.value || prodnCB.value) {
			cbEntered = true;
		}

		if (ppmCB.value!="") {
			if (!checkDigit(ppmCB.value)) {
				alert("Please enter numeric value only.");
				ppmCB.focus();
				return false;
			}
		}

		if (prodnCB.value!="") {
			if (!checkDigit(prodnCB.value)) {
				alert("Please enter numeric value only.");
				prodnCB.focus();
				return false;
			}
		}
	}
	
	if (!cbEntered) {
		alert("Please enter atleast one item closing balance.");
		return false;
	}

	if (rowCount>0) {
		for (i=0; i<rowCount; i++) {
			var status = document.getElementById("status_"+i).value;
			if (status!='N') {

				if (!validateExptPCRepeat()) {
					return false;
				}

				var fishId	= document.getElementById("exptfishId_"+i);
				var pcId	= document.getElementById("exptPCode_"+i);
				var exptPPCS	= document.getElementById("exptPPCS_"+i);
				var exptProdnCS	= document.getElementById("exptProdnCS_"+i);
				var exptRPMCS	= document.getElementById("exptRPMCS_"+i);

				if (fishId.value) {
					if (pcId.value=="") {
						alert("Please select Process Code.");
						pcId.focus();
						return false;
					}
					if (exptPPCS.value=="" && exptProdnCS.value=="" && exptRPMCS.value=="") {
						alert("Please enter closing balance.");
						return false;
					}
					
				}
			}
		}  // For Loop Ends Here
	} // Row Count checking End
	
	if (confirmSave())
	{
		jQuery('#selFish').attr("disabled", false); 
		jQuery('#company').attr("disabled", false); 
		jQuery('#unit').attr("disabled", false); 
		return true;
	}
	else
	{
		return false;
	}
}

	// left /right /up/down moving (Focus Next)
	function nCBTxtBox(e, form, fldName)
	{
		var ecode = getKeyCode(e);	
		//alert(ecode);			
		var fName  = fldName.split("_");
			
		// Down Arrow and enter key
		if ((ecode==13) || (ecode == 0) || (ecode==40)) {
			nextTextBoxName = fName[0]+"_"+(parseInt(fName[1])+1);
			var nextControl = eval(form+"."+nextTextBoxName);
			if ( nextControl ) { nextControl.focus();}			
			return false;
		}
		// UP aRROW
		if (ecode==38) {
			nextTextBoxName = fName[0]+"_"+(parseInt(fName[1])-1);
			var nextControl = eval(form+"."+nextTextBoxName);
			if ( nextControl ) { nextControl.focus(); }
			return false;
		}
		//Right Arrow
		if (ecode==39) {			
			/*
			if (fName[0]=="ppmCB") var rightArrow =   "rpmCB_"+(parseInt(fName[1]));
			else if (fName[0]=="rpmCB") var rightArrow =   "prodnCB_"+(parseInt(fName[1]));	
			*/		
			if (fName[0]=="ppmCB") var rightArrow =   "prodnCB_"+(parseInt(fName[1]));
			else if (fName[0]=="prodnCB") var rightArrow =   "rpmCB_"+(parseInt(fName[1]));	
			var nextControl = eval(form+"."+rightArrow);
			if ( nextControl ) { nextControl.focus(); }
			return false;
		}
		//Left Arrow
		if (ecode==37) {
			if (fName[0]=="rpmCB") var leftArrow =   "prodnCB_"+(parseInt(fName[1]));
			else if (fName[0]=="prodnCB") var leftArrow =   "ppmCB_"+(parseInt(fName[1]));
			
			var nextControl = eval(form+"."+leftArrow);
			if ( nextControl ) { nextControl.focus(); }
			return false;
		}
	} // Cursor Move Ends Here

	// calculate Total CS
	function calcTotCS()
	{
		var pcRowCount = document.getElementById("pcRowCount").value;
		if (pcRowCount>0) {
			var calcTotCS = 0
			var totPrePMCS = 0;
			var totRePMCS = 0;
			var totPrdnMCS = 0;
			var grandTotCS = 0;
			
			for (var i=1; i<=pcRowCount; i++) {
				var ppmCB 	= document.getElementById("ppmCB_"+i);
				var prodnCB 	= document.getElementById("prodnCB_"+i);
				var rpmCB	= document.getElementById("rpmCB_"+i);
				var prePMCS = (ppmCB.value!="")?(ppmCB.value):0;
				totPrePMCS += parseFloat(prePMCS);
				
				var rePMCS = (rpmCB.value!="")?(rpmCB.value):0;
				totRePMCS += parseFloat(rePMCS);
	
				var prdnMCS = (prodnCB.value!="")?(prodnCB.value):0;	
				totPrdnMCS += parseFloat(prdnMCS);
				
				//+parseFloat(rePMCS)
				calcTotCS  = 	parseFloat(prePMCS)+parseFloat(prdnMCS);
				grandTotCS += calcTotCS;

				if (!isNaN(calcTotCS) && calcTotCS!=0) {
					document.getElementById("totalCB_"+i).value = number_format(calcTotCS,2,'.','');
				} else document.getElementById("totalCB_"+i).value = "";
			}
	
			if (!isNaN(totPrePMCS)) {
				document.getElementById("totalPPMCB").value = number_format(totPrePMCS,2,'.','');
			}
			if (!isNaN(totRePMCS)) {
				document.getElementById("totalRPMCB").value = number_format(totRePMCS,2,'.','');
			}
			if (!isNaN(totPrdnMCS)) {
				document.getElementById("totalProdCB").value = number_format(totPrdnMCS,2,'.','');
			}
			if (!isNaN(grandTotCS)) {
				document.getElementById("grandTotalCS").value = number_format(grandTotCS,2,'.','');
			}
		}
	}

	//ADD MULTIPLE Item- ADD ROW START
	function addNewPCItemRow(tableId)
	{
		var tbl		= document.getElementById(tableId);
		var lastRow	= tbl.rows.length;
		var iteration	= lastRow+1;
		var row		= tbl.insertRow(lastRow);
		
		row.height	= "28";
		row.className 	= "whiteRow";
		row.align 	= "center";
		row.id 		= "row_"+fieldId;
		
		var cell1	= row.insertCell(0);
		var cell2	= row.insertCell(1);
		var cell3	= row.insertCell(2);
		var cell4	= row.insertCell(3);
		var cell5	= row.insertCell(4);
		var cell6	= row.insertCell(5);		
		var cell7	= row.insertCell(6);
		
		cell1.className	= "listing-item"; cell1.align	= "center";cell1.noWrap = "true";
		cell2.className	= "listing-item"; cell2.align	= "center";cell2.noWrap = "true";
		cell3.className	= "listing-item"; cell3.align	= "center";cell3.noWrap = "true";
		cell4.className	= "listing-item"; cell4.align	= "center";cell4.noWrap = "true";
		cell5.className	= "listing-item"; cell5.align	= "center";cell5.noWrap = "true";
		cell6.className	= "listing-item"; cell6.align	= "center";cell6.noWrap = "true";
		cell7.className	= "listing-item"; cell7.align	= "center";cell7.noWrap = "true";
		
	
		var fishList = "<select name='exptfishId_"+fieldId+"' id='exptfishId_"+fieldId+"' onChange=\"filterPCRecs(document.getElementById('exptfishId_"+fieldId+"').value,'"+fieldId+"');\"><option value=''>--Select--</option>";
		<?php
		if (sizeof($fishMasterRecords)>0) {
			foreach ($fishMasterRecords as $fl) {
				$rFishId	= $fl[0];
				$fishName	= $fl[1];					
		?>
		var selFishOpt = '';
		fishList += "<option value='<?=$rFishId?>' "+selFishOpt+"><?=$fishName?></option>";
		<?php
				}
			}
		?>
		fishList += "</select>";

		var pcList = "<select name='exptPCode_"+fieldId+"' id='exptPCode_"+fieldId+"'><option value=''>--Select--</option>";
		pcList += "</select>";

		var ds = "N";	
		var imageButton = "<a href='###' onClick=\"setIngItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";

		var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'>";
	
		cell1.innerHTML = fishList;
		cell2.innerHTML = pcList;	
		cell3.innerHTML = "<input type='text' name='exptPPCS_"+fieldId+"' id='exptPPCS_"+fieldId+"' value='' size='9' style='text-align:right;' onkeyup='calcFilterTotCS();' autocomplete='off'>";
		cell4.innerHTML = "<input type='text' name='exptProdnCS_"+fieldId+"' id='exptProdnCS_"+fieldId+"' value='' size='9' style='text-align:right;' onkeyup='calcFilterTotCS();' autocomplete='off'>";
		cell5.innerHTML = "<input type='text' name='exptTotalCS_"+fieldId+"' id='exptTotalCS_"+fieldId+"' value='' size='9' style='text-align:right;' readonly>";
		cell6.innerHTML = "<input type='text' name='exptRPMCS_"+fieldId+"' id='exptRPMCS_"+fieldId+"' value='' size='9' style='text-align:right;' autocomplete='off'>";
		cell7.innerHTML = imageButton+hiddenFields;
		fieldId		= parseInt(fieldId)+1;
		document.getElementById("hidTableRowCount").value = fieldId;
	}
	
	function setIngItemStatus(id)
	{
		if (confirmRemoveItem()) {
			document.getElementById("status_"+id).value = document.getElementById("IsFromDB_"+id).value;
			document.getElementById("row_"+id).style.display = 'none'; 		
		}
		return false;
	}

	/* ------------------------------------------------------ */
	// Duplication check starts here
	/* ------------------------------------------------------ */
	var cArr = new Array();
	var cArri = 0;	
	function validateExptPCRepeat()
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
		var arr = new Array();
		var arri=0;
	
		for (j=0; j<rc; j++) {
			var status = document.getElementById("status_"+j).value;
			if (status!='N') {
				var fish = document.getElementById("exptfishId_"+j).value;
				var pCode = document.getElementById("exptPCode_"+j).value;
				var rv = fish+" "+pCode;
				
				if ( arr.indexOf(rv) != -1 )    {
					alert("Please make sure the selected item is not duplicate.");
					document.getElementById("exptfishId_"+j).focus();
					return false;
				}
				arr[arri++]=rv;
			}
		}
	return true;
	}
	/*
	if (!validateExptPCRepeat()) {
			return false;
		}
	*/
	
	// ------------------------------------------------------
	// Duplication check Ends here
	// ------------------------------------------------------

	function filterPCRecs(fishId, rowId)
	{
		//xajax_getPCRecs(document.getElementById('exptfishId_"+fieldId+"').value, '"+fieldId+"', '')
		var pcId = pcArr[fishId];
		xajax_getPCRecs(fishId, rowId, pcId)
	}

	function calcFilterTotCS()
	{
		var rowCount = document.getElementById("hidTableRowCount").value;
		if (rowCount>0) {
			var calcTotCS = 0
			var totPrePMCS = 0;
			var totRePMCS = 0;
			var totPrdnMCS = 0;
			var grandTotCS = 0;
			
			for (i=0; i<rowCount; i++) {
				var status = document.getElementById("status_"+i).value;
				if (status!='N') {
					var ppmCB 	= document.getElementById("exptPPCS_"+i);
					var prodnCB 	= document.getElementById("exptProdnCS_"+i);
					var rpmCB	= document.getElementById("exptRPMCS_"+i);
					var prePMCS = (ppmCB.value!="")?(ppmCB.value):0;
					var prdnMCS = (prodnCB.value!="")?(prodnCB.value):0;
					var rePMCS = (rpmCB.value!="")?(rpmCB.value):0;
					calcTotCS  = 	parseFloat(prePMCS)+parseFloat(prdnMCS);
	
					if (!isNaN(calcTotCS) && calcTotCS!=0) {
						document.getElementById("exptTotalCS_"+i).value = number_format(calcTotCS,2,'.','');
					} else document.getElementById("exptTotalCS_"+i).value = "";
				}
			} // Loop Ends here
		}
	}

	function fishLoad(formObj)
	{
		showFnLoading(); 
		formObj.form.submit();
	}

	function enableDPPButton(mode)
	{
		if (mode==1) {
			document.getElementById("cmdAdd").disabled = false;
			document.getElementById("cmdAdd1").disabled = false;
			
		} else if (mode==0) {
			document.getElementById("cmdSaveChange").disabled = false;
			document.getElementById("cmdSaveChange1").disabled = false;
		}
	}
	
	function disableDPPButton(mode)
	{		
		if (mode==1) {
			document.getElementById("cmdAdd").disabled = true;
			document.getElementById("cmdAdd1").disabled = true;
		}
		else if (mode==0) {
			document.getElementById("cmdSaveChange").disabled = true;
			document.getElementById("cmdSaveChange1").disabled = true;
		}
	}

	