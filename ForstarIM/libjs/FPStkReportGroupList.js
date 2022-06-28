function validateFPStkReportGroupList(form)
{	
	var groupName	= form.groupName.value;	
	var sortOrder = document.getElementById("sortOrder");
	var freezingStyle = document.getElementById("freezingStyle");
	var freezingStage = document.getElementById("freezingStage");
	var hideSortOrder	= document.getElementById("hideSortOrder").value;
	
	if (trim(groupName)=="") {
		alert("Please enter a group name.");
		form.groupName.focus();
		return false;
	}

	if (hideSortOrder!="") {
		alert("Please enter another sort order.");
		sortOrder.focus();
		return false;
	}

	if (trim(sortOrder.value)=="") {
		alert("Please enter sort order.");
		sortOrder.focus();
		return false;
	}

	if (!isDigit(sortOrder.value)) {
		alert("Please enter a number.");
		sortOrder.focus();
		return false;
	}

	if (freezingStyle.value=="") {
		alert("Please select a freezing style.");
		freezingStyle.focus();
		return false;
	}

	if (freezingStage.value=="") {
		alert("Please select a freezing stage.");
		freezingStage.focus();
		return false;
	}
		
	var rowCount	= document.getElementById("hidTableRowCount").value;	
	var itemsSelected = false;
		
		if (rowCount>0) {
			for (i=0; i<rowCount; i++) {
				var status = document.getElementById("status_"+i).value;
				if (status!='N') {
									
					var selQEL = document.getElementById("selQEL_"+i);
							
					if (selQEL.value=="") {
						alert("Please select a Quick Entry List.");
						selQEL.focus();
						return false;
					}			

					/*
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
					*/				
					if (selQEL.value!="") {
						itemsSelected = true;
					}
				}
			}  // For Loop Ends Here
		} // Row Count checking End

		if (!itemsSelected) {
			alert("Please add atleast one Quick Entry List.");
			//alert("Please add atleast one combination");
			return false;
		}
		if (!validateItemRepeat()) {
			return false;
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
		
		var rc = document.getElementById("hidTableRowCount").value;	
		var pArr	= new Array();	
		var pa		= 0;
	
		for (j=0; j<rc; j++) {
			var status = document.getElementById("status_"+j).value;
			if (status!='N') {
				var selQEL = document.getElementById("selQEL_"+j).value;
				/*
				var selFish = document.getElementById("selFish_"+j).value;		
				var selProcessCode = document.getElementById("selProcessCode_"+j).value;
				var addVal = selFish+""+selProcessCode;
				*/
				var addVal = selQEL;
				
				if (pArr.indexOf(addVal)!=-1) {
					//alert(" Combination cannot be duplicate.");
					alert("Quick Entry List cannot be duplicate.");
					document.getElementById("selQEL_"+j).focus();
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
	function addNewRawDataRow(tableId)
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
		//var cell3	= row.insertCell(2);
					
		cell1.className	= "listing-item"; cell1.align	= "center";
		cell2.className	= "listing-item"; cell2.align	= "center";
		//cell3.className	= "listing-item"; cell3.align	= "center";
						
		var selQEL = "<select name='selQEL_"+fieldId+"' id='selQEL_"+fieldId+"' style='width:200px;' onchange=\"xajax_setQELId("+fieldId+", document.getElementById('selQEL_"+fieldId+"').value);\"><option value=''>-- Select --</option>";
		<?php		
			if (sizeof($qelRecs)>0) {	
				foreach ($qelRecs as $qel) {
					$qelId		= $qel[0];
					$qelName	= stripSlash($qel[1]);
		?>	
		var sel = "";
		selQEL += "<option value=\"<?=$qelId?>\" "+sel+"><?=$qelName?></option>";	
		<?php		
				}
			}		
		?>
		selQEL += "</select>";


		//var selFish = "<select name='selFish_"+fieldId+"' id='selFish_"+fieldId+"' onchange=\"xajax_getProcessCodeRecords(document.getElementById('selFish_"+fieldId+"').value, '"+fieldId+"', '');\"><option value=''>-- Select --</option>";
		<?php
			/*
			if (sizeof($fishMasterRecords)>0) {	
				foreach ($fishMasterRecords as $fr) {
					$fId		= $fr[0];
					$fishName	= stripSlash($fr[1]);
			*/
		?>	
		//var sel = ""; 
		//selFish += "<option value=\"<?=$fId?>\" "+sel+"><?=$fishName?></option>";	
		<?php
		/*
				}
			}
		*/
		?>
		//selFish += "</select>";
		
		//var selProcessCode = "<select name='selProcessCode_"+fieldId+"' id='selProcessCode_"+fieldId+"'><option value=''>-- Select --</option>";
		//selProcessCode += "</select>";
		
		
		var ds = "N";	
		//if( fieldId >= 1) 
		var imageButton = "<a href='###' onClick=\"setRowItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
		//else var imageButton = "&nbsp;&nbsp;&nbsp;&nbsp;";
	
		var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input type='hidden' name='qelEntryId_"+fieldId+"' id='qelEntryId_"+fieldId+"' value=''><input name='pcFromDB_"+fieldId+"' type='hidden' id='pcFromDB_"+fieldId+"' value=''><input name='hidSelQEL_"+fieldId+"' type='hidden' id='hidSelQEL_"+fieldId+"' value=''>";	
		
		cell1.innerHTML	= selQEL;		
		cell2.innerHTML = imageButton+hiddenFields;
		
		fieldId		= parseInt(fieldId)+1;	
		document.getElementById("hidTableRowCount").value = fieldId;
		
		loadQEList();
	}

	function setRowItemStatus(id)
	{
		if (confirmRemoveItem()) {
			document.getElementById("status_"+id).value = document.getElementById("IsFromDB_"+id).value;
			document.getElementById("row_"+id).style.display = 'none';
		}
		return false;
	}

	function loadQEList()
	{
		//alert(document.getElementById('hidTableRowCount').value+","+ document.getElementById('freezingStyle').value+","+ document.getElementById('freezingStage').value);
		xajax_filterQEList(document.getElementById('hidTableRowCount').value, document.getElementById('freezingStyle').value, document.getElementById('freezingStage').value);
	}
