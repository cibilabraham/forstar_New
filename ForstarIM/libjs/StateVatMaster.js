function validateStateVatMaster(form)
{
	var state		= form.state.value;	
	//var categoryRowCount	= document.getElementById("hidProdCategoryCount").value;
	var stateVatRateList	= document.getElementById("stateVatRateList").value;

	if (state=="") {
		alert("Please select a State.");
		form.state.focus();
		return false;
	}
	var mode   = document.getElementById("hidMode").value; // Mode =1 : addmode, mode =2 : edit Mode
	var copyFrom = "";
	if (mode==1) copyFrom = document.getElementById("copyFromStateId").value;
	if (copyFrom=="") {
		var rowCount	= document.getElementById("hidTableRowCount").value;
		var itemsSelected = false;
		
		if (rowCount>0) {
			for (i=0; i<rowCount; i++) {
				var status = document.getElementById("status_"+i).value;
				if (status!='N') {
					var selPCategory = document.getElementById("selProductCategory_"+i);
					var selPState = document.getElementById("selProductState_"+i);
					var selPGroup = document.getElementById("selProductGroup_"+i);
					var stateGroupExist = document.getElementById("productStateGroup_"+i);
					var vatPercent	    = document.getElementById("vatPercent_"+i);	
				
					if (selPCategory.value=="") {
						alert("Please select a Category.");
						selPCategory.focus();
						return false;
					}
					
					if (selPState.value==0 && mode==2) {
						alert("Please select a Product State.");
						selPState.focus();
						return false;
					}
	
					if (stateGroupExist.value!="") {
						if (selPGroup.value==0 && mode==2) {
							alert("Please select a Product Group.");
							selPGroup.focus();
							return false;
						}
					}
					if (vatPercent.value=="") {
						alert("Please enter VAT.");
						vatPercent.focus();
						return false;
					}
	
					if (!checkNumber(vatPercent.value)) {
						vatPercent.value = "";
						vatPercent.focus();
						return false;
					}
					//&& selPState.value!=""
					if (selPCategory.value!="") {
						itemsSelected = true;
					}
				}
			}  // For Loop Ends Here
		} // Row Count checking End
		if (itemsSelected==false) {
			alert("Please add atleast one combination");
			return false;
		}
		if (!validateItemRepeat()) {
			return false;
		}
	} else if (copyFrom!="") { // Copy From ends here
		var copyFromStateVatRateList = document.getElementById("copyFromStateVatRateList").value;
		if (copyFromStateVatRateList=="") {
			alert("Please select a Rate List");
			document.getElementById("copyFromStateVatRateList").focus();
			return false;
		}
	}

	if (stateVatRateList=="" && mode==2) {
		alert("Please select a rate List");
		document.getElementById("stateVatRateList").focus();
		return false;
	}

	
	if (!confirmSave()) return false;	
	return true;
}


