function validateFrozenPacking(form)
{
	var frozenCode		= form.frozenCode.value;
	var selUnit		= form.selUnit.value;
	var freezing		= form.freezing.value;
	var declWt		= form.declWt.value;
	var glaze		= form.glaze.value;
	var hidFrozenCode	= document.getElementById("hidFrozenCode").value;
	var addMode		= document.getElementById("hidAddMode").value;

	
	if( frozenCode=="" ) {
		alert("Please enter a Frozen Packing code.");
		form.frozenCode.focus();
		return false;
	}
	
	if (addMode!="") {
		var selFrznPkgCode = document.getElementById("selFrznPkgCode").value;
		if (selFrznPkgCode!="") {
			if (hidFrozenCode==frozenCode) {
				alert("Please modifiy the Frozen Packing Code. ");
				form.frozenCode.focus();
				return false;
			}
		}
	}

	if (selUnit=="") {
		alert("Please select a Unit of Wt.");
		form.selUnit.focus();
		return false;
	}
	
	if (freezing=="") {
		alert("Please select a Freezing Code.");
		form.freezing.focus();
		return false;
	}
	
	
	if (declWt=="") {
		alert("Please enter a Declared Weight.");
		form.declWt.focus();
		return false;
	}
	
	if (glaze=="") {
		alert("Please select a Glaze Percentage");
		form.glaze.focus();
		return false;
	}
		
	if (!confirmSave()) return false;
	else return true;	
}

function calculateFilledWt()
{
	var declWt		=	0;
	var glazePercent	=	0;
	var filledWt		=	0;
	
	var freezing 		=	document.getElementById("freezing").value;	
	var splitOperator	=	freezing.split("_");
	var operator		=	splitOperator[1];
	
	var glaze		=	document.getElementById("glaze").value;
	var splitGlaze 		= 	glaze.split("_");	
	var glaze		=	splitGlaze[1]; 	
	glazePercent		=	glaze/100;
		
	declWt	=	document.getElementById("declWt").value;
	
	if(operator==1) {
		filledWt = declWt*(1+glazePercent);
	} else if(operator==0) {
		filledWt = declWt*(1-glazePercent);
	} else {
		filledWt = declWt;
	}
	if(!isNaN(filledWt)){		
		document.getElementById("filledWt").value = number_format(Math.abs(filledWt),2,'.','');
	}
}

	function enableFrznCodeBtn(mode)
	{
		if (mode==1) {
			document.getElementById("cmdAdd").disabled = false;
			document.getElementById("cmdAdd1").disabled = false;
		} else if (mode==2) {
			document.getElementById("cmdSaveChange").disabled = false;
			document.getElementById("cmdSaveChange1").disabled = false;
		}
	}
	
	function disableFrznCodeBtn(mode)
	{		
		if (mode==1) {
			document.getElementById("cmdAdd").disabled = true;
			document.getElementById("cmdAdd1").disabled = true;
		} else if (mode==2) {
			document.getElementById("cmdSaveChange").disabled = true;
			document.getElementById("cmdSaveChange1").disabled = true;
		}
	}

	//ADD MULTIPLE Item- ADD ROW START	
	function addNewExceptionRow(tableId, exptProcessorId, exptRate, exptCommission, selCriteria, exceptionId, yieldTolerance)
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
				
		cell1.className	= "listing-item"; cell1.align	= "left";cell1.noWrap = "true";
		cell2.className	= "listing-item"; cell2.align	= "center";cell2.noWrap = "true";
		cell3.className	= "listing-item"; cell3.align	= "center";cell3.noWrap = "true";
		cell4.className	= "listing-item"; cell4.align	= "center";cell4.noWrap = "true";
		cell5.className	= "listing-item"; cell5.align	= "center";cell5.noWrap = "true";
		cell6.className	= "listing-item"; cell6.align	= "center";cell6.noWrap = "true";
			
		var processorList = "<select name='selProcessor_"+fieldId+"' id='selProcessor_"+fieldId+"'><option value=''>--Select--</option>";
		<?php
			if (sizeof($preProcessorRecords)>0) {
				foreach($preProcessorRecords as $pr) {
					$processorId	= $pr[0];
					$processorName	= stripSlash($pr[1]);					
		?>		
			if (exptProcessorId==<?php echo $processorId?>) var selPrOpt = 'selected=true';
			else var selPrOpt = '';
		processorList += "<option value='<?=$processorId?>' "+selPrOpt+"><?=$processorName?></option>";
		<?php } } ?>
		processorList += "</select>";
	
		var allProcessorList = "<select name='selProcessor_"+fieldId+"' id='selProcessor_"+fieldId+"'><option value='0'>ALL</option>";
		allProcessorList += "</select>";
		

		if (selCriteria==0) var selToCriteria = 'selected=true';
		else 	var selToCriteria = '';
		if (selCriteria==1) var selFromCriteria = 'selected=true';
		else var selFromCriteria = '';
		var criteriaList = "<select name='processCriteria_"+fieldId+"' id='processCriteria_"+fieldId+"'>";
		criteriaList     += "<option value='0' "+selToCriteria+">RM Weight</option>";
		criteriaList     += "<option value='1' "+selFromCriteria+">Frozen Weight</option>";
		criteriaList     += "<option value='2' "+selFromCriteria+">Thawed Weight</option>";
		criteriaList     += "</select>";
	
			
		var ds = "N";	
		if (fieldId!=0) var imageButton = "<a href='###' onClick=\"setItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
		else var imageButton="";

		var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input name='hidExceptionId_"+fieldId+"' type='hidden' id='hidExceptionId_"+fieldId+"' value='"+exceptionId+"'>";
	
		if (fieldId==0) cell1.innerHTML = allProcessorList;
		else cell1.innerHTML = processorList;

		cell2.innerHTML = "<input name='processRate_"+fieldId+"' id='processRate_"+fieldId+"' type='text' size='3' value='"+exptRate+"' style='text-align:right;' autocomplete='off'>";	
		cell3.innerHTML = "<input name='processCommission_"+fieldId+"' id='processCommission_"+fieldId+"' type='text' size='3' value='"+exptCommission+"' style='text-align:right;' autocomplete='off'>";		
		cell4.innerHTML = criteriaList;
		cell5.innerHTML = "<input name='yieldTolerance_"+fieldId+"' id='yieldTolerance_"+fieldId+"' type='text' size='3' value='"+yieldTolerance+"' style='text-align:right;' autocomplete='off'>";
		cell6.innerHTML = imageButton+hiddenFields;

		fieldId		= parseInt(fieldId)+1;
		document.getElementById("hidTableRowCount").value = fieldId;
	}
	
	function setItemStatus(id)
	{
		if (confirmRemoveItem()) {
			document.getElementById("status_"+id).value = document.getElementById("IsFromDB_"+id).value;
			document.getElementById("row_"+id).style.display = 'none'; 		
		}
		return false;
	}

	
	function getFrznPkgCode(formObj)
	{
	showFnLoading(); 
	formObj.form.submit();

	}

	function frozenPackingLoad(formObj)
	{
	showFnLoading(); 
	formObj.form.submit();
	}


