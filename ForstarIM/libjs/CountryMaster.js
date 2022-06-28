function validateCountryMaster(form)
{
	var countryName		= form.countryName.value;	
	
	if (countryName=="") {
		alert("Please enter a country name.");
		form.countryName.focus();
		return false;
	}

	var mode   = document.getElementById("hidMode").value; // Mode =1 : addmode, mode =2 : edit Mode
	
	var rowCount	= document.getElementById("hidTableRowCount").value;
	var itemsSelected = false;
	var portExist = false;
		
	if (rowCount>0) {
		for (i=0; i<rowCount; i++) {
			var rowStatus = document.getElementById("status_"+i).value;
			if (rowStatus!='N') {
				var portName 	= document.getElementById("portName_"+i);
				var portCategory = document.getElementById("portCategory_"+i);
				var portExist	 = document.getElementById("portExist_"+i).value;
					
				if (portName.value=="") {
					alert("Please enter a port name.");
					portName.focus();
					return false;
				}

				if (portCategory.value=="") {
					alert("Please select port category.");
					portCategory.focus();
					return false;
				}					
					
				if (portName.value!="") {
					itemsSelected = true;
				}

				if (portExist) {
					portExist = true;
					alert("The port you have entered is already in database.");
					portName.focus();
					return false;
				} 
			}
		    }  // For Loop Ends Here
		} // Row Count checking End

	if (!itemsSelected || portExist) {
		alert("Please add atleast one item");
		return false;
	}

	if (!validateItemRepeat()) {
		return false;
	}
	

	if (!confirmSave()) {
		return false;
	}
	return true;
}


// ADD MULTIPLE Item- ADD ROW START
function addNewRow(tableId, countryPortEntryId, portName, portCategory)
{
	var tbl		= document.getElementById(tableId);
	var lastRow	= tbl.rows.length;
	// alert(lastRow);
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
	var imageButton = "<a href='###' onClick=\"setRowItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	//else var imageButton = "&nbsp;&nbsp;&nbsp;&nbsp;";

	var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input type='hidden' name='countryPortEntryId_"+fieldId+"' id='countryPortEntryId_"+fieldId+"' value='"+countryPortEntryId+"'><input type='hidden' name='portExist_"+fieldId+"' id='portExist_"+fieldId+"' value='' readonly>";

	if (portCategory=='S') var selSeaType = 'selected=true';
	else 	var selSeaType = '';
	if (portCategory=='A') var selAirType = 'selected=true';
	else var selAirType = '';

	var pCategoryType = "<select name='portCategory_"+fieldId+"' id='portCategory_"+fieldId+"'><option value=''>--Select--</option>";
	pCategoryType     += "<option value='S' "+selSeaType+">SEA</option>";
	pCategoryType     += "<option value='A' "+selAirType+">AIR</option>";
	pCategoryType     += "</select>";

	cell1.innerHTML	= "<input name='portName_"+fieldId+"' type='text' id='portName_"+fieldId+"' value='"+portName+"' size='24' onblur=\"xajax_chkPortExist(document.getElementById('portName_"+fieldId+"').value, '"+fieldId+"', '"+countryPortEntryId+"');\" autocomplete='off' >";
	cell2.innerHTML = pCategoryType;		
	cell3.innerHTML = imageButton+hiddenFields;	
	
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
			var portName = document.getElementById("portName_"+j).value;
					
			if (pArr.indexOf(portName)!=-1) {
				alert(" item cannot be duplicate.");
				document.getElementById("portName_"+j).focus();
				return false;	
			}						
			pArr[pa++]	= portName;					
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
	
	function disableCountryMasterButton(mode)
	{		
		if (mode==1) {
			document.getElementById("cmdAdd").disabled = true;
			document.getElementById("cmdAdd1").disabled = true;
		} else if (mode==2) {
			document.getElementById("cmdSaveChange").disabled = true;
			document.getElementById("cmdSaveChange1").disabled = true;
		}
	}