// ADD MULTIPLE Item- ADD ROW START
function addNewProductCatItemRow(tableId, prodCategoryId, prodStateId, vat, stateVatEntryId)
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
	var cell5	= row.insertCell(4);
		
	cell1.className	= "listing-item"; cell1.align	= "center";
	cell2.className	= "listing-item"; cell2.align	= "center";
	cell3.className	= "listing-item"; cell3.align	= "center";
        cell4.className	= "listing-item"; cell4.align	= "center";
	cell5.className	= "listing-item"; cell5.align	= "center";
			
	var selProuctCategory = "<select name='selProductCategory_"+fieldId+"' id='selProductCategory_"+fieldId+"'><option value=''>-- Select --</option>";
	<?php
		if (sizeof($productCategoryRecords)>0) {	
			 foreach ($productCategoryRecords as $cr) {
				$categoryId	= $cr[0];
				$categoryName	= stripSlash($cr[1]);
				$selected = "";
				if ($productCategory==$categoryId) $selected = "Selected";
	?>	
		if (prodCategoryId== "<?=$categoryId?>")  var sel = "Selected";
		else var sel = ""; 

	selProuctCategory += "<option value=\"<?=$categoryId?>\" "+sel+"><?=$categoryName?></option>";	
	<?php
			}
		}
	?>
	selProuctCategory += "</select>";

	var selProuctState = "<select name='selProductState_"+fieldId+"' id='selProductState_"+fieldId+"' onChange = \"xajax_getProductGroupExist(document.getElementById('selProductState_"+fieldId+"').value,"+fieldId+",''); \"><option value='0'>-- Select All --</option>";
	<?php
		if (sizeof($productStateRecords)>0) {	
			foreach ($productStateRecords as $cr) {
				$prodStateId	= $cr[0];
				$prodStateName	= stripSlash($cr[1]);
				$selected = "";
				if ($productState==$prodStateId) $selected = "Selected";
	?>	
		if (prodStateId== "<?=$prodStateId?>")  var sel = "Selected";
		else var sel = ""; 

	selProuctState += "<option value=\"<?=$prodStateId?>\" "+sel+"><?=$prodStateName?></option>";	
	<?php
			}
		}
	?>
	selProuctState += "</select>";

	var selProuctGroup = "<select name='selProductGroup_"+fieldId+"' id='selProductGroup_"+fieldId+"'><option value='0'>-- Select --</option>";
	selProuctGroup += "</select>";
	
	var ds = "N";	
	//if( fieldId >= 1) 
	var imageButton = "<a href='###' onClick=\"setProdItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	//else var imageButton = "&nbsp;&nbsp;&nbsp;&nbsp;";

	var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input type='hidden' name='productStateGroup_"+fieldId+"' id='productStateGroup_"+fieldId+"' value=''><input type='hidden' name='stateVatEntryId_"+fieldId+"' id='stateVatEntryId_"+fieldId+"' value='"+stateVatEntryId+"'><input name='hidVatPercent_"+fieldId+"' type='hidden' id='hidVatPercent_"+fieldId+"' value='"+vat+"' size='4' style='text-align:right' readonly='true'>";	
	
	cell1.innerHTML	= selProuctCategory;
	cell2.innerHTML	= selProuctState;
	cell3.innerHTML	= selProuctGroup;
	cell4.innerHTML	= "<input name='vatPercent_"+fieldId+"' type='text' id='vatPercent_"+fieldId+"' value='"+vat+"' size='4' style='text-align:right'>";
	cell5.innerHTML = imageButton+hiddenFields;	
	
	fieldId		= parseInt(fieldId)+1;	
	document.getElementById("hidTableRowCount").value = fieldId;		
}

function setProdItemStatus(id)
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
var cArr = new Array();
var cArri = 0;	
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
	var pCategory	= new Array();
	var pState	= new Array();
	var pGroup	= new Array();
	var pc = 0;
	var ps = 0;
	var pg = 0;	
	var pArr	= new Array();	
	var pa		= 0;

	for (j=0; j<rc; j++) {
		var status = document.getElementById("status_"+j).value;
		if (status!='N') {
			var selPCategory = document.getElementById("selProductCategory_"+j).value;		
			var selPState = document.getElementById("selProductState_"+j).value;
			var selPGroup = (document.getElementById("selProductGroup_"+j).value=="")?0:document.getElementById("selProductGroup_"+j).value;

			var addVal = selPCategory+""+selPState+""+selPGroup;			
			
			if (pArr.indexOf(addVal)!=-1) {
				alert(" Combination cannot be duplicate.");
				document.getElementById("selProductCategory_"+j).focus();
				return false;	
			}
						
			pArr[pa++]	= addVal;			

			pCategory[pc++] = selPCategory;
			pState[ps++] 	= selPState;
			pGroup[pg++] 	= selPGroup;			
		}
	}	
	return true;
}


// ------------------------------------------------------
// Duplication check Ends here
// ------------------------------------------------------

	function hideCategoryRows()
	{
		var copyFrom = document.getElementById("copyFromStateId").value;
		if (copyFrom!="") {
			document.getElementById("catRow0").style.display = "none";
			document.getElementById("catRow1").style.display = "none";
			document.getElementById("catRow2").style.display = "none";
			document.getElementById("catRow3").style.display = "none";
		} else {
			document.getElementById("catRow0").style.display = "";
			document.getElementById("catRow1").style.display = "";
			document.getElementById("catRow2").style.display = "";
			document.getElementById("catRow3").style.display = "";
		}
	}

	
	function enableStateVatButton(mode)
	{
		if (mode==1) {
			document.getElementById("cmdAdd").disabled = false;
			document.getElementById("cmdAdd1").disabled = false;
		} else if (mode==2) {
			document.getElementById("cmdSaveChange").disabled = false;
			document.getElementById("cmdSaveChange1").disabled = false;
		}
	}
	
	function disableStateVatButton(mode)
	{		
		if (mode==1) {
			document.getElementById("cmdAdd").disabled = true;
			document.getElementById("cmdAdd1").disabled = true;
		} else if (mode==2) {
			document.getElementById("cmdSaveChange").disabled = true;
			document.getElementById("cmdSaveChange1").disabled = true;
		}
	}