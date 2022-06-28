	function validateString(field) 
	{
		var valid = "\abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_"
		// ALL OTHER CHARACTERS ARE INVALID
		var validChar = false;
		var temp;
		for (var i=0; i<field.length; i++) {
			temp = "" + field.substring(i, i+1);
			if (valid.indexOf(temp) == "-1") validChar = true;
		}
		if (validChar) return false;
		else return true;
	}

	function validateStockGroup(form)
	{		
		var category		=	form.category.value;
		var subCategory		=	form.subCategory.value;		
	
		if (category=="") {
			alert("Please select a category.");
			form.category.focus();
			return false;
		}
	
		if (subCategory=="") {
			alert("Please select a subcategory.");
			form.subCategory.focus();
			return false;
		}	
	
	
		var itemCount	=	document.getElementById("hidTableRowCount").value;
		var labelSelected = false;
	
		var userPermissionNeed = false;
		for (i=0; i<itemCount; i++) {
			var status = document.getElementById("status_"+i).value;	
			if (status!='N') {
				var labelName	  = document.getElementById("stkField_"+i);
				var stkGroupInUse = document.getElementById("stkGroupInUse_"+i).value;
				var stkGroupEntryIds = document.getElementById("stkGroupEntryIds_"+i).value;

				if (labelName.value == "") {
					alert("Please select a Label Name.");
					labelName.focus();
					return false;
				}
				if (labelName.value!= "") {
					labelSelected = true;
				}

				if (stkGroupInUse!=0 && subCategory==0 ) {
					alert("The label you have selected is already using in Stock Master.");
					labelName.focus();
					return false;
				}

				if (subCategory==0 && stkGroupEntryIds!="") {
					userPermissionNeed = true;
				}
				
			}
		}

		if (userPermissionNeed) {
			var displayMsg	= "Do you wish to remove the labels linked with the Sub-Category?";
			if(!confirm(displayMsg)) {
				return false;
			}
		}

		if (!labelSelected) {
			alert("Please select atleast one label.");
			return false;
		}

		if (!validateItemRepeat()) {
			return false;
		}
		
		if (!confirmSave()) return false;
		return true;
	}

 // ADD MULTIPLE Item- ADD ROW START
function addNewItemRow(tableId, stkGroupEntryId, stockFieldId, dbExist, stkFValidation, selSubCategoryId)
{
	var tbl		= document.getElementById(tableId);
	var lastRow	= tbl.rows.length;	
	var row		= tbl.insertRow(lastRow);
	
	row.height	= "28";
	row.className 	= "whiteRow";
	row.align 	= "center";
	row.id 		= "row_"+fieldId;	
	
	var cell1	= row.insertCell(0);
	var cell2	= row.insertCell(1);
	var cell3	= row.insertCell(2);
	var cell4	= row.insertCell(3);
		
	cell1.className	= "listing-item"; cell1.align	= "center";cell1.noWrap = "true";
	cell2.className	= "listing-item"; cell2.align	= "center";cell2.noWrap = "true";
	cell3.className	= "listing-item"; cell3.align	= "center";cell3.noWrap = "true";
	cell4.className	= "listing-item"; cell4.align	= "center";cell4.noWrap = "true";
	if (selSubCategoryId!=0) cell3.style.display = "none";
	
	if (dbExist=='Y') var stkFieldStatus = "disabled='true'";
	else var stkFieldStatus = "";
	
	var selField = "<select name='stkField_"+fieldId+"' id='stkField_"+fieldId+"' "+stkFieldStatus+" onchange=\"xajax_fieldUsageStatus(document.getElementById('category').value, document.getElementById('subCategory').value, document.getElementById('stkField_"+fieldId+"').value, '"+fieldId+"');\"><option value=''>--Select--</option>";
	<?php
		if (sizeof($stkFieldRecs)>0) {	
			foreach ($stkFieldRecs as $sfr) {
				$stkFieldId	= $sfr[0];
				$stkFieldLabel  = $sfr[1];
	?>		
		if (stockFieldId== "<?=$stkFieldId?>")  var sel = "Selected";
		else var sel = "";
	selField += "<option value=\"<?=$stkFieldId?>\" "+sel+"><?=$stkFieldLabel?></option>";	
	<?php
			}
		}
	?>
	selField += "</select>";

	var selValidation = "<select name='stkFieldValidation_"+fieldId+"' id='stkFieldValidation_"+fieldId+"' "+stkFieldStatus+">";
	<?php
		if (sizeof($validationArr)>0) {	
			foreach ($validationArr as $vaKey=>$vaValue) {				
	?>	
			
		if (stkFValidation== "<?=$vaKey?>")  var sel = "Selected";
		else var sel = "";	
	selValidation += "<option value=\"<?=$vaKey?>\" "+sel+"><?=$vaValue?></option>";	
	<?php
			}
		}
	?>
	selValidation += "</select>";

	
	
	var ds = "N";	
	if (dbExist!='Y') {
	var imageButton = "<a href='###' onClick=\"setRowItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	} else var imageButton = "&nbsp;&nbsp;&nbsp;&nbsp;";

	var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input name='hidStkGroupEntryId_"+fieldId+"' type='hidden' id='hidStkGroupEntryId_"+fieldId+"' value='"+stkGroupEntryId+"'><input type='hidden' name='stkGroupInUse_"+fieldId+"' id='stkGroupInUse_"+fieldId+"' value=''><input type='hidden' name='stkGroupEntryIds_"+fieldId+"' id='stkGroupEntryIds_"+fieldId+"' value=''>";	
	//var hiddenFields = "<input name='status_"+fieldId+"' type='text' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='text' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input name='hidStkGroupEntryId_"+fieldId+"' type='hidden' id='hidStkGroupEntryId_"+fieldId+"' value='"+stkGroupEntryId+"'><input type='hidden' name='stkGroupInUse_"+fieldId+"' id='stkGroupInUse_"+fieldId+"' value=''><input type='text' name='stkGroupEntryIds_"+fieldId+"' id='stkGroupEntryIds_"+fieldId+"' value='"+stkGroupEntryId+"'>";		
	//value='"+stkGroupEntryId+"'
	cell1.innerHTML	= selField;
	cell2.innerHTML	= selValidation;
	cell3.innerHTML	= "<div id='usageStatus_"+fieldId+"'></div>";		
	cell4.innerHTML = imageButton+hiddenFields;
					

	fieldId		= parseInt(fieldId)+1;	
	document.getElementById("hidTableRowCount").value = fieldId;	
}


	function setRowItemStatus(id)
	{
		if (confirmRemoveItem()) {
			document.getElementById("status_"+id).value = document.getElementById("IsFromDB_"+id).value;
			document.getElementById("row_"+id).style.display = 'none';			
		}
		return false;
	}

// ------------------------------------------------------
// Duplication check starts here
// ------------------------------------------------------
function validateItemRepeat()
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
	var pArr	= new Array();	
	var pa		= 0;

	for (j=0; j<rc; j++) {
		var status = document.getElementById("status_"+j).value;
		if (status!='N') {
			var selFieldId = document.getElementById("stkField_"+j).value;
	
			if (pArr.indexOf(selFieldId)!=-1) {
				alert(" Label cannot be duplicate.");
				document.getElementById("stkField_"+j).focus();
				return false;	
			}						
			pArr[pa++]	= selFieldId;		
		}
	}	
	return true;
}

// ------------------------------------------------------
// Duplication check Ends here
// ------------------------------------------------------

function getLoading(formObj)
{
showFnLoading(); 
formObj.form.submit();
}