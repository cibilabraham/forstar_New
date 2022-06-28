	function validateTransporterWeightSlab(form)
	{
		var selTransporter	= form.selTransporter.value;			
		var mode		= document.getElementById("hidMode").value;
		
		if (selTransporter=="") {
			alert("Please select a Transporter.");
			form.selTransporter.focus();
			return false;
		}
	
		var rowCount	=	document.getElementById("hidTableRowCount").value;
		var wtSlabSelected = false;
			
		if (rowCount>0) {
			for (i=0; i<rowCount; i++) {
				var status = document.getElementById("status_"+i).value;
				if (status!='N') {
					var wtSlab	= document.getElementById("weightSlab_"+i);
		
						if (wtSlab.value=="") {
							alert("Please select a Weight Slab.");
							wtSlab.focus();
							return false;
						}		
					
						if (wtSlab.value!="") {
							wtSlabSelected = true;
						}
				}
			}  // For Loop Ends Here
		} // Row Count checking End
		if (!wtSlabSelected) {
			alert("Please add atleast one Weight Slab");
			return false;
		}
		
		if (!validateWtSlabDuplicate()) {
			return false;
		}

		
		if (!confirmSave()) {
			return false;
		}
		return true;
	}


	function enableTrptrWtSlabBtn(mode)
	{
		if (mode==1) {
			document.getElementById("cmdAdd").disabled = false;
			document.getElementById("cmdAdd1").disabled = false;
		} else if (mode==2) {
			document.getElementById("cmdSaveChange").disabled = false;
			document.getElementById("cmdSaveChange1").disabled = false;
		}
	}
	
	function disableTrptrWtSlabBtn(mode)
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
	function addNewTransporterWeightSlabRow(tableId, selWtSlabId, trptrWtSlabEntryId, wtSlabInUse)
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
				
		cell1.className	= "listing-item"; cell1.align	= "center";cell1.noWrap = "true";
		cell2.className	= "listing-item"; cell2.align	= "center";cell2.noWrap = "true";

		if (wtSlabInUse) var fieldStatus = "disabled='true'";
		else var fieldStatus = "";
			
		var WtSlabList = "<select name='weightSlab_"+fieldId+"' id='weightSlab_"+fieldId+"'><option value=''>--Select--</option>";
		<?php
			if (sizeof($weightSlabRecords)>0) {
				foreach ($weightSlabRecords as $wsr) {					
					$weightSlabId	= $wsr[0];		
					$wtSlabName 	= stripSlash($wsr[2]);				
		?>		
			if (selWtSlabId==<?=$weightSlabId?>) var selStateOpt = 'selected=true';
			else var selStateOpt = '';
		WtSlabList += "<option value='<?=$weightSlabId?>' "+selStateOpt+"><?=$wtSlabName?></option>";
		<?
				}
			}
		?>
		WtSlabList += "</select>";	
			
		var ds = "N";	
		if (!wtSlabInUse) {
			var imageButton = "<a href='###' onClick=\"setIngItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
		} else var imageButton = "&nbsp;&nbsp;&nbsp;&nbsp;";

		var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input name='hidTrptrWtSlabEntryId_"+fieldId+"' type='hidden' id='hidTrptrWtSlabEntryId_"+fieldId+"' value='"+trptrWtSlabEntryId+"'>";
	
		cell1.innerHTML = WtSlabList;			
		cell2.innerHTML = imageButton+hiddenFields;
		fieldId		= parseInt(fieldId)+1;
		document.getElementById("hidTableRowCount").value = fieldId;
	}
	
	function setIngItemStatus(id)
	{
		//chkWtSlabInUse(trptrWtSlabEntryId, '', '');

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
	function validateWtSlabDuplicate()
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
	
	for (j=0; j<rc; j++) {
		var status = document.getElementById("status_"+j).value;
		if (status!='N') {
			var rv = document.getElementById("weightSlab_"+j).value;
			if ( arr.indexOf(rv) != -1 )    {
				alert("Duplicate exist in weight slab.");
				document.getElementById("weightSlab_"+j).focus();
				return false;
			}		
			arr[arri++]=rv;
		}
	}
	return true;
	}
	/*
	function chkWtSlabInUse(trptrWtSlabEntryId, valFrom, exist)
	{
		if (!valFrom) xajax_chkTrptrWtSlabInUse(trptrWtSlabEntryId);
		alert(trptrWtSlabEntryId+","+valFrom+","+exist);
		if (valFrom && exist) {
			alert ("Failed to remove. Selected Weight slab is already in use.") ;	
			return false;
		}
		return true;
	}
	*/
	
	