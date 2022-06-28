function validateShippingCompanyMaster(form)
{
	var companyName		= form.companyName.value;	
	var selCity		= form.selCity.value;
	var state		= form.state.value;
	
	if (companyName=="") {
		alert("Please enter a shipping company name.");
		form.companyName.focus();
		return false;
	}

	if (selCity=="") {
		alert("Please select a city.");
		form.selCity.focus();
		return false;
	}

	if (state=="") {
		alert("Please select a state.");
		form.state.focus();
		return false;
	}

	/*
	var mode   = document.getElementById("hidMode").value; // Mode =1 : addmode, mode =2 : edit Mode
	var rowCount	= document.getElementById("hidTableRowCount").value;
	var itemsSelected = false;
		
	if (rowCount>0) {
		for (i=0; i<rowCount; i++) {
			var rowStatus = document.getElementById("status_"+i).value;
			if (rowStatus!='N') {
				var portName = document.getElementById("portName_"+i);
					
				if (portName.value=="") {
					alert("Please enter a port name.");
					portName.focus();
					return false;
				}			
					
				if (portName.value!="") {
					itemsSelected = true;
				}			
			}
		    }  // For Loop Ends Here
		} // Row Count checking End

	if (itemsSelected==false) {
		alert("Please add atleast one item");
		return false;
	}
	*/

	if (!validateItemRepeat()) {
		return false;
	}
	

	if (!confirmSave()) {
		return false;
	}
	return true;
}


// ADD MULTIPLE Item- ADD ROW START
function addNewRow(tableId, shipCompanyContactId, personName, designation, role, contactNo)
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
	var cell4	= row.insertCell(3);
	var cell5	= row.insertCell(4);
		
	cell1.className	= "listing-item"; cell1.align	= "center";
	cell2.className	= "listing-item"; cell2.align	= "center";
	cell3.className	= "listing-item"; cell3.align	= "center";
	cell4.className	= "listing-item"; cell4.align	= "center";
	cell5.className	= "listing-item"; cell5.align	= "center";
	
		
	var ds = "N";	
	//if( fieldId >= 1) 
	var imageButton = "<a href='###' onClick=\"setRowItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	//else var imageButton = "&nbsp;&nbsp;&nbsp;&nbsp;";

	var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input type='hidden' name='shipCompanyContactId_"+fieldId+"' id='shipCompanyContactId_"+fieldId+"' value='"+shipCompanyContactId+"'>";

	cell1.innerHTML	= "<input name='personName_"+fieldId+"' type='text' id='personName_"+fieldId+"' value=\""+unescape(personName)+"\" size='24'>";
	cell2.innerHTML	= "<input name='designation_"+fieldId+"' type='text' id='designation_"+fieldId+"' value=\""+unescape(designation)+"\" size='24'>";
	cell3.innerHTML	= "<input name='role_"+fieldId+"' type='text' id='role_"+fieldId+"' value=\""+unescape(role)+"\" size='24'>";
	cell4.innerHTML	= "<input name='contactNo_"+fieldId+"' type='text' id='contactNo_"+fieldId+"' value=\""+unescape(contactNo)+"\" size='24'>";
	cell5.innerHTML = imageButton+hiddenFields;	
	
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
			var personName = document.getElementById("personName_"+j).value;
					
			if (pArr.indexOf(personName)!=-1) {
				alert("Contact cannot be duplicate.");
				document.getElementById("personName_"+j).focus();
				return false;	
			}						
			pArr[pa++]	= personName;					
		}
	}	
	return true;
}

// ------------------------------------------------------
// Duplication check Ends here
// ------------------------------------------------------

		
	function enableCmdButton(mode)
	{
		if (mode==1) {
			document.getElementById("cmdAdd").disabled = false;
			document.getElementById("cmdAdd1").disabled = false;
		} else if (mode==2) {
			document.getElementById("cmdSaveChange").disabled = false;
			document.getElementById("cmdSaveChange1").disabled = false;
		}
	}
	
	function disableCmdButton(mode)
	{		
		if (mode==1) {
			document.getElementById("cmdAdd").disabled = true;
			document.getElementById("cmdAdd1").disabled = true;
		} else if (mode==2) {
			document.getElementById("cmdSaveChange").disabled = true;
			document.getElementById("cmdSaveChange1").disabled = true;
		}
	}
	

