

	function validateCommonReason(form)
	{
		var cod 	= document.getElementById("cod");
		var reason	= document.getElementById("reason");
		var checkPoint  = document.getElementById("checkPoint").checked;
		
		if (cod.value=="") {
			alert("Please select account type.");
			cod.focus();
			return false;
		}

		if (reason.value=="") {
			alert("Please enter a reason.");
			reason.focus();
			return false;
		}

		if (checkPoint) {
			var rowCount	= document.getElementById("hidTableRowCount").value;
			var chkListSelected = false;
			
			if (rowCount>0) {
				for (i=0; i<rowCount; i++) {
				var status = document.getElementById("status_"+i).value;
					if (status!='N') {
						var selCheckPoint= document.getElementById("chkListName_"+i);
						
							if (selCheckPoint.value=="") {
								alert("Please enter a check list.");
								selCheckPoint.focus();
								return false;
							}							
						
							if (selCheckPoint.value!="") {
								chkListSelected = true;
							}
					}
				}  // For Loop Ends Here
			} // Row Count checking End
			if (chkListSelected==false) {
				alert("Please add atleast one check list");
				return false;
			}
		
			if (!validateChkListRepeat()) {
				return false;
			}
		} // Chk Point condition ends here

		if (!confirmSave()) return false;
		return true;
	}

	//ADD MULTIPLE Item- ADD ROW START
	function addNewCheckList(tableId, chkListName, chkPointEntryId)
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
		
		
		var ds = "N";	
		//if( fieldId >= 1) 
		var imageButton = "<a href='###' onClick=\"setChkListItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
			
		var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input name='chkListEntryId_"+fieldId+"' type='hidden' id='chkListEntryId_"+fieldId+"' value='"+chkPointEntryId+"'>";	
		
		cell1.innerHTML	= "<input type='text' name='chkListName_"+fieldId+"' id='chkListName_"+fieldId+"' value='"+chkListName+"' size='38' autocomplete='off'>";
		cell2.innerHTML	= "<input type='checkbox' name='required_"+fieldId+"' id='required_"+fieldId+"' value='Y' class='chkBox'>";	
		cell3.innerHTML = imageButton+hiddenFields;	
		
		fieldId		= parseInt(fieldId)+1;	
		document.getElementById("hidTableRowCount").value = fieldId;		
	}

	function setChkListItemStatus(id)
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
	function validateChkListRepeat()
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
				var rv = document.getElementById("chkListName_"+j).value;
				if ( arr.indexOf(rv) != -1 )    {
					alert("Please make sure the check list is not duplicate.");
					document.getElementById("chkListName_"+j).focus();
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

