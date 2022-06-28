	function validateZoneMaster(form)
	{	
		var name	= form.name.value;
		
		if (name=="") {
			alert("Please enter a Zone Name.");
			form.name.focus();
			return false;
		}	

		var rowCount	=	document.getElementById("hidTableRowCount").value;
		var stateSelected = false;
		
		if (rowCount>0) {
			for (i=0; i<rowCount; i++) {
				var status = document.getElementById("status_"+i).value;
				if (status!='N') {
					var state	= document.getElementById("state_"+i);
					var city	= document.getElementById("city_"+i);
						if (state.value=="") {
							alert("Please select a State.");
							state.focus();
							return false;
						}
						if (city.value=="") {
							alert("Please select a City.");
							city.focus();
							return false;
						}
					
						if (state.value!="") {
							stateSelected = true;
						}
				}
			}  // For Loop Ends Here
		} // Row Count checking End
		if (stateSelected==false) {
			alert("Please add atleast one state");
			return false;
		}
	
		if (!validateDemarcationStateRepeat()) {
			return false;
		}
		if (!confirmSave()) {
			return false;
		}
		return true;
	}

	//ADD MULTIPLE Item- ADD ROW START
	function addNewAreaDemarcationRow(tableId, selStateId, mode, stateEntryId)
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
		
		
		cell1.className	= "listing-item"; cell1.align	= "center";cell1.noWrap = "true";
		cell2.className	= "listing-item"; cell2.align	= "center";cell2.noWrap = "true";
		cell3.className	= "listing-item"; cell3.align	= "center";cell3.noWrap = "true";
		
	
		var stateList = "<select name='state_"+fieldId+"' id='state_"+fieldId+"' onChange=\"xajax_getCityList(document.getElementById('state_"+fieldId+"').value,'"+fieldId+"', '"+mode+"','');\"><option value=''>--Select--</option>";
		<?
			if ($stateResultSetObj!="") {
				while ($sr=$stateResultSetObj->getRow()) {
					$stateId = $sr[0];
					$stateCode	= stripSlash($sr[1]);
					$stateName	= stripSlash($sr[2]);					
		?>		
			if (selStateId==<?=$stateId?>) var selStateOpt = 'selected=true';
			else var selStateOpt = '';
		stateList += "<option value='<?=$stateId?>' "+selStateOpt+"><?=$stateName?></option>";
		<?
				}
			}
		?>
		stateList += "</select>";
	
		var cityList = "<select name='city_"+fieldId+"[]' id='city_"+fieldId+"' multiple='true' size='5'><option value='0'>-- Select All --</option>";
		<?
			foreach ($cityRecords as $cr) {			
				$cityId 	= $cr[0];
				$cityCode	= stripSlash($cr[1]);
				$cityName	= stripSlash($cr[2]);				
				$selected = "";
				if ($selCityId==$cityId) $selected = "Selected"; 
		?>	
		cityList += "<option value='<?=$cityId?>'><?=$cityName?></option>";
		<? 
			}
		?>
		cityList += "</select>";
	
			
		var ds = "N";	
		var imageButton = "<a href='###' onClick=\"setIngItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
		var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input name='hidStateEntryId_"+fieldId+"' type='hidden' id='hidStateEntryId_"+fieldId+"' value='"+stateEntryId+"'>";
	
		cell1.innerHTML = stateList+hiddenFields;
		cell2.innerHTML = cityList;	
		cell3.innerHTML = imageButton;
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
	function validateDemarcationStateRepeat()
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
			var rv = document.getElementById("state_"+j).value;
			var city = document.getElementById("city_"+j);
			var cityOptLength = city.options.length;
			if ( arr.indexOf(rv) != -1 )    {
				alert("Please make sure the selected state is not duplicate.");
				document.getElementById("state_"+j).focus();
				return false;
			}
			arr[arri++]=rv;
	}
	}
	return true;
	}
	
	// ------------------------------------------------------
	// Duplication check Ends here
	// ------------------------------------------------------

	function enableAreaDemarcationBtn(mode)
	{
		if (mode==1) {
			document.getElementById("cmdAdd").disabled = false;
			document.getElementById("cmdAdd1").disabled = false;
		} else if (mode==2) {
			document.getElementById("cmdSaveChange").disabled = false;
			document.getElementById("cmdSaveChange1").disabled = false;
		}
	}
	
	function disableAreaDemarcationBtn(mode)
	{		
		if (mode==1) {
			document.getElementById("cmdAdd").disabled = true;
			document.getElementById("cmdAdd1").disabled = true;
		} else if (mode==2) {
			document.getElementById("cmdSaveChange").disabled = true;
			document.getElementById("cmdSaveChange1").disabled = true;
		}
	}





