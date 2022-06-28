function validateAddRMTestMaster(form)
{		
	var testName	= form.testName.value;
	 var testMethod	= form.testMethod.value;
		
	if (testName=="") {
		alert("Please enter a test Name.");
		form.testName.focus();
		return false;
	}

	 if (testMethod=="") {
		 alert("Please enter  test Method.");
		 form.testMethod.focus();
		 return false;
	 }
	
	if (!validateTestMethodRepeat()) {
		return false;
	}
	
	if (!confirmSave()) return false;	
	return true;
}

// ADD MULTIPLE Item- ADD ROW START
function addNewRow(testMethod, testMethodId, testMethodName)
{
	var tbl		= document.getElementById(testMethod);
	var lastRow	= tbl.rows.length;
	// alert(lastRow);
	var row		= tbl.insertRow(lastRow);
	
	row.height	= "28";
	row.className 	= "whiteRow";
	row.align 	= "center";
	row.id 		= "bRow_"+fldId;	
	
	var cell1	= row.insertCell(0);
	var cell2	= row.insertCell(1);
		
	cell1.className	= "listing-item"; cell1.align	= "center";
	cell2.className	= "listing-item"; cell2.align	= "center";

		
	var ds = "N";	
	//if( fieldId >= 1) 
	var imageButton = "<a href='###' onClick=\"setTestRowItemStatus('"+fldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	//else var imageButton = "&nbsp;&nbsp;&nbsp;&nbsp;";

	var hiddenFields = "<input name='bStatus_"+fldId+"' type='hidden' id='bStatus_"+fldId+"' value=''><input name='bIsFromDB_"+fldId+"' type='hidden' id='bIsFromDB_"+fldId+"' value='"+ds+"'><input type='hidden' name='rmTestMasterId_"+fldId+"' id='rmTestMasterId_"+fldId+"' value='"+testMethodId+"'>";

	cell1.innerHTML	= "<input name='test_"+fldId+"' type='text' id='test_"+fldId+"' value=\""+unescape(testMethodName)+"\" size='24'>";
	cell2.innerHTML = imageButton+hiddenFields;	
	
	fldId		= parseInt(fldId)+1;	
	document.getElementById("hidTestMethodTableRowCount").value = fldId;		
}

function setTestRowItemStatus(id)
{
	if (confirmRemoveItem()) {
		document.getElementById("bStatus_"+id).value = document.getElementById("bIsFromDB_"+id).value;
		document.getElementById("bRow_"+id).style.display = 'none';
//document.getElementById("bRow_"+id).style.display = 'block';			
	}
	return false;
}

// ------------------------------------------------------
// Duplication check starts here
// ------------------------------------------------------
var cArr = new Array();
var cArri = 0;	
function validateTestMethodRepeat()
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
	
	var rc = document.getElementById("hidTestMethodTableRowCount").value;
	var pArr	= new Array();	
	var pa		= 0;
	for (j=0; j<rc; j++) {
		var status = document.getElementById("bStatus_"+j).value;
		if (status!='N') {
			var testMethod = document.getElementById("test_"+j).value;
					
			if (pArr.indexOf(testMethod)!=-1) {
				alert("testMethod cannot be duplicate.");
				document.getElementById("test_"+j).focus();
				return false;	
			}						
			pArr[pa++]	= testMethod;					
		}
	}	
	return true;
}