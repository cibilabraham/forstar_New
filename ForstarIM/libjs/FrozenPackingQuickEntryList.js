function validateFznPkngQuickEntryList(form)
{
	var frozenLotId		=	form.frozenLotId.value;	
	var freezingStage	=	form.freezingStage.value;
	var eUCode		=	form.eUCode.value;
	var brand		=	form.brand.value;
	var frozenCode		=	form.frozenCode.value;
	var mCPacking		=	form.mCPacking.value;
	var exportLotId		=	form.exportLotId.value;
	var allocateMode	=	form.allocateMode.value;
	var selQuality		=	form.selQuality.value;
	var qeName		= form.qeName.value;
	var arrangeGrade	= document.getElementById("hidArrangeGrade").value;
	var selMode		= document.getElementById("hidMode").value;
	var addEditMode		= false;
	var gradeRecSize = document.getElementById("gradeRecSize").value;
	var selGradeRecSize 	= document.getElementById("selGradeRecSize").value;
	var selGradeRecSizeDiff = document.getElementById("selGradeRecSizeDiff").value;
	var codeType=form.codeType.value;

	if (selMode==1) {		
		var selQuickEntryList = document.getElementById("selQuickEntryList").value;
		var hidQeName  = document.getElementById("hidQeName").value;
		if (selQuickEntryList!="") {
			addEditMode = true;
			if (trim(qeName)==trim(hidQeName)) {
				alert("Please modifiy the Quick entry name. ");
				form.qeName.focus();
				return false;
			}
		}
	}
	
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

	if(codeType=="")
	{
		alert("Please select a Process code Type.");
		form.codeType.focus();
		return false;
	}
//	alert(codeType);	
	if(codeType=="S")
	{
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

			if (itemsSelected==false) {
				alert("Please add atleast one combination");
				return false;
			}
		}
		else if(codeType=="C")
		{ 
			var rowCounts	= document.getElementById("hidTableRowCount2").value;
			if (rowCounts>0) {
				for (i=0; i<rowCounts; i++) {
					var sstatus = document.getElementById("sstatus_"+i).value;
					if (sstatus!='N') {
						var selSecondaryProcessCode = document.getElementById("selSecondaryProcessCode_"+i);
						if (selSecondaryProcessCode.value=="") {
							alert("Please select a Secondary ProcessCode.");
							selSecondaryProcessCode.focus();
							return false;
						}					
					}
				}  // For Loop Ends Here
			} // Row Count checking End

		}


		if (!validateItemRepeat()) {
			return false;
		}
		if(codeType=="S")
		{
			if (selGradeRecSize!=gradeRecSize || selGradeRecSizeDiff!=0) {
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
		}	
	}
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
		
		var codeType=document.getElementById("codeType").value;	
		if(codeType=="S")
		{
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
		}
		else if(codeType=="C")
		{
			var rc = document.getElementById("hidTableRowCount2").value;	
			var pArr	= new Array();	
			var pa		= 0;
		
			for (j=0; j<rc; j++) {
				var sstatus = document.getElementById("sstatus_"+j).value;
				if (sstatus!='N') {
					var selSecondaryProcessCode = document.getElementById("selSecondaryProcessCode_"+j).value;
					if (pArr.indexOf(selSecondaryProcessCode)!=-1) {
						alert("	Secondary Process code cannot be duplicate.");
						document.getElementById("selSecondaryProcessCode_"+j).focus();
						return false;	
					}						
					pArr[pa++]	= selSecondaryProcessCode;
				}
			}	
		}
		return true;
	}
	// ------------------------------------------------------
	// Duplication check Ends here
	// ------------------------------------------------------


	// ADD MULTIPLE Item- ADD ROW START
	function addNewRawDataRow(tableId, sFishId, qelEntryId, selMode, sProcessCodeId, pcFromDB, userId, fznPkgQEListId, selQuickEntryList)
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
		
			
		cell1.className	= "listing-item"; cell1.align	= "center";
		cell2.className	= "listing-item"; cell2.align	= "center";
		cell3.className	= "listing-item"; cell3.align	= "center";
		
				
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
		
		var selProcessCode = "<select name='selProcessCode_"+fieldId+"' id='selProcessCode_"+fieldId+"' onchange=\"chkSortBtnDisplay('"+userId+"', '"+fznPkgQEListId+"');\"><option value=''>-- Select --</option>";
		selProcessCode += "</select>";
		
		var ds = "N";	
		//if( fieldId >= 1) 
		var imageButton = "<a href='###' onClick=\"setRowItemStatus('"+fieldId+"', '"+selMode+"', '"+userId+"', '"+fznPkgQEListId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
		//else var imageButton = "&nbsp;&nbsp;&nbsp;&nbsp;";
	
		var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input type='hidden' name='qelEntryId_"+fieldId+"' id='qelEntryId_"+fieldId+"' value='"+qelEntryId+"'><input name='pcFromDB_"+fieldId+"' type='hidden' id='pcFromDB_"+fieldId+"' value='"+pcFromDB+"'><input name='hidFishId_"+fieldId+"' type='hidden' id='hidFishId_"+fieldId+"' value='"+sFishId+"'><input name='hidProcessCodeId_"+fieldId+"' type='hidden' id='hidProcessCodeId_"+fieldId+"' value='"+sProcessCodeId+"'>";	
		
		cell1.innerHTML	= selFish;
		cell2.innerHTML	= selProcessCode;
		cell3.innerHTML = imageButton+hiddenFields;	
		
		// Get Process Code when edit Mode
		if (selMode==2 || selQuickEntryList!="") getProcessCode(sFishId, fieldId, sProcessCodeId);	
		
		fieldId		= parseInt(fieldId)+1;	
		document.getElementById("hidTableRowCount").value = fieldId;		
	}

	function setRowItemStatus(id, selMode, userId, fznPkgQEListId)
	{
		if (confirmRemoveItem()) {
			document.getElementById("status_"+id).value = document.getElementById("IsFromDB_"+id).value;
			document.getElementById("row_"+id).style.display = 'none';
			if (selMode==2) {
				chkSortBtnDisplay(userId, fznPkgQEListId);
			}
		}
		return false;
	}


	// ADD MULTIPLE Item- ADD ROW START
	function addNewRawDataRow2(tableId, selProcessCodeId, qelEntryId, selMode, sProcessCodeId, pcFromDB, userId, fznPkgQEListId, selQuickEntryList)
	{
		var tbl		= document.getElementById(tableId);
		var lastRow	= tbl.rows.length;
		var row		= tbl.insertRow(lastRow);
		
		row.height	= "28";
		row.className 	= "whiteRow";
		row.align 	= "center";
		row.id 		= "srow_"+fdId;	
		
		var cell1	= row.insertCell(0);
		var cell2	= row.insertCell(1);
		var cell3	= row.insertCell(2);
		
		cell1.className	= "listing-item"; cell1.align	= "center";
		cell2.className	= "listing-item"; cell2.align	= "center";
		cell3.className	= "listing-item"; cell3.align	= "center";
		
		var selProcessCode = "<select name='selSecondaryProcessCode_"+fdId+"' id='selSecondaryProcessCode_"+fdId+"' onchange='xajax_getSecondaryGrade(this.value,"+fdId+");'><option value=''>-- Select --</option>";
		<?php
			if (sizeof($secondaryProcessCode)>0) {	
				foreach ($secondaryProcessCode as $sPC) {
					$secondaryPCId		= $sPC[0];
					$secondaryName	= stripSlash($sPC[1]);
		?>	
			if (selProcessCodeId== "<?=$secondaryPCId?>")  var sel = "Selected";
			else var sel = ""; 
	
		selProcessCode += "<option value=\"<?=$secondaryPCId?>\" "+sel+"><?=$secondaryName?></option>";	
		<?php
				}
			}
		?>
		selProcessCode += "</select>";
		 
		 var selGrade="<select name='selGrade_"+fdId+"' id='selGrade_"+fdId+"'><option value=''>-- Select --</option>";
		selGrade += "</select>";

		var ds = "N";	
		//if( fieldId >= 1) 
		var imageButton = "<a href='###' onClick=\"setRowItemStatus2('"+fdId+"', '"+selMode+"', '"+userId+"', '"+fznPkgQEListId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
		//else var imageButton = "&nbsp;&nbsp;&nbsp;&nbsp;";
	
		var hiddenFields = "<input name='sstatus_"+fdId+"' type='hidden' id='sstatus_"+fdId+"' value=''><input name='IsFromDB_"+fdId+"' type='hidden' id='IsFromDB_"+fdId+"' value='"+ds+"'>";	
		
		cell1.innerHTML	= selProcessCode;
		cell2.innerHTML	= selGrade;
		cell3.innerHTML = imageButton+hiddenFields;	
		
		fdId		= parseInt(fdId)+1;	
		document.getElementById("hidTableRowCount2").value = fdId;		
	}

	function setRowItemStatus2(id, selMode, userId, fznPkgQEListId)
	{
		if (confirmRemoveItem()) {
			document.getElementById("sstatus_"+id).value = document.getElementById("IsFromDB_"+id).value;
			document.getElementById("srow_"+id).style.display = 'none';
			if (selMode==2) {
				chkSortBtnDisplay(userId, fznPkgQEListId);
			}
		}
		return false;
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
				//alert("hiii1");
				xajax_insertGradeRecs(userId, selProcesscode, gradeQELId);
			}
			if (j==0) {
				//alert("hiii2");
				xajax_delGradeRec(userId, gradeQELId);
				sortGraeR(userId, gradeQELId, selProcesscode);
			}
		} // Row Count checking End
		//alert("hiii3s");
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


	function displaytbl()
	{
		//alert("hii");
		var codeType=document.getElementById('codeType').value;
		if(codeType=="S")
		{
			document.getElementById('primary').style.display="block";
			document.getElementById('secondary').style.display="none";

		}
		else
		{
			document.getElementById('primary').style.display="none";
			document.getElementById('secondary').style.display="block";
		}
	}