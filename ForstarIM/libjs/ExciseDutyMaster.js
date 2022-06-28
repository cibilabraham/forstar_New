function validateExciseDutyMaster(form)
{	

	var mode   = document.getElementById("hidMode").value; // Mode =1 : addmode, mode =2 : edit Mode

		var rowCount	= document.getElementById("hidTableRowCount").value;
		var itemsSelected = false;
		var exciseDutyChanged = false;

		if (rowCount>0) {
			for (i=0; i<rowCount; i++) {
				var status = document.getElementById("status_"+i).value;
				if (status!='N') {
					var selPCategory = document.getElementById("selProductCategory_"+i);
					var selPState = document.getElementById("selProductState_"+i);
					var selPGroup = document.getElementById("selProductGroup_"+i);
					var stateGroupExist = document.getElementById("productStateGroup_"+i);
					var excisePercent	    = document.getElementById("excisePercent_"+i);	
					var hidExcisePercent	= document.getElementById("hidExcisePercent_"+i);

					if (selPCategory.value=="") {
						alert("Please select a Product Category.");
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
					if (excisePercent.value=="") {
						alert("Please enter excise duty.");
						excisePercent.focus();
						return false;
					}
	
					if (!checkNumber(excisePercent.value)) {
						excisePercent.value = "";
						excisePercent.focus();
						return false;
					}
					//&& selPState.value!=""
					if (selPCategory.value!="") {
						itemsSelected = true;
					}

					if (parseFloat(excisePercent.value)!=parseFloat(hidExcisePercent.value)) {
						exciseDutyChanged = true;
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

		if (mode==2 && exciseDutyChanged) {
			var confirmRateListMsg	= confirm("Do you want to save this to new Rate list?\n");
			if (confirmRateListMsg) {
				var confirmAgainMsg =  confirm("The new excise duty is only applicable from today onwards.\n");
				if (confirmAgainMsg) {
					document.getElementById("newRateList").value = 1;	
					return true;
				}
			}
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
	var cell6	= row.insertCell(5);
	var cell7	= row.insertCell(6);
		
	cell1.className	= "listing-item"; cell1.align	= "center";
	cell2.className	= "listing-item"; cell2.align	= "center";
	cell3.className	= "listing-item"; cell3.align	= "center";
        cell4.className	= "listing-item"; cell4.align	= "center";
	cell5.className	= "listing-item"; cell5.align	= "center";
	cell6.className	= "listing-item"; cell6.align	= "center";
	cell7.className	= "listing-item"; cell7.align	= "center";
			
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

	var selExGoods = "<select name='goodsType_"+fieldId+"' id='goodsType_"+fieldId+"'><option value=''>-- Select --</option>";
	<?php	
			foreach ($exGoodsMasterRecs as $egm) {
				$exGoodsId 	= $egm[0];
				$exGoodname	= $egm[1];
	?>
	selExGoods += "<option value=\"<?=$exGoodsId?>\"><?=$exGoodname?></option>";	
	<?php			
		}
	?>
	selExGoods += "</select>";
	
	var ds = "N";	
	//if( fieldId >= 1) 
	var imageButton = "<a href='###' onClick=\"setProdItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	//else var imageButton = "&nbsp;&nbsp;&nbsp;&nbsp;";

	var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input type='hidden' name='productStateGroup_"+fieldId+"' id='productStateGroup_"+fieldId+"' value=''><input type='hidden' name='stateVatEntryId_"+fieldId+"' id='stateVatEntryId_"+fieldId+"' value='"+stateVatEntryId+"'><input name='hidExcisePercent_"+fieldId+"' type='hidden' id='hidExcisePercent_"+fieldId+"' value='"+vat+"' size='4' style='text-align:right' readonly='true'>";	
	
	cell1.innerHTML	= selProuctCategory;
	cell2.innerHTML	= selProuctState;
	cell3.innerHTML	= selProuctGroup;
	cell4.innerHTML	= "<input name='excisePercent_"+fieldId+"' type='text' id='excisePercent_"+fieldId+"' value='"+vat+"' size='4' style='text-align:right'>";
	cell5.innerHTML	= "<input name='chapterSubheading_"+fieldId+"' type='text' id='chapterSubheading_"+fieldId+"' value='' size='20'>";
	cell6.innerHTML	= selExGoods;
	cell7.innerHTML = imageButton+hiddenFields;	
	
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

	function toggleStartDate()
	{
		var startDate 		= $("#startDate").val();
		var hidStartDate 	= $("#hidStartDate").val();
		if (startDate!=hidStartDate) $("#startDateUptd").show();
		else $("#startDateUptd").hide();
	}

	function updateStartDate()
	{	
		var startDate 		= $("#startDate").val();
		var exciseDutyRateList  = $("#exciseDutyRateList").val();
		var cMsg = "The start date you have selected is "+startDate+". Do you wish to continue?";
		if (confirm(cMsg)) {
			if (exciseDutyRateList!=0 && startDate!="") {
				xajax_changeRLDate(exciseDutyRateList,startDate);
			}
		}
		return false;
	}

	function uptdActiveFlag(edFlag)
	{
		var cMsg = "Do you wish to save the change?";
		if (confirm(cMsg)) {
			xajax_updateActiveFlag(edFlag);
		}
		return false;
	}

	function deleteEDRateList()
	{
		var exciseDutyRateList  = $("#exciseDutyRateList").val();
		var cMsg = "Do you wish to delete the selected rate list?";
		if (confirm(cMsg)) {
			xajax_deleteEDRateList(exciseDutyRateList);
		}
		return false;
	}