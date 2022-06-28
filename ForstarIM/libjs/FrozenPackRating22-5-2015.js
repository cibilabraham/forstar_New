function validateFznPkngRating(form)
{	//alert("hii");
	var qeName		= form.qeName.value;
	var frozenCode		=	form.frozenCode.value;
	var freezingStage	=	form.freezingStage.value;
	var selQuality		=	form.selQuality.value;
	var selProcessor = document.getElementById("selProcessor").value;
	//var arrangeGrade	= document.getElementById("hidArrangeGrade").value;
	var selMode		= document.getElementById("hidMode").value;
	var gradeRecSize = document.getElementById("gradeRecSize").value;
	var selGradeRecSize 	= document.getElementById("selGradeRecSize").value;
	var selGradeRecSizeDiff = document.getElementById("selGradeRecSizeDiff").value;
	
	
	var addEditMode		= false;
	//alert("hui");
	/*if (selMode==1) 
	{	
		//alert("hii");
		var selQuickEntryList = document.getElementById("selQuickEntryList").value;
		var hidQeName  = document.getElementById("hidQeName").value;
		//alert(qeName+'-------------'+hidQeName);
		if (selQuickEntryList!="") {
			addEditMode = true;
			if (trim(qeName)==trim(hidQeName)) {
				alert("Please modifiy the Frozen pack rate name. ");
				form.qeName.focus();
				return false;
			}
		}
	}*/

	if (trim(qeName)=="") {
		alert("Please enter a name.");
		form.qeName.focus();
		return false;
	}	
	
	if (frozenCode=="") {
		alert("Please select a Frozen Code.");
		form.frozenCode.focus();
		return false;
	}

	if (freezingStage=="") {
		alert("Please select a Freezing Stage.");
		form.freezingStage.focus();
		return false;
	}
	if (selQuality=="") {
		alert("Please select a Quality.");
		form.selQuality.focus();
		return false;
	}
	if (selProcessor=="") {
		alert("Please select a Processor.");
		form.selProcessor.focus();
		return false;
	}
	
	var rowCount	= document.getElementById("hidTableRowCount").value;
	var hidTRowCount = document.getElementById("hidTRowCount").value;
	var itemsSelected = false;
		
		if (rowCount>0) {
			for (i=0; i<rowCount; i++) {
				var status = document.getElementById("status_"+i).value;
				if (status!='N') {
					var selFish = document.getElementById("selFish_"+i);
					var selProcessCode = document.getElementById("selProcessCode_"+i);
				
					if (selFish.value=="") {
						alert("Please select a Fish.");
						selFish.focus();
						return false;
					}
					if (selProcessCode.value=="") {
						alert("Please select a Process Code.");
						selProcessCode.focus();
						return false;
					}					
									
					if (selFish.value!="") {
						itemsSelected = true;
					}
				}
			}  // For Loop Ends Here
		} // Row Count checking End
		//alert("huiiii");
		if (itemsSelected==false) {
			alert("Please add atleast one combination");
			return false;
		}
		
		if (!validateItemRepeat()) {
			return false;
		}

	
		/*if (selGradeRecSize!=gradeRecSize || selGradeRecSizeDiff!=0) {
			alert("Please click Sort and arrange button.\nProcess code wise grades are not matching.");
			return false;
		}

		var gradeRowCount = document.getElementById("hidGradeRowCount").value;
		if (gradeRowCount>0) {
			for (j=1; j<gradeRowCount; j++) {
				var displayOrderId = document.getElementById("displayOrderId_"+j).value;	
				if (parseInt(displayOrderId)>parseInt(gradeRowCount-1) || displayOrderId<1) {
					alert("Please check grade sort value");
					return false;
				}
			}
		} else {
			alert("No grade exist.");
			return false;
		}
		
		if (!chkDuplicateSortValue()) {
			return false;
		}	*/
		
	// End Here checking grade
	if (!confirmSave()) return false;
	else return true;
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
		var pArr	= new Array();	
		var pa		= 0;
	
		for (j=0; j<rc; j++) {
			var status = document.getElementById("status_"+j).value;
			if (status!='N') {
				var selFish = document.getElementById("selFish_"+j).value;		
				var selProcessCode = document.getElementById("selProcessCode_"+j).value;
	
				var addVal = selFish+""+selProcessCode;
				
				if (pArr.indexOf(addVal)!=-1) {
					alert(" Combination cannot be duplicate.");
					document.getElementById("selFish_"+j).focus();
					return false;	
				}						
				pArr[pa++]	= addVal;
			}
		}	
		return true;
	}
	// ------------------------------------------------------
	// Duplication check Ends here
	// ------------------------------------------------------


	// ADD MULTIPLE Item- ADD ROW START
	function addNewRawDataRow(tableId, sFishId, rateTag, selMode, sProcessCodeId, pcFromDB, userId, fznPkgQEListId, selQuickEntryList)
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
		cell2.className	= "listing-item"; cell2.align	= "center"; cell2.colspan=2;
		cell3.className	= "listing-item"; cell3.align	= "center";
		cell4.className	= "listing-item"; cell4.align	= "center";
		cell5.className	= "listing-item"; cell5.align	= "center";
		cell6.className	= "listing-item"; cell6.align	= "center";
		cell7.className	= "listing-item"; cell7.align	= "center";
				
		var selFish = "<select name='selFish_"+fieldId+"' id='selFish_"+fieldId+"' onchange=\"xajax_getProcessCodeRecords(document.getElementById('selFish_"+fieldId+"').value, '"+fieldId+"', '');\"><option value=''>-- Select --</option>";
		<?php
			if (sizeof($fishMasterRecords)>0) {	
				foreach ($fishMasterRecords as $fr) {
					$fId		= $fr[0];
					$fishName	= stripSlash($fr[1]);
		?>	
			if (sFishId== "<?=$fId?>")  var sel = "Selected";
			else var sel = ""; 
	
		selFish += "<option value=\"<?=$fId?>\" "+sel+"><?=$fishName?></option>";	
		<?php
				}
			}
		?>
		selFish += "</select>";
		
		var selProcessCode = "<select name='selProcessCode_"+fieldId+"' id='selProcessCode_"+fieldId+"'><option value=''>-- Select --</option>";
		selProcessCode += "</select>";
		var selFreezingStage = "<select name='selFreezingStage_"+fieldId+"' id='selFreezingStage_"+fieldId+"'><option value=''>-- Select --</option>";
		<? if(sizeof($freezingStageRecords)>0)
		{
			foreach($freezingStageRecords as $freezStage)
			{
			?>
			selFreezingStage+="<option value='<?=$freezStage[0]?>'><?=$freezStage[1]?></option>";	
		<?
			}
		}
		?>
		selFreezingStage += "</select>";
		var selQuality = "<select name='selQuality_"+fieldId+"' id='selQuality_"+fieldId+"'><option value=''>-- Select --</option>";
		<? if(sizeof($qualityMasterRecords)>0)
		{
			foreach($qualityMasterRecords as $qualityMaster)
			{
			?>
			selQuality+="<option value='<?=$qualityMaster[0]?>'><?=$qualityMaster[1]?></option>";	
		<?
			}
		}
		?>
		selQuality += "</select>";
		
		var setRate="<a href='javascript:void(0)' onclick=\"getGrade(document.getElementById('selProcessCode_"+fieldId+"').value,document.getElementById('selFish_"+fieldId+"').value,'"+fieldId+"')\">Set</a>";
		var ds = "N";	
		//if( fieldId >= 1) 
		var imageButton = "<a href='###' onClick=\"setRowItemStatus('"+fieldId+"', '"+selMode+"', '"+userId+"', '"+fznPkgQEListId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
		//else var imageButton = "&nbsp;&nbsp;&nbsp;&nbsp;";
	
		var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input type='hidden' name='rateTag_"+fieldId+"' id='rateTag_"+fieldId+"' value=''><input name='frozenPackId_"+fieldId+"' type='hidden' id='frozenPackId_"+fieldId+"' value='"+pcFromDB+"'>";
		
		var selFrozencode="<table cellspacing='1' cellpadding='3' bgcolor='#999999'><tr><td bgcolor='#E8EDFF'><table id='tblfrozen_"+fieldId+"' ><tr id='rows_"+fieldId+"_0'><td>";
		selFrozencode+="<select name='selFrozenCode_"+fieldId+"_0' id='selFrozenCode_"+fieldId+"_0'><option value=''>-- Select --</option>";
		<? if(sizeof($frozenPackingRecords)>0)
		{
			foreach($frozenPackingRecords as $frozenPacking)
			{
			?>
			selFrozencode+="<option value='<?=$frozenPacking[0]?>'><?=$frozenPacking[1]?></option>";	
		<?
			}
		}
		?>
		selFrozencode+="</select>";
		selFrozencode+="</td><td><a href='###' onClick=\"setRowFrozenStatus('"+fieldId+"','0');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a><input name='fstatus_"+fieldId+"_0' type='hidden' id='fstatus_"+fieldId+"_0' value=''><input name='IsFromDB_"+fieldId+"_0' type='hidden' id='IsFromDB_"+fieldId+"_0' value='"+ds+"'><input type='hidden' name='frozenPackId_"+fieldId+"_0' id='frozenPackId_"+fieldId+"_0' value=''></td></tr></table><tr><td class='listing-item' bgcolor='#E8EDFF'><input type='hidden'  name='hidTblRowCount_"+fieldId+"' id='hidTblRowCount_"+fieldId+"' value='1' /><a href='#' onclick=\"addrow('"+fieldId+"');\" style='color:red;font-size:10px'>Add New</a></td></tr></td></tr></table>";

		cell1.innerHTML	= selFish;
		cell2.innerHTML	= selProcessCode;
		cell3.innerHTML	= selFreezingStage;
		cell4.innerHTML	= selQuality;
		cell5.innerHTML	= selFrozencode;
		cell6.innerHTML = setRate;	
		cell7.innerHTML = imageButton+hiddenFields;	
		// Get Process Code when edit Mode
		if (selMode==2 || selQuickEntryList!="") getProcessCode(sFishId, fieldId, sProcessCodeId);	
		//addrow("tblfrozen",fieldId,cell5);
		fieldId		= parseInt(fieldId)+1;	
		document.getElementById("hidTableRowCount").value = fieldId;
		
	}

	function addrow(rowCount)
	{
		fld =document.getElementById("hidTblRowCount_"+rowCount).value;	
		var tbl		= document.getElementById("tblfrozen_"+rowCount);
		var lastRow	= tbl.rows.length;
		var row		= tbl.insertRow(lastRow);
		row.height	= "28";
		row.className 	= "whiteRow";
		row.align 	= "center";
		row.id 		= "rows_"+rowCount+"_"+fld;	
		
		var cell1	= row.insertCell(0);
		var cell2	= row.insertCell(1);
		cell1.className	= "listing-item"; cell1.align	= "center"; cell1.noWrap = "true";
		cell2.className	= "listing-item"; cell2.align	= "center"; cell2.noWrap = "true";	
		var selFrozencode = "<select name='selFrozenCode_"+rowCount+"_"+fld+"' id='selFrozenCode_"+rowCount+"_"+fld+"'><option value=''>-- Select --</option>";
		<? if(sizeof($frozenPackingRecords)>0)
		{
			foreach($frozenPackingRecords as $frozenPacking)
			{
			?>
			selFrozencode+="<option value='<?=$frozenPacking[0]?>'><?=$frozenPacking[1]?></option>";	
		<?
			}
		}
		?>
		selFrozencode += "</select>";
		
		var ds = "N";	
		var imageButton = "<a href='###' onClick=\"setRowFrozenStatus('"+rowCount+"','"+fld+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
		var hiddenFields = "<input name='fstatus_"+rowCount+"_"+fld+"' type='hidden' id='fstatus_"+rowCount+"_"+fld+"' value=''><input name='IsFromDB_"+rowCount+"_"+fld+"' type='hidden' id='IsFromDB_"+rowCount+"_"+fld+"' value='"+ds+"'><input type='hidden' name='frozenPackId_"+rowCount+"_"+fld+"' id='frozenPackId_"+rowCount+"_"+fld+"' value=''>";	
			
		cell1.innerHTML	= selFrozencode;
		cell2.innerHTML	= imageButton+hiddenFields;
		
		fld		= parseInt(fld)+1;	
		document.getElementById("hidTblRowCount_"+rowCount).value = fld;		
	}

	function setRowFrozenStatus(rowCnt,fieldCnt)
	{
		if (confirmRemoveItem()) 
		{
			document.getElementById("fstatus_"+rowCnt+"_"+fieldCnt).value = document.getElementById("IsFromDB_"+rowCnt+"_"+fieldCnt).value;
			document.getElementById("rows_"+rowCnt+"_"+fieldCnt).style.display = 'none';
		}
		return false;
	}

	function addrow_old(tableId,fieldId,row)
	{
		
		var tbl		= document.getElementById(tableId);
		var cell5	= row.insertCell(4);
		
		cell5.className	= "listing-item"; cell5.align	= "center";
				
		var selFrozencode = "<select name='selFreezingStage_"+fieldId+"' id='selFreezingStage_"+fieldId+"'><option value=''>-- Select --</option>";
		selFrozencode += "</select>";
		
		var ds = "N";	
		//if( fieldId >= 1) 

		//var imageButton = "<a href='###' onClick=\"setRowItemStatus('"+fieldId+"', '"+selMode+"', '"+userId+"', '"+fznPkgQEListId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
		
		//else var imageButton = "&nbsp;&nbsp;&nbsp;&nbsp;";
	
		//var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input type='hidden' name='rateTag_"+fieldId+"' id='rateTag_"+fieldId+"' value=''><input name='frozenPackId_"+fieldId+"' type='hidden' id='frozenPackId_"+fieldId+"' value='"+pcFromDB+"'>";	
		
		cell5.innerHTML	= selFrozencode;
		// Get Process Code when edit Mode
		fieldId		= parseInt(fieldId)+1;	
		document.getElementById("hidTableRowCount").value = fieldId;		
	}








	function setRowItemStatus(id, selMode, userId, fznPkgQEListId)
	{
		if (confirmRemoveItem()) 
		{
			document.getElementById("status_"+id).value = document.getElementById("IsFromDB_"+id).value;
			document.getElementById("row_"+id).style.display = 'none';
			if(selMode==2) 
			{
				chkSortBtnDisplay(userId, fznPkgQEListId);
			}
		}
		return false;
	}

	function getGrade(processcodeId,fishId,rowId)
	{
		var rateTag = document.getElementById('rateTag_'+rowId).value;
		xajax_displayFrozenRate(processcodeId,fishId,rowId,rateTag);
		$( "#dialog" ).dialog({ width: 500, resizable: true, modal: true   });
		//$( "#dialog" ).dialog({ width: 500, height:500, resizable: true, modal: true   });
	}

	function closeLightBox()
	{
		document.getElementById('box').style.display='none';
		document.getElementById('filter').style.display='none';
	}

	function closeSelLightBox(fishCategoryId, fishId, processCodeId, selRowId, rateListId)
	{		
		xajax_getQEL(fishCategoryId,fishId,processCodeId, selRowId, rateListId, 'CW');
		parent.document.getElementById('box').style.display='none';
		parent.document.getElementById('filter').style.display='none';
	}

	function getProcessCode(sFishId, fieldId, sProcessCodeId)
	{		
		xajax_getProcessCodeRecords(sFishId, fieldId, sProcessCodeId);
	}

	// Arrange Grade Records
	function arrangeGradeRecords(userId, gradeQELId, mode)
	{
		if (!validateItemRepeat()) {
			return false;
		}
		
		if (mode==2) {			
			//var cMsg= confirm("This Process will change the current sorting order.\nAfter arrange the grades don't forget to save the changes.");
			var cMsg= confirm("Please remember to save the changes.");
			if (!cMsg) {
				return false;
			}
		}		

		var rowCount	= document.getElementById("hidTableRowCount").value;

		var selProcesscode = "";
		if (rowCount>0) {
			var pcArray = new Array();
			var j=0;
			for (i=0; i<rowCount; i++) {
				var selStatus = document.getElementById("status_"+i).value;
				if (selStatus!='N') {
					var selFish = document.getElementById("selFish_"+i);
					var selProcessCode = document.getElementById("selProcessCode_"+i);	
					if (selFish.value!="" && selProcessCode.value!="") {
						pcArray[j] = selProcessCode.value;
						j++;
					}
				}
			}  // For Loop Ends Here
			selProcesscode = implode(",",pcArray);			
			if (selProcesscode) {
				xajax_insertGradeRecs(userId, selProcesscode, gradeQELId);
			}
			if (j==0) {
				xajax_delGradeRec(userId, gradeQELId);
				sortGraeR(userId, gradeQELId, selProcesscode);
			}
		} // Row Count checking End

		getSelPrCodeSize(userId, gradeQELId);
	}

	// Sort Grade Recs
	function sortGraeR(userId, gradeQELId, selProcesscode)
	{
		if (userId || gradeQELId) xajax_getGradeRecsForArrange(userId, gradeQELId, selProcesscode);
	}

	function changeDisplay(displayChangeId, userId, gradeQELId, selProcesscode)
	{
		xajax_changeDisplayOrder(displayChangeId, userId, gradeQELId, selProcesscode)
	}

	// displaying Sort Btn
	function displaySortBtn()
	{
		var rowCount	= document.getElementById("hidTableRowCount").value;
		var sortBtnActive = false;
		if (rowCount>0) {
			for (i=0; i<rowCount; i++) {
				var status = document.getElementById("status_"+i).value;
				if (status!='N') {
					var pcFromDB = document.getElementById("pcFromDB_"+i);			
					if (pcFromDB.value=="N") {
						sortBtnActive = true;
					}
				}
			}  // For Loop Ends Here
			if (sortBtnActive) document.getElementById("arrangeBtnRow").style.display="";
			else document.getElementById("arrangeBtnRow").style.display="none";
		} // Row Count checking End
	}

	// When Edit Display Btn
	function displaySortArrBtn()
	{
		var gradeRecSize = document.getElementById("gradeRecSize").value;
		if (gradeRecSize==0) document.getElementById("arrangeBtnRow").style.display="";
	}

	function nextBox(e,form,name)
	{
		var ecode = getKeyCode(e);
		var sName = name.split("_");
		dArrowName = sName[0]+"_"+(sName[1]-2);
		
		if ((ecode==13) || (ecode == 9) || (ecode==40)){
			var nextControl = eval(form+"."+name);
			if ( nextControl ) { nextControl.focus(); }
			return false;
		}
		if ((ecode==38)){
			var nextControl = eval(form+"."+dArrowName);
			if ( nextControl ) { nextControl.focus(); }
			return false;
		}		
	}

	// Update Sales Order
	function updateGardeOrder(userId, qelEntryId)
	{
		if (!chkDuplicateSortValue()) {
			return false;
		}

		var cMsg= confirm("Do you wish to save the current sort order.");
		if (!cMsg) {
			return false;
		}
		var gradeRowCount = document.getElementById("hidGradeRowCount").value;
		var recUpdated = false;
		if (gradeRowCount>0) {
			for (j=1; j<gradeRowCount; j++) {
				var displayOrderId = document.getElementById("displayOrderId_"+j).value;
				var gradeQELId	   = document.getElementById("hidGradeEntryId_"+j).value;
				if (displayOrderId && gradeQELId) {
					xajax_updateDisplayOrder(gradeQELId, displayOrderId);
					recUpdated = true;
				}
			}
		}			
		if (!recUpdated) return false;
		else return true;
	}

	function updateGdOrder(userId, qelEntryId, selProcesscodes)
	{		
		if (updateGardeOrder(userId, qelEntryId))
		{			
			// Display grade List
			xajax_getGradeRecsForArrange(userId, qelEntryId, selProcesscodes);
		}
		return true;
	}
	

	function chkDupSortOrder()
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

		var rc = document.getElementById("hidGradeRowCount").value;
		var arr = new Array();
		var arri=0;
		var currentArr = new Array();
		
		for( j=1; j<rc; j++ )    {			
			var rv = parseInt(document.getElementById("displayOrderId_"+j).value);	
			if ( arr.indexOf(rv) != -1 ) {				
				highLight(rv);	
			} else {
				document.getElementById("displayOrderId_"+j).className='input'; 
			}

			arr[arri++]=rv;
		}
	}

	function highLight(searchValue)
	{
		var rc = document.getElementById("hidGradeRowCount").value;	
		
		for( j=1; j<rc; j++ )    {
			var rv = parseInt(document.getElementById("displayOrderId_"+j).value);
			var cR = parseInt(document.getElementById("hidDisplayOrderId_"+j).value);
			if (rv==searchValue && rv==cR) {
				document.getElementById("displayOrderId_"+j).className='highlightTxt';
			} else document.getElementById("displayOrderId_"+j).className='input'; 
		}
	}

	function chkDuplicateSortValue()
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

		var rc = document.getElementById("hidGradeRowCount").value;
		var arr = new Array();
		var arri=0;
		var currentArr = new Array();
		
		for( j=1; j<rc; j++ )    {			
			var rv = parseInt(document.getElementById("displayOrderId_"+j).value);			
			if ( arr.indexOf(rv) != -1 ) {				
				alert("Duplicate Position value exist.");
				document.getElementById("displayOrderId_"+j).focus();	
				return false;			
			}
			arr[arri++]=rv;
		}		
		return true;
	}

	function chkSortBtnDisplay(userId, gradeQELId)
	{
		showFnLoading();
		var rowCount	= document.getElementById("hidTableRowCount").value;
		var gradeRecSize = document.getElementById("gradeRecSize").value;
		var sGradeRecSize = document.getElementById("selGradeRecSize").value;
		var selGradeRecSizeDiff = document.getElementById("selGradeRecSizeDiff").value;
		var sortBtnActive = false;
		if (rowCount>0) {
			for (i=0; i<rowCount; i++) {
				var selStatus = document.getElementById("status_"+i).value;
				var pcFromDB = document.getElementById("pcFromDB_"+i).value;
				if (selStatus!='N') {
					var selFish  = document.getElementById("selFish_"+i).value; 	
					var hidFishId  = document.getElementById("hidFishId_"+i).value; 	
					var selProcessCode  = document.getElementById("selProcessCode_"+i).value;	
					var hidProcessCodeId  = document.getElementById("hidProcessCodeId_"+i).value; 	

					if (pcFromDB=="N" || (selFish!=hidFishId) || (selProcessCode!=hidProcessCodeId)) {
						sortBtnActive = true;
					}
				} else if (selStatus=='N' && pcFromDB=="Y") {
					sortBtnActive = true;
				}
			}  // For Loop Ends Here
			
			if (sortBtnActive || gradeRecSize==0 || (gradeRecSize!=sGradeRecSize) || selGradeRecSizeDiff!=0) {
				 document.getElementById("arrangeBtnRow").style.display="";
				getSelPrCodeSize(userId, gradeQELId);
				//showLoading(); 
					//showFnLoading();
					//return true;
			}
			else {
			document.getElementById("arrangeBtnRow").style.display="none";
			hideFnLoading();
			}
			//showLoading(); 
				//showFnLoading();
				//return true;
		} // Row Count checking End	

	
	}

	function getSelPrCodeSize(userId, gradeQELId)
	{
		var rowCount	= document.getElementById("hidTableRowCount").value;
		
		var selProcesscode = "";
		if (rowCount>0) {
			var pcArray = new Array();
			var j=0;
			for (i=0; i<rowCount; i++) {
				var selStatus = document.getElementById("status_"+i).value;
				if (selStatus!='N') {
					var selFish = document.getElementById("selFish_"+i);
					var selProcessCode = document.getElementById("selProcessCode_"+i);	
					if (selFish.value!="" && selProcessCode.value!="") {
						pcArray[j] = selProcessCode.value;
						j++;
					}
				}
			}  // For Loop Ends Here
			selProcesscode = implode(",",pcArray);
			if (selProcesscode) {
				xajax_getSelPCGradeCount(selProcesscode, userId, gradeQELId);
			}
		} else {
			document.getElementById("selGradeRecSize").value = 0;
		}	
	}

	function chkSelPcsGradeSize(selProcesscode, userId, gradeQELId)
	{
		xajax_getSelPCGradeCount(selProcesscode, userId, gradeQELId);
	}

	// Bulk Grade Update
	function updateFullSetGrade(userId)
	{
		var uptdMsg	= "Do you wish to update all grade?";
		if(confirm(uptdMsg)) {
			xajax_updateFullGradeSet(userId);
			return true;
		}
		return false;	
	}

	function addGradeToPC(processCodeId, gradeId, userId, qelEntryId, selProcesscode)
	{
		var uptdMsg	= "Do you wish to add grade to process code?";
		if (confirm(uptdMsg)) {
			xajax_addGradeToProcessCode(processCodeId, gradeId, userId, qelEntryId, selProcesscode);
			return true;
		}
		return false;
	}

	function quickEntryLoad(formObj)
	{
		showFnLoading(); 
		formObj.form.submit();
	}

	function chkGradeExist()
	{
		var rowCount = document.getElementById("rowCount").value;
		var exptRate = document.getElementById("exptRate").value;
		var gradeAll = document.getElementById("gradeAll").checked;
		for (i=0; i<rowCount; i++) {
			if (gradeAll) {
				document.getElementById("gradeId_"+i).checked = false;
				document.getElementById("gradeId_"+i).disabled = true;
			} else {
				document.getElementById("gradeId_"+i).disabled = false;

			}
		}		
	}
	
	//save data in hidden field
	function addGrade(rowCnt,processcodeId,fishId)
	{
		//alert("hii");
		var itemArray= []; var gradeArray=[]; var oldTag=[];   var selGrades=[]; var selRate=[];
		var rowCount = document.getElementById("rowCount").value;
		//var exptRate = document.getElementById("exptRate").value;
		var gradeAll = document.getElementById("gradeAll").checked;
		var rateTag =	document.getElementById("rateTag_"+rowCnt).value;
		var editArrayCnt=document.getElementById("editArrayCnt").value;//row count of grades and rate used in edit
		var gradeChked = false;
		//alert(editArrayCnt);
		//throw new Error('This is not an error. This is just to abort javascript');
		for (i=0; i<rowCount; i++) {
			var gradeChk = document.getElementById("gradeId_"+i).checked;
			if (gradeChk) gradeChked = true;
		}

		if (!gradeAll && !gradeChked) {
			alert("Please make atleast one grade selection.");
			return false;
		}
		
	/*	if (exptRate=="") {
			alert("Please enter rate.");
			document.getElementById("exptRate").focus();
			return false;
		}
		*/
		//if (gradeAll && !rateTag)
		if (gradeAll) 
		{
			var allGrades=["A"];
			//var jsonData='{"GradeId":'+JSON.stringify(allGrades)+',"Rate":'+exptRate+'}';
			var jsonData='{"GradeId":'+JSON.stringify(allGrades)+'}';
			itemArray.push(jsonData);
			itemArray='['+itemArray+']';
			document.getElementById("rateTag_"+rowCnt).value=itemArray;
			xajax_displayFrozenRate(processcodeId,fishId,rowCnt,itemArray);
		}
		
		
		// Exception rate
		if (!gradeAll) 
		{
			var gArr = new Array();			
			var j=0;
			for (i=0; i<rowCount; i++) 
			{
				var gradeChk = document.getElementById("gradeId_"+i).checked;
				if (gradeChk) 
				{
					var gradeId = document.getElementById("gradeId_"+i).value;
					gArr[j] = gradeId;
					j++;
				}
			}
			if (gArr.length>0) 
			{
				if(rateTag!="")
				{
					var rt = JSON.parse(rateTag);
					//alert(rt);
					var len=rt.length;
					for(i=0; i<len; i++)
					{
						if(editArrayCnt)
						{
							if(i!=editArrayCnt)
							{
								selGrades=rt[i].GradeId;
								//selRate=rt[i].Rate;
								//var oldData='{"GradeId":'+JSON.stringify(selGrades)+',"Rate":'+selRate+'}';
								var oldData='{"GradeId":'+JSON.stringify(selGrades)+'}';
								itemArray.push(oldData);
							}
						}
						else
						{
							selGrades=rt[i].GradeId;
							//selRate=rt[i].Rate;
							//var oldData='{"GradeId":'+JSON.stringify(selGrades)+',"Rate":'+selRate+'}';
							var oldData='{"GradeId":'+JSON.stringify(selGrades)+'}';
							itemArray.push(oldData);
						}
					}
				}
				//var jsonData='{"GradeId":'+JSON.stringify(gArr)+',"Rate":'+exptRate+'}';
				var jsonData='{"GradeId":'+JSON.stringify(gArr)+'}';
				itemArray.push(jsonData);
				itemArray='['+itemArray+']';
				//alert(itemArray);
				document.getElementById("rateTag_"+rowCnt).value=itemArray;
				xajax_displayFrozenRate(processcodeId,fishId,rowCnt,itemArray);
			}
		}
	}
	
	

	//show grades and rate on edit
	function editGrade(processcodeId,fishId,rowCnt,grades)
	{
		var rateTag=document.getElementById("rateTag_"+rowCnt).value;
		//alert(grades);
		//alert(processcodeId+','+fishId+','+freezingStage+','+frozenCode+','+selQuality+','+rowCnt+','+itemArray+','+grades);
		xajax_displayFrozenRate(processcodeId,fishId,rowCnt,rateTag,grades);
	}

	//check all the rates and grades
	function chkAllData()
	{
		var rowCount = document.getElementById("tblRowCount").value;
		var selectall = document.getElementById("selectall").checked;
		for (i=0; i<rowCount; i++) {
			if (selectall) {
				document.getElementById("remove_"+i).checked = true;
				//document.getElementById("remove_"+i).disabled = true;
			} else {
				document.getElementById("remove_"+i).checked = false;
			}
		}		
	}


	//remove row from array and reconstruct the array
	function getGrades(processcodeId,fishId,freezingStage,frozenCode,selQuality,rowCnt)
	{
		var deleteStatus=0;
		var tblRowCount=document.getElementById("tblRowCount").value;
		for (l=0; l<tblRowCount; l++)
		{	
			var gradeChk = document.getElementById("remove_"+l).checked;
			if(gradeChk==true)
			{
				deleteStatus=1;
			}		
		}
		if(l==tblRowCount && deleteStatus==0)
		{
			alert("Please select a record.");
			//return false;
		}
		else if(l==tblRowCount && deleteStatus==1)
		{
			var conDelMsg	=	"Do you wish to delete the selected items?";
			if(confirm(conDelMsg))
			{
				//return true;
				var destArr=[]; var itemArray=[]; var selGrades=[]; var selRate=[];
				//alert(itemArray.length);
				var rateTag=document.getElementById("rateTag_"+rowCnt).value;
				var rates=JSON.parse(rateTag);
				var rs=rates.length;
				//$rates[$i]->GradeId
				for(i=0; i<rs; i++)
				{	
					var gradeChk = document.getElementById("remove_"+i).checked;
					//alert(document.getElementById("remove_"+i).checked);
					if(gradeChk==false) 
					{	
						var rowId = document.getElementById("remove_"+i).value;
						if(i==rowId)
						{
							selGrades=rates[i].GradeId;
							//selRate=rates[i].Rate;
							var oldData='{"GradeId":'+JSON.stringify(selGrades)+'}';
							//var oldData='{"GradeId":'+JSON.stringify(selGrades)+',"Rate":'+selRate+'}';
							itemArray.push(oldData);
						}
					}
				}
				if(i==rs) 
				{
					//alert(itemArray);
					if(itemArray.length>0)
					{
						itemArray='['+itemArray+']';
					}
					else
					{
						itemArray="";
					}
					document.getElementById("rateTag_"+rowCnt).value=itemArray;
					xajax_displayFrozenRate(processcodeId,fishId,rowCnt,itemArray);
				}
			}
			//return false;
		}
		
	}



	//remove row from array and reconstruct the array
	function getGradesold(processcodeId,fishId,freezingStage,frozenCode,selQuality,rowCnt)
	{
		var destArr=[]; var itemArray=[]; var selGrades=[]; var selRate=[];
		//alert(itemArray.length);
		var rateTag=document.getElementById("rateTag_"+rowCnt).value;
		var rates=JSON.parse(rateTag);
		var rs=rates.length;
		//$rates[$i]->GradeId
		
		for(i=0; i<rs; i++)
		{	
			var gradeChk = document.getElementById("remove_"+i).checked;
			//alert(document.getElementById("remove_"+i).checked);
			if(gradeChk==false) 
			{	
				var rowId = document.getElementById("remove_"+i).value;
				if(i==rowId)
				{
					selGrades=rates[i].GradeId;
					selRate=rates[i].Rate;
					var oldData='{"GradeId":'+JSON.stringify(selGrades)+',"Rate":'+selRate+'}';
					itemArray.push(oldData);
				}
			}
		}
		if(i==rs) 
		{
			//alert(itemArray);
			if(itemArray.length>0)
			{
				itemArray='['+itemArray+']';
			}
			else
			{
				itemArray="";
			}
			document.getElementById("rateTag_"+rowCnt).value=itemArray;
			xajax_displayFrozenRate(processcodeId,fishId,freezingStage,frozenCode,selQuality,rowCnt,itemArray);
		}
	}




