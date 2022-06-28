	function validateSubCategory(form)
	{
		var category	= form.category.value;
		var subCategory	= form.subCategoryName.value;
		var unitGroup	= form.unitGroup.value;
		var mode	= document.getElementById("hidMode").value;	
		var rowCount	= document.getElementById("hidTableRowCount").value;

		// Add Mode
		if (mode==1) { 
			var selSubCategory = document.getElementById("selSubCategory").value;
			var hidSubCategoryName = document.getElementById("hidSubCategoryName").value;
			if (selSubCategory!="") {				
				if (hidSubCategoryName==subCategory) {
					alert("Please modifiy the Sub-Category name. ");
					form.subCategoryName.focus();
					return false;
				}
			}
		}

		if (category=="") {
			alert("Please select a Category.");
			form.category.focus();
			return false;
		}
	
		if (subCategory=="") {
			alert("Please enter a Sub Category Name.");
			form.subCategoryName.focus();
			return false;
		}
		
		if (unitGroup=="") {
			alert("Please select a Unit Group.");
			form.unitGroup.focus();
			return false;
		}
		// Check Box Selected
		if (document.getElementById('checkPoint').checked) {
			var rowCount	=	document.getElementById("hidTableRowCount").value;
			var chkPointSelected = false;
			
			if (rowCount>0) {
				for (i=0; i<rowCount; i++) {
				var status = document.getElementById("status_"+i).value;
					if (status!='N') {
						var selCheckPoint= document.getElementById("selCheckPoint_"+i);
						
							if (selCheckPoint.value=="") {
								alert("Please select a Check Point.");
								selCheckPoint.focus();
								return false;
							}							
						
							if (selCheckPoint.value!="") {
								chkPointSelected = true;
							}
					}
				}  // For Loop Ends Here
			} // Row Count checking End
			if (chkPointSelected==false) {
				alert("Please add atleast one Check Point");
				return false;
			}
		
			if (!validateChkPointRepeat()) {
				return false;
			}
		}

		if (!confirmSave()) return false;
		return true;	
	}


	//ADD MULTIPLE Item- ADD ROW START
	function addNewCheckPoint(tableId, selChkPointId, chkPointEntryId)
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
	
		cell1.className	= "listing-item"; cell1.align	= "center";
		cell2.className	= "listing-item"; cell2.align	= "center";
				
		var selectCheckPoint	= "<select name='selCheckPoint_"+fieldId+"' id='selCheckPoint_"+fieldId+"'><option value=''>--Select--</option>";
		<?php
			if (sizeof($checkPointRecords)>0) {	
				foreach($checkPointRecords as $cpr) {
					$checkPointId	= $cpr[0];
					$name		= stripSlash($cpr[1]);
		?>	
			if (selChkPointId== "<?=$checkPointId?>")  var sel = "Selected";
			else var sel = "";
	
		selectCheckPoint += "<option value=\"<?=$checkPointId?>\" "+sel+"><?=$name?></option>";	
		<?php
				}
			}
		?>
		selectCheckPoint += "</select>";
		
		
		var ds = "N";	
		//if( fieldId >= 1) 
		var imageButton = "<a href='###' onClick=\"setCheckPointItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
			
		var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input name='chkPointEntryId_"+fieldId+"' type='hidden' id='chkPointEntryId_"+fieldId+"' value='"+chkPointEntryId+"'>";	
		
		cell1.innerHTML	= selectCheckPoint+hiddenFields;	
		cell2.innerHTML = imageButton;	
		
		fieldId		= parseInt(fieldId)+1;	
		document.getElementById("hidTableRowCount").value = fieldId;		
	}

	function setCheckPointItemStatus(id)
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
	function validateChkPointRepeat()
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
				var rv = document.getElementById("selCheckPoint_"+j).value;
				if ( arr.indexOf(rv) != -1 )    {
					alert("Please make sure the selected check Point is not duplicate.");
					document.getElementById("selCheckPoint_"+j).focus();
					return false;
				}		
				arr[arri++]=rv;
			}
		}
		return true;
	}

	// Show Table
	function showChkPoint()
	{		
		if (!document.getElementById('checkPoint').checked) {			
			document.getElementById("chkPointRow").style.display='none';
		} else {			
			document.getElementById("chkPointRow").style.display='';
		}		
	}



function getSubcategory(formObj)
{
showFnLoading(); 
formObj.form.submit();
}